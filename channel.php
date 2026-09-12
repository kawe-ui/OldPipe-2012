<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/channel_api.php');

// ─── Канала нет / забанен / закрыт → на oops.php ──────────────────────────────
// Причину берём из alertRenderer YouTube (channel_api.php), пустую страницу с
// битой шапкой не рендерим никогда.
if (!$channelExists) {
    header('Location: /oops.php', true, 302);
    exit;
}

// Хелпер плюрала «N videos»/«N subscribers» с сохранением формата («7.5K»)
function ch_plural(string $count, string $one, string $many): string {
    $count = trim($count);
    if ($count === '') return '';
    return $count . ' ' . ($count === '1' ? $one : $many);
}

// Превью для сетки канала — всегда mqdefault.
//
// Рамка сетки (yt-thumb-default-194) — 194×109, т.е. 16:9. InnerTube же через
// best_thumb() может отдать hqdefault (480×360, 4:3) — он бы влез в рамку с
// чёрными полосами сверху и снизу. mqdefault (320×180) — ровно 16:9 и именно
// он стоял в разметке 2012 года.
function grid_thumb(array $cv): string {
    $id = $cv['id'] ?? '';
    if ($id !== '') return 'https://i.ytimg.com/vi/' . rawurlencode($id) . '/mqdefault.jpg';
    return $cv['thumbnail'] ?? DEFAULT_VIDEO_THUMB;
}

// «Nov 12, 2005» → дата последней активности берём из первого видео сетки
$channelLatest = '';
if (!empty($channelVideos[0]['ago'])) $channelLatest = $channelVideos[0]['ago'];

// Описание → абзацы, как в разметке 2012 (<p> на каждую строку)
function ch_description_html(string $text): string {
    $out = '';
    foreach (preg_split('/\R/u', trim($text)) as $line) {
        $out .= '<p>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="en">

      <script>
var yt = yt || {};yt.timing = yt.timing || {};yt.timing.tick = function(label, opt_time) {var timer = yt.timing['timer'] || {};if(opt_time) {timer[label] = opt_time;}else {timer[label] = new Date().getTime();}yt.timing['timer'] = timer;};yt.timing.info = function(label, value) {var info_args = yt.timing['info_args'] || {};info_args[label] = value;yt.timing['info_args'] = info_args;};yt.timing.info('e', "906359,929302,900221,922401,920704,912806,900711,913546,913556,925109,919003,912706,900816");if (document.webkitVisibilityState == 'prerender') {document.addEventListener('webkitvisibilitychange', function() {yt.timing.tick('start');}, false);}yt.timing.tick('start');yt.timing.info('li','0');try {yt.timing['srt'] = window.gtbExternal && window.gtbExternal.pageT() ||window.external && window.external.pageT;} catch(e) {}if (window.chrome && window.chrome.csi) {yt.timing['srt'] = Math.floor(window.chrome.csi().pageT);}if (window.msPerformance && window.msPerformance.timing) {yt.timing['srt'] = window.msPerformance.timing.responseStart - window.msPerformance.timing.navigationStart;}    </script>

<title><?php echo htmlspecialchars($title) ?> - YouTube</title><link rel="search" type="application/opensearchdescription+xml" href="/opensearch?locale=en_US" title="YouTube Video Search"><link rel="icon" href="/yts/favicon-vfldLzJxy.ico" type="image/x-icon"><link rel="shortcut icon" href="/yts/favicon-vfldLzJxy.ico" type="image/x-icon">   <link rel="icon" href="/yts/favicon_32-vflWoMFGx.png" sizes="32x32"><link rel="canonical" href="/channel/<?php echo htmlspecialchars($channelId) ?>">  <meta name="title" content="<?php echo htmlspecialchars($channelTitle) ?>">
  <meta name="description" content="<?php echo htmlspecialchars(mb_substr($channelDescription, 0, 300)) ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($channelKeywords) ?>">
  <link rel="image_src" href="<?php echo htmlspecialchars($channelAvatar) ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($channelAvatar) ?>">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="https://www.youtube.com/feeds/videos.xml?channel_id=<?php echo htmlspecialchars($channelId) ?>">
 <link id="css-617957165" rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">
  <link rel="stylesheet" href="/yts/cssbin/www-the-rest-vflNb6rAI.css">
    <link rel="stylesheet" href="/yts/cssbin/www-channels3-vflHgudtI.css">

    <style>
      #branded-page-body-container {
      background-color: #000000;
      <?php if (!empty($channelBanner)): ?>
      background-image: url(<?php echo htmlspecialchars($channelBanner) ?>);
      <?php endif; ?>
      background-repeat: no-repeat;
      background-position: center top;
  }

    </style>
      <script>
if (window.yt.timing) {yt.timing.tick("ct");}    </script>

<link rel="stylesheet" href="/yts/cssbin/www-player-actions-vflrtkTn7.css"><style type="text/css">.gssb_c{border:0;position:absolute;z-index:989}.gssb_e{border:1px solid #ccc;border-top-color:#d9d9d9;box-shadow:0 2px 4px rgba(0,0,0,0.2);-webkit-box-shadow:0 2px 4px rgba(0,0,0,0.2);cursor:default}.gssb_f{visibility:hidden;white-space:nowrap}.gssb_k{border:0;display:block;position:absolute;top:0;z-index:988}.gsdd_a{border:none!important}.gsib_a{width:100%;padding:4px 6px 0}.gsib_a,.gsib_b{vertical-align:top}.gssb_a{padding:0 7px}.gssb_a,.gssb_a td{white-space:nowrap;overflow:hidden;line-height:22px}#gssb_b{font-size:11px;color:#36c;text-decoration:none}#gssb_b:hover{font-size:11px;color:#36c;text-decoration:underline}.gssb_m{color:#000;background:#fff}.gssb_g{text-align:center;padding:8px 0 7px;position:relative}.gssb_h{font-size:15px;height:28px;margin:0.2em;-webkit-appearance:button}.gssb_i{background:#eee}.gss_ifl{visibility:hidden;padding-left:5px}.gssb_i .gss_ifl{visibility:visible}a.gssb_j{font-size:13px;color:#36c;text-decoration:none;line-height:100%}a.gssb_j:hover{text-decoration:underline}.gssb_l{height:1px;background-color:#e5e5e5}.gscp_a,.gscp_c,.gscp_d,.gscp_e,.gscp_f{display:inline-block;vertical-align:bottom}.gscp_f{border:none}.gscp_a{background:#d9e7fe;border:1px solid #9cb0d8;cursor:default;outline:none;text-decoration:none!important;user-select:none;-webkit-user-select:none;}.gscp_a:hover{border-color:#869ec9}.gscp_a.gscp_b{background:#4787ec;border-color:#3967bf}.gscp_c{color:#444;font-size:13px;font-weight:bold}.gscp_d{color:#aeb8cb;cursor:pointer;font:21px arial,sans-serif;line-height:inherit;padding:0 7px}.gscp_d{position:relative;top:1px}.gscp_a:hover .gscp_d{color:#575b66}.gscp_c:hover,.gscp_a .gscp_d:hover{color:#222}.gscp_a.gscp_b .gscp_c,.gscp_a.gscp_b .gscp_d{color:#fff}.gscp_e{height:100%;padding:0 4px}a.gspqs_a{padding:0 3px 0 8px}.gspqs_b{color:#666;line-height:22px}.gspr_a{padding-right:1px}.gsq_a{padding:0}.gsfe_a{border:1px solid #b9b9b9;border-top-color:#a0a0a0;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);-moz-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);}.gsfe_b{border:1px solid #4d90fe;outline:none;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-moz-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);}.gsok_a{background:url(data:image/gif;base64,R0lGODlhEwALAKECAAAAABISEv///////yH5BAEKAAIALAAAAAATAAsAAAIdDI6pZ+suQJyy0ocV3bbm33EcCArmiUYk1qxAUAAAOw==) no-repeat center;display:inline-block;height:11px;line-height:0;width:19px}.gsok_a img{border:none;visibility:hidden}.gsst_a{display:inline-block}.gsst_a{cursor:pointer;padding:0 4px}.gsst_a:hover{text-decoration:none!important}.gsst_b{font-size:16px;padding:0 2px;user-select:none;-webkit-user-select:none;white-space:nowrap}.gsst_e{opacity:0.55;}.gsst_a:hover .gsst_e,.gsst_a:focus .gsst_e{opacity:0.72;}.gsst_a:active .gsst_e{opacity:1;}.gsst_f{background:white;text-align:left}.gsst_g{background-color:white;border:1px solid #ccc;border-top-color:#d9d9d9;box-shadow:0 2px 4px rgba(0,0,0,0.2);-webkit-box-shadow:0 2px 4px rgba(0,0,0,0.2);margin:-1px -3px;padding:0 6px}.gsst_h{background-color:white;height:1px;margin-bottom:-1px;position:relative;top:-1px}.gsfi{font-size:16px}.gsfs{font-size:16px}a.gssb_j{font-size:12px;color:#03c}.gssb_a,.gssb_a td{line-height:20px}.gssb_a{padding:0 6px}.gssb_c{z-index:3000001}.gssb_i td{background:#eee}.gssb_k{z-index:3000000}.gssb_l{margin:2px 0}.gsib_a{padding:0 4px}.gsok_a{padding:0}.gsok_a img{display:block}.gsfe_b{border:1px solid #1c62b9;box-shadow:inset 0 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,0.3);outline:none;}a.gscp_a{position:relative;background:#e2e2e2;border:1px solid #bbb;border-radius:3px}.gsfe_a a.gscp_a{border-width:1px;border-style:solid;border-color:#bbb}a.gscp_a.gscp_b{border-color:#777!important;background:#999;outline:none}.gscp_c{color:#666;font-size:11px;font-weight:bold;padding-right:20px;text-shadow:0 1px 0 rgba(255, 255, 255, 0.5);-ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80ffffff,Positive=true)";zoom:1;filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80ffffff,Positive=true)}.gsfe_a a.gscp_a .gscp_c{color:#444}a.gscp_a.gscp_b .gscp_c,.gsfe_a a.gscp_a.gscp_b .gscp_c{color:#fff;text-shadow:0 1px 0 rgba(100, 100, 100, 0.5);-ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80646464,Positive=true)";zoom:1;filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80646464,Positive=true)}.gscp_d{position:absolute;padding:0;background:url(/yts/img/icons/close-vflrEJzIW.png);background-repeat:no-repeat;background-position-y:0;right:3px;top:6px;font-size:0;width:13px;height:13px}.gscp_d:hover{background-position-y:-13px}a.gscp_a.gscp_b .gscp_d{background-position-y:-26px}.gsfe_a a.gscp_a.gscp_b .gscp_d:hover{background-position-y:-39px}.gscp_f{background:#000}</style><link rel="stylesheet" type="text/css" href="/yts/cssbin/www-player-vflAP1Pz1.css" id="www-player-css"><style></style></head>
<!-- machid: sNW5tN3Z2SWdXaDRRQm13c3N2SEdTemwxdVFScnFmZDRxbEJXWXM0S3ZURTl2aGJYUVpyR05B -->



  <body id="" class="date-20121003 en_US ltr   ytg-old-clearfix guide-feed-v2 " dir="ltr">




 

  <form name="logoutForm" method="POST" action="/web/20121003234217/http://www.youtube.com/logout">
    <input type="hidden" name="action_logout" value="1">
  <input name="session_token" type="hidden" value="4nZWQI3vFnRmWHRNmaxEx0FN9bR8MTM0OTM5NDEzN0AxMzQ5MzA3NzM3"></form>



  <!-- begin page -->
    <div id="page" class="  branded-page channel ">
        
  
  <div id="masthead-container">
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?> 
  <div id="content-container">
    <!-- begin content -->
    <div id="content">
        
    <div class="subscription-menu-expandable subscription-menu-expandable-channels3 yt-rounded ytg-wide hid">
    <div class="content" id="recommended-channels-list"></div>
    <button class="close" type="button">close</button>
  </div>

      <div class="hid">
    <div class="yt-alert yt-alert-default yt-alert-success  " id="success-template">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-buttons">  <button type="button" class="close yt-uix-close yt-uix-button yt-uix-button-close" onclick=";return false;" data-close-parent-class="yt-alert" role="button"><span class="yt-uix-button-content">Close </span></button>
</div><div class="yt-alert-content" role="alert"></div></div>
    <div class="yt-alert yt-alert-default yt-alert-error  " id="error-template">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-buttons">  <button type="button" class="close yt-uix-close yt-uix-button yt-uix-button-close" onclick=";return false;" data-close-parent-class="yt-alert" role="button"><span class="yt-uix-button-content">Close </span></button>
</div><div class="yt-alert-content" role="alert"></div></div>
    <div class="yt-alert yt-alert-default yt-alert-warn  " id="warn-template">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-buttons">  <button type="button" class="close yt-uix-close yt-uix-button yt-uix-button-close" onclick=";return false;" data-close-parent-class="yt-alert" role="button"><span class="yt-uix-button-content">Close </span></button>
</div><div class="yt-alert-content" role="alert"></div></div>
    <div class="yt-alert yt-alert-default yt-alert-info  " id="info-template">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-buttons">  <button type="button" class="close yt-uix-close yt-uix-button yt-uix-button-close" onclick=";return false;" data-close-parent-class="yt-alert" role="button"><span class="yt-uix-button-content">Close </span></button>
</div><div class="yt-alert-content" role="alert"></div></div>
    <div class="yt-alert yt-alert-default yt-alert-status  " id="status-template">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-buttons">  <button type="button" class="close yt-uix-close yt-uix-button yt-uix-button-close" onclick=";return false;" data-close-parent-class="yt-alert" role="button"><span class="yt-uix-button-content">Close </span></button>
</div><div class="yt-alert-content" role="alert"></div></div>
  </div>

  <div class="hid">
    <div id="message-container-template" class="message-container"></div>
  </div>




  <div id="branded-page-default-bg" class="ytg-base">
    <div id="branded-page-body-container" class="ytg-base clearfix enable-fancy-subscribe-button">
          <map name="branded_page_banner_image_map"></map>
  <img usemap="#branded_page_banner_image_map" height="150" class="ytg-wide branded-banner-image" src="/yts/img/pixel-vfl3z5WfW.gif">


      <div id="branded-page-header-container" class="ytg-wide banner-displayed-mode">
          <div id="branded-page-header" class="ytg-wide">
    <div id="channel-header-main">
      <div class="upper-section clearfix">
        <a href="/channel/<?php echo htmlspecialchars($channelId) ?>">
          <span class="channel-thumb">
            <span class="video-thumb ux-thumb yt-thumb-square-60 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="<?php echo htmlspecialchars($channelAvatar) ?>" alt="<?php echo htmlspecialchars($channelTitle) ?>" width="60"><span class="vertical-align"></span></span></span></span>
          </span>
        </a>
          <div class="upper-left-section ">
    <h1><?php echo htmlspecialchars($channelTitle) ?></h1>
  </div>

        <div class="upper-left-section">
              <div class="yt-subscription-button-hovercard yt-uix-hovercard"><span class="yt-uix-button-context-light yt-uix-button-subscription-container"><button href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dsubscribe%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fuser%252FCrackle%253Ffeature%253Dg-logo-xit%2526continue_action%253DrRS81vEn__uTit6lTGTO5hMcUMSSlEQvIOhmaHdS0Q0GOOevM-Huo3Gehj_tzSDau9TZfgOU8LYCTXHrJefm9G8np-UCe8F75MPt6DqqRe0=&amp;uilel=3&amp;hl=en_US&amp;service=youtube" onclick=";window.location.href=this.getAttribute('href');return false;" title="" type="button" class="yt-subscription-button subscription-button-with-recommended-channels yt-uix-button yt-uix-button-subscription yt-uix-tooltip" data-enable-hovercard="true" data-subscription-value="<?php echo htmlspecialchars($channelId) ?>" data-force-position="" data-position="" data-subscription-feature="channels3" data-subscription-type="" data-sessionlink="ei=COvBoIOD5rICFZAUIQode0bfvA%3D%3D&amp;feature=channels3" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-subscribe" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span><span class="yt-uix-button-content">  <span class="subscribe-label">Subscribe</span>
  <span class="subscribed-label">Subscribed</span>
  <span class="unsubscribe-label">Unsubscribe</span>
 </span></button><span class="yt-subscription-button-disabled-mask"></span></span></div>
        </div>
        <div class="upper-right-section">
            <div class="header-stats">

    
    <?php if ($channelSubscribers !== ''): ?>
    <div class="stat-entry">
        <span class="stat-value"><?php echo htmlspecialchars($channelSubscribers) ?></span>
  <span class="stat-name">subscribers</span>

    </div>
    <?php endif; ?>


    <?php if ($channelVideoCountCh !== ''): ?>
    <div class="stat-entry">
        <span class="stat-value"><?php echo htmlspecialchars($channelVideoCountCh) ?></span>
  <span class="stat-name"><?php echo ($channelVideoCountCh === '1') ? 'video' : 'videos' ?></span>

    </div>
    <?php endif; ?>

  </div>

          <span class="valign-shim"></span>
        </div>
      </div>
        <div class="channel-horizontal-menu clearfix">
            <ul>
          <li class="selected">
    <a href="/channel/<?php echo htmlspecialchars($channelId) ?>/featured" class="gh-tab-100">
      Featured

    </a>
  </li>

          <li>
    <a href="/channel/<?php echo htmlspecialchars($channelId) ?>/videos?view=0" class="gh-tab-101">
      Browse videos

    </a>
  </li>

  </ul>

              <form id="channel-search" class="
    " action="/channel/<?php echo htmlspecialchars($channelId) ?>/videos">
    <input name="query" type="text" autocomplete="off" class="search-field label-input-label" maxlength="100" placeholder="Search Channel" value="">
    <button class="search-btn" type="submit">
      <span class="search-btn-content">
Search
      </span>
    </button>
    <a class="search-dismiss-btn" href="/channel/<?php echo htmlspecialchars($channelId) ?>/videos?view=0">
      <span class="search-btn-content">
Clear
      </span>
    </a>
  </form>

        </div>
    </div>
  </div>

      </div>

      <div id="branded-page-body">
          <div class="channel-tab-content channel-layout-two-column selected   blogger-template ">
    <div class="tab-content-body">
      <div class="featured-top-pane">
        
      </div>
      <div class="primary-pane">
        <?php if ($channelExists && !empty($channelVideos)): ?>
        <div class="channels-browse-content-grid channel-module yt-uix-c3-module-container">
          <div class="module-view">
            <div class="c4-grid-branded-page">
              <ul class="channels-content-items clearfix">
                <?php foreach ($channelVideos as $cv): ?>
                <li class="channels-content-item">
                  <a href="/watch?v=<?php echo htmlspecialchars($cv['id']) ?>" class="yt-uix-sessionlink yt-uix-contextlink">
                    <span class="ux-thumb-wrap contains-addto "><span class="video-thumb ux-thumb yt-thumb-default-194 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="<?php echo htmlspecialchars(grid_thumb($cv)) ?>" alt="Thumbnail" width="194"><span class="vertical-align"></span></span></span></span><?php if (!empty($cv['duration'])): ?><span class="video-time"><?php echo htmlspecialchars($cv['duration']) ?></span><?php endif; ?><button onclick=";return false;" title="Watch Later" type="button" class="addto-button video-actions addto-watch-later-button-sign-in yt-uix-button yt-uix-button-default yt-uix-button-short yt-uix-tooltip" data-button-menu-id="shared-addto-watch-later-login" data-video-ids="<?php echo htmlspecialchars($cv['id']) ?>" role="button" data-tooltip-text="Watch Later"><span class="yt-uix-button-content">  <span class="addto-label">
Watch Later
  </span>
  <span class="addto-label-error">
Error
  </span>
  <img src="/yts/img/pixel-vfl3z5WfW.gif">
 </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></button></span>
                  </a>
                  <a href="/watch?v=<?php echo htmlspecialchars($cv['id']) ?>" class="content-item-title" title="<?php echo htmlspecialchars($cv['title']) ?>"><?php echo htmlspecialchars($cv['title']) ?></a>
                  <?php $detail = array_filter([
                          $cv['ago'] ?? '',
                          ($cv['views'] ?? '') !== '' ? $cv['views'] . ' views' : '',
                        ]); ?>
                  <?php if ($detail): ?>
                  <span class="content-item-detail"><?php echo implode(
                        '<span class="metadata-separator">|</span>',
                        array_map('htmlspecialchars', $detail)
                      ) ?></span>
                  <?php endif; ?>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
        <?php else: ?>
        <div class="channel-module yt-uix-c3-module-container"><div class="module-view"><p class="account-empty" style="padding:20px">This channel has no public videos.</p></div></div>
        <?php endif; ?>
      </div>
      <div class="secondary-pane">
          <div id="watch-longform-ad" style="display:none;">
    <div id="watch-longform-text">
Advertisement
    </div>
    <div id="watch-longform-ad-placeholder"><img src="/yts/img/pixel-vfl3z5WfW.gif" height="60" width="300"></div>
    <div id="instream_google_companion_ad_div"></div>
  </div>
  <div id="watch-channel-brand-div" class="companion-ads has-visible-edge channel-module yt-uix-c3-module-container hid">
    <div id="ad300x250"></div>
    <div id="google_companion_ad_div"></div>
    <div class="ad-label-text">
Advertisement
    </div>
  </div>

          
  
          <div class="user-profile channel-module yt-uix-c3-module-container ">
    <div class="module-view profile-view-module" data-owner-external-id="<?php echo htmlspecialchars($channelId) ?>">
        <h2>
About <?php echo htmlspecialchars($channelTitle) ?>

        </h2>
      <div class="section first">
        <?php if (trim($channelDescription) !== ''): ?>
        <div class="user-profile-item profile-description">
<?php echo ch_description_html($channelDescription) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($channelLinks)): ?>
          <div class="user-profile-item">
            <?php foreach ($channelLinks as $lnk): ?>
      <div class="yt-c3-profile-custom-url field-container ">
    <a href="<?php echo htmlspecialchars($lnk['url']) ?>" rel="me nofollow" target="_blank" title="<?php echo htmlspecialchars($lnk['title']) ?>" class="yt-uix-redirect-link">
        <img src="//s2.googleusercontent.com/s2/favicons?domain=<?php echo urlencode($lnk['domain']) ?>&amp;feature=youtube_channel" class="favicon" alt="">
      <span class="link-text">
        <?php echo htmlspecialchars($lnk['text']) ?>

      </span>
    </a>
  </div>
            <?php endforeach; ?>
          </div>
          <hr class="yt-horizontal-rule ">
        <?php endif; ?>
      </div>
      <div class="section created-by-section">
        <div class="user-profile-item">
by <span class="yt-user-name " dir="ltr"><?php echo htmlspecialchars($channelTitle) ?></span>
        </div>

        <?php if ($channelLatest !== ''): ?>
            <div class="user-profile-item ">
        <h5>Latest Activity</h5>
      <span class="value"><?php echo htmlspecialchars($channelLatest) ?></span>
    </div>
        <?php endif; ?>

        <?php if ($channelJoined !== ''): ?>
            <div class="user-profile-item ">
        <h5>Date Joined</h5>
      <span class="value"><?php echo htmlspecialchars($channelJoined) ?></span>
    </div>
        <?php endif; ?>

        <?php if ($channelTotalViews !== ''): ?>
            <div class="user-profile-item ">
        <h5>Total Upload Views</h5>
      <span class="value"><?php echo htmlspecialchars($channelTotalViews) ?></span>
    </div>
        <?php endif; ?>

      </div>

  <?php if ($channelCountry !== ''): ?>
  <div class="section">
        <div class="user-profile-item ">
        <h5>Country</h5>
      <span class="value"><?php echo htmlspecialchars($channelCountry) ?></span>
    </div>

      <hr class="yt-horizontal-rule ">

  </div>
  <?php endif; ?>

    </div>
  </div>


  
  
        <?php if (!empty($channelPlaylists)): ?>
        <div class="playlists-narrow channel-module yt-uix-c3-module-container">
    <div class="module-view gh-featured">
      <h2>Featured Playlists</h2>
          <?php foreach ($channelPlaylists as $pl): ?>
          <div class="playlist yt-tile-visible yt-uix-tile">
    <a href="/playlist?list=<?php echo htmlspecialchars($pl['id']) ?>" class="play-all yt-uix-sessionlink yt-uix-contextlink">
      <span class="playlist-thumb-strip playlist-thumb-strip-252"><span class="videos videos-1 horizontal-cutoff"><span class="clip"><span class="centering-offset"><span class="centering"><span class="ie7-vertical-align-hack">&nbsp;</span><img src="<?php echo htmlspecialchars($pl['thumbnail']) ?>" alt="<?php echo htmlspecialchars($pl['title']) ?>" class="thumb"></span></span></span></span><span class="resting-overlay"><img src="/yts/img/pixel-vfl3z5WfW.gif" class="play-button" alt="Play all">  <?php if ($pl['count'] !== ''): ?><span class="video-count-box">
    <?php echo htmlspecialchars($pl['count']) ?>

  </span><?php endif; ?>
</span><span class="hover-overlay"><span class="play-all-container"><strong><img src="/yts/img/ui/playlist_thumb_strip/mini-play-all-vflZu1SBs.png" alt="">Play all</strong></span></span></span>
    </a>
    <h3>
      <a href="/playlist?list=<?php echo htmlspecialchars($pl['id']) ?>" title="See all videos in playlist." class="yt-uix-tile-link">
        <?php echo htmlspecialchars($pl['title']) ?>

      </a>
    </h3>
      <span class="playlist-author-attribution">
by <?php echo htmlspecialchars($channelTitle) ?>

    </span>
  </div>
          <?php endforeach; ?>

    </div>
  </div>
        <?php endif; ?>






      </div>
    </div>
  </div>

      </div>


      
    </div>
  </div>


    </div>
    <!-- end content -->
  </div>
  <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>



  <div id="playlist-bar" class="hid passive editable" data-video-url="/watch?v=&amp;feature=BFql&amp;playnext=1&amp;list=QL" data-list-id="" data-list-type="QL">
    <div id="playlist-bar-bar-container">
      <div id="playlist-bar-bar">
        <div class="yt-alert yt-alert-naked yt-alert-success hid " id="playlist-bar-notifications">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-content" role="alert"></div></div>
<span id="playlist-bar-info"><span class="playlist-bar-active playlist-bar-group"><button onclick=";return false;" title="Previous video" type="button" id="playlist-bar-prev-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-prev" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Previous video"><span class="yt-valign-trick"></span></span></button><span class="playlist-bar-count"><span class="playing-index">0</span> / <span class="item-count">0</span></span><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-next-button" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-next" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span></button></span><span class="playlist-bar-active playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-autoplay-button" data-button-toggle="true" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-autoplay" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span></button><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-shuffle-button" data-button-toggle="true" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-shuffle" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span></button></span><span class="playlist-bar-passive playlist-bar-group"><button onclick=";return false;" title="Play videos" type="button" id="playlist-bar-play-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-play" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Play videos"><span class="yt-valign-trick"></span></span></button><span class="playlist-bar-count"><span class="item-count">0</span></span></span><span id="playlist-bar-title" class="yt-uix-button-group"><span class="playlist-title">Unsaved Playlist</span></span></span>
        <a id="playlist-bar-lists-back" href="#">
Return to active list
        </a>

<span id="playlist-bar-controls"><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-text yt-uix-button-empty" onclick=";return false;" id="playlist-bar-toggle-button" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-toggle" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span></button></span><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked yt-uix-button-reverse flip yt-uix-button yt-uix-button-text" onclick=";return false;" data-button-menu-id="playlist-bar-options-menu" data-button-has-sibling-menu="true" role="button"><span class="yt-uix-button-content">Options </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></button></span></span>      </div>
    </div>

<div id="playlist-bar-tray-container"><div id="playlist-bar-tray" class="yt-uix-slider yt-uix-slider-fluid"><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-prev" onclick="return false;"><img class="yt-uix-slider-prev-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Previous video"></button><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-next" onclick="return false;"><img class="yt-uix-slider-next-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Next video"></button><div class="yt-uix-slider-body"><div id="playlist-bar-tray-content" class="yt-uix-slider-slide"><ol class="video-list"></ol><ol id="playlist-bar-help"><li class="empty playlist-bar-help-message">Your queue is empty. Add videos to your queue using this button: <img src="/yts/img/pixel-vfl3z5WfW.gif" class="addto-button-help"><br> or <a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fuser%252FCrackle%253Ffeature%253Dg-logo-xit&amp;uilel=3&amp;hl=en_US&amp;service=youtube">sign in</a> to load a different list.</li></ol></div><div class="yt-uix-slider-shade-left"></div><div class="yt-uix-slider-shade-right"></div></div></div><div id="playlist-bar-save"></div><div id="playlist-bar-lists" class="dark-lolz"></div><div id="playlist-bar-loading"><img src="/yts/img/pixel-vfl3z5WfW.gif" alt="Loading..."><span id="playlist-bar-loading-message">Loading...</span><span id="playlist-bar-saving-message" class="hid">Saving...</span></div><div id="playlist-bar-template" style="display: none;" data-video-thumb-url="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg"><!--<li class="playlist-bar-item yt-uix-slider-slide-unit __classes__" data-video-id="__video_encrypted_id__"><a href="__video_url__" title="__video_title__" class="yt-uix-sessionlink" data-sessionlink="ei=COvBoIOD5rICFZAUIQode0bfvA%3D%3D&amp;feature=BFa"><span class="video-thumb ux-thumb yt-thumb-default-106 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="/yts/img/pixel-vfl3z5WfW.gif" alt="__video_title__" data-thumb-manual="true" data-thumb="__video_thumb_url__" width="106" ><span class="vertical-align"></span></span></span></span><span class="screen"></span><span class="count"><strong>__list_position__</strong></span><span class="play"><img src="/yts/img/pixel-vfl3z5WfW.gif"></span><span class="yt-uix-button yt-uix-button-default delete"><img class="yt-uix-button-icon-playlist-bar-delete" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Delete"></span><span class="now-playing">Now playing</span><span dir="ltr" class="title"><span>__video_title__  <span class="uploader">by __video_display_name__</span>
</span></span><span class="dragger"></span></a></li>--></div><div id="playlist-bar-next-up-template" style="display: none;"><!--<div class="playlist-bar-next-thumb"><span class="video-thumb ux-thumb yt-thumb-default-74 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg" alt="Thumbnail" width="74" ><span class="vertical-align"></span></span></span></span></div>--></div></div>      <div id="playlist-bar-options-menu" class="hid">

    <div id="playlist-bar-extras-menu">
        <ul>
      <li><span class="yt-uix-button-menu-item" data-action="clear">
Clear all videos from this list
      </span></li>
  </ul>

    </div>

    <ul>
      <li><span class="yt-uix-button-menu-item" onclick="window.location.href='http://support.google.com/youtube/bin/answer.py?answer=146749&amp;hl=en-US'">Learn more</span></li>
    </ul>
  </div>

  </div>


  
    <div id="shared-addto-watch-later-login" class="hid">
      <a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fuser%252FCrackle%253Ffeature%253Dg-logo-xit&amp;uilel=3&amp;hl=en_US&amp;service=youtube" class="sign-in-link">Sign in</a> to add this to a playlist

    </div>

  <div id="shared-addto-menu" style="display: none;" class="hid sign-in">
      <div class="addto-menu">
        <div id="addto-list-panel" class="menu-panel active-panel">
        <span class="yt-uix-button-menu-item yt-uix-tooltip sign-in" data-possible-tooltip="" data-tooltip-show-delay="750"><a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fuser%252FCrackle%253Ffeature%253Dg-logo-xit&amp;uilel=3&amp;hl=en_US&amp;service=youtube" class="sign-in-link">Sign in</a> to add this to a playlist
</span>

  </div>
  <div id="addto-list-saved-panel" class="menu-panel">
    <div class="panel-content">
      <div class="yt-alert yt-alert-naked yt-alert-success  ">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
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
      <img src="/yts/img/pixel-vfl3z5WfW.gif">
      <span class="error-details"></span>
      <a class="show-menu-link">Back to list</a>
    </div>
  </div>

        <div id="addto-note-input-panel" class="menu-panel">
    <div class="panel-content">
      <div class="yt-alert yt-alert-naked yt-alert-success  ">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
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
      <img src="/yts/img/pixel-vfl3z5WfW.gif">
      <span>Saving note...</span>
    </div>
  </div>
  <div id="addto-note-saved-panel" class="menu-panel">
    <div class="panel-content">
      <img src="/yts/img/pixel-vfl3z5WfW.gif">
      <span class="message">Note added to:</span>
    </div>
  </div>
  <div id="addto-note-error-panel" class="menu-panel">
    <div class="panel-content">
      <img src="/yts/img/pixel-vfl3z5WfW.gif">
      <span class="message">Error adding note:</span>
      <ul class="error-details"></ul>
      <a class="add-note-link">Click to add a new note</a>
    </div>
  </div>
  <div class="close-note hid">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="close-button">
  </div>

  </div>

  </div>


  

    </div>
  <!-- end page -->
    
  
    <script id="www-core-js" src="/yts/jsbin/www-core-vflcQDadT.js" data-loaded="true"></script>


  <script>
        yt.setConfig({
      'XSRF_TOKEN': '4nZWQI3vFnRmWHRNmaxEx0FN9bR8MTM0OTM5NDEzN0AxMzQ5MzA3NzM3',
      'XSRF_FIELD_NAME': 'session_token'
    });
    yt.pubsub.subscribe('init', yt.www.xsrf.populateSessionToken);

    yt.setConfig('XSRF_REDIRECT_TOKEN', 'N-mMFWMp6XOLwbjlEm2EU11pjRJ8MTM0OTM5NDEzOEAxMzQ5MzA3NzM4');

    yt.setConfig({
      'EVENT_ID': "COvBoIOD5rICFZAUIQode0bfvA==",
      'CURRENT_URL': <?php echo json_encode('/channel/' . $channelId, JSON_UNESCAPED_UNICODE) ?>,
      'LOGGED_IN': false,
      'SESSION_INDEX': null,

      'WATCH_CONTEXT_CLIENTSIDE': false,

      'FEEDBACK_LOCALE_LANGUAGE': "en",
      'FEEDBACK_LOCALE_EXTRAS': {"logged_in": false, "experiments": "906359,929302,900221,922401,920704,912806,900711,913546,913556,925109,919003,912706,900816", "guide_subs": "NA", "accept_language": null}    });
  </script>


      <script>
if (window.yt.timing) {yt.timing.tick("js_head");}    </script>

      
    <script src="/yts/jsbin/www-channels3-vflyMXwg-.js" data-loaded="true"></script>



  <script>
      yt.setConfig('CHANNEL_ID', "Njakstwb629k6IE9BualRw");
    yt.setAjaxToken('channel_ajax', "");
      yt.setMsg({
    'UNBLOCK_USER': "Are you sure you want to unblock this user?",
    'BLOCK_USER': "Are you sure you want to block this user?"
  });
  yt.setConfig('BLOCK_USER_AJAX_XSRF', '');


    yt.setMsg({
      'GENERIC_EDITOR_ERROR': "An error occurred. Please try again later."
    });
    yt.pubsub.subscribe('init', yt.www.channels.c3.channel.init);

  </script>
      <script>
      <?php /* архивный обнуляющий вызов (захват 2012 — разлогинен): держим живой
               токен, иначе Subscribe с этой страницы уходит с пустым → "Bad token" */ ?>
      yt.setAjaxToken('subscription_ajax', "<?php echo yt_session_token() ?>");
    yt.pubsub.subscribe('init', yt.www.subscriptions.SubscriptionButton.init);

  </script>





        
    <script src="/yts/jsbin/www-watch-livestreaming-vfl0Jsz0T.js" data-loaded="true"></script>

      <script>
      yt.setMsg('FLASH_UPGRADE', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e  \u003cdiv class=\"yt-alert-icon\"\u003e\n    \u003cimg s\u0072c=\"\/yts\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\n  \u003c\/div\u003e\n\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\" role=\"alert\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n            You need to upgrade your Adobe Flash Player to watch this video. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eDownload it from Adobe.\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");
  yt.setConfig({
    'PLAYER_CONFIG': {"assets": {"css_actions": "\/yts\/cssbin\/www-player-actions-vflrtkTn7.css", "html": "\/html5_player_template", "css": "\/yts\/cssbin\/www-player-vflAP1Pz1.css", "js": "\/yts\/jsbin\/html5player-vfliqXF24.js"}, "url": "\/yts\/swfbin\/watch_as3-vfldzrQK1.swf", "min_version": "8.0.0", "args": {"status": "fail", "el": "profilepage", "fexp": "906359,929302,900221,922401,920704,912806,900711,913546,913556,925109,919003,912706,900816", "url_encoded_fmt_stream_map": "", "sourceid": "y", "reason": "Embedding disabled by request\u003cbr\/\u003e\u003cu\u003e\u003ca href='http:\/\/www.youtube.com\/watch?v=Wriy3ICfF9U\u0026feature=player_embedded' target='_blank'\u003eWatch on YouTube\u003c\/a\u003e\u003c\/u\u003e", "hl": "", "keywords": "comedians,in,cars,getting,coffee,watch,free,streaming,television,tv,video,crackle", "cr": "", "eurl": "http:\/\/www.youtube.com\/user\/Crackle", "iurl": "http:\/\/i4.ytimg.com\/vi\/Wriy3ICfF9U\/hqdefault.jpg", "ps": "default", "fmt_list": "", "referrer": "http:\/\/www.youtube.com\/", "video_id": "Wriy3ICfF9U", "feature": "g-logo-xit", "enablejsapi": 1, "errorcode": 150, "delay": "9", "sk": "qSDKh6hrCpk7OFoQTVuHrUj_OzKNh_2SC", "rel": 0, "sdetail": "f:g-logo-xit,p:\/", "autoplay": "1"}, "url_v9as2": "\/yts\/swfbin\/cps-vflKAYgbA.swf", "params": {"allowscriptaccess": "always", "allowfullscreen": "true", "bgcolor": "#000000"}, "attrs": {"id": "movie_player"}, "url_v8": "\/yts\/swfbin\/cps-vflKAYgbA.swf", "html5": false}
  });

  </script>

    <script>
      yt.pubsub.subscribe('init', function() {
        <?php /* не обнулять: этот init-колбэк перетирал живой токен пустым уже
                 после фикса из header.php, из-за чего like/dislike ловил "Bad token" */ ?>
        yt.setAjaxToken('watch_actions_ajax', "<?php echo yt_session_token() ?>");
      });
      yt.setMsg({
          'CHANNELS3_FEATURED_PLAYER_GENERIC_ERROR': "This feature is not available right now. Please try again later."
      })
        yt.pubsub.subscribe('init', function () {
          yt.www.livestreaming.ConcurrentViewers(30000)
        });
    </script>

    <script>
    yt.pubsub.subscribe('init', yt.www.channels.c3.channel.initBloggerLayout);
  </script>



        




  

  <?php /* Рекламный блок 2012 удалён: 1x1-iframe в ad-g.doubleclick.net и
           блокирующий gpt.js с googletagservices тянулись при каждой загрузке,
           упирались в таймаут (эндпоинты мертвы) и ничего не отображали. */ ?>


      <script>
if (window.yt.timing) {yt.timing.tick("js_page");}    </script>

        <script>
yt.setConfig('TIMING_ACTION', "channels3");    </script>





  <script>yt.www.thumbnaildelayload.init(0);</script>

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
    'DRAGDROP_BINARY_URL': "\/yts\/jsbin\/www-dragdrop-vflWKaUyg.js",
    'PLAYLIST_BAR_PLAYING_INDEX': -1  });

    yt.setAjaxToken('addto_ajax_logged_out', "Ro3FBi--DpmpeXM9C0Ap2YlSkDR8MTM0OTM5NDEzOEAxMzQ5MzA3NzM4");

    yt.pubsub.subscribe('init', yt.www.lists.init);









    yt.setConfig({'SBOX_JS_URL': "\/yts\/jsbin\/www-searchbox-vflUkQubC.js",'SBOX_SETTINGS': {"CLOSE_ICON_URL": "\/yts\/img\/icons\/close-vflrEJzIW.png", "SHOW_CHIP": false, "PSUGGEST_TOKEN": null, "REQUEST_DOMAIN": "us", "EXPERIMENT_ID": -1, "SESSION_INDEX": null, "HAS_ON_SCREEN_KEYBOARD": false, "CHIP_PARAMETERS": {}, "REQUEST_LANGUAGE": "en"},'SBOX_LABELS': {"SUGGESTION_DISMISS_LABEL": "Dismiss", "SUGGESTION_DISMISSED_LABEL": "Suggestion dismissed"}});





  </script>

  <script>
    yt.setMsg({
      'ADDTO_WATCH_LATER_ADDED': "Added",
      'ADDTO_WATCH_LATER_ERROR': "Error"
    });
  </script>

  

      <script>
if (window.yt.timing) {yt.timing.tick("js_foot");}    </script>


  




<div id="yt-uix-hovercard-card1" class="yt-uix-hovercard-card hid" style="display: none;"><div class="yt-uix-card-border-arrow yt-uix-card-border-arrow-horizontal"></div><div class="yt-uix-hovercard-card-border"><div class="yt-uix-card-body-arrow yt-uix-card-body-arrow-horizontal"></div><div class="yt-uix-hovercard-card-body"><div class="hid yt-uix-hovercard-card-content">  <p class="loading-spinner">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
Loading...
  </p>
</div></div></div></div><iframe class="gstl_0 gssb_k" style="display: none; top: 45px; left: 0px; height: 0px;" allow="autoplay 'self'; fullscreen 'self'"></iframe><table cellspacing="0" cellpadding="0" class="gstl_0 gssb_c" style="width: 462px; display: none; top: 45px; position: absolute; left: 164px;"><tbody><tr><td class="gssb_f"></td><td class="gssb_e" style="width: 100%;"></td></tr></tbody></table><ul class="html5-context-menu yt-uix-button-menu hid" style="left: 938px; top: 452px; display: none;">
    <li>
      <span class="yt-uix-button-menu-item html5-context-menu-copy-video-url">Copy video URL</span>
    </li>
    <li>
      <span class="yt-uix-button-menu-item html5-context-menu-copy-video-url-at-current-time">Copy video URL at current time</span>
    </li>
    <li>
      <span class="yt-uix-button-menu-item html5-context-menu-copy-embed-html">Copy embed HTML</span>
    </li>
    <li>
      <span class="yt-uix-button-menu-item html5-context-menu-report-playback-issue">Report playback issue</span>
    </li>
    <li>
      <span class="yt-uix-button-menu-item html5-context-menu-copy-debug-info">Copy debug info</span>
    </li>
    <li>
      <span class="yt-uix-button-menu-item html5-context-menu-stop-download">Stop download</span>
    </li>
    <li>
      <span class="yt-uix-button-menu-item html5-context-menu-pop-out">Pop out</span>
    </li>
    <li>
      <a class="yt-uix-button-menu-item" target="_blank" href="/web/20121003234156/http://www.youtube.com/my_speed">Take speed test</a>
    </li>
    <li>
      <span class="yt-uix-button-menu-item html5-context-menu-show-video-info">Show video info</span>
    </li>
    <li>
      <a class="yt-uix-button-menu-item" target="_blank" href="/web/20121003234156/http://www.youtube.com/html5">About HTML5</a>
    </li>
  </ul></body></html>