<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  watch_ajax.php — служебные ajax watch-страницы (протокол 2012, format XML)
//  ?action_get_video_attributions_component=1&v=ID
//      → «Source videos: …» под описанием (у обычных видео — «none»)
//  ?action_get_flag_video_component=1&video_id=ID
//      → панель «Flag as inappropriate» (логаут → приглашение войти)
//  ?action_channel_videos=1&user_id=XX&video_id=ID
//      → стрип «See all N videos »» под кнопкой «N videos»
//  ?action_get_comments=1&v=ID&p=N
//      → следующая страница комментариев (AJAX-пагинация)
//  Ответ всегда: <root><return_code>0</return_code><html_content>…</html_content></root>
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

header('Content-Type: text/xml; charset=utf-8');
header('Cache-Control: no-cache');

function _wa_xml(string $html): void {
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<root><return_code>0</return_code><html_content><![CDATA[' . $html . ']]></html_content></root>';
    exit;
}

// timeAgo для комментариев (относительные строки InnerTube — как есть)
function _wa_time_ago(?string $s): string {
    $s = trim((string)$s);
    if ($s === '') return '';
    if (preg_match('/\bago\b/i', $s)) return preg_replace('/^Streamed\s+/i', '', $s);
    $ts = strtotime($s);
    if ($ts === false || $ts <= 0) return '';
    return date('M j, Y', $ts);
}

$req   = array_merge($_GET, $_POST);
$pixel = '/yts/img/pixel-vfl3z5WfW.gif';

// ─── AJAX-пагинация комментариев ──────────────────────────────────────────────
if (isset($req['action_get_comments'])) {
    $vid  = $req['v'] ?? ($req['video_id'] ?? '');
    $page = max(1, (int)($req['p'] ?? 1));
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $vid)) {
        _wa_xml('<div class="comments-section"><p class="comments-error">Comments unavailable.</p></div>');
    }

    // Токены страниц кэшируем по видео: [page => continuationToken]
    $tokFile = CACHE_DIR . '/comments_' . preg_replace('/[^A-Za-z0-9_-]/', '', $vid) . '.json';
    $tokens  = is_file($tokFile) ? (json_decode((string)file_get_contents($tokFile), true) ?: []) : [];

    // Определяем токен запрошенной страницы (реплей от 1-й при промахе кэша)
    $token = ($page === 1) ? null : ($tokens[(string)$page] ?? null);
    if ($page > 1 && $token === null) {
        // проходим страницы по порядку, пока не дойдём до нужной
        $cur = null;
        for ($p = 1; $p < $page; $p++) {
            $res = yt_fetch_comments($vid, $cur);
            if (empty($res['nextToken'])) break;
            $cur = $res['nextToken'];
            $tokens[(string)($p + 1)] = $cur;
        }
        $token = $tokens[(string)$page] ?? null;
    }

    $res = yt_fetch_comments($vid, $token);
    if (!empty($res['nextToken'])) {
        $tokens[(string)($page + 1)] = $res['nextToken'];
    }
    @file_put_contents($tokFile, json_encode($tokens));

    // Рендер списка комментариев (разметка 1:1 с watch.php)
    $itemsHtml = '';
    foreach ($res['comments'] as $c) {
        $author    = htmlspecialchars($c['author'], ENT_QUOTES);
        $authorUrl = htmlspecialchars($c['authorUrl'], ENT_QUOTES);
        $text      = htmlspecialchars($c['text']);
        $cid       = htmlspecialchars($c['id'], ENT_QUOTES);
        $likes     = htmlspecialchars($c['likes']);
        $time      = htmlspecialchars(_wa_time_ago($c['date']));
        $authorId  = htmlspecialchars($c['authorId'], ENT_QUOTES);
        $itemsHtml .= <<<HTML
  <li class="clearfix comment yt-tile-default" data-author-id="{$authorId}" data-id="{$cid}">
    <div class="comment-body">
  <div class="content-container">
    <div class="content">
          <div class="comment-text" dir="ltr"><p>{$text}</p></div>
        <p class="metadata">
          <span class="author "><a href="{$authorUrl}" class="yt-uix-sessionlink yt-user-name " dir="ltr">{$author}</a></span>
            <span class="time" dir="ltr"><a dir="ltr" href="/comment?lc={$cid}">{$time}</a></span>
              <span dir="ltr" class="comments-rating-positive">{$likes}<img class="comments-rating-thumbs-up" src="{$pixel}"></span>
        </p>
    </div>
  <div class="comment-actions"></div>
  </div>
    </div>
  </li>
HTML;
    }

    $countStr = htmlspecialchars($res['count'] ?? '');
    $hasNext  = !empty($res['nextToken']);
    $nextBtn  = $hasNext
        ? '<a href="/all_comments?v=' . htmlspecialchars($vid, ENT_QUOTES) . '&amp;page=' . ($page + 1)
            . '" class="yt-uix-button yt-uix-pager-button yt-uix-sessionlink yt-uix-button-default" data-page="' . ($page + 1)
            . '"><span class="yt-uix-button-content">Next &raquo;</span></a>'
        : '';

    if ($itemsHtml === '') {
        _wa_xml('<div class="comments-section"><ul class="comment-list"></ul></div>');
    }

    $html = '<div class="comments-section">'
        . '<h4><strong>All Comments</strong>' . ($countStr !== '' ? ' (' . $countStr . ')' : '') . '</h4>'
        . '<ul class="comment-list">' . $itemsHtml . '</ul>'
        . '</div>'
        . '<div class="comments-section"><div class="comments-pagination" data-ajax-enabled="true">'
        . '<div class="yt-uix-pager" role="navigation">' . $nextBtn . '</div>'
        . '</div></div>';

    _wa_xml($html);
}

// ─── Source videos (разворот описания) ────────────────────────────────────────
if (isset($req['action_get_video_attributions_component'])) {
    // как на оригинале 2012: у видео без CC-исходников — просто «none»
    _wa_xml('none');
}

// ─── Флаг «Report» ────────────────────────────────────────────────────────────
if (isset($req['action_get_flag_video_component'])) {
    _wa_xml('<div class="flag-video-signin" style="padding:10px">'
        . '<div class="yt-alert yt-alert-naked yt-alert-warn"><div class="yt-alert-content" role="alert">'
        . '<div class="yt-alert-message"><strong>'
        . '<a href="https://accounts.google.com/ServiceLogin?service=youtube">Sign in</a></strong> '
        . 'to report inappropriate content.</div>'
        . '</div></div></div>');
}

// ─── Стрип видео канала («N videos» возле Subscribe) ──────────────────────────
if (isset($req['action_channel_videos'])) {
    $uid = trim($req['user_id'] ?? '');
    $channelId = str_starts_with($uid, 'UC') ? $uid : 'UC' . $uid;
    if (!preg_match('/^UC[A-Za-z0-9_-]{22}$/', $channelId)) {
        _wa_xml('<p class="yt-spinner">No videos found.</p>');
    }

    $raw = innertube_post('browse', ['browseId' => $channelId, 'params' => 'EgZ2aWRlb3PyBgQKAjoA']);

    // имя канала + число видео
    $channelTitle = ''; $videoCount = '';
    $meta = innertube_post('browse', ['browseId' => $channelId]);
    if ($meta !== null) {
        $channelTitle = $meta['metadata']['channelMetadataRenderer']['title'] ?? '';
        array_walk_recursive($meta, function ($val) use (&$videoCount) {
            if ($videoCount !== '' || !is_string($val)) return;
            if (preg_match('/^([\d][\d,\.]*[KMB]?)\s+videos?$/i', trim($val), $m)) {
                $videoCount = expand_count($m[1]);
            }
        });
    }

    // сбор видео (videoRenderer / richItem+lockupViewModel)
    $vids = [];
    $collect = function (array $node, int $depth = 0) use (&$collect, &$vids, $channelTitle): void {
        if ($depth > 30 || count($vids) >= 8) return;
        foreach ($node as $k => $v) {
            if (!is_array($v)) continue;
            if ($k === 'videoRenderer' || $k === 'gridVideoRenderer') {
                $id = $v['videoId'] ?? '';
                if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) continue;
                $title = $v['title']['simpleText'] ?? runs_to_text($v['title']['runs'] ?? []);
                $dur   = $v['lengthText']['simpleText'] ?? '';
                $views = expand_count($v['viewCountText']['simpleText'] ?? '');
                if (trim($title) !== '') $vids[] = ['id' => $id, 'title' => $title, 'duration' => $dur, 'views' => $views];
            } elseif ($k === 'lockupViewModel' && ($v['contentType'] ?? '') === 'LOCKUP_CONTENT_TYPE_VIDEO') {
                $id = $v['contentId'] ?? '';
                if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) continue;
                $md    = $v['metadata']['lockupMetadataViewModel'] ?? [];
                $title = trim($md['title']['content'] ?? '');
                $views = '';
                foreach (($md['metadata']['contentMetadataViewModel']['metadataRows'] ?? []) as $row) {
                    foreach ($row['metadataParts'] ?? [] as $part) {
                        $txt = trim($part['text']['content'] ?? '');
                        if ($txt !== '' && stripos($txt, 'view') !== false) { $views = expand_count($txt); break 2; }
                    }
                }
                $dur = '';
                foreach (($v['contentImage']['thumbnailViewModel']['overlays'] ?? []) as $ov) {
                    foreach ($ov['thumbnailBottomOverlayViewModel']['badges'] ?? [] as $b) {
                        $t = $b['thumbnailBadgeViewModel']['text'] ?? '';
                        if (preg_match('/^\d+:\d{2}(:\d{2})?$/', $t)) { $dur = $t; break 2; }
                    }
                }
                if ($title !== '') $vids[] = ['id' => $id, 'title' => $title, 'duration' => $dur, 'views' => $views];
            } else {
                $collect($v, $depth + 1);
            }
        }
    };
    if ($raw !== null) $collect($raw);

    if (empty($vids)) {
        _wa_xml('');   // как в архиве: пусто → блок просто не наполняется
    }

    // ── Точные просмотры ───────────────────────────────────────────────────────
    // lockupViewModel отдаёт сокращённо («399M views»), а в 2012 в стрипе стояло
    // точное «9,810,048 views». Дотягиваем реальные числа батчем через /player.
    $mh = curl_multi_init();
    $hs = [];
    foreach ($vids as $i => $v) {
        $payload = json_encode([
            'videoId' => $v['id'], 'racyCheckOk' => true, 'contentCheckOk' => true,
            'context' => innertube_context(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => INNERTUBE_BASE_URL . 'player?key=' . INNERTUBE_API_KEY . '&prettyPrint=false',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-YouTube-Client-Name: 1',
                'X-YouTube-Client-Version: ' . INNERTUBE_CLIENT_VER,
            ],
        ]);
        _itube_ssl_opts($ch);
        _itube_proxy_opts($ch);
        curl_multi_add_handle($mh, $ch);
        $hs[$i] = $ch;
    }
    $running = null;
    do { curl_multi_exec($mh, $running); curl_multi_select($mh); } while ($running > 0);
    foreach ($hs as $i => $ch) {
        $body = curl_multi_getcontent($ch);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
        $d = json_decode((string)$body, true);
        $exact = (int)($d['videoDetails']['viewCount'] ?? 0);
        if ($exact > 0) $vids[$i]['views'] = number_format($exact, 0, '.', ',');
    }
    curl_multi_close($mh);

    if ($videoCount === '') $videoCount = (string)count($vids);
    $plural  = ($videoCount === '1') ? 'video' : 'videos';
    $author  = htmlspecialchars($channelTitle !== '' ? $channelTitle : '', ENT_QUOTES);
    $chanUrl = '/channel/' . htmlspecialchars($channelId, ENT_QUOTES) . '/videos?view=0';

    // Разбивка по слайдам: в архиве слайд = один <ul class="yt-uix-slider-slide">
    $perSlide = 4;
    $slides   = array_chunk($vids, $perSlide);

    // Плитки — разметка 1:1 с архивным ответом
    $slidesHtml = '';
    foreach ($slides as $slide) {
        $items = '';
        foreach ($slide as $v) {
            $id    = htmlspecialchars($v['id'], ENT_QUOTES);
            $full  = htmlspecialchars($v['title'], ENT_QUOTES);
            $dur   = htmlspecialchars($v['duration'], ENT_QUOTES);
            $views = htmlspecialchars($v['views'], ENT_QUOTES);
            $thumb = '//i3.ytimg.com/vi/' . $id . '/default.jpg';

            $items .= <<<HTML

          <li class="yt-uix-slider-slide-item ">

  <div class="video-list-item  yt-tile-visible ">
    <a href="/watch?v={$id}&amp;list=UL" class="related-video yt-uix-contextlink  yt-uix-sessionlink" data-sessionlink="feature=channel">    <span class="ux-thumb-wrap contains-addto "><span class="video-thumb ux-thumb yt-thumb-default-120 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="{$thumb}" data-thumb="{$thumb}" alt="{$full}" width="120"><span class="vertical-align"></span></span></span></span><span class="video-time">{$dur}</span>


  <button type="button" class="addto-button video-actions spf-nolink addto-watch-later-button yt-uix-button yt-uix-button-default yt-uix-button-short yt-uix-tooltip" onclick=";return false;" title="Watch Later" data-video-ids="{$id}" role="button"><span class="yt-uix-button-content">  <img src="{$pixel}" alt="Watch Later">
 </span></button>
</span>
<span dir="ltr" class="title" title="{$full}">{$full}</span><span class="stat attribution">by <span class="yt-user-name " dir="ltr">{$author}</span></span><span class="stat view-count">{$views} views</span></a>
  </div>

          </li>

HTML;
        }
        $slidesHtml .= "\n    <ul class=\"yt-uix-slider-slide \">\n\n{$items}\n\n    </ul>\n";
    }

    // Кнопки-номера слайдов
    $nums = '';
    foreach ($slides as $i => $_) {
        $cur = $i === 0 ? ' yt-uix-slider-num-current' : '';
        $nums .= '      <button class="yt-uix-slider-num' . $cur . ' yt-uix-button yt-uix-button-default" type="button" onclick=";return false;" data-slider-num="' . $i . '" role="button"><span class="yt-uix-button-content">' . ($i + 1) . " </span></button>\n";
    }
    $slideCount = count($slides);
    $seeAll     = htmlspecialchars($videoCount, ENT_QUOTES) . ' ' . $plural;

    $html = <<<HTML

  <div class="yt-uix-slider yt-rounded" id="watch-channel-discoverbox" data-slider-slides="{$slideCount}">
      <button class="yt-uix-button yt-uix-button-default yt-uix-slider-prev" rel="prev"><img class="yt-uix-slider-prev-arrow" src="{$pixel}" alt="previous"></button>
  <button class="yt-uix-button yt-uix-button-default yt-uix-slider-next" rel="next"><img class="yt-uix-slider-next-arrow" src="{$pixel}" alt="next"></button>

    <div class="yt-uix-slider-head">
        <span class="yt-uix-slider-nums yt-uix-pager">
{$nums}  </span>

      <div class="yt-uix-slider-title"><h2><a href="{$chanUrl}">See all {$seeAll}
 »</a></h2></div>
    </div>
    <div class="yt-uix-slider-body">
      <div class="yt-uix-slider-slides">

{$slidesHtml}
      </div>
    </div>
  </div>

HTML;

    _wa_xml($html);
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<root><return_code>1</return_code><error_message>Unknown action.</error_message></root>';
