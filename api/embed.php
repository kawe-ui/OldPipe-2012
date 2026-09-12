<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  api.php  —  InnerTube /player + /next + /browse(comments)  |  May 2026
//  Экспортирует все переменные для watch.php без изменений в watch.php
// ═══════════════════════════════════════════════════════════════════════════════

// stripped out embed
// Copyright (C) Pentagox

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

// ─── Defaults ─────────────────────────────────────────────────────────────────
$videoTitle           = null;
$videoAuthor          = null;
$videoAuthorId        = null;
$videoAuthorUrl       = null;
$videoDate            = null;
$videoDescription     = null;
$videoDescriptionHTML = null;
$videoThumbnail       = null;
$viewCount            = null;
$likeCount            = null;
$dislikeCount         = 0;
$commentsCount        = 0;
$videoLength          = 0;
$videoLengthFormat    = '0:00';
$videoRating          = null;
$likePercent          = 0;
$dislikePercent       = 0;
$videoTags            = '';
$videoCategory        = '';
$channelVideoCount    = null;
$channelSubscriberCount = null;
$channelAvatar        = DEFAULT_CHANNEL_AVATAR;
$videoMetadataUrl     = '';
$comments_enabled     = false;
$videoComments        = [];
$relatedVideos        = [];
$streamFormats        = [];
$highestQualityFormat = null;
$html5                = null;
$Calender = ['Days' => 0, 'Hours' => 0, 'Minutes' => 0, 'Seconds' => 0];

// ─── video_id ─────────────────────────────────────────────────────────────────
if (empty($video_id)) {
    $video_id = isset($_GET['v']) ? trim($_GET['v']) : '';
}
if (isset($_GET['html5'])) {
    $html5 = $_GET['html5'];
}
if (empty($video_id)) return;

// Панель «More info» плеера тянет метаданные отсюда (html5player: wf('/get_video_metadata'))
$videoMetadataUrl = '/get_video_metadata?video_id=' . rawurlencode($video_id);

// ─── DEBUG: ?v=ID&apidebug ────────────────────────────────────────────────────
if (isset($_GET['apidebug'])) {
    $dbgPlayer = innertube_post('player', ['videoId' => $video_id, 'racyCheckOk' => true, 'contentCheckOk' => true]);
    $dbgNext   = innertube_post('next',   ['videoId' => $video_id]);
    echo '<pre style="background:#111;color:#0f0;padding:10px;font-size:10px;position:fixed;top:0;left:0;right:0;max-height:80vh;overflow:auto;z-index:9999">';
    echo "=== /player ===\n" . json_encode($dbgPlayer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n";
    echo "=== /next ===\n"   . json_encode($dbgNext,   JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    echo '</pre>';
    exit;
}

// ═══════════════════════════════════════════════════════════════════════════════
//  1. /player — метаданные, thumbnail, stream formats
// ═══════════════════════════════════════════════════════════════════════════════
$playerRaw = innertube_post('player', [
    'videoId'        => $video_id,
    'racyCheckOk'    => true,
    'contentCheckOk' => true,
]);

if ($playerRaw === null) return;

$videoDetails  = $playerRaw['videoDetails']                            ?? [];
$microformat   = $playerRaw['microformat']['playerMicroformatRenderer'] ?? [];
$streamingData = $playerRaw['streamingData']                           ?? [];

// ── videoDetails ──────────────────────────────────────────────────────────────
$videoTitle    = $videoDetails['title']     ?? null;
$videoAuthorId = $videoDetails['channelId'] ?? null;
$videoAuthor   = $videoDetails['author']    ?? null;
$videoAuthorUrl = $videoAuthorId ? '/channel/' . $videoAuthorId : null;

$videoDescription = $videoDetails['shortDescription'] ?? null;
if ($videoDescription !== null) {
    $videoDescriptionHTML = nl2br(htmlspecialchars($videoDescription, ENT_QUOTES, 'UTF-8'));
}

$videoLength = (int)($videoDetails['lengthSeconds'] ?? 0);
$videoLengthFormat = SecondsFormater($videoLength);

$rem = $videoLength;
$Calender['Days']    = (int)floor($rem / 86400); $rem %= 86400;
$Calender['Hours']   = (int)floor($rem / 3600);  $rem %= 3600;
$Calender['Minutes'] = (int)floor($rem / 60);
$Calender['Seconds'] = $rem % 60;

$rawViews = (int)($videoDetails['viewCount'] ?? 0);
$viewCount = $rawViews > 0 ? number_format($rawViews, 0, '.', ',') : null;

$videoTags = !empty($videoDetails['keywords'])
    ? implode(',', $videoDetails['keywords'])
    : '';

// Thumbnail — берём наилучшее
$tArr = $videoDetails['thumbnail']['thumbnails'] ?? [];
if (!empty($tArr)) {
    usort($tArr, fn($a, $b) => (int)($b['width'] ?? 0) - (int)($a['width'] ?? 0));
    $videoThumbnail = $tArr[0]['url'] ?? null;
}
if (empty($videoThumbnail)) {
    $videoThumbnail = "https://i.ytimg.com/vi/{$video_id}/maxresdefault.jpg";
}

// ── microformat ───────────────────────────────────────────────────────────────
$publishedDate = $microformat['publishDate'] ?? $microformat['uploadDate'] ?? null;
if ($publishedDate) {
    $videoDate = date('M j, Y', strtotime($publishedDate));
}
$videoCategory = $microformat['category'] ?? '';

// Скрытое (unlisted) видео → плашка «This video is unlisted» на watch-странице
$videoIsUnlisted = (bool)($microformat['isUnlisted'] ?? false);

// ─── Плейлист (?list=…) → нижний playlist-bar на watch ───────────────────────
$watchPlaylist = null;
$plId = trim((string)($_GET['list'] ?? ''));
if ($plId !== '' && preg_match('/^[A-Za-z0-9_-]{10,60}$/', $plId)) {
    $plRaw = innertube_post('next', ['videoId' => $video_id, 'playlistId' => $plId]);
    $pl = $plRaw['contents']['twoColumnWatchNextResults']['playlist']['playlist'] ?? null;
    if (is_array($pl) && !empty($pl['contents'])) {
        $plItems = [];
        $pos = 0;
        $plCurrent = 1;
        foreach ($pl['contents'] as $it) {
            $r = $it['playlistPanelVideoRenderer'] ?? null;
            if ($r === null || empty($r['videoId'])) continue;
            $pos++;
            $selected = !empty($r['selected']);
            if ($selected) $plCurrent = $pos;
            $plItems[] = [
                'id'       => $r['videoId'],
                'title'    => $r['title']['simpleText'] ?? ($r['title']['runs'][0]['text'] ?? ''),
                'author'   => $r['shortBylineText']['runs'][0]['text'] ?? '',
                'position' => $pos,
                'selected' => $selected,
            ];
        }
        if ($plItems !== []) {
            $watchPlaylist = [
                'id'    => $plId,
                'title' => is_string($pl['title'] ?? null) ? $pl['title'] : ($pl['title']['simpleText'] ?? 'Playlist'),
                'index' => $plCurrent,
                'count' => (int)($pl['totalVideos'] ?? count($plItems)) ?: count($plItems),
                'items' => $plItems,
            ];
        }
    }
}

// ── Stream formats ────────────────────────────────────────────────────────────
$allFormats = array_merge(
    $streamingData['formats']         ?? [],
    $streamingData['adaptiveFormats'] ?? []
);
foreach ($allFormats as $fmt) {
    $mimeType = $fmt['mimeType'] ?? '';
    if (empty($mimeType)) continue;
    $streamFormats[] = [
        'itag'     => $fmt['itag']           ?? 0,
        'mimeType' => $mimeType,
        'quality'  => $fmt['qualityLabel']   ?? ($fmt['quality'] ?? ''),
        'width'    => $fmt['width']          ?? 0,
        'height'   => $fmt['height']         ?? 0,
        'fps'      => $fmt['fps']            ?? 0,
        'bitrate'  => $fmt['averageBitrate'] ?? ($fmt['bitrate'] ?? 0),
        'url'      => $fmt['url']            ?? '',
    ];
}
$muxed = array_filter($streamFormats, fn($f) => !empty($f['url']) && !str_contains($f['mimeType'], 'audio/'));
if (!empty($muxed)) {
    usort($muxed, fn($a, $b) => (int)$b['height'] - (int)$a['height']);
    $highestQualityFormat = reset($muxed);
}

// ═══════════════════════════════════════════════════════════════════════════════
//  2. /next — лайки, похожие видео, continuationToken для комментариев
// ═══════════════════════════════════════════════════════════════════════════════
$nextRaw = innertube_post('next', ['videoId' => $video_id]);

// ── Рекурсивный поиск рендерера по имени ключа ────────────────────────────────
function _find_renderer(array $data, string $key, int $depth = 0): ?array {
    if ($depth > 25) return null;
    foreach ($data as $k => $v) {
        if ($k === $key && is_array($v)) return $v;
        if (is_array($v)) {
            $r = _find_renderer($v, $key, $depth + 1);
            if ($r !== null) return $r;
        }
    }
    return null;
}

// ── Сбор всех toggleButtonRenderer ───────────────────────────────────────────
function _collect_toggle_buttons(array $data, array &$out, int $depth = 0): void {
    if ($depth > 15) return;
    foreach ($data as $k => $v) {
        if ($k === 'toggleButtonRenderer' && is_array($v)) $out[] = $v;
        elseif (is_array($v)) _collect_toggle_buttons($v, $out, $depth + 1);
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
//  3. Лайки / дизлайки — RYD + кэш-fallback (Требование 6)
//     Если автор скрыл рейтинг или RYD недоступен, берём последние известные
//     значения из локального кэша, чтобы шкала sparkbars не ломалась.
// ═══════════════════════════════════════════════════════════════════════════════
// Лайки, которые InnerTube уже дал строкой, — подсказка для yt_video_ratings()
$likesHint = null;
if (!empty($likeCount)) {
    $likesHint = (int)str_replace([',', ' ', "\u{00a0}"], '', $likeCount);
}

$__ratings   = yt_video_ratings($video_id, $likesHint);
$likesInt    = $__ratings['likes'];
$dislikesInt = $__ratings['dislikes'];

if (empty($viewCount) && !empty($__ratings['viewCount'])) {
    $viewCount = number_format((int)$__ratings['viewCount'], 0, '.', ',');
}

// Финальные строки + проценты для шкалы (никогда не оставляем UI сломанным)
if ($likesInt !== null)    $likeCount    = number_format($likesInt, 0, '.', ',');
if ($dislikesInt !== null) $dislikeCount = number_format($dislikesInt, 0, '.', ',');

$totalVotes = (int)$likesInt + (int)$dislikesInt;
if ($totalVotes > 0) {
    $likePercent    = round(((int)$likesInt    / $totalVotes) * 100, 2);
    $dislikePercent = round(((int)$dislikesInt / $totalVotes) * 100, 2);
} else {
    // нет данных вообще — показываем нейтральную (полностью зелёную) шкалу как в 2012
    $likePercent    = 100;
    $dislikePercent = 0;
}

// ── channelVideoCount ─────────────────────────────────────────────────────────
if (!empty($videoAuthorId)) {
    // Сначала пробуем microformat (быстро, без лишнего запроса)
    if (isset($microformat['videoCount']) && $microformat['videoCount'] !== '') {
        $channelVideoCount = number_format((int)$microformat['videoCount'], 0, '.', ',');
    }

    // Если microformat не дал — запрашиваем страницу канала.
    // Хедер канала 2025+ (pageHeaderRenderer) отдаёт счётчик только строкой
    // вида «123 videos» / «1.2K videos» в metadata-рядах, поэтому ищем её
    // по всем строковым полям ответа.
    //
    // ВАЖНО (Требование 3): сохраняем ОРИГИНАЛЬНЫЙ формат с дробной частью и
    // суффиксом — «7.5K», «1.2M», «7,5 тыс.» — НЕ разворачиваем в целое число.
    if (empty($channelVideoCount)) {
        $chanRaw = innertube_post('browse', [
            'browseId' => $videoAuthorId,
            'params'   => 'EgZ2aWRlb3PyBgQKAjoA',
        ]);

        if ($chanRaw !== null) {
            // Метод A: старый videoCountText (runs/simpleText)
            $videoCountText = null;
            array_walk_recursive($chanRaw, function ($val, $key) use (&$videoCountText) {
                if ($videoCountText !== null) return;
                if ($key === 'videoCountText' && is_array($val)) {
                    $videoCountText = $val;
                }
            });
            if ($videoCountText !== null) {
                $vcStr = runs_to_text($videoCountText['runs'] ?? []);
                if ($vcStr === '') $vcStr = $videoCountText['simpleText'] ?? '';
                // «1,234 videos» / «7.5K videos» → сохраняем «1,234» / «7.5K»
                if (preg_match('/^([\d][\d.,\x{00A0}\s]*(?:[KMB]|\s*тыс\.?|\s*млн\.?|\s*млрд\.?)?)/u', trim($vcStr), $m)) {
                    $channelVideoCount = trim($m[1]);
                }
            }

            // Метод B: строка «N videos» / «7.5K videos» в ответе (pageHeaderRenderer)
            if (empty($channelVideoCount)) {
                $vcFound = null;
                array_walk_recursive($chanRaw, function ($val) use (&$vcFound) {
                    if ($vcFound !== null || !is_string($val)) return;
                    // сохраняем оригинал: «7.5K», «1,234», «1.2M», «7,5 тыс.»
                    if (preg_match('/^([\d][\d.,\x{00A0}\s]*(?:[KMB]|\s*тыс\.?|\s*млн\.?|\s*млрд\.?)?)\s+(?:videos?|видео)$/iu', trim($val), $m)) {
                        $vcFound = trim($m[1]);
                    }
                });
                if ($vcFound !== null) $channelVideoCount = $vcFound;
            }

            // Метод C: считаем количество видео в сетке канала (последний резерв)
            if (empty($channelVideoCount)) {
                $gridCount = 0;
                $stack = [$chanRaw];
                while ($stack) {
                    $node = array_pop($stack);
                    foreach ($node as $k => $v) {
                        if (!is_array($v)) continue;
                        if ($k === 'gridVideoRenderer' || $k === 'richItemRenderer' || $k === 'lockupViewModel') $gridCount++;
                        else $stack[] = $v;
                    }
                }
                if ($gridCount > 0) {
                    $channelVideoCount = $gridCount . '+';
                }
            }
        }
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
//  4.5 Данные для флеш-плеера 2012 (локальные get_video / get_video_info)
// ═══════════════════════════════════════════════════════════════════════════════
$streamFormatsByItag = [];
foreach ($streamFormats as $fmt) {
    if (!empty($fmt['itag'])) $streamFormatsByItag[(int)$fmt['itag']] = $fmt;
}

// Все качества, которые можем отдать, — их проксирует /get_video.php:
// 360p muxed напрямую, остальные склеиваются ремуксом. Раскладка по классическим
// itag 2012 (5/18/35/22/37) живёт в yt_quality_map().
$localStreams     = innertube_get_streams($video_id);
$playerQualityMap = yt_quality_map($localStreams);

// Меню качества плеера строится из fmt_list + url_encoded_fmt_stream_map,
// поэтому перечисляем качества по убыванию — лучшее первым.
uasort($playerQualityMap, fn(array $a, array $b) => $b['height'] <=> $a['height']);

$playerFmtListParts   = [];
$playerStreamMapParts = [];
foreach ($playerQualityMap as $itag => $q) {
    $w = $q['width'] ?: 640;
    $h = $q['height'] ?: 360;
    // формат записи 2012: itag/ШxВ/видео-кодек/аудио-кодек/битрейт
    $playerFmtListParts[] = "{$itag}/{$w}x{$h}/9/0/115";

    $localUrl = $HTTP_Host_Full . '/get_video.php?video_id=' . urlencode($video_id) . '&itag=' . $itag;
    $playerStreamMapParts[] = 'itag=' . $itag
        . '&url=' . urlencode($localUrl)
        . '&type=' . urlencode($q['mime'])
        . '&quality=' . $q['quality']
        . '&fallback_host=' . urlencode($HTTP_Host);
}
$playerFmtList   = implode(',', $playerFmtListParts);
$playerStreamMap = implode(',', $playerStreamMapParts);

// rvs — related videos для end-screen плеера
$playerRvsParts = [];
foreach (array_slice($relatedVideos, 0, 12) as $rv) {
    $lenSec = 0;
    if (preg_match('/^(?:(\d+):)?(\d+):(\d{2})$/', $rv['duration'], $dm) === 1) {
        $lenSec = ((int)($dm[1] ?: 0)) * 3600 + ((int)$dm[2]) * 60 + (int)$dm[3];
    }
    $playerRvsParts[] = http_build_query([
        'view_count'     => $rv['views'],
        'author'         => $rv['author'],
        'title'          => $rv['title'],
        'length_seconds' => $lenSec,
        'id'             => $rv['id'],
    ]);
}
$playerRvs = implode(',', $playerRvsParts);

// Итоговые flashvars — одинаковы для inline-<embed> и yt.playerConfig
$playerFlashVars = [
    'video_id'       => $video_id,
    'vid'            => $video_id,
    'title'          => $videoTitle ?? '',
    'length_seconds' => $videoLength,
    'keywords'       => $videoTags,
    'fmt_list'       => $playerFmtList,
    'url_encoded_fmt_stream_map' => $playerStreamMap,
    'rvs'            => $playerRvs,
    'ttsurl'         => '/timedtext',
    'allow_embed'    => 1,
    'allow_ratings'  => 1,
    'vq'             => 'auto',
    'autohide'       => '2',
    'autoplay'       => '0',
    'cr'             => 'US',
    'hl'             => 'en_US',
    'csi_page_type'  => 'watch5',
    'enablejsapi'    => 1,
    'enablecsi'      => '0',
    'sendtmp'        => '0',
    'no_get_video_log' => '1',
    'iv_load_policy' => 1,
    'metadata_url'   => $videoMetadataUrl,
    'iv_read_url'    => $HTTP_Host_Full . '/annotations_iv/read2.php?video_id=' . $video_id,
    'iv_module'      => '/yts/swfbin/modules/2012_iv3_module-vfl7CyC10.swf',
    'cc3_module'     => '/yts/swfbin/modules/2012_subtitles3_module-vflX-PxNh.swf',
    'showpopout'     => 1,
    'pltype'         => 'contentugc',
    'plid'           => 'AATQJdc1-gxnOCVY',
    'timestamp'      => time(),
    'referrer'       => $HTTP_Host_Full . '/watch?v=' . $video_id,
    'sdetail'        => 'p:/watch/' . $video_id,
    'sourceid'       => 'y',
    'watermark'      => '',
    't'              => 'vjVQa1PpcFPTXpgY6mYDGHvfyt_a_bp1XtYr2eqEPNU=',
];
$playerSwfUrl        = '/yts/swfbin/2012lplayer_localhost_patched.swf';
$playerFlashVarsQS   = http_build_query($playerFlashVars, '', '&');

// Каким плеером играть: по умолчанию — настоящий SWF 2012 (через Ruffle), как
// в оригинале. HTML5-плеер 2012 подключён как поддержка: www-core сам уходит
// на него, если флеша нет, а ?html5=1 включает его принудительно (спасение,
// когда Ruffle крашится). ?flash=1 — явно флеш.
$useFlashPlayer = !($html5 !== null && $html5 !== '' && $html5 !== '0');
if (isset($_GET['flash']) && $_GET['flash'] !== '0') $useFlashPlayer = true;

// yt.playerConfig для www-core (yt.player.update пересоздаёт плеер из него)
$ytPlayerConfig = [
    "assets" => [
        "html" => "/html5_player_template",
        "css" => "http://s.ytimg.com/yt/cssbin/www-player-vflNffkIU.css",
        "js" => "http://s.ytimg.com/yt/jsbin/html5player-vflZifxKH.js"
    ],
    "url" => $playerSwfUrl,
    "min_version" => "8.0.0",
    "args" => $playerFlashVars,
    "url_v9as2" => "http://s.ytimg.com/yt/swfbin/cps-vflEiiy1p.swf",
    "params" => [
        "allowscriptaccess" => "always",
        "allowfullscreen" => "true",
        "bgcolor" => "#000000"
    ],
    "attrs" => [
        "width" => "100%",
        "id" => "video-player",
        "height" => "100%"
    ],
    "url_v8" => "http://s.ytimg.com/yt/swfbin/cps-vflEiiy1p.swf",
    "html5" => !$useFlashPlayer
];

// Готовый <embed> для inline-вставки (как в архивном оригинале)
$swfEmbedHtml = '<embed type="application/x-shockwave-flash" src="' . $playerSwfUrl . '" id="movie_player"'
    . ' flashvars="' . htmlspecialchars($playerFlashVarsQS, ENT_QUOTES, 'UTF-8') . '"'
    . ' allowscriptaccess="always" allowfullscreen="true" bgcolor="#000000">'
    . '<noembed><div class="yt-alert yt-alert-default yt-alert-error  yt-alert-player">'
    . '<div class="yt-alert-icon"><img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon"></div>'
    . '<div class="yt-alert-buttons"></div><div class="yt-alert-content" role="alert">'
    . '<span class="yt-alert-vertical-trick"></span><div class="yt-alert-message">'
    . 'You need Adobe Flash Player to watch this video. <br> '
    . '<a href="http://get.adobe.com/flashplayer/">Download it from Adobe.</a>'
    . '</div></div></div></noembed>';
