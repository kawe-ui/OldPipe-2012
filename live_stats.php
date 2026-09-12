<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: text/plain; charset=utf-8');

// 1. Grab and validate the incoming video ID from the query parameter
$video_id = $_GET['v'] ?? '';

if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id)) {
    echo "0";
    exit;
}

/*// 2. Make the API request
$playerRaw = innertube_post('player', [
    'videoId'        => $video_id,
    'racyCheckOk'    => true,
    'contentCheckOk' => true,
]);

// 3. Handle failure cases gracefully so the JS engine gets a valid integer response
if ($playerRaw === null) {
    echo "0";
    exit;
}

$videoDetails = $playerRaw['videoDetails'] ?? [];
$videoisLive  = $videoDetails['isLive'] ?? false;
$videoViews   = (int)($videoDetails['viewCount'] ?? 0);
*/
$metadataRaw = innertube_post('updated_metadata', [
    'videoId'        => $video_id,
    'racyCheckOk'    => true,
    'contentCheckOk' => true,
]);
error_log(json_encode($metadataRaw['actions'][0]));
$videoViews = $metadataRaw['actions'][0]['updateViewershipAction']['viewCount']['videoViewCountRenderer']['originalViewCount'] ?? null;
// 4. Ensure it's a live stream before returning the view count
if (!$videoViews) {
    echo "0";
    exit;
}

echo $videoViews;