<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  guide_ajax.php — ленты гайда главной страницы (протокол 2012/2013)
//  Вызывается www-guide-vflYBwsId.js:
//    /guide_ajax?action_load_system_feed=1&feed_name=trending|music|youtube
//    /guide_ajax?action_load_chart_feed=1&chart_name=entertainment|sports|…
//    /guide_ajax?action_load_user_feed=1&user_id=XXXX (внешний id канала, без UC)
//    /guide_ajax?action_load_main_feed=1
//  Ответ: {"feed_html": "<div class=feed-header…><div class=feed-container…>"}
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/config.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php');
$ytAccount = yt_account_info();
$ytAccessToken = yt_google_access_token();

$ytLoggedIn = isset($ytLoggedIn) ? $ytLoggedIn : ($ytAccount !== null);
error_log("Access Token:" . $ytAccessToken);
$oldera = true;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$req = array_merge($_GET, $_POST);

// dismiss-действия — просто подтверждаем
foreach (['action_dismiss_video', 'action_dismiss_channel', 'action_dismiss_item'] as $a) {
    if (isset($req[$a])) {
        echo json_encode(['success' => 1]);
        exit;
    }
}

// ─── Парсер videoRenderer / gridVideoRenderer → элемент ленты ─────────────────
function _ga_parse_video(array $vr): ?array {
    $id = $vr['videoId'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) return null;

    foreach ($vr['thumbnailOverlays'] ?? [] as $ov) {
        $style = $ov['thumbnailOverlayTimeStatusRenderer']['style'] ?? '';
        if (in_array($style, ['LIVE', 'SHORTS'], true)) return null;
    }

    $title = $vr['title']['simpleText'] ?? '';
    if ($title === '' && !empty($vr['title']['runs'])) $title = runs_to_text($vr['title']['runs']);
    if (trim($title) === '') return null;

    $author = ''; $authorId = '';
    foreach (['ownerText', 'shortBylineText', 'longBylineText'] as $f) {
        if (!empty($vr[$f]['runs'][0])) {
            $author   = $vr[$f]['runs'][0]['text'] ?? '';
            $authorId = $vr[$f]['runs'][0]['navigationEndpoint']['browseEndpoint']['browseId'] ?? '';
            break;
        }
    }

    $avatar = best_thumb($vr['channelThumbnailSupportedRenderers']['channelThumbnailWithLinkRenderer']['thumbnail']['thumbnails']
        ?? ($vr['channelThumbnail']['thumbnails'] ?? []));

    $thumb = best_thumb($vr['thumbnail']['thumbnails'] ?? []);
    if ($thumb === '') $thumb = "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";

    $duration = $vr['lengthText']['simpleText'] ?? '';
    if ($duration === '') {
        foreach ($vr['thumbnailOverlays'] ?? [] as $ov) {
            $duration = $ov['thumbnailOverlayTimeStatusRenderer']['text']['simpleText'] ?? '';
            if ($duration !== '') break;
        }
    }

    $viewsRaw = $vr['viewCountText']['simpleText']
        ?? ($vr['viewCountText']['runs'][0]['text']
        ?? ($vr['shortViewCountText']['simpleText'] ?? ''));
    $views = $viewsRaw !== '' ? expand_count($viewsRaw) : '';

    $desc = '';
    if (!empty($vr['descriptionSnippet']['runs'])) $desc = runs_to_text($vr['descriptionSnippet']['runs']);
    elseif (!empty($vr['detailedMetadataSnippets'][0]['snippetText']['runs'])) {
        $desc = runs_to_text($vr['detailedMetadataSnippets'][0]['snippetText']['runs']);
    }
    if (mb_strlen($desc) > 120) $desc = mb_substr($desc, 0, 117) . '...';

    return [
        'id' => $id, 'title' => $title, 'author' => $author, 'authorId' => $authorId,
        'authorAvatar' => $avatar, 'thumbnail' => $thumb, 'duration' => $duration,
        'views' => $views, 'publishedAgo' => $vr['publishedTimeText']['simpleText'] ?? '',
        'description' => $desc,
    ];
}

// ─── Парсер lockupViewModel (каналы 2025+) ────────────────────────────────────
function _ga_parse_lockup(array $lv): ?array {
    if (($lv['contentType'] ?? '') !== 'LOCKUP_CONTENT_TYPE_VIDEO') return null;
    $id = $lv['contentId'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) return null;

    $md    = $lv['metadata']['lockupMetadataViewModel'] ?? [];
    $title = trim($md['title']['content'] ?? '');
    if ($title === '') return null;

    // части бывают «канал», «N views», «N hours ago» — распознаём по
    // содержимому: на вкладке видео канала строки-«канал» нет вообще
    $author = ''; $authorId = ''; $views = ''; $ago = '';
    foreach (($md['metadata']['contentMetadataViewModel']['metadataRows'] ?? []) as $row) {
        foreach ($row['metadataParts'] ?? [] as $part) {
            $txt = trim($part['text']['content'] ?? '');
            if ($txt === '') continue;
            if (stripos($txt, 'view') !== false) {
                if ($views === '') $views = expand_count($txt);
            } elseif (preg_match('/\bago$|^Streamed|^Scheduled|^Premiere/i', $txt)) {
                if ($ago === '') $ago = preg_replace('/^Streamed\s+/i', '', $txt);
            } elseif ($author === '') {
                $author   = $txt;
                $authorId = $part['text']['commandRuns'][0]['onTap']['innertubeCommand']['browseEndpoint']['browseId'] ?? '';
            }
        }
    }

    $thumb = "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";
    $tvm   = $lv['contentImage']['thumbnailViewModel'] ?? [];
    $best  = best_thumb($tvm['image']['sources'] ?? []);
    if ($best !== '') $thumb = $best;

    $duration = '';
    foreach ($tvm['overlays'] ?? [] as $ov) {
        foreach ($ov['thumbnailBottomOverlayViewModel']['badges'] ?? [] as $b) {
            $t = $b['thumbnailBadgeViewModel']['text'] ?? '';
            if (preg_match('/^\d+:\d{2}(:\d{2})?$/', $t)) { $duration = $t; break 2; }
        }
    }

    return [
        'id' => $id, 'title' => $title, 'author' => $author, 'authorId' => $authorId,
        'authorAvatar' => '', 'thumbnail' => $thumb, 'duration' => $duration,
        'views' => $views, 'publishedAgo' => $ago, 'description' => '',
    ];
}

// ─── Рекурсивный сборщик ──────────────────────────────────────────────────────
function _ga_collect(array $node, array &$out, int $depth = 0): void {
    if ($depth > 30 || count($out) >= 25) return;
    foreach ($node as $key => $value) {
        if (!is_array($value)) continue;
        switch ($key) {
            case 'videoRenderer':
            case 'gridVideoRenderer':
                $p = _ga_parse_video($value);
                if ($p !== null) $out[] = $p;
                break;
            case 'richItemRenderer':
                $inner = $value['content'] ?? [];
                if (!empty($inner['videoRenderer'])) {
                    $p = _ga_parse_video($inner['videoRenderer']);
                    if ($p !== null) $out[] = $p;
                } else {
                    _ga_collect($inner, $out, $depth + 1);
                }
                break;
            case 'lockupViewModel':
                $p = _ga_parse_lockup($value);
                if ($p !== null) $out[] = $p;
                break;
            default:
                _ga_collect($value, $out, $depth + 1);
        }
    }
}

// ─── Источники данных ─────────────────────────────────────────────────────────
function _ga_videos_from_browse(string $browseId, string $params = ''): array {
    global $ytLoggedIn, $ytAccessToken;
    $payload = ['browseId' => $browseId];
    if ($params !== '') $payload['params'] = $params;
    
    $raw = innertube_post('browse', $payload);

    $out = [];
    if ($raw !== null) _ga_collect($raw, $out);
    
    return $out;
}

function _ga_videos_from_browse_auth(string $browseId, string $params = ''): array {
    global $ytLoggedIn, $ytAccessToken;
    $payload = ['browseId' => $browseId];
    if ($params !== '') $payload['params'] = $params;
    
    $raw = yt_innertube_authed('browse', $payload);

    if ($raw !== null) {
        file_put_contents(__DIR__ . '/browse.json', json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } else {
        file_put_contents(__DIR__ . '/browse.json', json_encode(['error' => 'API returned null/empty response']));
    }

    $out = [];
    if ($raw !== null) _ga_collect($raw, $out);
    
    return $out;
}

function _ga_videos_from_search(string $query): array {
    // EgIQAQ== — фильтр «только видео»
    $raw = innertube_post('search', ['query' => $query, 'params' => 'EgIQAQ%3D%3D']);
    $out = [];
    if ($raw !== null) _ga_collect($raw, $out);
    if (empty($out)) {
        $raw = innertube_post('search', ['query' => $query]);
        if ($raw !== null) _ga_collect($raw, $out);
    }
    return $out;
}

// ─── Определяем фид ───────────────────────────────────────────────────────────
$feedTitle = 'From YouTube';
$feedKey   = 'youtube';
$videos    = [];

if (isset($req['action_load_main_feed'])) {
    $videos = _ga_videos_from_browse('FEwhat_to_watch');
    if (empty($videos)) $videos = _ga_videos_from_search('popular videos');
    $feedTitle = 'From YouTube';
} elseif (isset($req['action_load_system_feed']) || isset($req['action_load_personal_feed']) || isset($req['action_load_social_feed'])) {
    $feedKey = strtolower(trim($req['feed_name'] ?? 'youtube'));
    // FEtrending мёртв (2025), FEwhat_to_watch без логина пуст — берём /search
    switch ($feedKey) {
        case 'trending':
            $videos = _ga_videos_from_search('trending videos today' . ($oldera ? " before:2012" : ""));
            $feedTitle = 'Trending';
            break;
        case 'subscriptions':
            $videos = _ga_videos_from_browse_auth("FEsubscriptions");
            $feedTitle = 'Subscriptions';
            break;
        case 'music':
            $videos = _ga_videos_from_search('popular music videos' . ($oldera ? " before:2012" : ""));
            $feedTitle = 'Music';
            break;
        case 'popular':
            $videos = _ga_videos_from_search('popular videos this week' . ($oldera ? " before:2012" : ""));
            $feedTitle = 'Popular';
            break;
        default:
            $videos = _ga_videos_from_search('popular videos' . ($oldera ? " before:2012" : ""));
            $feedTitle = 'From YouTube';
    }
} elseif (isset($req['action_load_chart_feed'])) {
    $feedKey = strtolower(trim($req['chart_name'] ?? ($req['feed_name'] ?? 'entertainment')));
    $chartQueries = [
        'entertainment' => 'entertainment videos' . ($oldera ? " before:2012" : ""),
        'sports'        => 'sports highlights' . ($oldera ? " before:2012" : ""),
        'comedy'        => 'comedy videos' . ($oldera ? " before:2012" : ""),
        'film'          => 'film and animation' . ($oldera ? " before:2012" : ""),
        'gadgets'       => 'tech gadgets review' . ($oldera ? " before:2012" : ""),
        'music'         => 'popular music videos' . ($oldera ? " before:2012" : ""),
        'gaming'        => 'gaming videos' . ($oldera ? " before:2012" : ""),
        'news'          => 'news today' . ($oldera ? " before:2012" : "")
        ,
    ];
    $feedTitle = ucfirst($feedKey);
    $videos = _ga_videos_from_search($chartQueries[$feedKey] ?? ($feedKey . ' videos'));
} elseif (isset($req['action_load_user_feed']) || isset($req['action_load_browse_feed'])) {
    $uid = trim($req['user_id'] ?? ($req['browse_channel_id'] ?? ''));
    $browseId = str_starts_with($uid, 'UC') ? $uid : 'UC' . $uid;
    $feedKey  = 'user';
    if (preg_match('/^UC[A-Za-z0-9_-]{22}$/', $browseId)) {
        // вкладка Videos канала
        $videos = _ga_videos_from_browse($browseId, 'EgZ2aWRlb3PyBgQKAjoA');
        // имя канала для заголовка
        if (!empty($videos[0]['author'])) $feedTitle = $videos[0]['author'];
        else $feedTitle = 'Channel';
        // у видео с вкладки канала нет имени автора — заполним заголовком
        $chanMeta = innertube_post('browse', ['browseId' => $browseId]);
        if ($chanMeta !== null) {
            $t = $chanMeta['metadata']['channelMetadataRenderer']['title'] ?? '';
            if ($t !== '') $feedTitle = $t;
            $av = best_thumb($chanMeta['metadata']['channelMetadataRenderer']['avatar']['thumbnails'] ?? []);
            foreach ($videos as &$v) {
                if ($v['author'] === '') { $v['author'] = $feedTitle; $v['authorId'] = $browseId; }
                if ($v['authorAvatar'] === '' && $av !== '') $v['authorAvatar'] = $av;
            }
            unset($v);
        }
    }
} else {
    // topic/show/прочее — отдадим общую ленту, чтобы гайд не падал
    $videos = _ga_videos_from_search('popular videos');
}

// ─── Рендер feed_html (разметка 1:1 с index.php) ──────────────────────────────
function _ga_render_feed(string $feedTitle, string $feedKey, array $videos): string {
    $pixel = '/yts/img/pixel-vfl3z5WfW.gif';
    $e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

    $items = '';
    foreach ($videos as $fi) {
        $vid    = $e($fi['id']);
        $title  = $e($fi['title']);
        $author = $e($fi['author']);
        $chan   = $e($fi['authorId']);
        $thumb  = $e($fi['thumbnail']);
        // default_avatar(): пусто → /dynamic/pfp/default.png, живая ава → через pfp-прокси
        // (моно­грамма → дефолт 2012 / реальная → как есть). Так же, как на watch/channel.
        $avatar = $e(default_avatar((string)($fi['authorAvatar'] ?? '')));
        $dur    = $e($fi['duration']);
        $views  = $e($fi['views']);
        $ago    = $e($fi['publishedAgo']);
        $desc   = $e($fi['description']);

        $viewsHtml = $fi['views'] !== ''
            ? '<span class="bull">•</span><span class="view-count">' . $views . ' views</span>'
            : '';
        $descHtml = $fi['description'] !== ''
            ? '<div class="description"><p>' . $desc . '</p></div>'
            : '';

        $items .= <<<HTML
      <li>
          <div class="feed-item-container first">
          <div class="feed-author-bubble-container">
<a href="/channel/{$chan}?feature=g-logo-xit" class="feed-author-bubble">  <span class="feed-item-author">
      <span class="video-thumb ux-thumb yt-thumb-square-28"><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner">
        <img src="{$pixel}" alt="{$author}" data-thumb="{$avatar}" width="28">
        <span class="vertical-align"></span></span></span></span>
  </span>
</a>  </div>
    <div class="feed-item-main">
      <div class="feed-item-header">
        <span class="feed-item-actions-line">
          <span class="feed-item-owner"><a href="/channel/{$chan}?feature=g-logo-xit" class="yt-uix-sessionlink yt-user-name" dir="ltr">{$author}</a></span>
 uploaded a video
                <span class="feed-item-time">{$ago}</span>
        </span>
      </div>
            <div class="feed-item-content-wrapper clearfix context-data-item"
                data-context-item-actionverb="uploaded"
                data-context-item-title="{$title}"
                data-context-item-type="video"
                data-context-item-time="{$dur}"
                data-context-item-user="{$author}"
                data-context-item-id="{$vid}"
                data-context-item-views="{$views} views">
    <div class="feed-item-thumb">
          <a class="ux-thumb-wrap contains-addto yt-uix-contextlink yt-uix-sessionlink" href="/watch?v={$vid}&amp;feature=g-logo-xit">
      <span class="video-thumb ux-thumb yt-thumb-default-185"><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="{$thumb}" alt="Thumbnail" data-thumb="{$thumb}" width="185"><span class="vertical-align"></span></span></span></span>
    <span class="video-time">{$dur}</span>
  </a>
    </div>
    <div class="feed-item-content">
      <h4>
        <a class="feed-video-title title yt-uix-contextlink yt-uix-sessionlink secondary" href="/watch?v={$vid}&amp;feature=g-logo-xit">{$title}</a>
      </h4>
        <div class="metadata">
      <a href="/channel/{$chan}?feature=g-logo-xit" class="yt-uix-sessionlink yt-user-name" dir="ltr">{$author}</a>
        {$viewsHtml}
      {$descHtml}
  </div>
    </div>
  </div>
    </div>
  </div>
  <div class="feed-item-dismissal-notices"></div>
      </li>
HTML;
    }

    return <<<HTML
        <div class="feed-header no-metadata">
      <div class="feed-header-thumb">
          <img class="feed-header-icon {$feedKey}" src="{$pixel}" alt="">
      </div>
      <div class="feed-header-details context-source-container" data-context-source="{$feedTitle}">
        <h2>    {$feedTitle}
</h2>
      </div>
  </div>
      <div class="feed-container" data-filter-type="" data-view-type="">
    <div class="feed-page">
          <ul class="context-data-container">
{$items}
      </ul>
    </div>
  </div>
HTML;
}

if (empty($videos)) {
    // guide JS покажет #feed-error при onError → отдадим валидный, но пустой фид
    echo json_encode([
        'feed_html' => '<div class="feed-header no-metadata"><div class="feed-header-details"><h2>' .
            htmlspecialchars($feedTitle) . '</h2></div></div>' .
            '<div class="feed-container"><div class="feed-page"><p class="feed-message">' .
            'No videos found. Please try again later.</p></div></div>',
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'feed_html' => _ga_render_feed($feedTitle, $feedKey, $videos),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
