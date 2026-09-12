<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  get_video_info.php — протокол 2012 для флеш-плеера
//  /get_video_info?video_id=XXXXXXXXXXX[&el=...&ps=...]
//  Отдаёт urlencoded-пары: status, title, fmt_list, url_encoded_fmt_stream_map…
//  Все stream-URL указывают на локальный /get_video.php (same-origin для Flash).
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

header('Content-Type: application/x-www-form-urlencoded; charset=utf-8');
header('Cache-Control: no-cache');

$video_id = $_GET['video_id'] ?? ($_GET['v'] ?? '');

if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id)) {
    echo http_build_query(['status' => 'fail', 'errorcode' => 2, 'reason' => 'Invalid parameters.']);
    exit;
}

$streams = innertube_get_streams($video_id);
if ($streams === null || empty($streams['formats'])) {
    echo http_build_query([
        'status'    => 'fail',
        'errorcode' => 100,
        'reason'    => 'This video is unavailable.',
    ]);
    exit;
}

function _gvi_quality(int $h): string {
    if ($h >= 1080) return 'hd1080';
    if ($h >= 720)  return 'hd720';
    if ($h >= 480)  return 'large';
    if ($h >= 360)  return 'medium';
    return 'small';
}

// Метаданные (просмотры, рейтинг, автор) — тот же источник, что и у
// /get_video_metadata. Плеер их не требует, поэтому отсутствие не фатально.
$meta = yt_video_metadata($video_id);
$metaOk = ($meta !== null && ($meta['status'] ?? '') === 'OK');

// avg_rating 2012 — пятизвёздочная шкала: 100% лайков → 5.0, 0% → 1.0
$avgRating = '5.0';
if ($metaOk) {
    $totalVotes = (int)($meta['likes'] ?? 0) + (int)($meta['dislikes'] ?? 0);
    if ($totalVotes > 0) {
        $avgRating = number_format(1 + 4 * ((int)$meta['likes'] / $totalVotes), 2, '.', '');
    }
}

$fmtList   = [];
$streamMap = [];
$formats   = $streams['formats'];
krsort($formats);
foreach ($formats as $itag => $f) {
    $w = $f['width'] ?: 640; $h = $f['height'] ?: 360;
    $fmtList[]   = "{$itag}/{$w}x{$h}/9/0/115";
    $localUrl    = $HTTP_Host_Full . '/get_video.php?video_id=' . urlencode($video_id) . '&itag=' . $itag;
    $streamMap[] = 'itag=' . $itag
        . '&url=' . urlencode($localUrl)
        . '&type=' . urlencode($f['mimeType'] ?: 'video/mp4')
        . '&quality=' . _gvi_quality($h)
        . '&fallback_host=' . urlencode($HTTP_Host);
}

echo http_build_query([
    'status'         => 'ok',
    'video_id'       => $video_id,
    'title'          => $streams['title'],
    'author'         => $streams['author'],
    'keywords'       => implode(',', $streams['keywords'] ?? []),
    'length_seconds' => $streams['lengthSeconds'],
    'fmt_list'       => implode(',', $fmtList),
    'url_encoded_fmt_stream_map' => implode(',', $streamMap),
    'thumbnail_url'  => 'https://i.ytimg.com/vi/' . $video_id . '/default.jpg',
    'iurlsd'         => 'https://i.ytimg.com/vi/' . $video_id . '/sddefault.jpg',
    'iurlhq'         => 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg',
    'allow_embed'    => 1,
    'allow_ratings'  => 1,
    'vq'             => 'auto',
    'hl'             => 'en_US',
    'avg_rating'     => $avgRating,
    'view_count'     => $metaOk ? (int)$meta['viewCount'] : 0,
    'timestamp'      => time(),
    'plid'           => 'AATQJdc1-gxnOCVY',
    'token'          => 'vjVQa1PpcFPTXpgY6mYDGHvfyt_a_bp1XtYr2eqEPNU=',
    't'              => 'vjVQa1PpcFPTXpgY6mYDGHvfyt_a_bp1XtYr2eqEPNU=',
], '', '&');
