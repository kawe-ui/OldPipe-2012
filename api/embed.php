<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  embed_api.php — данные для embed.php (InnerTube /player + stream map)
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

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
$ytPlayerConfig       = null;
$swfEmbedHtml         = '';
$playerFlashVars      = [];
$playerFlashVarsQS    = '';
$playerSwfUrl         = '/yts/swfbin/2012lplayer_localhost_patched.swf';

// ─── video_id: v= или id= ─────────────────────────────────────────────────────
if (empty($video_id)) {
    $video_id = isset($_GET['v']) ? trim($_GET['v']) : (isset($_GET['id']) ? trim($_GET['id']) : '');
}
$video_id = trim((string)$video_id);
if (isset($_GET['html5'])) {
    $html5 = $_GET['html5'];
}
if ($video_id === '' || !preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id)) {
    return;
}

$videoMetadataUrl = '/get_video_metadata?video_id=' . rawurlencode($video_id);

if (isset($_GET['apidebug'])) {
    $dbgPlayer = innertube_post('player', ['videoId' => $video_id, 'racyCheckOk' => true, 'contentCheckOk' => true]);
    $dbgNext   = innertube_post('next',   ['videoId' => $video_id]);
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== /player ===\n" . json_encode($dbgPlayer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n";
    echo "=== /next ===\n"   . json_encode($dbgNext,   JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    exit;
}

// ─── /player ──────────────────────────────────────────────────────────────────
$playerRaw = innertube_post('player', [
    'videoId'        => $video_id,
    'racyCheckOk'    => true,
    'contentCheckOk' => true,
]);
if ($playerRaw === null) return;

$videoDetails  = $playerRaw['videoDetails']                            ?? [];
$microformat   = $playerRaw['microformat']['playerMicroformatRenderer'] ?? [];
$streamingData = $playerRaw['streamingData']                           ?? [];

$videoTitle     = $videoDetails['title']     ?? null;
$videoAuthorId  = $videoDetails['channelId'] ?? null;
$videoAuthor    = $videoDetails['author']    ?? null;
$videoAuthorUrl = $videoAuthorId ? '/channel/' . $videoAuthorId : null;

$videoDescription = $videoDetails['shortDescription'] ?? null;
if ($videoDescription !== null) {
    $videoDescriptionHTML = nl2br(htmlspecialchars($videoDescription, ENT_QUOTES, 'UTF-8'));
}

$videoLength = (int)($videoDetails['lengthSeconds'] ?? 0);
$videoLengthFormat = function_exists('SecondsFormater') ? SecondsFormater($videoLength) : sprintf('%d:%02d', intdiv($videoLength, 60), $videoLength % 60);

$rem = $videoLength;
$Calender['Days']    = (int)floor($rem / 86400); $rem %= 86400;
$Calender['Hours']   = (int)floor($rem / 3600);  $rem %= 3600;
$Calender['Minutes'] = (int)floor($rem / 60);
$Calender['Seconds'] = $rem % 60;

$rawViews  = (int)($videoDetails['viewCount'] ?? 0);
$viewCount = $rawViews > 0 ? number_format($rawViews, 0, '.', ',') : null;
$videoTags = !empty($videoDetails['keywords']) ? implode(',', $videoDetails['keywords']) : '';

$tArr = $videoDetails['thumbnail']['thumbnails'] ?? [];
if (!empty($tArr)) {
    usort($tArr, fn($a, $b) => (int)($b['width'] ?? 0) - (int)($a['width'] ?? 0));
    $videoThumbnail = $tArr[0]['url'] ?? null;
}
if (empty($videoThumbnail)) {
    $videoThumbnail = "https://i.ytimg.com/vi/{$video_id}/maxresdefault.jpg";
}

$publishedDate = $microformat['publishDate'] ?? $microformat['uploadDate'] ?? null;
if ($publishedDate) {
    $videoDate = date('M j, Y', strtotime($publishedDate));
}
$videoCategory   = $microformat['category'] ?? '';
$videoIsUnlisted = (bool)($microformat['isUnlisted'] ?? false);

// ─── Stream formats (для fallback <video>) ────────────────────────────────────
$allFormats = array_merge(
    $streamingData['formats']         ?? [],
    $streamingData['adaptiveFormats'] ?? []
);
foreach ($allFormats as $fmt) {
    $mimeType = $fmt['mimeType'] ?? '';
    if ($mimeType === '') continue;
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

// ─── /next + рейтинги (лёгкий набор для embed) ────────────────────────────────
$nextRaw = innertube_post('next', ['videoId' => $video_id]);

$likesHint = null;
$__ratings = function_exists('yt_video_ratings') ? yt_video_ratings($video_id, $likesHint) : ['likes' => null, 'dislikes' => null, 'viewCount' => null];
$likesInt    = $__ratings['likes']    ?? null;
$dislikesInt = $__ratings['dislikes'] ?? null;
if (empty($viewCount) && !empty($__ratings['viewCount'])) {
    $viewCount = number_format((int)$__ratings['viewCount'], 0, '.', ',');
}
if ($likesInt !== null)    $likeCount    = number_format($likesInt, 0, '.', ',');
if ($dislikesInt !== null) $dislikeCount = number_format($dislikesInt, 0, '.', ',');
$totalVotes = (int)$likesInt + (int)$dislikesInt;
if ($totalVotes > 0) {
    $likePercent    = round(((int)$likesInt / $totalVotes) * 100, 2);
    $dislikePercent = round(((int)$dislikesInt / $totalVotes) * 100, 2);
} else {
    $likePercent = 100;
    $dislikePercent = 0;
}

// ─── Локальные стримы → fmt_list / stream_map ─────────────────────────────────
$localStreams     = innertube_get_streams($video_id);
$playerQualityMap = yt_quality_map($localStreams);
if (!is_array($playerQualityMap)) $playerQualityMap = [];
uasort($playerQualityMap, fn(array $a, array $b) => $b['height'] <=> $a['height']);

$playerFmtListParts   = [];
$playerStreamMapParts = [];
foreach ($playerQualityMap as $itag => $q) {
    $w = $q['width'] ?: 640;
    $h = $q['height'] ?: 360;
    $playerFmtListParts[] = "{$itag}/{$w}x{$h}/9/0/115";
    $localUrl = $HTTP_Host_Full . '/get_video.php?video_id=' . urlencode($video_id) . '&itag=' . $itag;
    $playerStreamMapParts[] = 'itag=' . $itag
        . '&url=' . urlencode($localUrl)
        . '&type=' . urlencode($q['mime'] ?? 'video/mp4')
        . '&quality=' . ($q['quality'] ?? 'medium')
        . '&fallback_host=' . urlencode($HTTP_Host);
}
$playerFmtList   = implode(',', $playerFmtListParts);
$playerStreamMap = implode(',', $playerStreamMapParts);

$playerRvs = '';

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
    'autoplay'       => '1',
    'cr'             => 'US',
    'hl'             => 'en_US',
    'csi_page_type'  => 'watch5',
    'enablejsapi'    => 1,
    'enablecsi'      => '0',
    'sendtmp'        => '0',
    'no_get_video_log' => '1',
    'iv_load_policy' => 1,
    'metadata_url'   => $videoMetadataUrl,
    'showpopout'     => 1,
    'pltype'         => 'contentugc',
    'timestamp'      => time(),
    'referrer'       => $HTTP_Host_Full . '/embed/' . $video_id,
    'sdetail'        => 'p:/embed/' . $video_id,
    'sourceid'       => 'y',
    'watermark'      => '',
];
$playerSwfUrl      = '/yts/swfbin/2012lplayer_localhost_patched.swf';
$playerFlashVarsQS = http_build_query($playerFlashVars, '', '&');

// Embed → HTML5 по умолчанию (Flash без Ruffle = чёрный экран)
$isEmbedContext = true; // этот файл только для embed
$useFlashPlayer = false;
if (isset($_GET['flash']) && $_GET['flash'] !== '0') {
    $useFlashPlayer = true;
}
if ($html5 !== null && ($html5 === '0' || $html5 === 'false')) {
    $useFlashPlayer = true;
}
if ($html5 !== null && $html5 !== '' && $html5 !== '0') {
    $useFlashPlayer = false;
}

$ytPlayerConfig = [
    'assets' => [
        'html' => '/html5_player_template',
        'css'  => '/yts/cssbin/www-player-vflNffkIU.css',
        'js'   => '/yts/jsbin/html5player-vflZifxKH.js',
    ],
    'url' => $playerSwfUrl,
    'min_version' => '8.0.0',
    'args' => $playerFlashVars,
    'url_v9as2' => '/yts/swfbin/cps-vflEiiy1p.swf',
    'params' => [
        'allowscriptaccess' => 'always',
        'allowfullscreen'   => 'true',
        'bgcolor'           => '#000000',
    ],
    'attrs' => [
        'width'  => '100%',
        'id'     => 'video-player',
        'height' => '100%',
    ],
    'url_v8' => '/yts/swfbin/cps-vflEiiy1p.swf',
    'html5'  => !$useFlashPlayer,
];

$swfEmbedHtml = '<embed type="application/x-shockwave-flash" src="' . htmlspecialchars($playerSwfUrl, ENT_QUOTES, 'UTF-8') . '" id="movie_player"'
    . ' flashvars="' . htmlspecialchars($playerFlashVarsQS, ENT_QUOTES, 'UTF-8') . '"'
    . ' allowscriptaccess="always" allowfullscreen="true" bgcolor="#000000" width="100%" height="100%">'
    . '<noembed><div style="color:#fff;padding:20px;font:14px Arial">You need Adobe Flash Player to watch this video.</div></noembed>';