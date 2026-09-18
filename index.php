<?php 
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/home_api.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/config.inc.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <script>
    var yt = yt || {}; yt.timing = yt.timing || {}; yt.timing.tick = function (label, opt_time) { var timer = yt.timing['timer'] || {}; if (opt_time) { timer[label] = opt_time; } else { timer[label] = new Date().getTime(); } yt.timing['timer'] = timer; }; yt.timing.info = function (label, value) { var info_args = yt.timing['info_args'] || {}; info_args[label] = value; yt.timing['info_args'] = info_args; }; yt.timing.info('e', "927103,911614,907519,922401,920704,912806,927201,925003,913546,913556,920201,900816,911112,901451"); if (document.webkitVisibilityState == 'prerender') { document.addEventListener('webkitvisibilitychange', function () { yt.timing.tick('start'); }, false); } yt.timing.tick('start'); yt.timing.info('li', '0'); try { yt.timing['srt'] = window.gtbExternal && window.gtbExternal.pageT() || window.external && window.external.pageT; } catch (e) { } if (window.chrome && window.chrome.csi) { yt.timing['srt'] = Math.floor(window.chrome.csi().pageT); } if (window.msPerformance && window.msPerformance.timing) { yt.timing['srt'] = window.msPerformance.timing.responseStart - window.msPerformance.timing.navigationStart; }    </script>

  <title>YouTube</title>
  <link rel="search" type="application/opensearchdescription+xml" href="http://www.youtube.com/opensearch?locale=en_US"
    title="YouTube Video Search">
  <link rel="icon" href="/yts/img/favicon-vfldLzJxy.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/yts/img/favicon-vfldLzJxy.ico" type="image/x-icon">
  <link rel="icon" href="/yts/img/favicon_32-vflWoMFGx.png" sizes="32x32">
  <link rel="canonical" href="http://www.youtube.com/">
  <link rel="alternate" media="handheld" href="http://m.youtube.com/index?&amp;desktop_uri=%2F">
  <link rel="alternate" media="only screen and (max-width: 640px)"
    href="http://m.youtube.com/index?&amp;desktop_uri=%2F">
  <meta name="description" content="Share your videos with friends, family, and the world">
  <meta name="keywords" content="video, sharing, camera phone, video phone, free, upload">
  <meta property="og:image" content="/yts/img/youtube_logo_stacked-vfl225ZTx.png">
  <meta property="fb:app_id" content="87741124305">
  <link rel="publisher" href="https://plus.google.com/115229808208707341778">
  <link id="css-617957165" rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">
  <link id="css-1807079883" rel="stylesheet" href="/yts/cssbin/www-guide-vflAio4Bl.css">


  <script>
    if (window.yt.timing) { yt.timing.tick("ct"); }    </script>

  <style type="text/css">
    .gssb_c {
      border: 0;
      position: absolute;
      z-index: 989
    }

    .gssb_e {
      border: 1px solid #ccc;
      border-top-color: #d9d9d9;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      -webkit-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      cursor: default
    }

    .gssb_f {
      visibility: hidden;
      white-space: nowrap
    }

    .gssb_k {
      border: 0;
      display: block;
      position: absolute;
      top: 0;
      z-index: 988
    }

    .gsdd_a {
      border: none !important
    }

    .gsib_a {
      width: 100%;
      padding: 4px 6px 0
    }

    .gsib_a,
    .gsib_b {
      vertical-align: top
    }

    .gssb_a {
      padding: 0 7px
    }

    .gssb_a,
    .gssb_a td {
      white-space: nowrap;
      overflow: hidden;
      line-height: 22px
    }

    #gssb_b {
      font-size: 11px;
      color: #36c;
      text-decoration: none
    }

    #gssb_b:hover {
      font-size: 11px;
      color: #36c;
      text-decoration: underline
    }

    .gssb_g {
      text-align: center;
      padding: 8px 0 7px;
      position: relative
    }

    .gssb_h {
      font-size: 15px;
      height: 28px;
      margin: 0.2em;
      -webkit-appearance: button
    }

    .gssb_i {
      background: #eee
    }

    .gss_ifl {
      visibility: hidden;
      padding-left: 5px
    }

    .gssb_i .gss_ifl {
      visibility: visible
    }

    a.gssb_j {
      font-size: 13px;
      color: #36c;
      text-decoration: none;
      line-height: 100%
    }

    a.gssb_j:hover {
      text-decoration: underline
    }

    .gssb_l {
      height: 1px;
      background-color: #e5e5e5
    }

    .gssb_m {
      color: #000;
      background: #fff
    }

    .gscp_a,
    .gscp_c,
    .gscp_d,
    .gscp_e,
    .gscp_f {
      display: inline-block;
      vertical-align: bottom
    }

    .gscp_f {
      border: none
    }

    .gscp_a {
      background: #d9e7fe;
      border: 1px solid #9cb0d8;
      cursor: default;
      outline: none;
      text-decoration: none !important;
      user-select: none;
      -webkit-user-select: none;
    }

    .gscp_a:hover {
      border-color: #869ec9
    }

    .gscp_a.gscp_b {
      background: #4787ec;
      border-color: #3967bf
    }

    .gscp_c {
      color: #444;
      font-size: 13px;
      font-weight: bold
    }

    .gscp_d {
      color: #aeb8cb;
      cursor: pointer;
      font: 21px arial, sans-serif;
      line-height: inherit;
      padding: 0 7px
    }

    .gscp_d {
      position: relative;
      top: 1px
    }

    .gscp_a:hover .gscp_d {
      color: #575b66
    }

    .gscp_c:hover,
    .gscp_a .gscp_d:hover {
      color: #222
    }

    .gscp_a.gscp_b .gscp_c,
    .gscp_a.gscp_b .gscp_d {
      color: #fff
    }

    .gscp_e {
      height: 100%;
      padding: 0 4px
    }

    a.gspqs_a {
      padding: 0 3px 0 8px
    }

    .gspqs_b {
      color: #666;
      line-height: 22px
    }

    .gspr_a {
      padding-right: 1px
    }

    .gsq_a {
      padding: 0
    }

    .gsfe_a {
      border: 1px solid #b9b9b9;
      border-top-color: #a0a0a0;
      box-shadow: inset 0px 1px 2px rgba(0, 0, 0, 0.1);
      -moz-box-shadow: inset 0px 1px 2px rgba(0, 0, 0, 0.1);
      -webkit-box-shadow: inset 0px 1px 2px rgba(0, 0, 0, 0.1);
    }

    .gsfe_b {
      border: 1px solid #4d90fe;
      outline: none;
      box-shadow: inset 0px 1px 2px rgba(0, 0, 0, 0.3);
      -moz-box-shadow: inset 0px 1px 2px rgba(0, 0, 0, 0.3);
      -webkit-box-shadow: inset 0px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .gsok_a {
      background: url(data:image/gif;base64,R0lGODlhEwALAKECAAAAABISEv///////yH5BAEKAAIALAAAAAATAAsAAAIdDI6pZ+suQJyy0ocV3bbm33EcCArmiUYk1qxAUAAAOw==) no-repeat center;
      display: inline-block;
      height: 11px;
      line-height: 0;
      width: 19px
    }

    .gsok_a img {
      border: none;
      visibility: hidden
    }

    .gsst_a {
      display: inline-block
    }

    .gsst_a {
      cursor: pointer;
      padding: 0 4px
    }

    .gsst_a:hover {
      text-decoration: none !important
    }

    .gsst_b {
      font-size: 16px;
      padding: 0 2px;
      user-select: none;
      -webkit-user-select: none;
      white-space: nowrap
    }

    .gsst_e {
      opacity: 0.55;
    }

    .gsst_a:hover .gsst_e,
    .gsst_a:focus .gsst_e {
      opacity: 0.72;
    }

    .gsst_a:active .gsst_e {
      opacity: 1;
    }

    .gsst_f {
      background: white;
      text-align: left
    }

    .gsst_g {
      background-color: white;
      border: 1px solid #ccc;
      border-top-color: #d9d9d9;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      -webkit-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      margin: -1px -3px;
      padding: 0 6px
    }

    .gsst_h {
      background-color: white;
      height: 1px;
      margin-bottom: -1px;
      position: relative;
      top: -1px
    }

    .gsfi {
      font-size: 16px
    }

    .gsfs {
      font-size: 16px
    }

    a.gssb_j {
      font-size: 12px;
      color: #03c
    }

    .gssb_a,
    .gssb_a td {
      line-height: 20px
    }

    .gssb_a {
      padding: 0 6px
    }

    .gssb_c {
      z-index: 3000001
    }

    .gssb_i td {
      background: #eee
    }

    .gssb_k {
      z-index: 3000000
    }

    .gssb_l {
      margin: 2px 0
    }

    .gsib_a {
      padding: 0 4px
    }

    .gsok_a {
      padding: 0
    }

    .gsok_a img {
      display: block
    }

    .gsfe_b {
      border: 1px solid #1c62b9;
      box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3);
      -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3);
      outline: none;
    }

    a.gscp_a {
      position: relative;
      background: #e2e2e2;
      border: 1px solid #bbb;
      border-radius: 3px
    }

    .gsfe_a a.gscp_a {
      border-width: 1px;
      border-style: solid;
      border-color: #bbb
    }

    a.gscp_a.gscp_b {
      border-color: #777 !important;
      background: #999;
      outline: none
    }

    .gscp_c {
      color: #666;
      font-size: 11px;
      font-weight: bold;
      padding-right: 20px;
      text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
      -ms-filter: "progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80ffffff,Positive=true)";
      zoom: 1;
      filter: progid:DXImageTransform.Microsoft.dropshadow(OffX=0, OffY=1, Color=#80ffffff, Positive=true)
    }

    .gsfe_a a.gscp_a .gscp_c {
      color: #444
    }

    a.gscp_a.gscp_b .gscp_c,
    .gsfe_a a.gscp_a.gscp_b .gscp_c {
      color: #fff;
      text-shadow: 0 1px 0 rgba(100, 100, 100, 0.5);
      -ms-filter: "progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80646464,Positive=true)";
      zoom: 1;
      filter: progid:DXImageTransform.Microsoft.dropshadow(OffX=0, OffY=1, Color=#80646464, Positive=true)
    }

    .gscp_d {
      position: absolute;
      padding: 0;
      background: url(/yts/img/icons/close-vflrEJzIW.png);
      background-repeat: no-repeat;
      background-position-y: 0;
      right: 3px;
      top: 6px;
      font-size: 0;
      width: 13px;
      height: 13px
    }

    .gscp_d:hover {
      background-position-y: -13px
    }

    a.gscp_a.gscp_b .gscp_d {
      background-position-y: -26px
    }

    .gsfe_a a.gscp_a.gscp_b .gscp_d:hover {
      background-position-y: -39px
    }

    .gscp_f {
      background: #000
    }
  </style>
</head>
<!-- machid: sNW5tN3Z2SWdXaDRzUHRkVWk3YXpyTG1LUXdRLTR0TnM4Q2RrdlFJN1NtUHVjdGIyTWVEUVFR -->

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXXX"
  crossorigin="anonymous"></script>

<body id="" class="date-20121105 en_US ltr   ytg-old-clearfix guide-feed-v2 " dir="ltr"
  __processed_c253e8d2-c7f6-4fdd-a032-865906d4b655__="true"
  bis_register="W3sibWFzdGVyIjp0cnVlLCJleHRlbnNpb25JZCI6ImVwcGlvY2VtaG1ubGJoanBsY2drb2ZjaWllZ29tY29uIiwiYWRibG9ja2VyU3RhdHVzIjp7IkRJU1BMQVkiOiJlbmFibGVkIiwiRkFDRUJPT0siOiJlbmFibGVkIiwiVFdJVFRFUiI6ImVuYWJsZWQiLCJSRURESVQiOiJlbmFibGVkIiwiUElOVEVSRVNUIjoiZW5hYmxlZCIsIklOU1RBR1JBTSI6ImVuYWJsZWQiLCJUSUtUT0siOiJkaXNhYmxlZCIsIkxJTktFRElOIjoiZW5hYmxlZCIsIkNPTkZJRyI6ImRpc2FibGVkIn0sInZlcnNpb24iOiIyLjAuNDciLCJzY29yZSI6MjAwNDcwfV0="
  bis_skin_checked="1">






  <div id="body-container" bis_skin_checked="1">
    <form name="logoutForm" method="POST" action="/logout">
      <input type="hidden" name="action_logout" value="1">
      <input name="session_token" type="hidden" value="MB0aEytuxeqBoS2KFscpI2snohB8MTM1MjI0Nzg3OEAxMzUyMTYxNDc4">
    </form>





    <!-- begin page -->
    <div id="page" class="  home  ">
      <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?>
      <!-- end masthead -->
      <div id="content-container" bis_skin_checked="1">
        <!-- begin content -->
        <div id="content" bis_skin_checked="1">
          <div id="masthead_child_div" bis_skin_checked="1">
            <div id="flash-upgrade" bis_skin_checked="1">
              <div class="yt-alert yt-alert-default yt-alert-error  yt-alert-player" bis_skin_checked="1">
                <div class="yt-alert-icon" bis_skin_checked="1">
                  <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
                </div>
                <div class="yt-alert-buttons" bis_skin_checked="1"></div>
                <div class="yt-alert-content" role="alert" bis_skin_checked="1"> <span
                    class="yt-alert-vertical-trick"></span>
                  <div class="yt-alert-message" bis_skin_checked="1">
                    You need to upgrade your Adobe Flash Player to watch this video. <br> <a
                      href="https://get.adobe.com/flashplayer/">Download it from Adobe.</a>
                  </div>
                </div>
              </div>
            </div>
          </div>







          <div id="ad_creative_1" class="ad-div mastad" style="z-index: 1;">
            <ins class="adsbygoogle" style="display:inline-block;width:970px;height:250px"
              data-ad-client="ca-pub-9877643383688694" data-ad-slot="3818989330"></ins>
            <script>
              (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
          </div>



          <div class="guide-layout-container enable-fancy-subscribe-button" bis_skin_checked="1">
            <div class="guide-container" bis_skin_checked="1">
              <?php if ($ytLoggedIn): // залогиненному — «Browse channels», как в 2012 ?>
              <div id="guide-builder-promo" bis_skin_checked="1">
                <div id="guide-builder-promo-buttons" bis_skin_checked="1">
                  <button href="/channels?feature=promo" type="button" class=" yt-uix-button yt-uix-button-primary"
                    onclick=";window.location.href=this.getAttribute('href');return false;" role="button"><span
                      class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-add"
                        src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span><span
                      class="yt-uix-button-content">Browse channels </span></button>
                </div>
              </div>
              <?php else: // гостю — приглашение войти через наш OAuth ?>
              <div id="guide-builder-promo" bis_skin_checked="1">
                <h2>
                  Sign in to add channels to your homepage
                </h2>
                <div id="guide-builder-promo-buttons" class="signed-out" bis_skin_checked="1">
                  <button href="/auth/google/login?return=%2F" type="button" class=" yt-uix-button yt-uix-button-dark"
                    onclick=";window.location.href=this.getAttribute('href');return false;" role="button"><span
                      class="yt-uix-button-content">Sign In </span></button>
                </div>
              </div>
              <?php endif; ?>
              <div class="guide" bis_skin_checked="1">

                <?php if ($ytLoggedIn): // ─── ГАЙД ЗАЛОГИНЕННОГО: канал + подписки + From YouTube ─── ?>
                <div id="channel">
                  <span id="channel-thumb">
                    <a href="<?php echo htmlspecialchars($ytUserChannel) ?>" class="yt-user-photo ">
                      <span class="video-thumb ux-thumb yt-thumb-square-77 "><span class="yt-thumb-clip"><span
                            class="yt-thumb-clip-inner"><img src="<?php echo htmlspecialchars($ytUserAvatar) ?>"
                              alt="<?php echo htmlspecialchars($ytUserName) ?>" width="77"
                              onerror="this.onerror=null;this.src='/dynamic/pfp/default.png'"><span
                              class="vertical-align"></span></span></span></span>
                    </a>
                  </span>
                  <div id="personal-feeds">
                    <ul>
                      <li class="guide-item-container"><a class="guide-item guide-item-action"
                          href="<?php echo htmlspecialchars($ytUserChannel) ?>?feature=guide">My channel<img
                            src="/yts/img/pixel-vfl3z5WfW.gif" class="see-more-arrow" alt=""></a></li>
                      <li class="guide-item-container"><a class="guide-item" data-feed-name="uploads"
                          data-feed-type="personal" title="Videos you have uploaded">Videos</a></li>
                      <li class="guide-item-container"><a class="guide-item" data-feed-name="likes"
                          data-feed-type="personal" title="Videos you have liked">Likes</a></li>
                      <li class="guide-item-container"><a class="guide-item" data-feed-name="history"
                          data-feed-type="personal" title="Videos you have watched">History</a></li>
                      <li class="guide-item-container"><a class="guide-item" data-feed-name="watch_later"
                          data-feed-type="personal" title="Videos you have added to your Watch Later list">Watch
                          Later</a></li>
                    </ul>
                  </div>
                </div>

                <div class="guide-section yt-uix-expander  first ">
                  <h3 class="guide-item-container">
                    <a class="guide-item" data-feed-name="subscriptions" data-feed-type="system" id="all-subscriptions"
                      data-feed-name="all" data-feed-type="main">
                      <span class="thumb">
                        <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="" class="system-icon category">
                      </span>
                      <span class="display-name">
                        Subscriptions
                      </span>
                    </a>
                  </h3>
                  <ul>
                    <li class="guide-item-container hideable">
                      <a id="social-guide-item" class="guide-item" data-feed-name="social_all" data-feed-type="social">
                        <span class="thumb">
                          <img src="/yts/img/pixel-vfl3z5WfW.gif" class="system-icon social">
                        </span>
                        <span class="display-name">
                          Social
                        </span>
                      </a>
                    </li>
                  </ul>
                  <div class="guide-item-container">
                    <span class="guide-item guide-item-fake guide-item-action">
                      <a href="/subscription_manager?feature=foot">see all<img src="/yts/img/pixel-vfl3z5WfW.gif"
                          class="see-more-arrow" alt=""></a> </span>
                  </div>
                </div>

                <div class="guide-section yt-uix-expander  yt-uix-expander-collapsed ">
                  <h3 class="guide-item-container">
                    <a class="guide-item selected" data-feed-name="youtube" data-feed-type="system">
                      <span class="thumb">
                        <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="" class="system-icon category">
                      </span>
                      <span class="display-name">
                        From YouTube
                      </span>
                    </a>
                  </h3>
                  <ul>
                    <li class="guide-item-container "><a class="guide-item" data-feed-name="trending"
                        data-feed-type="system"><span class="thumb"><img class="system-icon system trending"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span
                          class="display-name">Trending</span></a></li>
                    <li class="guide-item-container "><a class="guide-item" data-feed-name="music"
                        data-feed-type="system"><span class="thumb"><img class="system-icon system music"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">Music</span></a>
                    </li>
                    <li class="guide-item-container "><a class="guide-item" data-feed-name="entertainment"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart entertainment"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span
                          class="display-name">Entertainment</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="sports"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart sports"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span
                          class="display-name">Sports</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="film"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart film"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">Film &amp;
                          Animation</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="news"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart news"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">News &amp;
                          Politics</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="comedy"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart comedy"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span
                          class="display-name">Comedy</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="people"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart people"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">People &amp;
                          Blogs</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="science"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart science"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">Science &amp;
                          Technology</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="gadgets"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart gadgets"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span
                          class="display-name">Gaming</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="howto"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart howto"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">Howto &amp;
                          Style</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="education"
                        data-feed-type="system"><span class="thumb"><img class="system-icon system education"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span
                          class="display-name">Education</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="animals"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart animals"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">Pets &amp;
                          Animals</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="vehicles"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart vehicles"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">Autos &amp;
                          Vehicles</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="travel"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart travel"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">Travel &amp;
                          Events</span></a></li>
                    <li class="guide-item-container hideable"><a class="guide-item" data-feed-name="nonprofits"
                        data-feed-type="chart"><span class="thumb"><img class="system-icon chart nonprofits"
                            src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span><span class="display-name">Nonprofits &amp;
                          Activism</span></a></li>
                  </ul>
                  <div class="guide-item-container">
                    <span class="guide-item guide-item-action guide-item-fake">
                      <a class="yt-uix-expander-head guide-show-more-less"><span class="show-more">more</span><span
                          class="show-less">less</span></a>
                      <a href="/videos?feature=hp">see all<img src="/yts/img/pixel-vfl3z5WfW.gif" class="see-more-arrow"
                          alt=""></a> </span>
                  </div>
                </div>

                <div class="guide-section">
                  <h3 class="guide-item-container ">
                    <a class="guide-item" data-feed-name="channels" data-feed-type="system">
                      <span class="thumb">
                        <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="" class="system-icon category">
                      </span>
                      <span class="display-name">
                        Suggested channels
                      </span>
                    </a>
                  </h3>
                  <ul>
                    <?php foreach ($guideChannels as $gc): ?>
                    <li class="guide-item-container ">
                      <a class="guide-item guide-recommendation-item"
                        data-external-id="<?php echo htmlspecialchars($gc['id']) ?>"
                        data-feed-name="<?php echo htmlspecialchars($gc['id']) ?>" data-feed-type="user">
                        <span class="thumb"> <span class="video-thumb ux-thumb yt-thumb-square-28 "><span
                              class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img
                                  src="<?php echo htmlspecialchars($gc['avatar']) ?>" alt="Thumbnail"
                                  data-thumb="<?php echo htmlspecialchars($gc['avatar']) ?>" width="28"><span
                                  class="vertical-align"></span></span></span></span>
                        </span>
                        <span class="display-name">
                          <?php echo htmlspecialchars($gc['title']) ?>
                        </span>
                        <span
                          class="guide-subscription-button yt-subscription-button-js-default guide-item-action yt-uix-tooltip"
                          title="Subscribe to <?php echo htmlspecialchars($gc['title']) ?>"
                          data-tooltip-show-delay="250" data-subscription-feature="guide-recs"
                          data-subscription-value="<?php echo htmlspecialchars($gc['id']) ?>">
                          <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="Subscribe">
                        </span>
                        <span class="guide-subscription-dismiss guide-item-action">
                          <img src="/yts/img/pixel-vfl3z5WfW.gif" title="remove" alt="Close">
                        </span>
                      </a>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                  <div class="guide-item-container">
                    <span class="guide-item guide-item-action guide-item-fake">
                      <a href="/channels?feature=foot">see all<img src="/yts/img/pixel-vfl3z5WfW.gif"
                          class="see-more-arrow" alt=""></a> </span>
                  </div>
                </div>

                <?php else: // ─── ГАЙД ГОСТЯ (как было) ─── ?>

                <div class="guide-section yt-uix-expander  first " bis_skin_checked="1">
                  <h3 class="guide-item-container selected-child">
                    <a class="guide-item selected" data-feed-name="youtube" data-feed-type="system">
                      <span class="thumb">
                        <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="" class="system-icon category">
                      </span>
                      <span class="display-name">
                        From YouTube
                      </span>
                    </a>
                  </h3>
                  <ul>
                    <li class="guide-item-container ">
                      <a class="guide-item" data-feed-name="trending" data-feed-type="system">
                        <span class="thumb">
                          <img class="system-icon system trending" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                        </span>
                        <span class="display-name">
                          Trending
                        </span>
                      </a>
                    </li>

                    <li class="guide-item-container ">
                      <a class="guide-item" data-feed-name="music" data-feed-type="system">
                        <span class="thumb">
                          <img class="system-icon system music" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                        </span>
                        <span class="display-name">
                          Music
                        </span>
                      </a>
                    </li>

                    <li class="guide-item-container ">
                      <a class="guide-item" data-feed-name="entertainment" data-feed-type="chart">
                        <span class="thumb">
                          <img class="system-icon chart entertainment" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                        </span>
                        <span class="display-name">
                          Entertainment
                        </span>
                      </a>
                    </li>

                    <li class="guide-item-container ">
                      <a class="guide-item" data-feed-name="sports" data-feed-type="chart">
                        <span class="thumb">
                          <img class="system-icon chart sports" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                        </span>
                        <span class="display-name">
                          Sports
                        </span>
                      </a>
                    </li>

                    <li class="guide-item-container ">
                      <a class="guide-item" data-feed-name="comedy" data-feed-type="chart">
                        <span class="thumb">
                          <img class="system-icon chart comedy" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                        </span>
                        <span class="display-name">
                          Comedy
                        </span>
                      </a>
                    </li>

                    <li class="guide-item-container ">
                      <a class="guide-item" data-feed-name="film" data-feed-type="chart">
                        <span class="thumb">
                          <img class="system-icon chart film" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                        </span>
                        <span class="display-name">
                          Film &amp; Animation
                        </span>
                      </a>
                    </li>

                    <li class="guide-item-container ">
                      <a class="guide-item" data-feed-name="gadgets" data-feed-type="chart">
                        <span class="thumb">
                          <img class="system-icon chart gadgets" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                        </span>
                        <span class="display-name">
                          Gaming
                        </span>
                      </a>
                    </li>

                    <?php foreach ($guideChannels as $gc): ?>
                    <li class="guide-item-container ">
                      <a class="guide-item" data-external-id="<?php echo htmlspecialchars($gc['id']) ?>"
                        data-feed-name="<?php echo htmlspecialchars($gc['id']) ?>" data-feed-type="user">
                        <span class="thumb"><span class="video-thumb ux-thumb yt-thumb-square-28 "><span
                              class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img
                                  src="<?php echo htmlspecialchars($gc['avatar']) ?>" alt="Thumbnail"
                                  data-thumb="<?php echo htmlspecialchars($gc['avatar']) ?>" width="28"
                                  data-group-key="thumb-group-0"><span
                                  class="vertical-align"></span></span></span></span></span>
                        <span class="display-name">
                          <?php echo htmlspecialchars($gc['title']) ?>
                        </span>
                      </a>
                    </li>
                    <?php endforeach; ?>

                  </ul>
                  <div class="guide-item-container" bis_skin_checked="1">
                    <span class="guide-item guide-item-action guide-item-fake">
                      <a href="/videos?feature=hp" bis_skin_checked="1">see all<img src="/yts/img/pixel-vfl3z5WfW.gif"
                          class="see-more-arrow" alt=""></a> </span>
                  </div>
                </div>

                <?php endif; // $ytLoggedIn ?>
              </div>

            </div>
            <div class="guide-background" bis_skin_checked="1"></div>


            <div id="video-sidebar" bis_skin_checked="1">

              <div id="ad_creative_expand_btn_1" class="masthead-ad-control open hid" bis_skin_checked="1">
                <a onclick="masthead.expand_ad(); return false;">
                  <span>Show ad</span>
                  <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                </a>
              </div>




              <h3 class="sidebar-module-header">
                Spotlight
              </h3>
              <h2>The 2012 U.S. Presidential Campaign</h2>
              <p class="sidebar-module-description">
                With Election Day just around the corner in the United States, we look back at some of the most
                talked-about moments from this cycle's campaign for president. From Big Bird to the 47 percent, these
                videos helped drive the mainstream media conversation.
              </p>
              <p class="sidebar-module-description">
                Presented by: <a href="/politics" bis_skin_checked="1">politics</a>
              </p>
              <?php $sidebarList = $spotlightVideos; ?>
              <ul>
                <?php foreach ($sidebarList as $sv): ?>
                <li class="video-list-item"><a
                    href="/watch?v=<?php echo htmlspecialchars($sv['id']) ?>&amp;feature=g-sptl"
                    class="video-list-item-link yt-uix-sessionlink"><span class="ux-thumb-wrap contains-addto "><span
                        class="video-thumb ux-thumb yt-thumb-default-120 "><span class="yt-thumb-clip"><span
                            class="yt-thumb-clip-inner"><img src="/yts/img/pixel-vfl3z5WfW.gif"
                              alt="<?php echo htmlspecialchars($sv['title']) ?>"
                              data-thumb="<?php echo htmlspecialchars($sv['thumbnail']) ?>" width="120"><span
                              class="vertical-align"></span></span></span></span><span class="video-time">
                        <?php echo htmlspecialchars($sv['duration']) ?>
                      </span></span><span dir="ltr" class="title" title="<?php echo htmlspecialchars($sv['title']) ?>">
                      <?php echo htmlspecialchars(mb_strimwidth($sv['title'], 0, 45, '...')) ?>
                    </span><span class="stat">by <span class="yt-user-name" dir="ltr">
                        <?php echo htmlspecialchars($sv['author']) ?>
                      </span></span><span class="stat view-count"><span class="viewcount">
                        <?php echo htmlspecialchars($sv['views']) ?> views
                      </span></span></a></li>
                <?php endforeach; ?>
              </ul>

              <h3>
                Featured
              </h3>
              <?php $sidebarList = $featuredVideos; ?>
              <ul>
                <?php foreach ($sidebarList as $sv): ?>
                <li class="video-list-item"><a
                    href="/watch?v=<?php echo htmlspecialchars($sv['id']) ?>&amp;feature=g-sptl"
                    class="video-list-item-link yt-uix-sessionlink"><span class="ux-thumb-wrap contains-addto "><span
                        class="video-thumb ux-thumb yt-thumb-default-120 "><span class="yt-thumb-clip"><span
                            class="yt-thumb-clip-inner"><img src="/yts/img/pixel-vfl3z5WfW.gif"
                              alt="<?php echo htmlspecialchars($sv['title']) ?>"
                              data-thumb="<?php echo htmlspecialchars($sv['thumbnail']) ?>" width="120"><span
                              class="vertical-align"></span></span></span></span><span class="video-time">
                        <?php echo htmlspecialchars($sv['duration']) ?>
                      </span></span><span dir="ltr" class="title" title="<?php echo htmlspecialchars($sv['title']) ?>">
                      <?php echo htmlspecialchars(mb_strimwidth($sv['title'], 0, 45, '...')) ?>
                    </span><span class="stat">by <span class="yt-user-name" dir="ltr">
                        <?php echo htmlspecialchars($sv['author']) ?>
                      </span></span><span class="stat view-count"><span class="viewcount">
                        <?php echo htmlspecialchars($sv['views']) ?> views
                      </span></span></a></li>
                <?php endforeach; ?>
              </ul>

            </div>

            <div id="feed" bis_skin_checked="1">
              <div id="feed-system-youtube" class="individual-feed" data-loaded="true" data-feed-name="youtube"
                data-feed-type="system" bis_skin_checked="1">
                <div class="feed-header no-metadata" bis_skin_checked="1">
                  <div class="feed-header-thumb" bis_skin_checked="1">
                    <img class="feed-header-icon youtube" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                  </div>
                  <div class="feed-header-details context-source-container" data-context-source="From YouTube"
                    bis_skin_checked="1">
                    <h2> From YouTube
                    </h2>
                  </div>
                </div>

                <div class="feed-container" data-filter-type="" data-view-type="" bis_skin_checked="1">

                  <div class="feed-page" bis_skin_checked="1">
                    <ul class="context-data-container">
                      <?php foreach ($homeFeedVideos as $fi): ?>
                      <li>
                        <div class="feed-item-container  first " data-channel-key="UCR2A9ZNliJfgC66IvIpe-Zw"
                          bis_skin_checked="1">
                          <div class="feed-author-bubble-container">
                            <a href="/channel/<?php echo htmlspecialchars($fi['authorId']) ?>?feature=g-logo-xit"
                              class="feed-author-bubble"> <span class="feed-item-author">
                                <span class="video-thumb ux-thumb yt-thumb-square-28"><span class="yt-thumb-clip"><span
                                      class="yt-thumb-clip-inner">
                                      <img src="/yts/img/pixel-vfl3z5WfW.gif"
                                        alt="<?php echo htmlspecialchars($fi['author']) ?>"
                                        data-thumb="<?php echo htmlspecialchars($fi['authorAvatar'] ?? '') ?>"
                                        width="28">
                                      <span class="vertical-align"></span></span></span></span>
                              </span>
                            </a>
                          </div>

                          <div class="feed-item-main">
                            <div class="feed-item-header">
                              <span class="feed-item-actions-line">
                                <span class="feed-item-owner"><a
                                    href="/channel/<?php echo htmlspecialchars($fi['authorId']) ?>?feature=g-logo-xit"
                                    class="yt-uix-sessionlink yt-user-name" dir="ltr">
                                    <?php echo htmlspecialchars($fi['author']) ?>
                                  </a></span>
                                uploaded a video
                                <span class="feed-item-time">
                                  <?php echo htmlspecialchars($fi['publishedAgo']) ?>
                                </span>
                              </span>
                            </div>

                            <div class="feed-item-content-wrapper clearfix context-data-item"
                              data-context-item-actionverb="uploaded"
                              data-context-item-title="<?php echo htmlspecialchars($fi['title']) ?>"
                              data-context-item-type="video"
                              data-context-item-time="<?php echo htmlspecialchars($fi['duration']) ?>"
                              data-context-item-user="<?php echo htmlspecialchars($fi['author']) ?>"
                              data-context-item-id="<?php echo htmlspecialchars($fi['id']) ?>"
                              data-context-item-views="<?php echo htmlspecialchars($fi['views']) ?> views">
                              <div class="feed-item-thumb">
                                <a class="ux-thumb-wrap contains-addto yt-uix-contextlink yt-uix-sessionlink"
                                  href="/watch?v=<?php echo htmlspecialchars($fi['id']) ?>&amp;feature=g-logo-xit">
                                  <span class="video-thumb ux-thumb yt-thumb-default-185"><span
                                      class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img
                                          src="<?php echo htmlspecialchars($fi['thumbnail']) ?>" alt="Thumbnail"
                                          data-thumb="<?php echo htmlspecialchars($fi['thumbnail']) ?>"
                                          width="185"><span class="vertical-align"></span></span></span></span>
                                  <span class="video-time">
                                    <?php echo htmlspecialchars($fi['duration']) ?>
                                  </span>

                                  <button onclick=";return false;" title="Watch Later" type="button"
                                    class="addto-button video-actions addto-watch-later-button-sign-in yt-uix-button yt-uix-button-default yt-uix-button-short yt-uix-tooltip"
                                    data-button-menu-id="shared-addto-watch-later-login"
                                    data-video-ids="<?php echo htmlspecialchars($fi['id']) ?>" role="button"><span
                                      class="yt-uix-button-content"> <img src="/yts/img/pixel-vfl3z5WfW.gif"
                                        alt="Watch Later">
                                    </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif"
                                      alt=""></button>
                                </a>
                              </div>
                              <div class="feed-item-content">
                                <h4>
                                  <a class="feed-video-title title yt-uix-contextlink yt-uix-sessionlink secondary"
                                    href="/watch?v=<?php echo htmlspecialchars($fi['id']) ?>&amp;feature=g-logo-xit">
                                    <?php echo htmlspecialchars($fi['title']) ?>
                                  </a>
                                </h4>
                                <div class="metadata">
                                  <a href="/channel/<?php echo htmlspecialchars($fi['authorId']) ?>?feature=g-logo-xit"
                                    class="yt-user-photo"><span class="video-thumb ux-thumb yt-thumb-square-18"><span
                                        class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img
                                            src="/yts/img/pixel-vfl3z5WfW.gif"
                                            alt="<?php echo htmlspecialchars($fi['author']) ?>"
                                            data-thumb="<?php echo htmlspecialchars($fi['authorAvatar'] ?? '') ?>"
                                            width="18"><span class="vertical-align"></span></span></span></span></a><a
                                    href="/channel/<?php echo htmlspecialchars($fi['authorId']) ?>?feature=g-logo-xit"
                                    class="yt-uix-sessionlink yt-user-name" dir="ltr">
                                    <?php echo htmlspecialchars($fi['author']) ?>
                                  </a>
                                  <?php if (!empty($fi['views'])): ?>
                                  <span class="bull">•</span>
                                  <span class="view-count">
                                    <?php echo htmlspecialchars($fi['views']) ?> views
                                  </span>
                                  <?php endif; ?>
                                  <?php if (!empty($fi['description'])): ?>
                                  <div class="description">
                                    <p>
                                      <?= htmlspecialchars($fi['description']) ?>
                                    </p>
                                  </div>
                                  <?php endif; ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="feed-item-dismissal-notices"></div>
                      </li>
                      <?php endforeach; ?>
                    </ul>

                  </div>
                </div>

              </div>


              <div id="feed-error" class="individual-feed hid" bis_skin_checked="1">
                <p class="feed-message">
                  We were unable to complete the request, please try again later.
                </p>
              </div>

              <div id="feed-loading-template" class="hid" bis_skin_checked="1">
                <div class="feed-message" bis_skin_checked="1">
                  <p class="yt-spinner">
                    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="yt-spinner-img" alt="Loading icon">

                    Loading...
                  </p>

                </div>
              </div>

            </div>
            <div id="feed-background" bis_skin_checked="1"></div>

            <div id="footer-ads" bis_skin_checked="1">






              <?php /* Рекламный 1x1-iframe в ad-g.doubleclick.net удалён: эндпоинт мёртв,
           запрос висел до таймаута при каждой загрузке главной. */ ?>


            </div>
          </div>



        </div>
        <!-- end content -->
      </div>
      <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>




      <div id="playlist-bar" class="hid passive editable"
        data-video-url="/watch?v=&amp;feature=BFql&amp;playnext=1&amp;list=QL" data-list-id="" data-list-type="QL"
        bis_skin_checked="1">
        <div id="playlist-bar-bar-container" bis_skin_checked="1">
          <div id="playlist-bar-bar" bis_skin_checked="1">
            <div class="yt-alert yt-alert-naked yt-alert-success hid " id="playlist-bar-notifications"
              bis_skin_checked="1">
              <div class="yt-alert-icon" bis_skin_checked="1">
                <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
              </div>
              <div class="yt-alert-content" role="alert" bis_skin_checked="1"></div>
            </div>
            <span id="playlist-bar-info"><span class="playlist-bar-active playlist-bar-group"><button
                  onclick=";return false;" title="Previous video" type="button" id="playlist-bar-prev-button"
                  class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty"
                  role="button"><span class="yt-uix-button-icon-wrapper"><img
                      class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-prev" src="/yts/img/pixel-vfl3z5WfW.gif"
                      alt="Previous video"><span class="yt-uix-button-valign"></span></span></button><span
                  class="playlist-bar-count"><span class="playing-index">0</span> / <span
                    class="item-count">0</span></span><button type="button"
                  class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty"
                  onclick=";return false;" id="playlist-bar-next-button" role="button"><span
                    class="yt-uix-button-icon-wrapper"><img
                      class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-next" src="/yts/img/pixel-vfl3z5WfW.gif"
                      alt=""><span class="yt-uix-button-valign"></span></span></button></span><span
                class="playlist-bar-active playlist-bar-group"><button type="button"
                  class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty"
                  onclick=";return false;" id="playlist-bar-autoplay-button" data-button-toggle="true"
                  role="button"><span class="yt-uix-button-icon-wrapper"><img
                      class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-autoplay"
                      src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span
                      class="yt-uix-button-valign"></span></span></button><button type="button"
                  class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty"
                  onclick=";return false;" id="playlist-bar-shuffle-button" data-button-toggle="true"
                  role="button"><span class="yt-uix-button-icon-wrapper"><img
                      class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-shuffle"
                      src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span
                      class="yt-uix-button-valign"></span></span></button></span><span
                class="playlist-bar-passive playlist-bar-group"><button onclick=";return false;" title="Play videos"
                  type="button" id="playlist-bar-play-button"
                  class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty"
                  role="button"><span class="yt-uix-button-icon-wrapper"><img
                      class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-play" src="/yts/img/pixel-vfl3z5WfW.gif"
                      alt="Play videos"><span class="yt-uix-button-valign"></span></span></button><span
                  class="playlist-bar-count"><span class="item-count">0</span></span></span><span
                id="playlist-bar-title" class="yt-uix-button-group"><span class="playlist-title">Unsaved
                  Playlist</span></span></span>
            <a id="playlist-bar-lists-back" href="#">
              Return to active list
            </a>

            <span id="playlist-bar-controls"><span class="playlist-bar-group"><button type="button"
                  class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-text yt-uix-button-empty"
                  onclick=";return false;" id="playlist-bar-toggle-button" role="button"><span
                    class="yt-uix-button-icon-wrapper"><img
                      class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-toggle"
                      src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span
                      class="yt-uix-button-valign"></span></span></button></span><span
                class="playlist-bar-group"><button type="button"
                  class="yt-uix-tooltip yt-uix-tooltip-masked yt-uix-button-reverse flip yt-uix-button yt-uix-button-text"
                  onclick=";return false;" data-button-menu-id="playlist-bar-options-menu"
                  data-button-has-sibling-menu="true" role="button"><span class="yt-uix-button-content">Options
                  </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif"
                    alt=""></button></span></span>
          </div>
        </div>

        <div id="playlist-bar-tray-container" bis_skin_checked="1">
          <div id="playlist-bar-tray" class="yt-uix-slider yt-uix-slider-fluid" bis_skin_checked="1"><button
              class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-prev"
              onclick="return false;"><img class="yt-uix-slider-prev-arrow" src="/yts/img/pixel-vfl3z5WfW.gif"
                alt="Previous video"></button><button
              class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-next"
              onclick="return false;"><img class="yt-uix-slider-next-arrow" src="/yts/img/pixel-vfl3z5WfW.gif"
                alt="Next video"></button>
            <div class="yt-uix-slider-body" bis_skin_checked="1">
              <div id="playlist-bar-tray-content" class="yt-uix-slider-slide" bis_skin_checked="1">
                <ol class="video-list"></ol>
                <ol id="playlist-bar-help">
                  <li class="empty playlist-bar-help-message">Your queue is empty. Add videos to your queue using this
                    button: <img src="/yts/img/pixel-vfl3z5WfW.gif" class="addto-button-help"><br> or <a
                      href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252F&amp;uilel=3&amp;hl=en_US&amp;service=youtube"
                      bis_skin_checked="1">sign in</a> to load a different list.</li>
                </ol>
              </div>
              <div class="yt-uix-slider-shade-left" bis_skin_checked="1"></div>
              <div class="yt-uix-slider-shade-right" bis_skin_checked="1"></div>
            </div>
          </div>
          <div id="playlist-bar-save" bis_skin_checked="1"></div>
          <div id="playlist-bar-lists" class="dark-lolz" bis_skin_checked="1"></div>
          <div id="playlist-bar-loading" bis_skin_checked="1"><img src="/yts/img/pixel-vfl3z5WfW.gif"
              alt="Loading..."><span id="playlist-bar-loading-message">Loading...</span><span
              id="playlist-bar-saving-message" class="hid">Saving...</span></div>
          <div id="playlist-bar-template" style="display: none;"
            data-video-thumb-url="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg" bis_skin_checked="1"><!--<li class="playlist-bar-item yt-uix-slider-slide-unit __classes__" data-video-id="__video_encrypted_id__"><a href="__video_url__" title="__video_title__" class="yt-uix-sessionlink" data-sessionlink="ei=CJnt54SKubMCFYwTIQodgnQ9eQ%3D%3D&amp;feature=BFa"><span class="video-thumb ux-thumb yt-thumb-default-106 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="/yts/img/pixel-vfl3z5WfW.gif" alt="__video_title__" data-thumb-manual="true" data-thumb="__video_thumb_url__" width="106" ><span class="vertical-align"></span></span></span></span><span class="screen"></span><span class="count"><strong>__list_position__</strong></span><span class="play"><img src="/yts/img/pixel-vfl3z5WfW.gif"></span><span class="yt-uix-button yt-uix-button-default delete"><img class="yt-uix-button-icon-playlist-bar-delete" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Delete"></span><span class="now-playing">Now playing</span><span dir="ltr" class="title"><span>__video_title__  <span class="uploader">by __video_display_name__</span>
</span></span><span class="dragger"></span></a></li>--></div>
          <div id="playlist-bar-next-up-template" style="display: none;" bis_skin_checked="1">
            <!--<div class="playlist-bar-next-thumb"><span class="video-thumb ux-thumb yt-thumb-default-74 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg" alt="Thumbnail" width="74" ><span class="vertical-align"></span></span></span></span></div>-->
          </div>
        </div>
        <div id="playlist-bar-options-menu" class="hid" bis_skin_checked="1">

          <div id="playlist-bar-extras-menu" bis_skin_checked="1">
            <ul>
              <li><span class="yt-uix-button-menu-item" data-action="clear">
                  Clear all videos from this list
                </span></li>
            </ul>

          </div>

          <ul>
            <li><span class="yt-uix-button-menu-item"
                onclick="window.location.href='https://support.google.com/youtube/bin/answer.py?answer=146749&amp;hl=en-US'">Learn
                more</span></li>
          </ul>
        </div>

      </div>



      <div id="shared-addto-watch-later-login" class="hid" bis_skin_checked="1">
        <a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252F&amp;uilel=3&amp;hl=en_US&amp;service=youtube"
          class="sign-in-link" bis_skin_checked="1">Sign in</a> to add this to a playlist

      </div>

      <div id="shared-addto-menu" style="display: none;" class="hid sign-in" bis_skin_checked="1">
        <div class="addto-menu" bis_skin_checked="1">
          <div id="addto-list-panel" class="menu-panel active-panel" bis_skin_checked="1">
            <span class="yt-uix-button-menu-item yt-uix-tooltip sign-in" data-possible-tooltip=""
              data-tooltip-show-delay="750"><a
                href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252F&amp;uilel=3&amp;hl=en_US&amp;service=youtube"
                class="sign-in-link" bis_skin_checked="1">Sign in</a> to add this to a playlist
            </span>

          </div>
          <div id="addto-list-saved-panel" class="menu-panel" bis_skin_checked="1">
            <div class="panel-content" bis_skin_checked="1">
              <div class="yt-alert yt-alert-naked yt-alert-success  " bis_skin_checked="1">
                <div class="yt-alert-icon" bis_skin_checked="1">
                  <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
                </div>
                <div class="yt-alert-content" role="alert" bis_skin_checked="1"> <span
                    class="yt-alert-vertical-trick"></span>
                  <div class="yt-alert-message" bis_skin_checked="1">

                    <span class="message">Added to <span class="addto-title yt-uix-tooltip yt-uix-tooltip-reverse"
                        title="More information about this playlist" data-tooltip-show-delay="750"></span></span>

                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="addto-list-error-panel" class="menu-panel" bis_skin_checked="1">
            <div class="panel-content" bis_skin_checked="1">
              <img src="/yts/img/pixel-vfl3z5WfW.gif">
              <span class="error-details"></span>
              <a class="show-menu-link">Back to list</a>
            </div>
          </div>

          <div id="addto-note-input-panel" class="menu-panel" bis_skin_checked="1">
            <div class="panel-content" bis_skin_checked="1">
              <div class="yt-alert yt-alert-naked yt-alert-success  " bis_skin_checked="1">
                <div class="yt-alert-icon" bis_skin_checked="1">
                  <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
                </div>
                <div class="yt-alert-content" role="alert" bis_skin_checked="1"> <span
                    class="yt-alert-vertical-trick"></span>
                  <div class="yt-alert-message" bis_skin_checked="1">
                    <span class="message">Added to playlist:</span>
                    <span class="addto-title yt-uix-tooltip" title="More information about this playlist"
                      data-tooltip-show-delay="750"></span>

                  </div>
                </div>
              </div>
            </div>
            <div class="yt-uix-char-counter" data-char-limit="150" bis_skin_checked="1">
              <div class="addto-note-box addto-text-box" bis_skin_checked="1"><textarea id="addto-note"
                  class="addto-note yt-uix-char-counter-input" maxlength="150"></textarea><label for="addto-note"
                  class="addto-note-label">Add an optional note</label></div><span
                class="yt-uix-char-counter-remaining">150</span>
            </div> <button disabled="disabled" type="button"
              class="playlist-save-note yt-uix-button yt-uix-button-default" onclick=";return false;"
              role="button"><span class="yt-uix-button-content">Add note </span></button>
          </div>
          <div id="addto-note-saving-panel" class="menu-panel" bis_skin_checked="1">
            <div class="panel-content loading-content" bis_skin_checked="1">
              <img src="/yts/img/pixel-vfl3z5WfW.gif">
              <span>Saving note...</span>
            </div>
          </div>
          <div id="addto-note-saved-panel" class="menu-panel" bis_skin_checked="1">
            <div class="panel-content" bis_skin_checked="1">
              <img src="/yts/img/pixel-vfl3z5WfW.gif">
              <span class="message">Note added to:</span>
            </div>
          </div>
          <div id="addto-note-error-panel" class="menu-panel" bis_skin_checked="1">
            <div class="panel-content" bis_skin_checked="1">
              <img src="/yts/img/pixel-vfl3z5WfW.gif">
              <span class="message">Error adding note:</span>
              <ul class="error-details"></ul>
              <a class="add-note-link">Click to add a new note</a>
            </div>
          </div>
          <div class="close-note hid" bis_skin_checked="1">
            <img src="/yts/img/pixel-vfl3z5WfW.gif" class="close-button">
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
      'FEEDBACK_LOCALE_EXTRAS': { "logged_in": false, "experiments": "927103,911614,907519,922401,920704,912806,927201,925003,913546,913556,920201,900816,911112,901451", "guide_subs": "NA", "accept_language": null }
    });
  </script>


  <script>
    if (window.yt.timing) { yt.timing.tick("js_head"); }    </script>

  <script>
    _gel('masthead-search-term').focus();
    yt.setConfig('GUIDE_VERSION', 1);
  </script>

  <script>
    yt.setMsg('FLASH_UPGRADE', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e  \u003cdiv class=\"yt-alert-icon\"\u003e\n    \u003cimg s\u0072c=\"\/yts\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\n  \u003c\/div\u003e\n\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\" role=\"alert\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n            You need to upgrade your Adobe Flash Player to watch this video. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eDownload it from Adobe.\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");
    yt.setConfig({
      'PLAYER_CONFIG': { "url": "\/yts\/swf\/masthead_child-vflRMMO6_.swf", "min_version": "8.0.0", "args": { "enablejsapi": 1 }, "url_v9as2": "", "params": { "bgcolor": "#FFFFFF", "allowfullscreen": "false", "allowscriptaccess": "always" }, "attrs": { "width": "1", "id": "masthead_child", "height": "1" }, "url_v8": "", "html5": false }
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
    if (window.yt.timing) { yt.timing.tick("js_page"); }    </script>

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
      'UNDO_LINK': "Undo"
    });


    yt.setConfig({
      'DRAGDROP_BINARY_URL': "\/yts\/jsbin\/www-dragdrop-vflVtCQG3.js",
      'PLAYLIST_BAR_PLAYING_INDEX': -1
    });

    yt.setAjaxToken('addto_ajax_logged_out', "2980s-6wm9fmz4X3IdrxF3DzaBF8MTM1MjI0Nzg3OEAxMzUyMTYxNDc4");

    yt.pubsub.subscribe('init', yt.www.lists.init);






    yt.events.listen(_gel('masthead-search-term'), 'focus', yt.www.home.ads.workaroundReset);



    yt.setConfig({ 'SBOX_JS_URL': "\/yts\/jsbin\/www-searchbox-vflWtMugU.js", 'SBOX_SETTINGS': { "CLOSE_ICON_URL": "\/yts\/img\/icons\/close-vflrEJzIW.png", "SHOW_CHIP": false, "PSUGGEST_TOKEN": null, "REQUEST_DOMAIN": "us", "EXPERIMENT_ID": -1, "SESSION_INDEX": null, "HAS_ON_SCREEN_KEYBOARD": false, "CHIP_PARAMETERS": {}, "REQUEST_LANGUAGE": "en" }, 'SBOX_LABELS': { "SUGGESTION_DISMISS_LABEL": "Dismiss", "SUGGESTION_DISMISSED_LABEL": "Suggestion dismissed" } });





  </script>

  <script>
    yt.setMsg({
      'ADDTO_WATCH_LATER_ADDED': "Added",
      'ADDTO_WATCH_LATER_ERROR': "Error"
    });
  </script>



  <script>
    if (window.yt.timing) { yt.timing.tick("js_foot"); }    </script>







  <iframe class="gstl_0 gssb_k" style="display: none; top: 45px; left: 0px; height: 0px;"
    allow="autoplay 'self'; fullscreen 'self'"></iframe>
  <table cellspacing="0" cellpadding="0" class="gstl_0 gssb_c"
    style="width: 462px; display: none; top: 45px; position: absolute; left: 164px;">
    <tbody>
      <tr>
        <td class="gssb_f"></td>
        <td class="gssb_e" style="width: 100%;"></td>
      </tr>
    </tbody>
  </table>
</body>

</html>