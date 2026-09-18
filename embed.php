<?php
$video_id = trim((string)($_GET['id'] ?? $_GET['v'] ?? ''));
if ($video_id === '' || !preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id)) {
    http_response_code(404);
    echo 'Video not found';
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/embed.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php';

if (empty($videoTitle) && empty($ytPlayerConfig)) {
    http_response_code(404);
    echo 'Video unavailable';
    exit;
}

$videoCategory = $videoCategory ?? '';
$pageTitle = htmlspecialchars($videoTitle ?? 'YouTube', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <title><?php echo $pageTitle ?> - YouTube</title>
  <link rel="stylesheet" href="/yts/cssbin/www-embed-vflqnQAF6.css">
  <link rel="canonical" href="/watch?v=<?php echo htmlspecialchars($video_id) ?>">
  <style>
    html, body { margin: 0; padding: 0; width: 100%; height: 100%; background: #000; overflow: hidden; }
    #player, #player.full-frame { position: absolute; inset: 0; width: 100%; height: 100%; }
    @-o-viewport { width: device-width; }
    @-moz-viewport { width: device-width; }
    @-ms-viewport { width: device-width; }
    @-webkit-viewport { width: device-width; }
    @viewport { width: device-width; }
  </style>
</head>
<body id="" class="date-20120508 en_US ltr ytg-old-clearfix" dir="ltr">

<div id="watch-longform-ad" class="hid">
  <div id="watch-longform-text">Advertisement</div>
  <div id="watch-longform-ad-placeholder"><img src="/yts/img/pixel-vfl3z5WfW.gif" height="60" width="300" alt=""></div>
</div>

<div id="player" class="full-frame"></div>

<script src="/yts/jsbin/www-embed_core_module-vflPraSPj.js"></script>
<script>
yt.setConfig({
  'EMBED_BINARY_URL': '/yts/jsbin/www-embed_core_module-vflPraSPj.js',
  'ORIGIN': '*',
  'IS_OPERA_MINI': false
});
yt.setConfig({
  'PLAYER_CONFIG': <?php echo json_encode($ytPlayerConfig ?? new stdClass(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
  'EMBED_HTML_TEMPLATE': "\u003ciframe width=\"__width__\" height=\"__height__\" src=\"__url__\" frameborder=\"0\" allowfullscreen\u003e\u003c\/iframe\u003e",
  'EMBED_HTML_URL': "/embed/__videoid__"
});
yt.setMsg('HTML5_DEFAULT_FALLBACK', "This video is currently unavailable.");
yt.setMsg('FLASH_UPGRADE', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error yt-alert-player\"\u003e\u003cdiv class=\"yt-alert-icon\"\u003e\u003cimg src=\"/yts/img/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\"\u003e\u003cdiv class=\"yt-alert-message\"\u003eYou need to upgrade your Adobe Flash Player to watch this video.\u003c\/div\u003e\u003c\/div\u003e\u003c\/div\u003e");
yt.setMsg('PLAYER_FALLBACK', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error yt-alert-player\"\u003e\u003cdiv class=\"yt-alert-message\"\u003eThe Adobe Flash Player or an HTML5 supported browser is required for video playback.\u003c\/div\u003e\u003c\/div\u003e");
yt.setMsg('HTML5_NO_AVAILABLE_FORMATS_FALLBACK', "Your browser does not currently recognize any of the video formats available.");
yt.setMsg('HTML5_SPEED_NORMAL', "Normal");
yt.setMsg('HTML5_QUALITY_SETTING', "quality");
yt.setMsg('HTML5_SPEED_SETTING', "speed");
yt.setMsg('HTML5_VOLUME_SETTING', "volume");
yt.setMsg('HTML5_VOLUME_MUTED', "muted");
yt.setMsg('HTML5_VOLUME_MUTE', "mute");
yt.setMsg('HTML5_VOLUME_UNMUTE', "unmute");
yt.setMsg('HTML5_CONTROL_TOGGLE', "toggle");
yt.setMsg('HTML5_SUBS_TRANSCRIBED', "transcribed");
yt.setMsg('FRESCA_COMPLETE_MESSAGE', "Thanks for watching!");
yt.setMsg('FRESCA_STAND_BY_MESSAGE', "Please stand by.");

try {
  yt.embed.writeEmbed();
} catch (e) {
  document.getElementById('player').innerHTML = <?php
    // fallback: прямой embed SWF/HTML5 из api
    if (!empty($swfEmbedHtml)) {
      echo json_encode($swfEmbedHtml, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
      echo json_encode('<div style="color:#fff;padding:20px;font:14px Arial">Player failed to load.</div>');
    }
  ?>;
}
</script>
</body>
</html>