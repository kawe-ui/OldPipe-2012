<?php
$video_id = $_GET["id"];
global $video_id;
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/embed.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php');
$videoCategory = $videoCategory ?? '';

function timeAgo(?string $dateStr): string {
    $dateStr = trim((string)$dateStr);
    if ($dateStr === '') return '';

    // InnerTube отдаёт готовые относительные строки: «6 years ago»,
    // «2 days ago (edited)», «Streamed 3 months ago». Возвращаем как есть —
    // повторно парсить их через strtotime нельзя (даёт битые «56 years ago»).
    if (preg_match('/\bago\b/i', $dateStr)) {
        return preg_replace('/^Streamed\s+/i', '', $dateStr);
    }

    // Абсолютная дата («Apr 23, 2005») — считаем относительное время.
    $ts = strtotime($dateStr);
    if ($ts === false || $ts <= 0) return '';   // защита от «56 years ago»

    $diff = time() - $ts;
    if ($diff < 0)          return 'just now';
    if ($diff < 60)         return $diff . ' seconds ago';
    if ($diff < 3600)       return (int)($diff / 60) . ' minutes ago';
    if ($diff < 86400)      return (int)($diff / 3600) . ' hours ago';
    if ($diff < 604800)     return (int)($diff / 86400) . ' days ago';
    if ($diff < 2592000)    return (int)($diff / 604800) . ' weeks ago';
    if ($diff < 31536000)   return (int)($diff / 2592000) . ' months ago';
    return (int)($diff / 31536000) . ' years ago';
}

// «N videos» с правильным плюралом; сохраняет дробный формат («7.5K videos»)
function videoCountLabel(?string $count): string {
    $count = trim((string)$count);
    if ($count === '') return '';
    // единственное число только для ровно «1»
    $plural = ($count === '1') ? 'video' : 'videos';
    return $count . ' ' . $plural;
}
?>
<!DOCTYPE html>
  <html lang="en" dir="ltr" >

<head>
    <title><?php echo $videoTitle ?> - YouTube</title>

    <link  rel="stylesheet" href="http://s.ytimg.com/yt/cssbin/www-embed-vflqnQAF6.css">


    <link rel="canonical" href="/watch?v=dQw4w9WgXcQ">

  <style>
    @-o-viewport { width: device-width; }
    @-moz-viewport { width: device-width; }
    @-ms-viewport { width: device-width; }
    @-webkit-viewport { width: device-width; }
    @viewport { width: device-width; }
  </style>

  
</head>
  <body id="" class="date-20120508 en_US ltr   ytg-old-clearfix" dir="ltr">


<div id="watch-longform-ad" class="hid">
  <div id="watch-longform-text">
Advertisement
  </div>
  <div id="watch-longform-ad-placeholder"><img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" height="60" width="300"></div>
</div>



  <div id="player" class="full-frame"></div>

        
    <script  src="//s.ytimg.com/yt/jsbin/www-embed_core_module-vflPraSPj.js"></script>


  <script>
    yt.setConfig({
      'EMBED_BINARY_URL': '//s.ytimg.com/yt/jsbin/www-embed_core_module-vflPraSPj.js',
      'ORIGIN': "*",
      'IS_OPERA_MINI': false
    });
    yt.setMsg({
      'FLASH_UPGRADE': '<div class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"><div class=\"yt-alert-icon\"><img src=\"\/\/s.ytimg.com\/yt\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"><\/div><div class=\"yt-alert-buttons\"><\/div><div class=\"yt-alert-content\">    <span class=\"yt-alert-vertical-trick\"><\/span>\n    <div class=\"yt-alert-message\">\n          You need to upgrade your Adobe Flash Player to watch this video. <br> <a href=\"http:\/\/get.adobe.com\/flashplayer\/\">Download it from Adobe.<\/a>\n    <\/div>\n<\/div><\/div>'
    });
      yt.setConfig({
      'PLAYER_CONFIG': <?php echo json_encode($ytPlayerConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    'EMBED_HTML_TEMPLATE': "\u003ciframe width=\"__width__\" height=\"__height__\" src=\"__url__\" frameborder=\"0\" allowfullscreen\u003e\u003c\/iframe\u003e",
    'EMBED_HTML_URL': "http:\/\/www.youtube.com\/embed\/__videoid__"
  });
    yt.setMsg('HTML5_DEFAULT_FALLBACK', "This video is currently unavailable.");
  yt.setMsg('FLASH_UPGRADE', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e\u003cdiv class=\"yt-alert-icon\"\u003e\u003cimg s\u0072c=\"\/\/s.ytimg.com\/yt\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n          You need to upgrade your Adobe Flash Player to watch this video. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eDownload it from Adobe.\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");
  yt.setMsg('HTML5_NO_AVAILABLE_FORMATS_FALLBACK', "Your browser does not currently recognize any of the video formats available.\u003cbr\u003e\u003ca href=\"\/html5\"\u003eClick here to visit our frequently asked questions about HTML5 video.\u003c\/a\u003e");
  yt.setMsg('PLAYER_FALLBACK', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e\u003cdiv class=\"yt-alert-icon\"\u003e\u003cimg s\u0072c=\"\/\/s.ytimg.com\/yt\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n          The Adobe Flash Player or an HTML5 supported browser is required for video playback. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eGet the latest Flash Player\u003c\/a\u003e \u003cbr\u003e \u003ca href=\"\/html5\"\u003eLearn more about upgrading to an HTML5 browser\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");
  yt.setMsg('QUICKTIME_FALLBACK', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e\u003cdiv class=\"yt-alert-icon\"\u003e\u003cimg s\u0072c=\"\/\/s.ytimg.com\/yt\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n          The Adobe Flash Player or QuickTime is required for video playback. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eGet the latest Flash Player\u003c\/a\u003e \u003cbr\u003e \u003ca href=\"http:\/\/www.apple.com\/quicktime\/download\/\"\u003eGet the latest version of QuickTime\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");

  yt.setMsg('HTML5_SPEED_NORMAL', "Normal");
  yt.setMsg('HTML5_QUALITY_SETTING', "quality");
  yt.setMsg('HTML5_SPEED_SETTING', "speed");
  yt.setMsg('HTML5_VOLUME_SETTING', "volume");
  yt.setMsg('HTML5_VOLUME_MUTED', "muted");
  yt.setMsg('HTML5_VOLUME_MUTE', "mute");
  yt.setMsg('HTML5_VOLUME_UNMUTE', "unmute");
  yt.setMsg('HTML5_CONTROL_TOGGLE', "toggle");

  yt.setMsg('HTML5_SUBS_TRANSCRIBED', "transcribed");
  yt.setMsg('VISIT_ADVERTISERS_SITE', "Visit advertiser's site");
  yt.setMsg('FRESCA_COMPLETE_MESSAGE', "Thanks for watching!");
  yt.setMsg('FRESCA_STAND_BY_MESSAGE', "Please stand by.");



      yt.embed.writeEmbed();
  </script>



</body>
</html>
