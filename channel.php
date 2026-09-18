<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/channel_api.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php');
$currentUser = function_exists('yt_account_info') ? yt_account_info() : null;
$myChannelId = '';

if (is_array($currentUser)) {
    $myChannelId = $currentUser['channel_id'] ?? $currentUser['channelId'] ?? $currentUser['id'] ?? '';
} elseif (is_string($currentUser)) {
    $myChannelId = $currentUser;
}

if ($myChannelId === '' && !empty($_SESSION['channel_id'])) {
    $myChannelId = $_SESSION['channel_id'];
}

if ($myChannelId === '' && function_exists('yt_cookie_account')) {
    $cookieAcc = yt_cookie_account();
    $myChannelId = $cookieAcc['channelId'] ?? $cookieAcc['channel_id'] ?? '';
}

if (!$channelExists) {
    header('Location: /oops.php', true, 302);
    exit;
}

$isOwner = (!empty($myChannelId) && !empty($channelId) && $myChannelId === $channelId);

$Subscribed = '';
if (!$isOwner && $channelId !== '') {
    $isSub = false;
    if (function_exists('yt_should_auth') && yt_should_auth() && function_exists('yt_is_subscribed')) {
        $isSub = (bool) yt_is_subscribed($myChannelId, $channelId);
    } elseif (!empty($channelIsSubscribed)) {
        $isSub = true;
    }
    if ($isSub) {
        $Subscribed = 'subscribed';
    }
}

if (isset($_GET['debug_sub'])) {
    header('Content-Type: text/plain; charset=utf-8');
    $authOk = function_exists('yt_should_auth') && yt_should_auth();
    $sapisid = isset($_COOKIE['SAPISID']) ? 'yes(' . strlen($_COOKIE['SAPISID']) . ')' : 'NO';
    $cacheKey = sys_get_temp_dir() . '/yt_sub_v4_' . sha1((string)($_COOKIE['SAPISID'] ?? '') . '|' . $channelId);
    $cacheVal = is_file($cacheKey) ? file_get_contents($cacheKey) : '(no cache)';
    $live = (function_exists('yt_is_subscribed') && $authOk) ? yt_is_subscribed($myChannelId, $channelId) : null;
    $log = sys_get_temp_dir() . '/yt_sub_debug.log';
    echo "channelId=$channelId\n";
    echo "myChannelId=$myChannelId\n";
    echo "isOwner=" . ($isOwner ? '1' : '0') . "\n";
    echo "yt_should_auth=" . ($authOk ? '1' : '0') . "\n";
    echo "SAPISID=$sapisid\n";
    echo "Subscribed='$Subscribed'\n";
    echo "cacheVal=$cacheVal\n";
    echo "live=" . var_export($live, true) . "\n";
    echo "tempDir=" . sys_get_temp_dir() . "\n";
    echo "---- log ----\n";
    echo is_file($log) ? file_get_contents($log) : '(no log)';
    exit;
}

function ch_plural(string $count, string $one, string $many): string {
    $count = trim($count);
    if ($count === '') return '';
    return $count . ' ' . ($count === '1' ? $one : $many);
}

function grid_thumb(array $cv): string {
    $id = $cv['id'] ?? '';
    if ($id !== '') return 'https://i.ytimg.com/vi/' . rawurlencode($id) . '/mqdefault.jpg';
    return $cv['thumbnail'] ?? (defined('DEFAULT_VIDEO_THUMB') ? DEFAULT_VIDEO_THUMB : '');
}

/** Только число: «41» / «4,177» — слово views в HTML как в archive */
function ch_views_label($raw): string {
    $raw = trim((string)$raw);
    if ($raw === '' || $raw === '0') return '';
    $raw = preg_replace('/\s*views?\s*$/iu', '', $raw);
    if (preg_match('/^\d+$/', $raw)) {
        return number_format((int)$raw, 0, '.', ',');
    }
    if (preg_match('/^([\d.,]+)\s*([KMB])$/iu', $raw, $m)) {
        return $m[1] . strtoupper($m[2]);
    }
    return $raw;
}

$channelLatest = '';
if (!empty($channelVideos[0]['ago'])) $channelLatest = $channelVideos[0]['ago'];

function ch_description_html(string $text): string {
    $out = '';
    foreach (preg_split('/\R/u', trim($text)) as $line) {
        $out .= '<p>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
    }
    return $out;
}

$feat = $channelFeatured ?? ($channelVideos[0] ?? null);
$blogVideos = $channelVideos;
$uploadsList = (is_string($channelId) && str_starts_with($channelId, 'UC'))
    ? ('UU' . substr($channelId, 2))
    : '';
$blogTotal = 0;
if (!empty($channelVideoCountCh)) {
    $n = preg_replace('/[^\d]/', '', (string)$channelVideoCountCh);
    if ($n !== '') $blogTotal = (int)$n;
}
$blogShown = count($blogVideos);
if ($blogTotal < $blogShown) $blogTotal = $blogShown;
$blogRange = $blogShown > 0 ? ('1-' . $blogShown . ' of ' . $blogTotal) : '';

/* data-swf-config как в archive EthosLab (watch_as3 + attrs 640x390) */
$featSwfConfig = '';
if (!empty($feat['id'])) {
    $fid = $feat['id'];
    $ftitle = $feat['title'] ?? '';
    $fviews = (int)preg_replace('/[^\d]/', '', (string)($feat['views'] ?? '0'));
    $swf = [
        'assets' => [
            'html' => '/html5_player_template',
            'css'  => 'https://s.ytimg.com/yt/cssbin/www-player-vfllhw7HB.css',
            'js'   => 'https://s.ytimg.com/yt/jsbin/html5player-vflzTrRqK.js',
        ],
        'url' => 'https://s.ytimg.com/yt/swfbin/watch_as3-vfl_U1gcG.swf',
        'min_version' => '8.0.0',
        'args' => [
            'account_playback_token' => '',
            'ptk' => rawurlencode(($feat['author'] ?? $channelTitle) . ' user'),
            'video_id' => $fid,
            'title' => $ftitle,
            'view_count' => $fviews,
            'iurl' => 'https://i.ytimg.com/vi/' . $fid . '/hqdefault.jpg',
            'iurlsd' => 'https://i.ytimg.com/vi/' . $fid . '/sddefault.jpg',
            'thumbnail_url' => 'https://i.ytimg.com/vi/' . $fid . '/default.jpg',
            'length_seconds' => 0,
            'avg_rating' => 5,
            'allow_ratings' => 1,
            'allow_embed' => 1,
            'is_html5_mobile_device' => false,
            'autoplay' => '1',
            'autohide' => '2',
            'enablejsapi' => '1',
            'jsapicallback' => 'onYouTubePlayerReady',
            'keywords' => '',
            'plid' => '',
            'eurl' => 'https://www.youtube.com/channel/' . $channelId,
            'status' => 'ok',
            'pltype' => 'content',
            'is_video_preview' => false,
            'watermark' => ',https://s.ytimg.com/yt/img/watermark/youtube_watermark-vflHX6b6E.png,https://s.ytimg.com/yt/img/watermark/youtube_hd_watermark-vflAzLcD6.png',
            'fmt_list' => '18/640x360/9/0/115,5/320x240/7/0/0',
            'url_encoded_fmt_stream_map' => '',
        ],
        'url_v9as2' => 'https://s.ytimg.com/yt/swfbin/cps-vflHeTjgT.swf',
        'url_v8'    => 'https://s.ytimg.com/yt/swfbin/cps-vflHeTjgT.swf',
        'params' => [
            'allowscriptaccess' => 'always',
            'allowfullscreen' => 'true',
            'bgcolor' => '#000000',
        ],
        'attrs' => [
            'width'  => '640',
            'id'     => 'movie_player',
            'height' => '390',
        ],
        'html5' => false,
    ];
    $featSwfConfig = htmlspecialchars(json_encode($swf, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
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

<link rel="stylesheet" href="/yts/cssbin/www-player-actions-vflrtkTn7.css">
<link rel="stylesheet" type="text/css" href="/yts/cssbin/www-player-vflAP1Pz1.css" id="www-player-css">
</head>

  <body id="" class="date-20121003 en_US ltr   ytg-old-clearfix guide-feed-v2 " dir="ltr">

  <form name="logoutForm" method="POST" action="/logout">
    <input type="hidden" name="action_logout" value="1">
  </form>

  <div id="page" class="  branded-page channel ">
        
  <div id="masthead-container">
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?> 
  <div id="content-container">
    <div id="content">
        
    <div class="subscription-menu-expandable subscription-menu-expandable-channels3 yt-rounded ytg-wide hid">
    <div class="content" id="recommended-channels-list"></div>
    <button class="close" type="button">close</button>
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
          
          <?php if (!$isOwner): ?>
              <div class="upper-left-section enable-fancy-subscribe-button">
                  <div class="yt-subscription-button-hovercard yt-uix-hovercard">
                      <button href="#" onclick=";subscribe();return false;" title="" id="subscribe-button" type="button" class="yt-subscription-button yt-uix-button yt-uix-button-subscription yt-uix-tooltip <?php echo htmlspecialchars($Subscribed); ?>" role="button">
                          <span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-subscribe" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span>
                          <span class="yt-uix-button-content">
                              <span class="subscribe-label">Subscribe</span>
                              <span class="subscribed-label">Subscribed</span>
                              <span class="unsubscribe-label">Unsubscribe</span>
                          </span>
                      </button>
                      <div class="yt-uix-hovercard-content hid">
                          <p class="loading-spinner">
                              <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
                              Loading...
                          </p>
                      </div>
                  </div>
              </div>
          <?php endif; ?>

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

    <?php if (!empty($isOwner) && $isOwner): ?>
                  <a href="/channel_editor">
                      <button class="yt-uix-button yt-uix-button-default">Edit Channel</button>
                  </a>
                  <span class="valign-shim"></span>
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

              <form id="channel-search" class="" action="/channel/<?php echo htmlspecialchars($channelId) ?>/videos">
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
          <div class="channel-tab-content channel-layout-two-column selected blogger-template ">
    <div class="tab-content-body">
      <div class="featured-top-pane">
        
      </div>
      <div class="primary-pane">
<?php if (!empty($feat)): ?>
              <div class="channels-featured-video channel-module yt-uix-c3-module-container has-visible-edge">
      <div class="module-view featured-video-view-module">
            <div class="channels-video-player " data-swf-config="<?php echo $featSwfConfig; ?>" data-video-id="<?php echo htmlspecialchars($feat['id']) ?>">
  </div>
    <div class="channels-featured-video-details yt-tile-visible clearfix">
      <h3 class="title">
        <a href="/watch?v=<?php echo htmlspecialchars($feat['id']) ?><?php echo $uploadsList !== '' ? '&amp;list=' . htmlspecialchars($uploadsList) . '&amp;index=1&amp;feature=plcp' : ''; ?>">
          <?php echo htmlspecialchars($feat['title'] ?? '') ?>
        </a>
        <div class="view-count-and-actions">
              <div class="view-count">
                <span class="count">
                  <?php echo htmlspecialchars(ch_views_label($feat['views'] ?? '')) ?>
                </span>
views
              </div>
        </div>
      </h3>
      <p class="channels-featured-video-metadata">
        <span>by <?php echo htmlspecialchars($feat['author'] ?? $channelTitle) ?></span>
          <?php if (!empty($feat['ago'])): ?>
          <span class="created-date"><?php echo htmlspecialchars($feat['ago']) ?></span>
          <?php endif; ?>
      </p>
    </div>

      </div>
    </div>
<?php endif; ?>

<?php if (!empty($blogVideos)): ?>
      <div class="single-playlist channel-module yt-uix-c3-module-container">
      <div class="module-view single-playlist-view-module">
            <div class="blogger-playall">
      <a class="yt-playall-link yt-playall-link-default " href="/watch?v=<?php echo htmlspecialchars($feat['id'] ?? $blogVideos[0]['id'] ?? '') ?><?php echo $uploadsList !== '' ? '&amp;list=' . htmlspecialchars($uploadsList) . '&amp;feature=plcp' : ''; ?>">
    <img class="small-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
Play all
  </a>

  </div>

        <div class="playlist-info">
          <h2>Uploaded videos</h2>
            <?php if ($blogRange !== ''): ?>
            <span class="blogger-video-count"><?php echo htmlspecialchars($blogRange) ?></span>
            <?php endif; ?>
            <div class="yt-horizontal-rule "><span class="first"></span><span class="second"></span><span class="third"></span></div>

        </div>
          <ul class="gh-single-playlist">
<?php foreach ($blogVideos as $i => $cv): ?>
          <li class="blogger-video">
          <div class="video yt-tile-visible">
    <a href="/watch?v=<?php echo htmlspecialchars($cv['id']) ?><?php
      echo $uploadsList !== '' ? '&amp;list=' . htmlspecialchars($uploadsList) . '&amp;index=' . ($i + 1) . '&amp;feature=plcp' : '';
    ?>">
        <span class="ux-thumb-wrap contains-addto "><span class="video-thumb ux-thumb yt-thumb-default-288 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="<?php echo htmlspecialchars(grid_thumb($cv)) ?>" alt="Thumbnail" width="288"><span class="vertical-align"></span></span></span></span><?php if (!empty($cv['duration'])): ?><span class="video-time"><?php echo htmlspecialchars($cv['duration']) ?></span><?php endif; ?>


  <button onclick=";return false;" title="Watch Later" type="button" class="addto-button video-actions addto-watch-later-button-sign-in yt-uix-button yt-uix-button-default yt-uix-button-short yt-uix-tooltip" data-button-menu-id="shared-addto-watch-later-login" data-video-ids="<?php echo htmlspecialchars($cv['id']) ?>" role="button"><span class="yt-uix-button-content">  <span class="addto-label">
Watch Later
  </span>
  <span class="addto-label-error">
Error
  </span>
  <img src="/yts/img/pixel-vfl3z5WfW.gif">
 </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></button>
</span>
      <span class="video-item-content">
          <span class="video-overview">
    <span class="title video-title" title="<?php echo htmlspecialchars($cv['title']) ?>"><?php echo htmlspecialchars($cv['title']) ?></span>
  </span>
  <span class="video-details">
    <span class="yt-user-name video-owner" dir="ltr"><?php echo htmlspecialchars($cv['author'] ?? $channelTitle) ?></span>
      <?php $vl = ch_views_label($cv['views'] ?? ''); if ($vl !== ''): ?>
      <span class="video-view-count">
<?php echo htmlspecialchars($vl) ?> views
      </span>
      <?php endif; ?>
      <?php if (!empty($cv['ago'])): ?>
      <span class="video-time-published"><?php echo htmlspecialchars($cv['ago']) ?></span>
      <?php endif; ?>
      <?php if (!empty($cv['description'])): ?>
      <span class="video-item-description"><?php
        echo htmlspecialchars(mb_substr($cv['description'], 0, 160));
        echo mb_strlen($cv['description']) > 160 ? '...' : '';
      ?></span>
      <?php endif; ?>
  </span>

      </span>
    </a>
  </div>

      </li>
<?php endforeach; ?>

      <li class="video">
    <button name="page" onclick=";return false;" type="button" class="more-videos yt-uix-button yt-uix-button-default" value="2" data-list_id="<?php echo htmlspecialchars($uploadsList) ?>" role="button"><span class="yt-uix-button-content">  <span class="load-more-text">
    Load 10 more videos
  </span>
  <span class="loading-indicator">
    <img src="/yts/img/loader-vflff1Mjj.gif" alt="Loading">
  </span>
 </span></button>
  </li>


  </ul>

      </div>
    </div>
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
  </div>
  <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>

    <script id="www-core-js" src="/yts/jsbin/www-core-vflcQDadT.js" data-loaded="true"></script>
    <script src="/yts/jsbin/www-channels3-vflyMXwg-.js" data-loaded="true"></script>
    <script src="/yts/jsbin/www-watch-livestreaming-vfl0Jsz0T.js" data-loaded="true"></script>
</body>
</html>