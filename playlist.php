<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/playlist_api.php');
// Плейлиста нет / приватный / недоступен → на oops.php (как channel.php при ошибке).
if (!$playlistExists) { header('Location: /oops.php'); exit; }
?>
<!doctype html>
<html lang="en"><head><script>
var yt = yt || {};yt.timing = yt.timing || {};yt.timing.tick = function() {};yt.timing.info = function() {};    </script>

<title><?php echo htmlspecialchars($playlistTitle) ?> - YouTube</title><link rel="search" type="application/opensearchdescription+xml" href="https://web.archive.org/web/20121109001901/http://www.youtube.com/opensearch?locale=en_US" title="YouTube Video Search"><link rel="icon" href="https://web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/favicon-vfldLzJxy.ico" type="image/x-icon"><link rel="shortcut icon" href="https://web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/favicon-vfldLzJxy.ico" type="image/x-icon">   <link rel="icon" href="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/favicon_32-vflWoMFGx.png" sizes="32x32"><link rel="canonical" href="https://web.archive.org/web/20121109001901/http://www.youtube.com/playlist?list=PL0D35B04AF2E85B2D"><link rel="alternate" media="handheld" href="https://web.archive.org/web/20121109001901/http://m.youtube.com/playlist?list=PL0D35B04AF2E85B2D&amp;feature=plcp&amp;desktop_uri=%2Fplaylist%3Flist%3DPL0D35B04AF2E85B2D%26feature%3Dplcp"><link rel="alternate" media="only screen and (max-width: 640px)" href="https://web.archive.org/web/20121109001901/http://m.youtube.com/playlist?list=PL0D35B04AF2E85B2D&amp;feature=plcp&amp;desktop_uri=%2Fplaylist%3Flist%3DPL0D35B04AF2E85B2D%26feature%3Dplcp">  <meta name="description" content="Elections 2012">
  <meta name="keywords" content="video, sharing, camera phone, video phone, free, upload">
    <link rel="alternate" type="application/json+oembed" href="https://web.archive.org/web/20121109001901/http://www.youtube.com/oembed?url=http%3A%2F%2Fwww.youtube.com%2Fplaylist%3Flist%3DPL0D35B04AF2E85B2D&amp;format=json" title="Elections 2012">
    <link rel="alternate" type="text/xml+oembed" href="https://web.archive.org/web/20121109001901/http://www.youtube.com/oembed?url=http%3A%2F%2Fwww.youtube.com%2Fplaylist%3Flist%3DPL0D35B04AF2E85B2D&amp;format=xml" title="Elections 2012">

    <meta property="fb:app_id" content="87741124305">
    <meta property="og:site_name" content="YouTube">
    <meta property="og:url" content="https://web.archive.org/web/20121109001901/http://www.youtube.com/playlist?list=PL0D35B04AF2E85B2D">
    <meta property="og:title" content="<?php echo htmlspecialchars($playlistTitle, ENT_QUOTES) ?>">
      <meta property="og:description" content="<?php echo htmlspecialchars($playlistDescription !== '' ? $playlistDescription : $playlistTitle, ENT_QUOTES) ?>">
    <meta property="og:type" content="video">
    <meta property="og:image" content="https://web.archive.org/web/20121109001901im_/https://i2.ytimg.com/vi/m_DuQQmoqk4/hqdefault.jpg">

      <meta property="og:video" content="https://web.archive.org/web/20121109001901/http://www.youtube.com/embed/videoseries?list=PL0D35B04AF2E85B2D">
      <meta property="og:video:type" content="application/x-shockwave-flash">
      <meta property="og:video:width" content="398">
      <meta property="og:video:height" content="224">

<link id="css-617957165" rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">
  <link id="css-3031658408" rel="stylesheet" href="https://web.archive.org/web/20121109001901cs_/https://s.ytimg.com/yts/cssbin/www-the-rest-vflzYVqky.css">
      <link id="css-3933189995" rel="stylesheet" href="https://web.archive.org/web/20121109001901cs_/https://s.ytimg.com/yts/cssbin/www-playlist-vfl_LGyFp.css">

  <style>
    #branded-page-body-container {
      background-color: #FFFFFF;
      background-image: url(https://web.archive.org/web/20121109001901im_/https://i2.ytimg.com/u/qnbDFdCpuN8CMEg0VuEBqA/channels3_background.jpg?v=4f592fd2);
      background-repeat: no-repeat;
      background-position: center top;
  }

  </style>

  
<style type="text/css">.gssb_c{border:0;position:absolute;z-index:989}.gssb_e{border:1px solid #ccc;border-top-color:#d9d9d9;box-shadow:0 2px 4px rgba(0,0,0,0.2);-webkit-box-shadow:0 2px 4px rgba(0,0,0,0.2);cursor:default}.gssb_f{visibility:hidden;white-space:nowrap}.gssb_k{border:0;display:block;position:absolute;top:0;z-index:988}.gsdd_a{border:none!important}.gsib_a{width:100%;padding:4px 6px 0}.gsib_a,.gsib_b{vertical-align:top}.gssb_a{padding:0 7px}.gssb_a,.gssb_a td{white-space:nowrap;overflow:hidden;line-height:22px}#gssb_b{font-size:11px;color:#36c;text-decoration:none}#gssb_b:hover{font-size:11px;color:#36c;text-decoration:underline}.gssb_g{text-align:center;padding:8px 0 7px;position:relative}.gssb_h{font-size:15px;height:28px;margin:0.2em;-webkit-appearance:button}.gssb_i{background:#eee}.gss_ifl{visibility:hidden;padding-left:5px}.gssb_i .gss_ifl{visibility:visible}a.gssb_j{font-size:13px;color:#36c;text-decoration:none;line-height:100%}a.gssb_j:hover{text-decoration:underline}.gssb_l{height:1px;background-color:#e5e5e5}.gssb_m{color:#000;background:#fff}.gscp_a,.gscp_c,.gscp_d,.gscp_e,.gscp_f{display:inline-block;vertical-align:bottom}.gscp_f{border:none}.gscp_a{background:#d9e7fe;border:1px solid #9cb0d8;cursor:default;outline:none;text-decoration:none!important;user-select:none;-webkit-user-select:none;}.gscp_a:hover{border-color:#869ec9}.gscp_a.gscp_b{background:#4787ec;border-color:#3967bf}.gscp_c{color:#444;font-size:13px;font-weight:bold}.gscp_d{color:#aeb8cb;cursor:pointer;font:21px arial,sans-serif;line-height:inherit;padding:0 7px}.gscp_d{position:relative;top:1px}.gscp_a:hover .gscp_d{color:#575b66}.gscp_c:hover,.gscp_a .gscp_d:hover{color:#222}.gscp_a.gscp_b .gscp_c,.gscp_a.gscp_b .gscp_d{color:#fff}.gscp_e{height:100%;padding:0 4px}a.gspqs_a{padding:0 3px 0 8px}.gspqs_b{color:#666;line-height:22px}.gspr_a{padding-right:1px}.gsq_a{padding:0}.gsfe_a{border:1px solid #b9b9b9;border-top-color:#a0a0a0;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);-moz-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);}.gsfe_b{border:1px solid #4d90fe;outline:none;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-moz-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);}.gsok_a{background:url(data:image/gif;base64,R0lGODlhEwALAKECAAAAABISEv///////yH5BAEKAAIALAAAAAATAAsAAAIdDI6pZ+suQJyy0ocV3bbm33EcCArmiUYk1qxAUAAAOw==) no-repeat center;display:inline-block;height:11px;line-height:0;width:19px}.gsok_a img{border:none;visibility:hidden}.gsst_a{display:inline-block}.gsst_a{cursor:pointer;padding:0 4px}.gsst_a:hover{text-decoration:none!important}.gsst_b{font-size:16px;padding:0 2px;user-select:none;-webkit-user-select:none;white-space:nowrap}.gsst_e{opacity:0.55;}.gsst_a:hover .gsst_e,.gsst_a:focus .gsst_e{opacity:0.72;}.gsst_a:active .gsst_e{opacity:1;}.gsst_f{background:white;text-align:left}.gsst_g{background-color:white;border:1px solid #ccc;border-top-color:#d9d9d9;box-shadow:0 2px 4px rgba(0,0,0,0.2);-webkit-box-shadow:0 2px 4px rgba(0,0,0,0.2);margin:-1px -3px;padding:0 6px}.gsst_h{background-color:white;height:1px;margin-bottom:-1px;position:relative;top:-1px}.gsfi{font-size:16px}.gsfs{font-size:16px}a.gssb_j{font-size:12px;color:#03c}.gssb_a,.gssb_a td{line-height:20px}.gssb_a{padding:0 6px}.gssb_c{z-index:3000001}.gssb_i td{background:#eee}.gssb_k{z-index:3000000}.gssb_l{margin:2px 0}.gsib_a{padding:0 4px}.gsok_a{padding:0}.gsok_a img{display:block}.gsfe_b{border:1px solid #1c62b9;box-shadow:inset 0 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,0.3);outline:none;}a.gscp_a{position:relative;background:#e2e2e2;border:1px solid #bbb;border-radius:3px}.gsfe_a a.gscp_a{border-width:1px;border-style:solid;border-color:#bbb}a.gscp_a.gscp_b{border-color:#777!important;background:#999;outline:none}.gscp_c{color:#666;font-size:11px;font-weight:bold;padding-right:20px;text-shadow:0 1px 0 rgba(255, 255, 255, 0.5);-ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80ffffff,Positive=true)";zoom:1;filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80ffffff,Positive=true)}.gsfe_a a.gscp_a .gscp_c{color:#444}a.gscp_a.gscp_b .gscp_c,.gsfe_a a.gscp_a.gscp_b .gscp_c{color:#fff;text-shadow:0 1px 0 rgba(100, 100, 100, 0.5);-ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80646464,Positive=true)";zoom:1;filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80646464,Positive=true)}.gscp_d{position:absolute;padding:0;background:url(//web.archive.org/web/20121109001901/https://s.ytimg.com/yts/img/icons/close-vflrEJzIW.png);background-repeat:no-repeat;background-position-y:0;right:3px;top:6px;font-size:0;width:13px;height:13px}.gscp_d:hover{background-position-y:-13px}a.gscp_a.gscp_b .gscp_d{background-position-y:-26px}.gsfe_a a.gscp_a.gscp_b .gscp_d:hover{background-position-y:-39px}.gscp_f{background:#000}</style></head>
<!-- machid: sNW5tN3Z2SWdXaDZIM0NxQUhBeEx0YWluV1JGS0xaNDhueWE2U19yekl0bWl5b1piWU44c2F3 -->



  <body id="" class="date-20121108 en_US ltr   ytg-old-clearfix guide-feed-v2 " dir="ltr">




 

  <div id="body-container">
    <form name="logoutForm" method="POST" action="/web/20121109001901/https://www.youtube.com/logout">
      <input type="hidden" name="action_logout" value="1">
    <input name="session_token" type="hidden" value="pyR7f2w5TU5puhPOmVwuOTzwmIZ8MTM1MjUwNjc0MUAxMzUyNDIwMzQx"></form>


    


    <!-- begin page -->
      <div id="page" class="  playlist branded-page ">
          
  
  <div id="masthead-container">
    <!-- begin masthead -->
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>
    <!-- end masthead -->
  <div id="content-container">
    <!-- begin content -->
    <div id="content">
        

  <div id="branded-page-default-bg" class="ytg-base">
    <div id="branded-page-body-container" class="ytg-base clearfix ">
          <map name="branded_page_banner_image_map"><area shape="rect" coords="183,31,693,120" href="/web/20121109001901/https://www.youtube.com/redirect?q=http%3A%2F%2Fwww.nytimes.com&amp;event=imagemapurl&amp;usg=zXoiVVnQ4uJ9fUW3Mo5Mx-nxlJY=" alt="The New York Times"></map>
  <img usemap="#branded_page_banner_image_map" height="100" class="ytg-wide branded-banner-image" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif">



      <div id="branded-page-header-container" class="ytg-wide banner-displayed-mode">
            <div id="branded-page-header" class="ytg-wide ytg-box">
        <a class="profile-thumb" href="<?php echo htmlspecialchars($playlistAuthorUrl !== '' ? $playlistAuthorUrl : '/', ENT_QUOTES) ?>">
    <span class="video-thumb ux-thumb yt-thumb-square-77 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="<?php echo htmlspecialchars($playlistThumbnail !== '' ? $playlistThumbnail : '/dynamic/pfp/default.png', ENT_QUOTES) ?>" alt="Thumbnail" width="77"><span class="vertical-align"></span></span></span></span>
  </a>


    <div class="ytg-box">
      <div class="playlist-info">
        <div class="header-right">
          <div class="header-stats ytg-box">
              <ul>
    <li class="stat-entry first">
      <span class="stat-value"><?php echo htmlspecialchars($playlistVideoCount) ?></span>
      <span class="stat-name">
        videos
      </span>
    </li>
<?php if ($playlistDuration !== ''): ?>      <li class="stat-entry">
        <span class="stat-value"><?php echo htmlspecialchars($playlistDuration) ?></span>
        <span class="stat-name">
duration
        </span>
      </li>
<?php endif; ?>
<?php if ($playlistViews !== ''): ?>      <li class="stat-entry">
        <span class="stat-value"><?php echo htmlspecialchars($playlistViews) ?></span>
        <span class="stat-name">
          views
        </span>
      </li>
<?php endif; ?>
  </ul>

          </div>
        </div>
        <div class="playlist-reference">
          <h1 title="<?php echo htmlspecialchars($playlistTitle, ENT_QUOTES) ?>"><?php echo htmlspecialchars($playlistTitle) ?></h1>
<?php if ($playlistAuthor !== ''): ?>            <p class="channel-author-attribution">
by <a href="<?php echo htmlspecialchars($playlistAuthorUrl !== '' ? $playlistAuthorUrl : '/', ENT_QUOTES) ?>"><?php echo htmlspecialchars($playlistAuthor) ?></a>
            </p>
<?php endif; ?>
        </div>
            <span id="play-all-button">
      <a class="yt-playall-link yt-playall-link-dark yt-uix-sessionlink " href="<?php echo htmlspecialchars(!empty($playlistVideos) ? '/watch?v=' . $playlistVideos[0]['id'] . '&list=' . $playlistId . '&feature=plpp_play_all' : '#', ENT_QUOTES) ?>" data-sessionlink="ei=CK3grLDOwLMCFSkKIQodvwhYGw%3D%3D&amp;feature=plpp_play_all">
    <img class="small-arrow" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="">
Play all
  </a>

  </span>

      </div>
    </div>
  </div>

      </div>

      <div id="branded-page-body">
          <div id="playlist-pane-container">
    <div class="primary-pane">
      <div class="playlist-landing ypc-list-container">
          


            <div id="playlist-actions">
      <div id="playlist-action-buttons">
          <div id="playlist-likes-container">
<?php /* лайки/дизлайки плейлиста InnerTube-браузингом не отдаёт — не показываем фейковые */ ?>

          </div>
          <div class="playlist-like-dislike yt-uix-button-group"><button type="button" class="playlist-like start yt-uix-button yt-uix-button-default yt-uix-tooltip" onclick=";return false;" title="I like this" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-like" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="I like this"><span class="yt-uix-button-valign"></span></span><span class="yt-uix-button-content">Like </span></button><button type="button" class="playlist-dislike end yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" onclick=";return false;" title="I dislike this" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-dislike" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="I dislike this"><span class="yt-uix-button-valign"></span></span></button></div>
            <button type="button" class="playlist-share yt-uix-button yt-uix-button-default yt-uix-tooltip" onclick=";return false;" title="Share or embed this playlist" data-button-toggle="true" data-button-action="yt.www.playlist.share" role="button"><span class="yt-uix-button-content">Share </span></button>

        <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/playlist/playlist-hangout-icon-vflZSwp9g.png" alt="Google Hangout" class="playlist-hangout-button" title="Watch with your friends.">
      </div>
      <div id="playlist-share-container" class="playlist-share-area hid">
      </div>
      <div id="playlist-share-loading" class="playlist-share-area hid">
Loading...
      </div>

        <div id="playlist-likes-signin-container" class="playlist-share-area hid">
          <div class="yt-alert yt-alert-naked yt-alert-warn  ">  <div class="yt-alert-icon">
    <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-content" role="alert">    <span class="yt-alert-vertical-trick"></span>
    <div class="yt-alert-message">
              <strong><a href="https://web.archive.org/web/20121109001901/https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26nomobiletemp%3D1%26hl%3Den_US%26next%3DNone&amp;uilel=3&amp;hl=en_US&amp;service=youtube">Sign in</a> or <a href="/web/20121109001901/https://www.youtube.com/signup">sign up</a> now!
</strong>

    </div>
</div></div>
        </div>
  </div>

              <ol>
<?php foreach ($playlistVideos as $i => $v):
    $vId     = htmlspecialchars($v['id'], ENT_QUOTES);
    $vTitle  = htmlspecialchars($v['title'], ENT_QUOTES);
    $vAuthor = htmlspecialchars($v['author']);
    $vDur    = htmlspecialchars($v['duration']);
    $vIndex  = ($v['index'] !== '' ? htmlspecialchars($v['index']) : (string)($i + 1));
    $vViews  = $v['views'] !== '' ? htmlspecialchars($v['views']) . ' views' : '';
    $vThumb  = htmlspecialchars($v['thumbnail'] !== '' ? $v['thumbnail'] : ('https://i.ytimg.com/vi/' . $v['id'] . '/default.jpg'), ENT_QUOTES);
    $vRow    = ($i % 2 === 0) ? 'odd' : 'even';
    $vWatch  = htmlspecialchars('/watch?v=' . $v['id'] . '&list=' . $playlistId . '&index=' . ($i + 1) . '&feature=plpp_video', ENT_QUOTES);
?>
      <li class="playlist-video-item <?php echo $vRow ?>">
          <div class="yt-uix-tile playlist-video-item-base-content">
    <span class="video-index"><?php echo $vIndex ?></span>
      <div class="thumb-container">
    <div class="ux-thumb-wrap">
          <span class="video-thumb ux-thumb yt-thumb-default-124 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="<?php echo $vThumb ?>" alt="Thumbnail" width="124"><span class="vertical-align"></span></span></span></span>
<?php if ($vDur !== ''): ?>          <span class="video-time"><?php echo $vDur ?></span>
<?php endif; ?>
  <button onclick=";return false;" title="Watch Later" type="button" class="addto-button video-actions addto-watch-later-button-sign-in yt-uix-button yt-uix-button-default yt-uix-button-short yt-uix-tooltip" data-video-ids="<?php echo $vId ?>" role="button"><span class="yt-uix-button-content">  <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="Watch Later"> </span></button>
    </div>
  </div>
    <div class="video-info ">
        <div class="video-overview">
    <h3 class="video-title-container">
      <a href="<?php echo $vWatch ?>" class="yt-uix-tile-link">
        <span class="title video-title yt-uix-tooltip" title="<?php echo $vTitle ?>" dir="ltr"><?php echo $vTitle ?></span>
      </a>
    </h3>
      <p class="video-details">
        <span class="video-owner">
by <span class="yt-user-name " dir="ltr"><?php echo $vAuthor ?></span>
        </span>
<?php if ($vViews !== ''): ?>          <span class="video-view-count"><?php echo $vViews ?></span>
<?php endif; ?>
      </p>
  </div>
    </div>
  </div>
      </li>
<?php endforeach; ?>
<?php if (empty($playlistVideos)): ?>
      <li class="playlist-video-item"><div class="yt-uix-tile playlist-video-item-base-content"><p style="padding:16px 8px">This playlist has no videos.</p></div></li>
<?php endif; ?>
  </ol>

            


      </div>
    </div>
      <div class="secondary-pane">
          <div class="channel-module">
            <div class="playlist-description">
              <?php echo nl2br(htmlspecialchars($playlistDescription !== '' ? $playlistDescription : $playlistTitle)) ?>
            </div>
            <div class="yt-horizontal-rule "><span class="first"></span><span class="second"></span><span class="third"></span></div>
          </div>

        
<?php if (false): /* архивные сайдбар-модули (About канала + Featured Playlists) — статичные данные чужого плейлиста, реального источника нет; скрываем */ ?>
          <div class="channel-module">
    <div class="playlist-creator-info">
      <h2>About The New York Times</h2>
            <p>Check in daily to watch new videos from the acclaimed producers and editors of The New York Times. </p>
<p></p>
<p>http://nytimes.com/video</p>
<p>Where the conversation begins.</p>


      <div class="creator-links">
          <a href="/web/20121109001901/https://www.youtube.com/user/TheNewYorkTimes/videos?view=1">
60 playlists by The New York Times
          </a>
          <a href="/web/20121109001901/https://www.youtube.com/user/TheNewYorkTimes/videos?view=0">
View all videos
          </a>
      </div>
      <div class="creator-stats">
          <p>
            71,687,475 views
          </p>
        <p>
134,485 subscribers
        </p>
      </div>
        <div class="enable-fancy-subscribe-button">
            <span class="yt-uix-button-context-light yt-uix-button-subscription-container"><button href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dsubscribe%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fplaylist%253Flist%253DPL0D35B04AF2E85B2D%2526feature%253Dplcp%2526continue_action%253DIafRi20y7GRWOYa9EkECCVPPsQghvgVPdjxDzA2sYmIcPCKUUgeBF1pmONmBICib10cc5LiO_Cwz94as0PfGcnyWaGuXthz7nPIF-zq7oAU=&amp;uilel=3&amp;hl=en_US&amp;service=youtube" onclick=";window.location.href=this.getAttribute('href');return false;" type="button" class="yt-subscription-button yt-subscription-button-js-default yt-uix-button yt-uix-button-subscription" data-subscription-feature="playlist" data-sessionlink="ei=CK3grLDOwLMCFSkKIQodvwhYGw%3D%3D&amp;feature=playlist" data-subscription-value="UCqnbDFdCpuN8CMEg0VuEBqA" data-subscription-type="" role="button" data-subscription-initialized="true"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-subscribe" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span><span class="yt-uix-button-content">  <span class="subscribe-label">Subscribe</span>
  <span class="subscribed-label">Subscribed</span>
  <span class="unsubscribe-label">Unsubscribe</span>
 </span></button><span class="yt-subscription-button-disabled-mask"></span></span>
        </div>
    </div>
      <div class="yt-horizontal-rule "><span class="first"></span><span class="second"></span><span class="third"></span></div>
  </div>

          
        <div class="playlists-narrow channel-module yt-uix-c3-module-container">
    <div class="module-view gh-featured">
      <h2>Featured Playlists</h2>
          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=IpiJrtpL5ec&amp;list=PL6ED9B90A5018B52C&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C4ccbcfcFDvjVQa1PpcFNfahjfRfprz4iCMmPK2QnUgHWFWoaKKMU%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/IpiJrtpL5ec/default.jpg" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/IpiJrtpL5ec/default.jpg" alt="" class="thumb" data-group-key="thumb-group-0"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/w5hMpse8fM0/default.jpg" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/w5hMpse8fM0/default.jpg" alt="" class="thumb" data-group-key="thumb-group-0"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/eXd3BUnBz9U/default.jpg" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/eXd3BUnBz9U/default.jpg" alt="" class="thumb" data-group-key="thumb-group-0"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/d7fCe7tTA9E/default.jpg" data-thumb="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/d7fCe7tTA9E/default.jpg" alt="" class="thumb" data-group-key="thumb-group-0"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    16 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=PL6ED9B90A5018B52C&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        Thanksgiving Recipes - The New York Times Dining
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=GKkFL1sTigc&amp;list=PL4CGYNsoW2iCb4uQUNgWK6TJJgNVp-MpP&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C4f36db5FDvjVQa1PpcFNfahjfRfprzxOjsTabYrP8TaBFsiisDlg%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/GKkFL1sTigc/default.jpg" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/GKkFL1sTigc/default.jpg" alt="" class="thumb" data-group-key="thumb-group-0"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/ttnl10IybYI/default.jpg" data-thumb="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/ttnl10IybYI/default.jpg" alt="" class="thumb" data-group-key="thumb-group-0"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/3Gz5BC8mA4o/default.jpg" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/3Gz5BC8mA4o/default.jpg" alt="" class="thumb" data-group-key="thumb-group-0"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/pJotrsvQbNU/default.jpg" data-thumb="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/pJotrsvQbNU/default.jpg" alt="" class="thumb" data-group-key="thumb-group-0"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    9 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=PL4CGYNsoW2iCb4uQUNgWK6TJJgNVp-MpP&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        Op-Docs
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=3QpAoaYpxnU&amp;list=PL4CGYNsoW2iAoC02Ji5p6WzgHZKLLnphS&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C47b962eFDvjVQa1PpcFNfahjfRfprz3HK3rVscKyG03MrDPMErx0%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/1ew2d0TIp08/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/JN_FVG9tb3Y/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/abp829E3YXw/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/zjcw4417tG8/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    27 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=PL4CGYNsoW2iAoC02Ji5p6WzgHZKLLnphS&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        Coverage of Hurricane Sandy 2012
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=GKkFL1sTigc&amp;list=UUqnbDFdCpuN8CMEg0VuEBqA&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C470b648FDvjVQa1PpcFNfahjfRfprz7hh0uCWKANEVzkUB8qobhs%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/GKkFL1sTigc/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/65MPQu-KecA/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/WrAwbTlY_fA/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/s037HSts99I/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    3,793 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=UUqnbDFdCpuN8CMEg0VuEBqA&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        Uploaded videos
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=9i8qy4gDsd0&amp;list=FLqnbDFdCpuN8CMEg0VuEBqA&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C4624849FDvjVQa1PpcFNfahjfRfprz6JAIJ-puPwDjuNpbUjUOcA%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/9i8qy4gDsd0/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/vrJIFiq_GDc/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/VB6LPgtr1cc/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/uCv9tp1MOuA/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    145 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=FLqnbDFdCpuN8CMEg0VuEBqA&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        Favorite videos
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=SXIlAlkvKQU&amp;list=PLCCA528F8DE2F4765&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C44a506cFDvjVQa1PpcFNfahjfRfprz7VHFWh91BD2fgXG8M9f_wA%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/aINaHOxZN_4/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/uQ1-zZVvc68/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/9ia3mh1M-Ks/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/SXIlAlkvKQU/default.jpg" alt="" class="thumb" data-group-key="thumb-group-1"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    45 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=PLCCA528F8DE2F4765&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        NYTimes.com/Magazine - The New York Times Sunday Magazine
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=v8yE30-YLpg&amp;list=SPDBB31A4DF612F7DF&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C47df182FDvjVQa1PpcFNfahjfRfprz_KyDv_f_MB7v-ZC-lKGWIw%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/v8yE30-YLpg/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/GD5kFZScG3w/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/AwWfLI-4tAo/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/JNhCqal-qe8/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    15 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=PLDBB31A4DF612F7DF&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        Touch of Evil
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=HTC7_a0xugg&amp;list=PL074352F6ACE39E76&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C4f1d5baFDvjVQa1PpcFNfahjfRfprzyLMWzd2zw0FWcoeXPw60fw%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/HTC7_a0xugg/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/t9yuPL1kKfM/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/NKIuRHqCUSw/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/hB6RmLtEgHA/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    46 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=PL074352F6ACE39E76&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        NYTimes.com/Style - On the Street with Bill Cunningham
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=R3CFp2Sj__I&amp;list=PL1F5CE190342039B5&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C44fac86FDvjVQa1PpcFNfahjfRfprzyHHBtLy8k1egG2nhdAiq1g%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/w-cU490W9PE/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/R3CFp2Sj__I/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i4.ytimg.com/vi/O9BOK4uT5jE/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i2.ytimg.com/vi/YZfCycydN2M/default.jpg" alt="" class="thumb" data-group-key="thumb-group-2"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    49 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=PL1F5CE190342039B5&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        NYTimes.com/Opinion - On the Ground with Nick Kristof
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/web/20121109001901/https://www.youtube.com/watch?v=tGszVuOvosA&amp;list=SP2ECEDCA1A77BC8CC&amp;feature=plpp" class="play-all yt-uix-sessionlink yt-uix-contextlink" data-sessionlink="context=C49909eaFDvjVQa1PpcFNfahjfRfprz_-GiyUMyTgRXBakgLlKPgM%3D">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-4 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/tGszVuOvosA/default.jpg" alt="" class="thumb" data-group-key="thumb-group-3"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/fxdKDHIYcDQ/default.jpg" alt="" class="thumb" data-group-key="thumb-group-3"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i3.ytimg.com/vi/nGXQPsnmBQU/default.jpg" alt="" class="thumb" data-group-key="thumb-group-3"></span></span></span><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" data-thumb="//web.archive.org/web/20121109001901/https://i1.ytimg.com/vi/P83ZvmJWQOQ/default.jpg" alt="" class="thumb" data-group-key="thumb-group-3"></span></span></span></span><span class="resting-overlay"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <span class="video-count-box">
    4 videos
  </span>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/web/20121109001901/https://www.youtube.com/playlist?list=PL2ECEDCA1A77BC8CC&amp;feature=plpp" title="See all videos in playlist." class="yt-uix-tile-link">
        The New York Times Experience
      </a>
    </h3>
      <span class="playlist-author-attribution">
by The New York Times
    </span>
  </div>

          <a class="view-all-link" href="/web/20121109001901/https://www.youtube.com/user/TheNewYorkTimes/videos?view=1">
view all
    <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="">
  </a>

    </div>
  </div>
<?php endif; ?>


      </div>
  </div>

      </div>
    </div>
  </div>

    </div>
    <!-- end content -->
  </div>

  <div id="footer-container">
          <div id="footer">  <div class="yt-horizontal-rule "><span class="first"></span><span class="second"></span><span class="third"></span></div>
<div id="footer-logo"><a href="/web/20121109001901/https://www.youtube.com/" title="YouTube home"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="YouTube home"></a><span id="footer-divider"></span></div><div id="footer-main"><ul id="footer-links-primary">    <li><a href="//web.archive.org/web/20121109001901/https://support.google.com/youtube/?hl=en-US&amp;p=">Help</a></li>
  <li><a href="/web/20121109001901/https://www.youtube.com/t/about_youtube">About</a></li>
  <li><a href="/web/20121109001901/https://www.youtube.com/t/press">Press &amp; Blogs</a></li>
  <li><a href="/web/20121109001901/https://www.youtube.com/t/copyright_center">Copyright</a></li>
  <li><a href="/web/20121109001901/https://www.youtube.com/creators">Creators &amp; Partners</a></li>
  <li><a href="/web/20121109001901/https://www.youtube.com/t/advertising_overview">Advertising</a></li>
  <li><a href="/web/20121109001901/https://www.youtube.com/dev">Developers</a></li>
</ul><ul id="footer-links-secondary">  <li><a href="/web/20121109001901/https://www.youtube.com/t/terms">Terms</a></li>
  <li><a href="https://web.archive.org/web/20121109001901/http://www.google.com/intl/en/policies/privacy/">Privacy</a></li>
  <li><a href="//web.archive.org/web/20121109001901/https://support.google.com/youtube/bin/request.py?contact_type=abuse&amp;hl=en-US">Safety</a></li>
  <li><a href="//web.archive.org/web/20121109001901/https://www.google.com/tools/feedback/intl/en/error.html" onclick="return yt.www.feedback.start(yt.getConfig('FEEDBACK_LOCALE_LANGUAGE'), yt.getConfig('FEEDBACK_LOCALE_EXTRAS'));" id="reportbug">Send feedback</a></li>
  <li><a href="/web/20121109001901/https://www.youtube.com/testtube">Try something new!</a></li>
</ul>  <ul class="pickers yt-uix-button-group" data-button-toggle-group="optional">
      <li>
Language:
          
  <button type="button" class=" yt-uix-button yt-uix-button-text" onclick=";return false;" data-button-toggle="true" data-picker-position="footer" data-button-menu-id="arrow-display" data-picker-key="language" data-button-action="yt.www.picker.load" role="button"><span class="yt-uix-button-content">  English
 </span><img class="yt-uix-button-arrow" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""></button>


      </li>
      <li>
Location:
          
  <button type="button" class=" yt-uix-button yt-uix-button-text" onclick=";return false;" data-button-toggle="true" data-picker-position="footer" data-button-menu-id="arrow-display" data-picker-key="country" data-button-action="yt.www.picker.load" role="button"><span class="yt-uix-button-content">  Worldwide
 </span><img class="yt-uix-button-arrow" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""></button>


      </li>
      <li>
Safety:
          
  <button type="button" class=" yt-uix-button yt-uix-button-text" onclick=";return false;" data-button-toggle="true" data-picker-position="footer" data-button-menu-id="arrow-display" data-picker-key="safetymode" data-button-action="yt.www.picker.load" role="button"><span class="yt-uix-button-content">Off
 </span><img class="yt-uix-button-arrow" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""></button>


      </li>
  </ul>
      <div id="yt-picker-language-footer" class="yt-picker" style="display: none">
      <p class="yt-spinner">
      <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="yt-spinner-img" alt="Loading icon">

Loading...
  </p>

  </div>

      <div id="yt-picker-country-footer" class="yt-picker" style="display: none">
      <p class="yt-spinner">
      <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="yt-spinner-img" alt="Loading icon">

Loading...
  </p>

  </div>

      <div id="yt-picker-safetymode-footer" class="yt-picker" style="display: none">
      <p class="yt-spinner">
      <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="yt-spinner-img" alt="Loading icon">

Loading...
  </p>

  </div>

</div>    
</div>


  </div>
    



  <div id="playlist-bar" class="hid passive editable" data-video-url="/watch?v=&amp;feature=BFql&amp;playnext=1&amp;list=QL" data-list-id="" data-list-type="QL">
    <div id="playlist-bar-bar-container">
      <div id="playlist-bar-bar">
        <div class="yt-alert yt-alert-naked yt-alert-success hid " id="playlist-bar-notifications">  <div class="yt-alert-icon">
    <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-content" role="alert"></div></div>
<span id="playlist-bar-info"><span class="playlist-bar-active playlist-bar-group"><button onclick=";return false;" title="Previous video" type="button" id="playlist-bar-prev-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-prev" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="Previous video"><span class="yt-uix-button-valign"></span></span></button><span class="playlist-bar-count"><span class="playing-index">0</span> / <span class="item-count">0</span></span><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-next-button" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-next" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span></button></span><span class="playlist-bar-active playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-autoplay-button" data-button-toggle="true" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-autoplay" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span></button><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-shuffle-button" data-button-toggle="true" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-shuffle" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span></button></span><span class="playlist-bar-passive playlist-bar-group"><button onclick=";return false;" title="Play videos" type="button" id="playlist-bar-play-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-play" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="Play videos"><span class="yt-uix-button-valign"></span></span></button><span class="playlist-bar-count"><span class="item-count">0</span></span></span><span id="playlist-bar-title" class="yt-uix-button-group"><span class="playlist-title">Unsaved Playlist</span></span></span>
        <a id="playlist-bar-lists-back" href="#">
Return to active list
        </a>

<span id="playlist-bar-controls"><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-text yt-uix-button-empty" onclick=";return false;" id="playlist-bar-toggle-button" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-toggle" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span></button></span><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked yt-uix-button-reverse flip yt-uix-button yt-uix-button-text" onclick=";return false;" data-button-menu-id="playlist-bar-options-menu" data-button-has-sibling-menu="true" role="button"><span class="yt-uix-button-content">Options </span><img class="yt-uix-button-arrow" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt=""></button></span></span>      </div>
    </div>

<div id="playlist-bar-tray-container"><div id="playlist-bar-tray" class="yt-uix-slider yt-uix-slider-fluid"><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-prev" onclick="return false;"><img class="yt-uix-slider-prev-arrow" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="Previous video"></button><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-next" onclick="return false;"><img class="yt-uix-slider-next-arrow" src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="Next video"></button><div class="yt-uix-slider-body"><div id="playlist-bar-tray-content" class="yt-uix-slider-slide"><ol class="video-list"></ol><ol id="playlist-bar-help"><li class="empty playlist-bar-help-message">Your queue is empty. Add videos to your queue using this button: <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="addto-button-help"><br> or <a href="https://web.archive.org/web/20121109001901/https://accounts.google.com/ServiceLogin?passive=true&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fplaylist%253Flist%253DPL0D35B04AF2E85B2D%2526feature%253Dplcp&amp;uilel=3&amp;hl=en_US&amp;service=youtube">sign in</a> to load a different list.</li></ol></div><div class="yt-uix-slider-shade-left"></div><div class="yt-uix-slider-shade-right"></div></div></div><div id="playlist-bar-save"></div><div id="playlist-bar-lists" class="dark-lolz"></div><div id="playlist-bar-loading"><img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="Loading..."><span id="playlist-bar-loading-message">Loading...</span><span id="playlist-bar-saving-message" class="hid">Saving...</span></div><div id="playlist-bar-template" style="display: none;" data-video-thumb-url="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg"><!--<li class="playlist-bar-item yt-uix-slider-slide-unit __classes__" data-video-id="__video_encrypted_id__"><a href="__video_url__" title="__video_title__" class="yt-uix-sessionlink" data-sessionlink="ei=CK3grLDOwLMCFSkKIQodvwhYGw%3D%3D&amp;feature=BFa"><span class="video-thumb ux-thumb yt-thumb-default-106 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="__video_title__" data-thumb-manual="true" data-thumb="__video_thumb_url__" width="106" ><span class="vertical-align"></span></span></span></span><span class="screen"></span><span class="count"><strong>__list_position__</strong></span><span class="play"><img src="//s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif"></span><span class="yt-uix-button yt-uix-button-default delete"><img class="yt-uix-button-icon-playlist-bar-delete" src="//s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" alt="Delete"></span><span class="now-playing">Now playing</span><span dir="ltr" class="title"><span>__video_title__  <span class="uploader">by __video_display_name__</span>
</span></span><span class="dragger"></span></a></li>--></div><div id="playlist-bar-next-up-template" style="display: none;"><!--<div class="playlist-bar-next-thumb"><span class="video-thumb ux-thumb yt-thumb-default-74 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg" alt="Thumbnail" width="74" ><span class="vertical-align"></span></span></span></span></div>--></div></div>      <div id="playlist-bar-options-menu" class="hid">

    <div id="playlist-bar-extras-menu">
        <ul>
      <li><span class="yt-uix-button-menu-item" data-action="clear">
Clear all videos from this list
      </span></li>
  </ul>

    </div>

    <ul>
      <li><span class="yt-uix-button-menu-item" onclick="window.location.href='//web.archive.org/web/20121109001901/https://support.google.com/youtube/bin/answer.py?answer=146749&amp;hl=en-US'">Learn more</span></li>
    </ul>
  </div>

  </div>


  
    <div id="shared-addto-watch-later-login" class="hid">
      <a href="https://web.archive.org/web/20121109001901/https://accounts.google.com/ServiceLogin?passive=true&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fplaylist%253Flist%253DPL0D35B04AF2E85B2D%2526feature%253Dplcp&amp;uilel=3&amp;hl=en_US&amp;service=youtube" class="sign-in-link">Sign in</a> to add this to a playlist

    </div>

  <div id="shared-addto-menu" style="display: none;" class="hid sign-in">
      <div class="addto-menu">
        <div id="addto-list-panel" class="menu-panel active-panel">
        <span class="yt-uix-button-menu-item yt-uix-tooltip sign-in" data-possible-tooltip="" data-tooltip-show-delay="750"><a href="https://web.archive.org/web/20121109001901/https://accounts.google.com/ServiceLogin?passive=true&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fplaylist%253Flist%253DPL0D35B04AF2E85B2D%2526feature%253Dplcp&amp;uilel=3&amp;hl=en_US&amp;service=youtube" class="sign-in-link">Sign in</a> to add this to a playlist
</span>

  </div>
  <div id="addto-list-saved-panel" class="menu-panel">
    <div class="panel-content">
      <div class="yt-alert yt-alert-naked yt-alert-success  ">  <div class="yt-alert-icon">
    <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-content" role="alert">    <span class="yt-alert-vertical-trick"></span>
    <div class="yt-alert-message">
            
  <span class="message">Added to <span class="addto-title yt-uix-tooltip yt-uix-tooltip-reverse" title="More information about this playlist" data-tooltip-show-delay="750"></span></span>

    </div>
</div></div>
    </div>
  </div>
  <div id="addto-list-error-panel" class="menu-panel">
    <div class="panel-content">
      <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif">
      <span class="error-details"></span>
      <a class="show-menu-link">Back to list</a>
    </div>
  </div>

        <div id="addto-note-input-panel" class="menu-panel">
    <div class="panel-content">
      <div class="yt-alert yt-alert-naked yt-alert-success  ">  <div class="yt-alert-icon">
    <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-content" role="alert">    <span class="yt-alert-vertical-trick"></span>
    <div class="yt-alert-message">
              <span class="message">Added to playlist:</span>
  <span class="addto-title yt-uix-tooltip" title="More information about this playlist" data-tooltip-show-delay="750"></span>

    </div>
</div></div>
    </div>
<div class="yt-uix-char-counter" data-char-limit="150"><div class="addto-note-box addto-text-box"><textarea id="addto-note" class="addto-note yt-uix-char-counter-input" maxlength="150"></textarea><label for="addto-note" class="addto-note-label">Add an optional note</label></div><span class="yt-uix-char-counter-remaining">150</span></div>    <button disabled="disabled" type="button" class="playlist-save-note yt-uix-button yt-uix-button-default" onclick=";return false;" role="button"><span class="yt-uix-button-content">Add note </span></button>
  </div>
  <div id="addto-note-saving-panel" class="menu-panel">
    <div class="panel-content loading-content">
      <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif">
      <span>Saving note...</span>
    </div>
  </div>
  <div id="addto-note-saved-panel" class="menu-panel">
    <div class="panel-content">
      <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif">
      <span class="message">Note added to:</span>
    </div>
  </div>
  <div id="addto-note-error-panel" class="menu-panel">
    <div class="panel-content">
      <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif">
      <span class="message">Error adding note:</span>
      <ul class="error-details"></ul>
      <a class="add-note-link">Click to add a new note</a>
    </div>
  </div>
  <div class="close-note hid">
    <img src="//web.archive.org/web/20121109001901im_/https://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif" class="close-button">
  </div>

  </div>

  </div>


  

      </div>
    <!-- end page -->
  </div>


    
  
  
    <script id="js-618049471" src="/yts/jsbin/www-core-vfl63tHg3.js" data-loaded="true"></script>


  <script>
        yt.setConfig({
      'XSRF_TOKEN': 'MB0aEytuxeqBoS2KFscpI2snohB8MTM1MjI0Nzg3OEAxMzUyMTYxNDc4',
      'XSRF_FIELD_NAME': 'session_token'
    });

    yt.setConfig('XSRF_REDIRECT_TOKEN', 'sV8OkkIdiWqnXopCKkJVMpjJlrh8MTM1MjI0Nzg3OEAxMzUyMTYxNDc4');

    yt.setConfig({
      'EVENT_ID': "CJnt54SKubMCFYwTIQodgnQ9eQ==",
      'CURRENT_URL': "http:\/\/www.youtube.com\/",
      'LOGGED_IN': false,
      'SESSION_INDEX': null,

      'WATCH_CONTEXT_CLIENTSIDE': false,

      'FEEDBACK_LOCALE_LANGUAGE': "en",
      'FEEDBACK_LOCALE_EXTRAS': {"logged_in": false, "experiments": "927103,911614,907519,922401,920704,912806,927201,925003,913546,913556,920201,900816,911112,901451", "guide_subs": "NA", "accept_language": null}    });
  </script>


      <script>
if (window.yt.timing) {yt.timing.tick("js_head");}    </script>

    <script>
    _gel('masthead-search-term').focus();
    yt.setConfig('GUIDE_VERSION', 1);
  </script>

      <script>
      yt.setMsg('FLASH_UPGRADE', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e  \u003cdiv class=\"yt-alert-icon\"\u003e\n    \u003cimg s\u0072c=\"\/yts\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\n  \u003c\/div\u003e\n\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\" role=\"alert\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n            You need to upgrade your Adobe Flash Player to watch this video. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eDownload it from Adobe.\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");
  yt.setConfig({
    'PLAYER_CONFIG': {"url": "\/yts\/swf\/masthead_child-vflRMMO6_.swf", "min_version": "8.0.0", "args": {"enablejsapi": 1}, "url_v9as2": "", "params": {"bgcolor": "#FFFFFF", "allowfullscreen": "false", "allowscriptaccess": "always"}, "attrs": {"width": "1", "id": "masthead_child", "height": "1"}, "url_v8": "", "html5": false}
  });

    yt.flash.embed("masthead_child_div", yt.getConfig('PLAYER_CONFIG'));
  </script>


  
    <script id="js-2464382393" src="/yts/jsbin/www-guide-vflYBwsId.js" data-loaded="true"></script>



  <script>
      window.masthead = new yt.www.ads.MastheadAd(
          "ktzusH81JSs",
          true);

    yt.www.guide.init();


  </script>

      <script>
if (window.yt.timing) {yt.timing.tick("js_page");}    </script>

        <script>
yt.setConfig('TIMING_ACTION', "glo");    </script>





  <script>yt.setConfig('THUMB_DELAY_LOAD_BUFFER', 300);</script>

  <script>
    


  yt.setMsg({
    'LIST_CLEARED': "List cleared",
    'PLAYLIST_VIDEO_DELETED': "Video deleted.",
    'ERROR_OCCURRED': "Sorry, an error occurred.",
    'NEXT_VIDEO_TOOLTIP': "Next video:\u003cbr\u003e \u0026#8220;${next_video_title}\u0026#8221;",
    'NEXT_VIDEO_NOTHUMB_TOOLTIP': "Next video",
    'SHOW_PLAYLIST_TOOLTIP': "Show playlist",
    'HIDE_PLAYLIST_TOOLTIP': "Hide playlist",
    'AUTOPLAY_ON_TOOLTIP': "Turn autoplay off",
    'AUTOPLAY_OFF_TOOLTIP': "Turn autoplay on",
    'SHUFFLE_ON_TOOLTIP': "Turn shuffle off",
    'SHUFFLE_OFF_TOOLTIP': "Turn shuffle on",
    'PLAYLIST_BAR_PLAYLIST_SAVED': "Playlist saved!",
    'PLAYLIST_BAR_ADDED_TO_FAVORITES': "Added to favorites",
    'PLAYLIST_BAR_ADDED_TO_PLAYLIST': "Added to playlist",
    'PLAYLIST_BAR_ADDED_TO_QUEUE': "Added to queue",
    'AUTOPLAY_WARNING1': "Next video starts in 1 second...",
    'AUTOPLAY_WARNING2': "Next video starts in 2 seconds...",
    'AUTOPLAY_WARNING3': "Next video starts in 3 seconds...",
    'AUTOPLAY_WARNING4': "Next video starts in 4 seconds...",
    'AUTOPLAY_WARNING5': "Next video starts in 5 seconds...",
    'UNDO_LINK': "Undo"  });


  yt.setConfig({
    'DRAGDROP_BINARY_URL': "\/yts\/jsbin\/www-dragdrop-vflVtCQG3.js",
    'PLAYLIST_BAR_PLAYING_INDEX': -1  });

    yt.setAjaxToken('addto_ajax_logged_out', "2980s-6wm9fmz4X3IdrxF3DzaBF8MTM1MjI0Nzg3OEAxMzUyMTYxNDc4");

    yt.pubsub.subscribe('init', yt.www.lists.init);






      yt.events.listen(_gel('masthead-search-term'), 'focus', yt.www.home.ads.workaroundReset);



    yt.setConfig({'SBOX_JS_URL': "\/yts\/jsbin\/www-searchbox-vflWtMugU.js",'SBOX_SETTINGS': {"CLOSE_ICON_URL": "\/yts\/img\/icons\/close-vflrEJzIW.png", "SHOW_CHIP": false, "PSUGGEST_TOKEN": null, "REQUEST_DOMAIN": "us", "EXPERIMENT_ID": -1, "SESSION_INDEX": null, "HAS_ON_SCREEN_KEYBOARD": false, "CHIP_PARAMETERS": {}, "REQUEST_LANGUAGE": "en"},'SBOX_LABELS': {"SUGGESTION_DISMISS_LABEL": "Dismiss", "SUGGESTION_DISMISSED_LABEL": "Suggestion dismissed"}});





  </script>

  <script>
    yt.setMsg({
      'ADDTO_WATCH_LATER_ADDED': "Added",
      'ADDTO_WATCH_LATER_ERROR': "Error"
    });
  </script>

  

      <script>
if (window.yt.timing) {yt.timing.tick("js_foot");}    </script>


  




<iframe class="gstl_0 gssb_k" style="display: none; top: 45px; left: 0px; height: 0px;" allow="autoplay 'self'; fullscreen 'self'"></iframe><table cellspacing="0" cellpadding="0" class="gstl_0 gssb_c" style="width: 462px; display: none; top: 45px; position: absolute; left: 164px;"><tbody><tr><td class="gssb_f"></td><td class="gssb_e" style="width: 100%;"></td></tr></tbody></table></body></html>