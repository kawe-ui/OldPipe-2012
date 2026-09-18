<?php
$video_id = $_GET["v"];
global $video_id;
require_once ($_SERVER['DOCUMENT_ROOT'] . '/api/api.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php');
if (!empty($videoUnavailable)) {
    require ($_SERVER['DOCUMENT_ROOT'] . '/watch_404.php');
    exit;
}
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
      <script>
var yt = yt || {};yt.timing = yt.timing || {};yt.timing.data_ = yt.timing.data_ || {};yt.timing.tick = function(label, opt_time) {var timer = yt.timing.data_['timer'] || {};if(opt_time) {timer[label] = opt_time;}else {timer[label] = new Date().getTime();}yt.timing.data_['timer'] = timer;};yt.timing.info = function(label, value) {var info_args = yt.timing.data_['info_args'] || {};info_args[label] = value;yt.timing.data_['info_args'] = info_args;};yt.timing.info('e', "922401,920704,912806,925703,925706,928001,922403,913605,913546,913556,908493,920201,911116,901451");yt.timing.data_['wff'] = true;yt.timing.info('an', "");if (document.webkitVisibilityState == 'prerender') {document.addEventListener('webkitvisibilitychange', function() {yt.timing.tick('start');}, false);}yt.timing.tick('start');yt.timing.info('li','0');try {yt.timing.data_['srt'] = window.gtbExternal && window.gtbExternal.pageT() ||window.external && window.external.pageT;} catch(e) {}if (window.chrome && window.chrome.csi) {yt.timing.data_['srt'] = Math.floor(window.chrome.csi().pageT);}if (window.msPerformance && window.msPerformance.timing) {yt.timing.data_['srt'] = window.msPerformance.timing.responseStart - window.msPerformance.timing.navigationStart;}    </script>

<script>var yt = yt || {};yt.preload = {};yt.preload.counter_ = 0;yt.preload.start = function(src) {var img = new Image();var counter = ++yt.preload.counter_;yt.preload[counter] = img;img.onload = img.onerror = function () {delete yt.preload[counter];};img.src = src;img = null;};yt.preload.start("http:\/\/o-o---preferred---sn-nwj7kner---v3---lscache8.c.youtube.com\/crossdomain.xml");yt.preload.start("http:\/\/o-o---preferred---sn-nwj7kner---v3---lscache8.c.youtube.com\/generate_204?ip=207.241.226.214\u0026upn=F7zEJW0mmzY\u0026sparams=algorithm%2Cburst%2Ccp%2Cfactor%2Cid%2Cip%2Cipbits%2Citag%2Csource%2Cupn%2Cexpire\u0026fexp=922401%2C920704%2C912806%2C925703%2C925706%2C928001%2C922403%2C913605%2C913546%2C913556%2C908493%2C920201%2C911116%2C901451\u0026mt=1354760830\u0026ms=au\u0026algorithm=throttle-factor\u0026burst=40\u0026ipbits=8\u0026itag=34\u0026sver=3\u0026signature=CD621DF7D570DD5013E150FEFA5A1F1A95A45572.A0ADA29EBC2D7026B5B2A38C2F762B0ADCFD0C9F\u0026mv=m\u0026source=youtube\u0026expire=1354782601\u0026key=yt1\u0026factor=1.25\u0026cp=U0hUSVdTUF9FS0NONF9PTVRHOlJ2TFZGTDZfYi1Y\u0026id=8cd417002f48551c");</script><title><?php echo $videoTitle ?> - YouTube</title><link rel="search" type="application/opensearchdescription+xml" href="http://www.youtube.com/opensearch?locale=en_US" title="YouTube Video Search"><link rel="icon" href="/yts/img/favicon-vfldLzJxy.ico" type="image/x-icon"><link rel="shortcut icon" href="/yts/img/favicon-vfldLzJxy.ico" type="image/x-icon">   <link rel="icon" href="/yts/img/favicon_32-vflWoMFGx.png" sizes="32x32"><link rel="canonical" href="/watch?v=<?php echo $video_id ?>"><link rel="alternate" media="handheld" href="http://m.youtube.com/watch?v=<?php echo $video_id ?>"><link rel="alternate" media="only screen and (max-width: 640px)" href="http://m.youtube.com/watch?v=<?php echo $video_id ?>"><link rel="shortlink" href="http://youtu.be/<?php echo $video_id ?>">    <meta name="title" content="<?php echo $videoTitle ?>">

    <meta name="description" content="<?php echo $videoDescription ?>">

    <meta name="keywords" content="<?php echo $videoTags ?>">

    <link rel="alternate" type="application/json+oembed" href="http://www.youtube.com/oembed?url=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D<?php echo $video_id ?>&amp;format=json" title="<?php echo $videoTitle ?>">
  <link rel="alternate" type="text/xml+oembed" href="http://www.youtube.com/oembed?url=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D<?php echo $video_id ?>&amp;format=xml" title="<?php echo $videoTitle ?>">

      <meta property="og:url" content="http://www.youtube.com/watch?v=<?php echo $video_id ?>">
    <meta property="og:title" content="<?php echo $videoTitle ?>">
    <meta property="og:description" content="<?php echo $videoDescription ?>">
    <meta property="og:type" content="video">
    <meta property="og:image" content="http://i3.ytimg.com/vi/<?php echo $video_id ?>/mqdefault.jpg">
      <meta property="og:video" content="http://www.youtube.com/v/<?php echo $video_id ?>?version=3&amp;autohide=1">
      <meta property="og:video:type" content="application/x-shockwave-flash">
      <meta property="og:video:width" content="480">
      <meta property="og:video:height" content="360">
    <meta property="og:site_name" content="YouTube">
    <meta property="fb:app_id" content="87741124305">
    <meta name="twitter:card" value="player">
    <meta name="twitter:site" value="@youtube">
      <meta name="twitter:player" value="https://www.youtube.com/embed/<?php echo $video_id ?>">
      <meta property="twitter:player:width" content="480">
      <meta property="twitter:player:height" content="360">

  
  <link id="css-617957165" rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">


      <link id="css-1181818654" rel="stylesheet" href="/yts/cssbin/www-watch-transcript-vfl-zKZyz.css">






  <script>
    var gYouTubePlayerReady = false;
    if (!window['onYouTubePlayerReady']) {
      window['onYouTubePlayerReady'] = function() {
        gYouTubePlayerReady = true;
      };
    }
  </script>
      <script>
if (window.yt.timing) {yt.timing.tick("ct");}    </script>

</head>
<!-- machid: pOVVWMjY1Mlk2ZGhvb0ZQdTNXODVoX1dFcmMyVnhnelVtQXU4bm9fOEtXSUJ3WDFNQlNoek9B -->




  <body id="" class="date-20121205 en_US ltr   ytg-old-clearfix guide-feed-v2 " dir="ltr">

  <div id="body-container">
    <form name="logoutForm" method="POST" action="/logout">
      <input type="hidden" name="action_logout" value="1">
    </form>



    

    <!-- begin page -->
      <div id="page" class="  watch  ">
          
    
    
  <div id="masthead-container">
 <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?>
  <div id="content-container">
    <!-- begin content -->
    <div id="content">
      
<div id="watch-container" itemscope itemid="" itemtype="http://schema.org/VideoObject">
      <link itemprop="url" href="http://www.youtube.com/watch?v=<?php echo $video_id ?>">
    <meta itemprop="name" content="<?php echo $videoTitle ?>">
    <meta itemprop="description" content="The first video on YouTube, uploaded at 8:27PM on Saturday April 23rd, 2005. The video was shot by Yakov Lapitsky at the San Diego Zoo.">
    <meta itemprop="duration" content="PT0M18S">
    <meta itemprop="unlisted" content="<?php echo !empty($videoIsUnlisted) ? 'True' : 'False' ?>">
    <meta itemprop="paid" content="False">
      <span itemprop="author" itemscope itemtype="http://schema.org/Person">
        <link itemprop="url" href="http://www.youtube.com/user/<?php echo $videoAuthor ?>">
      </span>
    <link itemprop="thumbnailUrl" href="http://i3.ytimg.com/vi/<?php echo $video_id ?>/hqdefault.jpg">
    <span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">
      <link itemprop="url" href="http://i3.ytimg.com/vi/<?php echo $video_id ?>/mqdefault.jpg">
      <meta itemprop="width" content="320">
      <meta itemprop="height" content="180">
    </span>
      <link itemprop="embedURL" href="http://www.youtube.com/v/<?php echo $video_id ?>?version=3&amp;autohide=1">
      <meta itemprop="playerType" content="Flash">
      <meta itemprop="width" content="480">
      <meta itemprop="height" content="360">
      <meta itemprop="isFamilyFriendly" content="True">
      <meta itemprop="regionsAllowed" content="AD,AE,AF,AG,AI,AL,AM,AN,AO,AQ,AR,AS,AT,AU,AW,AX,AZ,BA,BB,BD,BE,BF,BG,BH,BI,BJ,BL,BM,BN,BO,BR,BS,BT,BV,BW,BY,BZ,CA,CC,CD,CF,CG,CH,CI,CK,CL,CM,CN,CO,CR,CU,CV,CX,CY,CZ,DE,DJ,DK,DM,DO,DZ,EC,EE,EG,EH,ER,ES,ET,FI,FJ,FK,FM,FO,FR,GA,GB,GD,GE,GF,GG,GH,GI,GL,GM,GN,GP,GQ,GR,GS,GT,GU,GW,GY,HK,HM,HN,HR,HT,HU,ID,IE,IL,IM,IN,IO,IQ,IR,IS,IT,JE,JM,JO,JP,KE,KG,KH,KI,KM,KN,KP,KR,KW,KY,KZ,LA,LB,LC,LI,LK,LR,LS,LT,LU,LV,LY,MA,MC,MD,ME,MF,MG,MH,MK,ML,MM,MN,MO,MP,MQ,MR,MS,MT,MU,MV,MW,MX,MY,MZ,NA,NC,NE,NF,NG,NI,NL,NO,NP,NR,NU,NZ,OM,PA,PE,PF,PG,PH,PK,PL,PM,PN,PR,PS,PT,PW,PY,QA,RE,RO,RS,RU,RW,SA,SB,SC,SD,SE,SG,SH,SI,SJ,SK,SL,SM,SN,SO,SR,SS,ST,SV,SY,SZ,TC,TD,TF,TG,TH,TJ,TK,TL,TM,TN,TO,TR,TT,TV,TW,TZ,UA,UG,UM,US,UY,UZ,VA,VC,VE,VG,VI,VN,VU,WF,WS,YE,YT,ZA,ZM,ZW">


<?php
  // Владелец ли залогиненный пользователь этого видео: его канал == каналу видео
  $ytIsVideoOwner = !empty($ytLoggedIn) && !empty($videoAuthorId)
      && ($ytUserChannelId ?? '') === $videoAuthorId;
?>

  <!-- begin watch-headline-container -->
  <div id="watch-headline-container">
<?php if ($ytIsVideoOwner): ?>
  <div id="watch-owner-container">
            <div id="masthead-subnav" class="yt-nav yt-nav-dark">
    <ul class="yt-nav-aside">
          <li>
    <a href="/analytics#fi=v-<?php echo htmlspecialchars($video_id) ?>" class="yt-uix-button  yt-uix-button-subnav  yt-uix-sessionlink yt-uix-button-dark"><span class="yt-uix-button-content">Analytics
</span></a>
  </li>

    <li>
    <a href="/my_videos" class="yt-uix-button  yt-uix-button-subnav  yt-uix-sessionlink yt-uix-button-dark"><span class="yt-uix-button-content">Video Manager</span></a>
  </li>


    </ul>

    <ul>
      <li>

        <a href="/my_videos_edit?ns=1&amp;video_id=<?php echo htmlspecialchars($video_id) ?>" class="yt-uix-button  yt-uix-button-subnav yt-uix-sessionlink yt-uix-button-dark"><span class="yt-uix-button-content">Edit</span></a>
      </li>

            <li>
    <a href="/enhance?feature=wenh&amp;v=<?php echo htmlspecialchars($video_id) ?>" class="yt-uix-button  yt-uix-button-subnav  yt-uix-sessionlink yt-uix-button-dark"><span class="yt-uix-button-content">Enhancements</span></a>
  </li>

            <li>
    <a href="/audio?feature=wenh&amp;v=<?php echo htmlspecialchars($video_id) ?>" class="yt-uix-button  yt-uix-button-subnav  yt-uix-sessionlink yt-uix-button-dark"><span class="yt-uix-button-content">Audio</span></a>
  </li>


          <li>
    <a href="/my_videos_annotate?v=<?php echo htmlspecialchars($video_id) ?>" class="yt-uix-button  yt-uix-button-subnav  yt-uix-sessionlink yt-uix-button-dark"><span class="yt-uix-button-content">Annotations</span></a>
  </li>





    <li>
      <button type="button" class=" yt-uix-button yt-uix-button-dark yt-uix-button-empty" onclick=";return false;" role="button" aria-pressed="false" aria-expanded="false" aria-haspopup="true" aria-activedescendant=""><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><ul class=" yt-uix-button-menu yt-uix-button-menu-dark" role="menu" aria-haspopup="true" style="display: none;"><li role="menuitem" id="aria-id-82239352096"><span href="/my_videos_timedtext?video_id=<?php echo htmlspecialchars($video_id) ?>" class=" yt-uix-button-menu-item" onclick=";window.location.href=this.getAttribute('href');return false;">Captions</span></li><li role="menuitem" id="aria-id-93589831653"><span href="/my_video_ad?v=<?php echo htmlspecialchars($video_id) ?>&amp;utm_source=youtube&amp;utm_campaign=yt_watch&amp;utm_medium=permanent&amp;utm_content=header_menu&amp;utm_term=dropdown" class=" yt-uix-button-menu-item" onclick=";window.location.href=this.getAttribute('href');return false;">Promote</span></li></ul></button>
    </li>

    </ul>
  </div>


      </div>
<?php endif; ?>

      <div id="watch-headline" class="watch-headline">
<?php if (!empty($ytIsVideoOwner)): ?>
        <form id="watch-headline-title-form" action="/watch_inlineedit_ajax?action_save_video=1" method="POST" class="hid">
    <input type="hidden" name="session_token" value="<?php echo htmlspecialchars(yt_session_token()); ?>">
    <input name="video_id" value="<?php echo htmlspecialchars($video_id); ?>" type="hidden">
    <span class=" yt-uix-form-input-container "><input class="yt-uix-form-input-text " name="field_myvideo_title" value="<?php echo htmlspecialchars($videoTitle); ?>"></span>
    <span class="form-buttons">
      <button type="submit" class=" yt-uix-button yt-uix-button-primary" onclick=";return true;" role="button"><span class="yt-uix-button-content">Save </span></button>
      <button type="button" id="watch-headline-title-reset" onclick=";return false;" class=" yt-uix-button yt-uix-button-default" role="button"><span class="yt-uix-button-content">Cancel </span></button>
    </span>
  </form>
<?php endif; ?>
      <h1 id="watch-headline-title">
  <span id="eow-title" class=" " dir="ltr" title="<?php echo htmlspecialchars($videoTitle); ?>">
    <?php echo htmlspecialchars($videoTitle); ?>
  </span>
  </h1>


    <div id="watch-headline-user-info">
      <?php
      // Состояние «подписан?» — только для залогиненных, один запрос на страницу
      $ytIsSubscribed = !empty($ytLoggedIn) && !empty($videoAuthorId)
          ? yt_action_is_subscribed($videoAuthorId)
          : false;
      ?>
      <span class="yt-uix-button-group"><button href="/user/<?php echo $videoAuthor ?>?feature=watch" type="button" class="start yt-uix-button yt-uix-button-default" onclick=";window.location.href=this.getAttribute(&#39;href&#39;);return false;"  role="button"><span class="yt-uix-button-content"><?php echo $videoAuthor ?> </span></button><div class="yt-subscription-button-hovercard yt-uix-hovercard" data-card-class="watch-subscription-card"><span class="yt-uix-button-context-light yt-uix-button-subscription-container"><?php if (!empty($ytIsVideoOwner)): ?>
  <button disabled="True" onclick=";return false;" title="No need to subscribe to yourself!" type="button" class="yt-subscription-button end yt-uix-button yt-uix-button-default yt-uix-tooltip" role="button"><span class="yt-uix-button-content">Subscribe </span></button>
<span class="yt-subscription-button-disabled-mask"></span>
<?php elseif (!empty($ytLoggedIn) && !empty($videoAuthorId)): ?><button onclick=";subscribe();return false;" id="subscribe-button" type="button" class="yt-subscription-button end yt-uix-button yt-uix-button-subscription<?php echo $ytIsSubscribed ? ' subscribed' : '' ?>" data-subscription-value="<?php echo htmlspecialchars($videoAuthorId) ?>" data-subscription-feature="watch" role="button"><?php else: ?><button href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dsubscribe%26hl%3Den_US%26next%3D%252Fwatch%253Fv%253D<?php echo $video_id ?>%26nomobiletemp%3D1&amp;uilel=3&amp;hl=en_US&amp;service=youtube" onclick=";window.location.href=this.getAttribute(&#39;href&#39;);return false;" type="button" class="yt-subscription-button yt-subscription-button-js-default end  yt-uix-button yt-uix-button-subscription" data-enable-hovercard="true" data-subscription-value="<?php echo htmlspecialchars($videoAuthorId ?? '') ?>" data-force-position="true" data-position="topright" data-subscription-feature="watch" data-subscription-type="" role="button"><?php endif; ?><?php if (empty($ytIsVideoOwner)): ?><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-subscribe" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span><span class="yt-uix-button-content">  <span class="subscribe-label">Subscribe</span>
  <span class="subscribed-label">Subscribed</span>
  <span class="unsubscribe-label">Unsubscribe</span>
 </span></button><span class="yt-subscription-button-disabled-mask"></span><?php endif; ?></span><div class="yt-uix-hovercard-content hid">  <p class="yt-spinner">
      <img src="/yts/img/pixel-vfl3z5WfW.gif" class="yt-spinner-img" alt="Loading icon">

Loading...
  </p>
</div></div></span>

        <button onclick="_toggleclass(this,&#39;yt-uix-expander-collapsed&#39;);return false;" type="button" id="watch-mfu-button" class="yt-uix-expander-collapsed yt-uix-button yt-uix-button-default" data-button-toggle="true" data-video-user-id="<?php echo htmlspecialchars($videoAuthorId ?? '') ?>" data-button-menu-id="some-nonexistent-menu" data-video-id="<?php echo $video_id ?>" data-button-action="yt.www.watch.watch5.handleToggleMoreFromUser" role="button"><span class="yt-uix-button-content"><?php echo htmlspecialchars(videoCountLabel($channelVideoCount)) ?> </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></button>
        <button onclick="window.location.href='/testtube/changeplayer';" type="button" class="yt-uix-expander-collapsed yt-uix-button yt-uix-button-default" data-button-toggle="true" data-button-menu-id="some-nonexistent-menu" role="button"><span class="yt-uix-button-content"><?php echo $togglePText; ?></span></button>
    </div>

    <div id="watch-more-from-user" class="collapsed">
      <div id="watch-channel-discoverbox" class="yt-rounded">
        <span id="watch-channel-loading">Loading...</span>
      </div>
    </div>

  </div>

  </div>
  <!-- end watch-headline-container -->
<div id="watch-video-container">
    <div id="watch-video" >
          <script>
if (window.yt.timing) {yt.timing.tick("bf");}    </script>

          <div id="watch-player" class="<?php echo !empty($useFlashPlayer) ? 'flash-player' : 'html5-player' ?>"></div>
<?php if (!empty($useFlashPlayer)): ?>
    <script>
      (function() {
        var swf = <?php echo json_encode($swfEmbedHtml, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
        document.getElementById('watch-player').innerHTML = swf;
      })()
    </script>
<?php endif; ?>

      <!-- begin watch-video-extra -->
      <div id="watch-video-extra">
        
        
      </div>
      <!-- end watch-video-extra -->
    </div>
  </div>
  <!-- begin watch-main-container -->
  <div id="watch-main-container">
    <div id="watch-main">
      <div id="watch-panel">
            <div class="yt-alert yt-alert-default yt-alert-warn hid " id="flash10-promo-div">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-buttons"><button type="button" class="close yt-uix-close yt-uix-button yt-uix-button-close" onclick="yt.flash.dismissFlashUpgradePromo();return false;" data-close-parent-class="yt-alert" role="button"><span class="yt-uix-button-content">Close </span></button></div><div class="yt-alert-content" role="alert">    <span class="yt-alert-vertical-trick"></span>
    <div class="yt-alert-message">
            Upgrade to the latest Flash Player for improved playback performance. <a href="http://www.adobe.com/go/getflashplayer/" onmousedown="urchinTracker(&#39;/Events/VideoWatch/GetFlashUpgrade&#39;);">Upgrade now</a> or <a href="//support.google.com/youtube/bin/answer.py?answer=95402">more info</a>.
    </div>
</div></div>



  <?php if (!empty($ytIsVideoOwner)): ?>
  <div id="watch-privacy-contain">
      <div id="eow-privacy">
        <div class="yt-alert yt-alert-default yt-alert-warn  " id="watch-video-notification-alert"><div class="yt-alert-buttons"></div><div class="yt-alert-content" role="alert">    <span class="yt-alert-vertical-trick"></span>
    <div class="yt-alert-message">
            This video is public.
    </div>
</div></div>
      </div>
    </div>
<?php endif; ?>
  
<div id="watch-actions">
  <?php if (!$videoisLive): ?> 
          <div id="watch-actions-right">
    <span class="watch-view-count">
      <strong><?php echo $viewCount ?></strong>
    </span>
    <button onclick=";return false;" title="Show video statistics" type="button" id="watch-insight-button" class="yt-uix-tooltip yt-uix-tooltip-reverse yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" data-button-action="yt.www.watch.actions.stats" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-watch-insight" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Show video statistics"><span class="yt-uix-button-valign"></span></span></button>
  </div>
  <?PHP else: ?>
    <div class="concurrent-viewers hid" id="watch-actions-right">
    <span class="watch-view-count">
      <strong>
        <span class="concurrent-viewers-number" data-video-id="<?php echo $video_id; ?>"></span>
      </strong>
watching now
    </span>
  </div>
  <?php endif; ?>

        <span id="watch-like-unlike" class="yt-uix-button-group " data-button-toggle-group="optional"><button onclick=";return false;" title="I like this" type="button" class="start yt-uix-tooltip-reverse  yt-uix-button yt-uix-button-default yt-uix-tooltip" id="watch-like" data-button-toggle="true" data-button-action="yt.www.watch.actions.like" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-watch-like" src="/yts/img/pixel-vfl3z5WfW.gif" alt="I like this"><span class="yt-uix-button-valign"></span></span><span class="yt-uix-button-content">Like </span></button><button onclick=";return false;" title="I dislike this" type="button" class="end yt-uix-tooltip-reverse  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" id="watch-unlike" data-button-toggle="true" data-button-action="yt.www.watch.actions.unlike" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-watch-unlike" src="/yts/img/pixel-vfl3z5WfW.gif" alt="I dislike this"><span class="yt-uix-button-valign"></span></span></button></span>

  
  <button type="button" class="yt-uix-tooltip-reverse  yt-uix-button yt-uix-button-default yt-uix-tooltip" onclick=";return false;" title="Add to favorites or playlist" data-upsell="playlist" data-button-action="yt.www.watch.actions.addto" role="button"><span class="yt-uix-button-content"><span class="addto-label">Add to</span> </span></button>


    <button onclick=";return false;" title="Share or embed this video" type="button" class="yt-uix-tooltip-reverse yt-uix-button yt-uix-button-default yt-uix-tooltip" id="watch-share" data-button-action="yt.www.watch.actions.share" role="button"><span class="yt-uix-button-content">Share </span></button>

    <button onclick=";return false;" title="Flag as inappropriate" type="button" class="yt-uix-tooltip-reverse  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" id="watch-flag" data-button-action="yt.www.watch.actions.flag" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-watch-flag" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Flag as inappropriate"><span class="yt-uix-button-valign"></span></span></button>


      <button onclick=";return false;" title="Interactive Transcript" type="button" class="yt-uix-tooltip-reverse yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" id="watch-transcript" data-button-action="yt.www.watch.actions.transcript" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-transcript" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Interactive Transcript"><span class="yt-uix-button-valign"></span></span></button>



  </div>

  <div id="watch-actions-area-container" class="hid">
    <div id="watch-actions-area" class="yt-rounded">
        <div id="watch-actions-loading" class="watch-actions-panel hid">
Loading...
  </div>
      <div id="watch-actions-logged-out" class="watch-actions-panel hid">
      <div class="yt-alert yt-alert-naked yt-alert-warn  ">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-content" role="alert">    <span class="yt-alert-vertical-trick"></span>
    <div class="yt-alert-message">
              <strong><a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dlike%26hl%3Den_US%26next%3D%252Fwatch%253Fv%253D<?php echo $video_id ?>%26nomobiletemp%3D1&amp;uilel=3&amp;hl=en_US&amp;service=youtube">Sign in</a> or <a href="/signup?next=%2Fwatch%3Fv%3D<?php echo $video_id ?>">sign up</a> now!
</strong>

    </div>
</div></div>
  </div>


    <div id="watch-actions-error" class="watch-actions-panel hid">
    <div class="yt-alert yt-alert-naked yt-alert-error  ">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-content" role="alert" id="watch-error-string"></div></div>
  </div>


  <div id="watch-actions-addto" class="watch-actions-panel hid"></div>

    <div id="watch-actions-share" class="watch-actions-panel hid">
    <div id="watch-actions-share-loading">
Loading...
    </div>
    <div id="watch-actions-share-panel" class="hid"></div>
  </div>

<?php if (!$videoisLive): ?> 
      <div id="watch-actions-transcript" class="watch-actions-panel hid">
      <div id="caption-line-template" class="hid">
    <!--
    <div class="caption-line-time">
      <div class="caption-line-start">__start__</div>
    </div>
    <div class="editable-line-text">
      <span class="editable-line-text-original">__original__</span>
      <label class="editable-line-text-current hid">__current__</label>
      <textarea class="editable-line-text-input hid">__input__</textarea>
    </div>
    -->
  </div>




    <div id="watch-transcript-container" >
      <div id="watch-transcript-not-found" class="hid">
The interactive transcript could not be loaded.
      </div>


      
    </div>
  </div>
<?php endif; ?>




  <div id="watch-actions-ajax" class="watch-actions-panel hid"></div>

  <div class="close">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="close-button" onclick="yt.www.watch.actions.hide();">
  </div>

    </div>
  </div>

  <div id="watch-info">
      <div id="watch-description" class="yt-uix-expander  yt-uix-expander-collapsed" data-expander-action="yt.www.watch.watch5.handleToggleDescription">
    <div id="watch-description-clip">
      <p id="watch-uploader-info">
        <?php if(!$videoisLive): ?>
<!-- ✅ ИСПРАВЛЕНО: Отображение Uploaded vs Premiered -->
<?php if (!empty($isPremiered) && $isPremiered): ?>
Premiered by <a href="<?php echo htmlspecialchars($videoAuthorUrl) ?>" class="yt-uix-sessionlink yt-user-name author" rel="author" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" dir="ltr"><?php echo $videoAuthor ?></a> on <span id="eow-date" class="watch-video-date" ><?php echo $premieredDate ?></span>
<?php else: ?>
Uploaded by <a href="<?php echo htmlspecialchars($videoAuthorUrl) ?>" class="yt-uix-sessionlink yt-user-name author" rel="author" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" dir="ltr"><?php echo $videoAuthor ?></a> on <span id="eow-date" class="watch-video-date" ><?php echo $videoDate ?></span>
<?php endif; ?>
<?php else: ?>
  Streamed live on <span id="eow-date" class="watch-video-date" ><?php echo $videoDate ?></span> by <a href="<?php echo htmlspecialchars($videoAuthorUrl) ?>" class="yt-uix-sessionlink yt-user-name author" rel="author" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" dir="ltr"><?php echo $videoAuthor ?></a>
<?php endif; ?>      </p>
      <div id="watch-description-text">
        <p id="eow-description" ><?php echo $videoDescriptionHTML ?></p>
      </div>
        <div id="watch-description-extras">
    <h4>
Category:
    </h4>
        <p id="eow-category"><a href="/<?php echo $videoCategory ?>"><?php echo $videoCategory ?></a></p>
<?php if (!empty($videoTags)): ?>
      <h4>
Tags:
      </h4>
        <ul id="eow-tags" class="watch-info-tag-list">
    <li><a href="/results?search_query=<?php echo $videoTags ?>&amp;search=tag"><?php echo $videoTags ?></a></li>
  </ul>
<?php endif; ?>
      <h4>License:</h4>
        <p id="eow-reuse">
<a href="/t/creative_commons" target="_blank">Creative Commons Attribution license</a> (reuse allowed)</a>
  </p>


  </div>

    </div>

      <ul id="watch-description-extra-info">
      <li>
            <div class="video-extras-sparkbars">
    <div class="video-extras-sparkbar-likes" style="width: <?php echo $likePercent ?>%"></div>
    <div class="video-extras-sparkbar-dislikes" style="width: <?php echo $dislikePercent ?>%"></div>
  </div>
  <span class="video-extras-likes-dislikes">
    <span class="likes"><?php echo $likeCount ?></span> likes, <span class="dislikes"><?php echo $dislikeCount ?></span> dislikes
  </span>


      </li>
















      <li class="watch-extra-info-long">
          <img class="metadata-icon source-videos" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Source videos:">
  <span class="metadata-info link-list">
Source videos:
    <span id="watch-source-videos-list">Loading...</span>
  </span>
  <a class="attribution-link" href="/attribution?v=<?php echo $video_id ?>">View attributions &#187;</a>

      </li>
  </ul>


        <div class="yt-horizontal-rule "><span class="first"></span><span class="second"></span><span class="third"></span></div>

  <div id="watch-description-toggle" class="yt-uix-expander-head">
    <div id="watch-description-expand" class="expand">
      <button type="button" class="metadata-inline yt-uix-button yt-uix-button-text" onclick=";return false;"  role="button"><span class="yt-uix-button-content">Show more <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="Show more">
 </span></button>
    </div>
    <div id="watch-description-collapse" class="collapse">
      <button type="button" class="metadata-inline yt-uix-button yt-uix-button-text" onclick=";return false;"  role="button"><span class="yt-uix-button-content">Show less <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="Show less">
 </span></button>
    </div>
  </div>



  </div> 


  </div>

  <?php if (!empty($ytIsVideoOwner)): ?>
      <form action="/watch_inlineedit_ajax?action_save_video=1" method="POST" id="watch-video-info-form" class="hid">
    <input type="hidden" name="session_token" value="<?php echo htmlspecialchars(yt_session_token()); ?>">
    <input name="video_id" value="<?php echo htmlspecialchars($video_id); ?>" type="hidden">
    <input name="ignore_broadcast_settings" value="0" type="hidden">

    <p class="yt">
      <label class="yt-uix-form-label">
Description:
        <span class="yt-uix-form-input-container "><textarea class="yt-uix-form-textarea " name="field_myvideo_descr" rows="6"><?php echo htmlspecialchars($videoDescription ?? ''); ?></textarea></span>
      </label>
    </p>
    <p class="yt">
      <label class="yt-uix-form-label">
Category:<br>
        <span class="yt-uix-form-input-select "><span class="yt-uix-form-input-select-content"><img src="/yts/img/pixel-vfl3z5WfW.gif" class="yt-uix-form-input-select-arrow"><span class="yt-uix-form-input-select-value"><?php echo htmlspecialchars($videoCategory ?? 'People & Blogs'); ?></span></span><select class="yt-uix-form-input-select-element " name="field_myvideo_categories">
    <option value="2"<?php echo (($videoCategoryId ?? '')==='2')?' selected':''; ?>>Autos &amp; Vehicles</option>
    <option value="23"<?php echo (($videoCategoryId ?? '')==='23')?' selected':''; ?>>Comedy</option>
    <option value="27"<?php echo (($videoCategoryId ?? '')==='27')?' selected':''; ?>>Education</option>
    <option value="24"<?php echo (($videoCategoryId ?? '')==='24')?' selected':''; ?>>Entertainment</option>
    <option value="1"<?php echo (($videoCategoryId ?? '')==='1')?' selected':''; ?>>Film &amp; Animation</option>
    <option value="20"<?php echo (($videoCategoryId ?? '')==='20')?' selected':''; ?>>Gaming</option>
    <option value="26"<?php echo (($videoCategoryId ?? '')==='26')?' selected':''; ?>>Howto &amp; Style</option>
    <option value="10"<?php echo (($videoCategoryId ?? '')==='10')?' selected':''; ?>>Music</option>
    <option value="25"<?php echo (($videoCategoryId ?? '')==='25')?' selected':''; ?>>News &amp; Politics</option>
    <option value="29"<?php echo (($videoCategoryId ?? '')==='29')?' selected':''; ?>>Nonprofits &amp; Activism</option>
    <option value="22"<?php echo (($videoCategoryId ?? '')==='22' || ($videoCategoryId ?? '')==='')?' selected':''; ?>>People &amp; Blogs</option>
    <option value="15"<?php echo (($videoCategoryId ?? '')==='15')?' selected':''; ?>>Pets &amp; Animals</option>
    <option value="28"<?php echo (($videoCategoryId ?? '')==='28')?' selected':''; ?>>Science &amp; Technology</option>
    <option value="17"<?php echo (($videoCategoryId ?? '')==='17')?' selected':''; ?>>Sports</option>
    <option value="19"<?php echo (($videoCategoryId ?? '')==='19')?' selected':''; ?>>Travel &amp; Events</option>
</select></span>
      </label>
    </p>
      <p class="yt">
        <label class="yt-uix-form-label">
License:<br>
          <span class="yt-uix-form-input-select "><span class="yt-uix-form-input-select-content"><img src="/yts/img/pixel-vfl3z5WfW.gif" class="yt-uix-form-input-select-arrow"><span class="yt-uix-form-input-select-value">Standard YouTube License</span></span><select class="yt-uix-form-input-select-element " name="reuse">
  <option value="all_rights_reserved" selected>Standard YouTube License</option>
  <option value="creative_commons">Creative Commons Attribution license (reuse allowed)</option>
</select></span>
        </label>
      </p>
    <p class="yt">
      <label class="yt-uix-form-label">
Privacy:<br>
        <span class="yt-uix-form-input-select "><span class="yt-uix-form-input-select-content"><img src="/yts/img/pixel-vfl3z5WfW.gif" class="yt-uix-form-input-select-arrow"><span class="yt-uix-form-input-select-value">Public</span></span><select class="yt-uix-form-input-select-element " name="privacy">
  <option value="public" selected>Public</option>
  <option value="private">Private</option>
  <option value="unlisted">Unlisted</option>
</select></span>
      </label>
    </p>
   <p class="yt">
      <button type="submit" class=" yt-uix-button yt-uix-button-primary" onclick=";return true;" role="button"><span class="yt-uix-button-content">Save </span></button>
      <button type="button" id="watch-video-info-reset" onclick=";return false;" class=" yt-uix-button yt-uix-button-default" role="button"><span class="yt-uix-button-content">Cancel </span></button>
    </p>
  </form>
<?php endif; ?>

<div id="watch-discussion">
  <?php if (!$comments_enabled): ?>
          <div id="comments-view" class="comments-disabled">
    <div class="comments-section">
      <h4>All Comments</h4>
      <div class="comments-disabled-message">
        <img src="/yts/img/icon_comments_disabled-vflxokpZC.png">
          <span>Comments are disabled for this video.</span>
      </div>
    </div>
  </div>
      <?php endif; ?>

      <?php /* Comments enabled — рендерим Top/All Comments; иначе только заглушку выше */ ?>
      <?php if ($comments_enabled): ?>
        <div id="comments-view" data-type="highlights" class="">

                <div class="comments-section">
      <h4>
          <strong>Top Comments</strong>

  </h4>

      <ul class="comment-list" >
      <?php foreach ($videoComments as $comment): ?>

  <li class="comment yt-tile-default"
      data-tag="top"
    data-author-id="<?php echo htmlspecialchars($comment['authorId'] ?? '') ?>"
    data-id="<?php echo htmlspecialchars($comment['id'] ?? '') ?>"
      data-score="<?php echo htmlspecialchars($comment['likes'] ?? '0') ?>"
    >

    <div class="comment-body">
      

  <div class="content-container">
    <div class="content">

          <div class="comment-text" dir="ltr">
            <p><?php echo htmlspecialchars($comment['text'] ?? '') ?></p>

          </div>

        <p class="metadata">
          <span class="author ">
            <a href="<?php echo htmlspecialchars($comment['authorUrl'] ?? '') ?>" class="yt-uix-sessionlink yt-user-name " data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" dir="ltr"><?php echo htmlspecialchars($comment['author'] ?? '') ?></a>
          </span>
            <span class="time" dir="ltr">
              <a dir="ltr" href="/comment?lc=<?php echo htmlspecialchars($comment['id'] ?? '') ?>">
                <?php echo !empty($comment['date']) ? timeAgo($comment['date']) : '' ?>
              </a>
            </span>
              <span dir="ltr" class="comments-rating-positive" title="<?php echo htmlspecialchars($comment['likes'] ?? '0') ?> up, 0 down">
                <?php echo htmlspecialchars($comment['likes'] ?? '0') ?>
                <img class="comments-rating-thumbs-up" src="/yts/img/pixel-vfl3z5WfW.gif">
              </span>
        </p>

    </div>


      
  <div class="comment-actions">
<span class="yt-uix-button-group"><span ><button type="button" class="start comment-action-vote-up comment-action yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" onclick=";return false;" title="Vote Up" data-action="vote-up" data-tooltip-show-delay="300" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-watch-comment-vote-up" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Vote Up"><span class="yt-uix-button-valign"></span></span></button></span><span ><button type="button" class="end comment-action-vote-down comment-action yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" onclick=";return false;" title="Vote Down" data-action="vote-down" data-tooltip-show-delay="300" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-watch-comment-vote-down" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Vote Down"><span class="yt-uix-button-valign"></span></span></button></span></span><span class="yt-uix-button-group"><button type="button" class="start comment-action yt-uix-button yt-uix-button-default" onclick=";return false;" data-action="reply" role="button"><span class="yt-uix-button-content">Reply </span></button><button type="button" class="flip end yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" data-button-has-sibling-menu="true" role="button" aria-pressed="false" aria-expanded="false" aria-haspopup="true" aria-activedescendant=""><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><div class=" yt-uix-button-menu yt-uix-button-menu-default" style="display: none;"><ul><li class="comment-action-remove comment-action" data-action="remove"><span class="yt-uix-button-menu-item">Remove</span></li><li class="comment-action" data-action="flag"><span class="yt-uix-button-menu-item">Flag for spam</span></li><li class="comment-action-block comment-action" data-action="block"><span class="yt-uix-button-menu-item">Block User</span></li><li class="comment-action-unblock comment-action" data-action="unblock"><span class="yt-uix-button-menu-item">Unblock User</span></li></ul></div></button></span>  </div>

  </div>

    </div>
  </li>
<?php endforeach; ?>
  </ul>

  </div>

          <div class="comments-section" >
      <h4>
          <strong>All Comments</strong> (<?php echo $commentsCount ?>)

        <a class="comments-section-see-all" href="/all_comments?v=<?php echo $video_id ?>">
see all
        </a>
  </h4>


          <?php if (!empty($ytLoggedIn)): ?>
        <div class="comments-post-container clearfix">
          <form class="comments-post" method="post" action="/comment_servlet?add_comment=1">
    <div class="yt-alert yt-alert-default yt-alert-error hid comments-post-message">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-buttons"></div><div class="yt-alert-content" role="alert"></div></div>

    <input type="hidden" name="session_token" value="<?php echo yt_session_token() ?>">
    <input type="hidden" name="video_id" value="<?php echo htmlspecialchars($video_id) ?>">


    <input type="hidden" name="form_id" value="">
    <input type="hidden" name="source" value="w">
    <input type="hidden" value="" name="reply_parent_id">

<a href="<?php echo htmlspecialchars($ytUserChannel ?? '/') ?>" class="yt-user-photo comments-post-profile"><span class="video-thumb ux-thumb yt-thumb-square-46 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="<?php echo htmlspecialchars($ytUserAvatar ?? '/dynamic/pfp/default.png') ?>" alt="<?php echo htmlspecialchars($ytUserName ?? '') ?>" width="46" onerror="this.onerror=null;this.src='/dynamic/pfp/default.png'"><span class="vertical-align"></span></span></span></span></a><div class="comments-textarea-container" onclick="yt.www.comments.initForm(this, true, false);"><img src="/yts/img/pixel-vfl3z5WfW.gif" alt="" class="comments-textarea-tip"><label class="comments-textarea-label" data-upsell="comment">Respond to this video...</label>  <div class="yt-uix-form-input-fluid yt-grid-fluid ">
      <textarea id="" class="yt-uix-form-textarea comments-textarea" onfocus="yt.www.comments.initForm(this, false, false);" data-upsell="comment" name="comment"></textarea>

  </div>
</div>
    <?php /* data-max-count ДОЛЖЕН быть на .comments-remaining: www-core-vflegKBuo.js
             читает лимит именно с этого <p> (this.b), а не со span внутри —
             без него parseInt(undefined) давал "NaN characters remaining". */ ?>
    <p class="comments-remaining" data-max-count="500">
<span class="comments-remaining-count" data-max-count="500"></span> characters remaining
    </p>
    <p class="comments-threshold-countdown hid">
<span class="comments-threshold-count"></span> seconds remaining before you can post
    </p>
    <p class="comments-post-buttons">
<button type="submit" class="comments-post yt-uix-button yt-uix-button-default" onclick=";return true;" role="button"><span class="yt-uix-button-content">Post </span></button>    </p>
  </form>

    </div>
      <?php else: ?>
          <div class="comments-post-container clearfix">
        <div class="comments-post-alert">
          <a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dcomments%26hl%3Den_US%26next%3D%252Fwatch%253Fv%253D<?php echo $video_id ?>%26nomobiletemp%3D1&amp;uilel=3&amp;hl=en_US&amp;service=youtube">Sign In</a> or <a href="/signup?next=%2Fwatch%3Fv%3D<?php echo $video_id ?>">Sign Up</a><span class="comments-post-form-rollover-text"> now to post a comment!</span>

        </div>
    </div>
      <?php endif; ?>


        <ul class="comment-list" >
      
<?php foreach ($videoComments as $comment): ?>

  <li class="comment yt-tile-default"
    data-author-id="<?php echo htmlspecialchars($comment['authorId'] ?? '') ?>"
    data-id="<?php echo htmlspecialchars($comment['id'] ?? '') ?>"
    >

    <div class="comment-body">
      

  <div class="content-container">
    <div class="content">

          <div class="comment-text" dir="ltr">
            <p><?php echo htmlspecialchars($comment['text'] ?? '') ?></p>

          </div>

        <p class="metadata">
          <span class="author ">
            <a href="<?php echo htmlspecialchars($comment['authorUrl'] ?? '') ?>" class="yt-uix-sessionlink yt-user-name " data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" dir="ltr"><?php echo htmlspecialchars($comment['author'] ?? '') ?></a>
          </span>
            <span class="time" dir="ltr">
<?php if (!empty($comment['replyTo'])): ?>
in reply to <a href="<?php echo htmlspecialchars($comment['replyToUrl'] ?? '') ?>" class="yt-uix-sessionlink yt-user-name " data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" dir="ltr"><?php echo htmlspecialchars($comment['replyTo']) ?></a>
                <a href="#" class="comment-action comment-action-showparent" onclick="return false;" data-action="show-parent">(Show the comment)</a>
<?php endif; ?>
              <a dir="ltr" href="/comment?lc=<?php echo htmlspecialchars($comment['id'] ?? '') ?>">
                <?php echo !empty($comment['date']) ? timeAgo($comment['date']) : '' ?>
              </a>
            </span>
        </p>

    </div>


      
  <div class="comment-actions">
<span class="yt-uix-button-group"><span ><button type="button" class="start comment-action-vote-up comment-action yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" onclick=";return false;" title="Vote Up" data-action="vote-up" data-tooltip-show-delay="300" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-watch-comment-vote-up" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Vote Up"><span class="yt-uix-button-valign"></span></span></button></span><span ><button type="button" class="end comment-action-vote-down comment-action yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" onclick=";return false;" title="Vote Down" data-action="vote-down" data-tooltip-show-delay="300" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-watch-comment-vote-down" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Vote Down"><span class="yt-uix-button-valign"></span></span></button></span></span><span class="yt-uix-button-group"><button type="button" class="start comment-action yt-uix-button yt-uix-button-default" onclick=";return false;" data-action="reply" role="button"><span class="yt-uix-button-content">Reply </span></button><button type="button" class="flip end yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" data-button-has-sibling-menu="true" role="button" aria-pressed="false" aria-expanded="false" aria-haspopup="true" aria-activedescendant=""><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><div class=" yt-uix-button-menu yt-uix-button-menu-default" style="display: none;"><ul><li class="comment-action-remove comment-action" data-action="remove"><span class="yt-uix-button-menu-item">Remove</span></li><li class="comment-action" data-action="flag"><span class="yt-uix-button-menu-item">Flag for spam</span></li><li class="comment-action-block comment-action" data-action="block"><span class="yt-uix-button-menu-item">Block User</span></li><li class="comment-action-unblock comment-action" data-action="unblock"><span class="yt-uix-button-menu-item">Unblock User</span></li></ul></div></button></span>  </div>

  </div>

    </div>
  </li>
<?php endforeach; ?>

  </ul>

  </div>




          <div class="comments-section">
      <div class="comments-pagination" data-ajax-enabled="true">
          

    <div class="yt-uix-pager" role="navigation">

          
<a href="/all_comments?v=<?php echo $video_id ?>&amp;page=1" class="yt-uix-button  yt-uix-pager-button yt-uix-button-toggled yt-uix-sessionlink yt-uix-button-default" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" data-page="1" aria-label="Go to page 1"><span class="yt-uix-button-content">1</span></a>
          
<a href="/all_comments?v=<?php echo $video_id ?>&amp;page=2" class="yt-uix-button  yt-uix-pager-button yt-uix-sessionlink yt-uix-button-default" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" data-page="2" aria-label="Go to page 2"><span class="yt-uix-button-content">2</span></a>
          
<a href="/all_comments?v=<?php echo $video_id ?>&amp;page=3" class="yt-uix-button  yt-uix-pager-button yt-uix-sessionlink yt-uix-button-default" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" data-page="3" aria-label="Go to page 3"><span class="yt-uix-button-content">3</span></a>
          
<a href="/all_comments?v=<?php echo $video_id ?>&amp;page=4" class="yt-uix-button  yt-uix-pager-button yt-uix-sessionlink yt-uix-button-default" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" data-page="4" aria-label="Go to page 4"><span class="yt-uix-button-content">4</span></a>
          
<a href="/all_comments?v=<?php echo $video_id ?>&amp;page=5" class="yt-uix-button  yt-uix-pager-button yt-uix-sessionlink yt-uix-button-default" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" data-page="5" aria-label="Go to page 5"><span class="yt-uix-button-content">5</span></a>
          
<a href="/all_comments?v=<?php echo $video_id ?>&amp;page=6" class="yt-uix-button  yt-uix-pager-button yt-uix-sessionlink yt-uix-button-default" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" data-page="6" aria-label="Go to page 6"><span class="yt-uix-button-content">6</span></a>
          
<a href="/all_comments?v=<?php echo $video_id ?>&amp;page=7" class="yt-uix-button  yt-uix-pager-button yt-uix-sessionlink yt-uix-button-default" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" data-page="7" aria-label="Go to page 7"><span class="yt-uix-button-content">7</span></a>

        
<a href="/all_comments?v=<?php echo $video_id ?>&amp;page=2" class="yt-uix-button  yt-uix-pager-button yt-uix-sessionlink yt-uix-button-default" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D" data-page="2"><span class="yt-uix-button-content">Next »</span></a>
    </div>

      </div>
  </div>



      <ul>
    <li class="hid" id="parent-comment-loading"> Loading comment...</li>
  </ul>

    <div id="comments-loading" class="hid">Loading...</div>
  </div>
      <?php endif; /* $comments_enabled */ ?>



  </div>

      </div>
 <div id="watch-sidebar">
        

      <div class="watch-sidebar-section">
        <ul id="watch-related" class="video-list watch-sidebar-body">
<?php foreach ($relatedVideos as $rel): ?>
            <li class="video-list-item">
              <a href="/watch?v=<?php echo htmlspecialchars($rel['id']) ?>" class="related-video yt-uix-contextlink yt-uix-sessionlink" data-sessionlink="feature=related"><span class="ux-thumb-wrap contains-addto "><span class="video-thumb ux-thumb yt-thumb-default-120 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="/yts/img/pixel-vfl3z5WfW.gif" alt="<?php echo htmlspecialchars($rel['title']) ?>" data-thumb="<?php echo htmlspecialchars($rel['thumbnail']) ?>" width="120" ><span class="vertical-align"></span></span></span></span><span class="video-time"><?php echo htmlspecialchars($rel['duration']) ?></span>
  <button onclick=";return false;" title="Watch Later" type="button" class="addto-button video-actions spf-nolink addto-watch-later-button-sign-in yt-uix-button yt-uix-button-default yt-uix-button-short yt-uix-tooltip" data-button-menu-id="shared-addto-watch-later-login" data-video-ids="<?php echo htmlspecialchars($rel['id']) ?>" role="button"><span class="yt-uix-button-content">  <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="Watch Later">
 </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></button>
</span><span dir="ltr" class="title" title="<?php echo htmlspecialchars($rel['title']) ?>"><?php echo htmlspecialchars($rel['title']) ?></span><span class="stat attribution">by <span class="yt-user-name " dir="ltr"><?php echo htmlspecialchars($rel['author']) ?></span></span><span class="stat view-count"><?php
    // $rel['views'] = чистое число. format_view_count → «1,234,567 views»
    if (function_exists('format_view_count')) {
        echo htmlspecialchars(format_view_count($rel['views'] ?? ''));
    } else {
        $n = (int)preg_replace('/\D/', '', (string)($rel['views'] ?? ''));
        if ($n <= 0)      echo 'No views';
        elseif ($n === 1) echo '1 view';
        else              echo number_format($n, 0, '.', ',') . ' views';
    }
?></span>
              </a>
            </li>
<?php endforeach; ?>
        </ul>
          <ul id="watch-more-related" class="video-list hid">
            <li id="watch-more-related-loading">
Loading more suggestions...
            </li>
          </ul>
          <div class="watch-sidebar-foot">
            <p class="content">
              <button type="button" id="watch-more-related-button" onclick=";return false;" class=" yt-uix-button yt-uix-button-default" data-button-action="yt.www.watch.watch5.handleLoadMoreRelated" role="button"><span class="yt-uix-button-content">Load more suggestions </span></button>
            </p>
          </div>
      </div>

            <span class="yt-vertical-rule-main"></span>
    <span class="yt-vertical-rule-corner-top"></span>
    <span class="yt-vertical-rule-corner-bottom"></span>

      </div>
      <div class="clear"></div>
    </div>
      <div style="visibility: hidden; height: 0px; padding: 0px; overflow: hidden;">

  </div>

  </div>
  <!-- end watch-main-container -->
</div>

    </div>
    <!-- end content -->
  </div>

<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>
    



  <div id="playlist-bar" class="<?php echo !empty($watchPlaylist) ? '' : 'hid passive editable' ?>" data-video-url="/watch?v=&amp;playnext=1&amp;list=<?php echo !empty($watchPlaylist) ? htmlspecialchars($watchPlaylist['id']) : 'QL' ?>" data-list-id="<?php echo !empty($watchPlaylist) ? htmlspecialchars($watchPlaylist['id']) : '' ?>" data-list-type="<?php echo !empty($watchPlaylist) ? 'PL' : 'QL' ?>">
    <div id="playlist-bar-bar-container">
      <div id="playlist-bar-bar">
        <div class="yt-alert yt-alert-naked yt-alert-success hid " id="playlist-bar-notifications">  <div class="yt-alert-icon">
    <img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
  </div>
<div class="yt-alert-content" role="alert"></div></div>
<span id="playlist-bar-info"><span class="playlist-bar-active playlist-bar-group"><button onclick=";return false;" title="Previous video" type="button" id="playlist-bar-prev-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty"  role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-prev" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Previous video"><span class="yt-uix-button-valign"></span></span></button><span class="playlist-bar-count"><span class="playing-index"><?php echo (int)($watchPlaylist['index'] ?? 0) ?></span> / <span class="item-count"><?php echo (int)($watchPlaylist['count'] ?? 0) ?></span></span><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-next-button"  role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-next" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span></button></span><span class="playlist-bar-active playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-autoplay-button" data-button-toggle="true" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-autoplay" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span></button><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-shuffle-button" data-button-toggle="true" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-shuffle" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span></button></span><span class="playlist-bar-passive playlist-bar-group"><button onclick=";return false;" title="Play videos" type="button" id="playlist-bar-play-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty"  role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-play" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Play videos"><span class="yt-uix-button-valign"></span></span></button><span class="playlist-bar-count"><span class="item-count"><?php echo (int)($watchPlaylist['count'] ?? 0) ?></span></span></span><span id="playlist-bar-title" class="yt-uix-button-group"><span class="playlist-title"><?php echo !empty($watchPlaylist) ? htmlspecialchars($watchPlaylist['title']) : 'Unsaved Playlist' ?></span></span></span>
        <a id="playlist-bar-lists-back" href="#">
Return to active list
        </a>

<span id="playlist-bar-controls"><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-text yt-uix-button-empty" onclick=";return false;" id="playlist-bar-toggle-button"  role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-toggle" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span></button></span><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked yt-uix-button-reverse flip yt-uix-button yt-uix-button-text" onclick=";return false;" data-button-menu-id="playlist-bar-options-menu" data-button-has-sibling-menu="true" role="button"><span class="yt-uix-button-content">Options </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></button></span></span>      </div>
    </div>

<div id="playlist-bar-tray-container"><div id="playlist-bar-tray" class="yt-uix-slider yt-uix-slider-fluid"><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-prev" onclick="return false;"><img class="yt-uix-slider-prev-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Previous video"></button><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-next" onclick="return false;"><img class="yt-uix-slider-next-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Next video"></button><div class="yt-uix-slider-body"><div id="playlist-bar-tray-content" class="yt-uix-slider-slide"><ol class="video-list"><?php if (!empty($watchPlaylist)): foreach ($watchPlaylist['items'] as $plIt): ?><li class="playlist-bar-item yt-uix-slider-slide-unit<?php echo $plIt['selected'] ? ' playlist-bar-item-playing' : '' ?>" data-video-id="<?php echo htmlspecialchars($plIt['id']) ?>"><a href="/watch?v=<?php echo htmlspecialchars($plIt['id']) ?>&amp;list=<?php echo htmlspecialchars($watchPlaylist['id']) ?>&amp;index=<?php echo (int)$plIt['position'] ?>" title="<?php echo htmlspecialchars($plIt['title']) ?>" class="yt-uix-sessionlink"><span class="video-thumb ux-thumb yt-thumb-default-106 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="//i.ytimg.com/vi/<?php echo htmlspecialchars($plIt['id']) ?>/default.jpg" alt="<?php echo htmlspecialchars($plIt['title']) ?>" width="106"><span class="vertical-align"></span></span></span></span><span class="screen"></span><span class="count"><strong><?php echo (int)$plIt['position'] ?></strong></span><span class="play"><img src="/yts/img/pixel-vfl3z5WfW.gif"></span><span class="now-playing">Now playing</span><span dir="ltr" class="title"><span><?php echo htmlspecialchars($plIt['title']) ?>  <span class="uploader">by <?php echo htmlspecialchars($plIt['author']) ?></span>
</span></span></a></li><?php endforeach; endif; ?></ol><ol id="playlist-bar-help"<?php echo !empty($watchPlaylist) ? ' class="hid"' : '' ?>><li class="empty playlist-bar-help-message">Your queue is empty. Add videos to your queue using this button: <img src="/yts/img/pixel-vfl3z5WfW.gif" class="addto-button-help"><br> or <a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26hl%3Den_US%26next%3D%252Fwatch%253Fv%253D<?php echo $video_id ?>%26nomobiletemp%3D1&amp;uilel=3&amp;hl=en_US&amp;service=youtube">sign in</a> to load a different list.</li></ol></div><div class="yt-uix-slider-shade-left"></div><div class="yt-uix-slider-shade-right"></div></div></div><div id="playlist-bar-save"></div><div id="playlist-bar-lists" class="dark-lolz"></div><div id="playlist-bar-loading"><img src="/yts/img/pixel-vfl3z5WfW.gif" alt="Loading..."><span id="playlist-bar-loading-message">Loading...</span><span id="playlist-bar-saving-message" class="hid">Saving...</span></div><div id="playlist-bar-template" style="display: none;" data-video-thumb-url="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg"><!--<li class="playlist-bar-item yt-uix-slider-slide-unit __classes__" data-video-id="__video_encrypted_id__"><a href="__video_url__" title="__video_title__" class="yt-uix-sessionlink" data-sessionlink="ei=CJ6l17ndhLQCFeOCRAodBDLN_A%3D%3D&amp;feature=BFa"><span class="video-thumb ux-thumb yt-thumb-default-106 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="/yts/img/pixel-vfl3z5WfW.gif" alt="__video_title__" data-thumb-manual="true" data-thumb="__video_thumb_url__" width="106" ><span class="vertical-align"></span></span></span></span><span class="screen"></span><span class="count"><strong>__list_position__</strong></span><span class="play"><img src="/yts/img/pixel-vfl3z5WfW.gif"></span><span class="yt-uix-button yt-uix-button-default delete"><img class="yt-uix-button-icon-playlist-bar-delete" src="/yts/img/pixel-vfl3z5WfW.gif" alt="Delete"></span><span class="now-playing">Now playing</span><span dir="ltr" class="title"><span>__video_title__  <span class="uploader">by __video_display_name__</span>
</span></span><span class="dragger"></span></a></li>--></div><div id="playlist-bar-next-up-template" style="display: none;"><!--<div class="playlist-bar-next-thumb"><span class="video-thumb ux-thumb yt-thumb-default-74 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg" alt="Thumbnail" width="74" ><span class="vertical-align"></span></span></span></span></div>--></div></div>      <div id="playlist-bar-options-menu" class="hid">

    <div id="playlist-bar-extras-menu">
        <ul>
      <li><span class="yt-uix-button-menu-item" data-action="clear">
Clear all videos from this list
      </span></li>
  </ul>

    </div>

    <ul>
      <li><span class="yt-uix-button-menu-item" onclick="window.location.href=&#39;//support.google.com/youtube/bin/answer.py?answer=146749&amp;hl=en-US&#39;">Learn more</span></li>
    </ul>
  </div>

  </div>


  
    <div id="shared-addto-watch-later-login" class="hid">
      <a href="https://accounts.google.com/ServiceLogin?passive=true&continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26hl%3Den_US%26next%3D%252Fwatch%253Fv%253D<?php echo $video_id ?>%26nomobiletemp%3D1&uilel=3&hl=en_US&service=youtube" class="sign-in-link">Sign in</a> to add this to a playlist

    </div>

  <div id="shared-addto-menu" style="display: none;" class="hid sign-in">
      <div class="addto-menu">
        <div id="addto-list-panel" class="menu-panel active-panel">
        <span class="yt-uix-button-menu-item yt-uix-tooltip sign-in"data-possible-tooltip=""data-tooltip-show-delay="750"><a href="https://accounts.google.com/ServiceLogin?passive=true&continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26hl%3Den_US%26next%3D%252Fwatch%253Fv%253D<?php echo $video_id ?>%26nomobiletemp%3D1&uilel=3&hl=en_US&service=youtube" class="sign-in-link">Sign in</a> to add this to a playlist
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
<div class="yt-uix-char-counter" data-char-limit="150"><div class="addto-note-box addto-text-box"><textarea id="addto-note" class="addto-note yt-uix-char-counter-input" maxlength="150"></textarea><label for="addto-note" class="addto-note-label">Add an optional note</label></div><span class="yt-uix-char-counter-remaining">150</span></div>    <button disabled="disabled" type="button" class="playlist-save-note yt-uix-button yt-uix-button-default" onclick=";return false;"  role="button"><span class="yt-uix-button-content">Add note </span></button>
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


          <div class="yt-dialog hid" id="feed-privacy-lb">
    <div class="yt-dialog-base">
      <span class="yt-dialog-align"></span>
      <div class="yt-dialog-fg">
        <div class="yt-dialog-fg-content">
          <div class="yt-dialog-loading">
              <div class="yt-dialog-waiting-content">
    <div class="yt-spinner-img"></div><div class="yt-dialog-waiting-text">Loading...</div>
  </div>

          </div>
          <div class="yt-dialog-content">
              <div id="feed-privacy-dialog">
  </div>

          </div>
          <div class="yt-dialog-working">
              <div id="yt-dialog-working-overlay">
  </div>
  <div id="yt-dialog-working-bubble">
    <div class="yt-dialog-waiting-content">
      <div class="yt-spinner-img"></div><div class="yt-dialog-waiting-text">Working...</div>
    </div>
  </div>

          </div>
        </div>
      </div>
    </div>
  </div>




      </div>
    <!-- end page -->
  </div>


    
  
    <script id="js-4108856425" src="/yts/jsbin/www-core-vflegKBuo.js" data-loaded="true"></script>


  <script>
        yt.setConfig({
      'XSRF_TOKEN': '<?php echo yt_session_token() ?>',
      'XSRF_FIELD_NAME': 'session_token'
    });

    yt.setConfig('XSRF_REDIRECT_TOKEN', 'NZmAXQA4KvUInd9_8z1c3cjkjNp8MTM1NDg0NzI0OUAxMzU0NzYwODQ5');

    yt.setConfig({
      'EVENT_ID': "CJ6l17ndhLQCFeOCRAodBDLN_A==",
      'CURRENT_URL': "http:\/\/www.youtube.com\/watch?v=<?php echo $video_id ?>",
      'LOGGED_IN': <?php echo !empty($ytLoggedIn) ? 'true' : 'false' ?>,
      'SESSION_INDEX': <?php echo !empty($ytLoggedIn) ? '0' : 'null' ?>,

      'WATCH_CONTEXT_CLIENTSIDE': false,

      'FEEDBACK_LOCALE_LANGUAGE': "en",
      'FEEDBACK_LOCALE_EXTRAS': {"logged_in": false, "experiments": "922401,920704,912806,925703,925706,928001,922403,913605,913546,913556,908493,920201,911116,901451", "guide_subs": "NA", "accept_language": null}    });
  </script>


      <script>
if (window.yt.timing) {yt.timing.tick("js_head");}    </script>

      <script>
      yt.setAjaxToken('subscription_ajax', "<?php echo yt_session_token() ?>");
      yt.setAjaxToken('watch_actions_ajax', "<?php echo yt_session_token() ?>");
      yt.setAjaxToken('comment_servlet', "<?php echo yt_session_token() ?>");
      yt.setAjaxToken('playlist_bar_ajax', "<?php echo yt_session_token() ?>");
    yt.pubsub.subscribe('init', yt.www.subscriptions.SubscriptionButton.init);

    // Кнопка Subscribe/Unsubscribe (залогиненный вариант, id=subscribe-button):
    // POST на /subscription_ajax в контракте 2012 + переключение класса subscribed.
    function subscribe() {
      var btn = document.getElementById('subscribe-button');
      if (!btn || btn.getAttribute('data-busy')) return;
      var channelId  = btn.getAttribute('data-subscription-value');
      var subscribed = / (^|\s)subscribed(\s|$)/.test(' ' + btn.className + ' ');
      var url  = subscribed
        ? '/subscription_ajax?action_remove_subscriptions=1'
        : '/subscription_ajax?action_create_subscription_to_channel=1&c=' + encodeURIComponent(channelId);
      var body = 'session_token=' + encodeURIComponent('<?php echo yt_session_token() ?>')
               + (subscribed ? '&s=' + encodeURIComponent(channelId) : '');
      btn.setAttribute('data-busy', '1');
      var x = new XMLHttpRequest();
      x.open('POST', url, true);
      x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      x.onreadystatechange = function () {
        if (x.readyState !== 4) return;
        btn.removeAttribute('data-busy');
        if (x.status === 200) {
          btn.className = subscribed
            ? btn.className.replace(/(^|\s)subscribed(?=\s|$)/g, ' ').replace(/\s+/g, ' ')
            : btn.className + ' subscribed';
        }
      };
      x.send(body);
    }
  </script>
  <script>
    yt.setConfig({
        <?php /* rmktEnabled=false: иначе www-core шлёт ремаркетинг-пинг в
                 googleads.g.doubleclick.net при каждом открытии watch. */ ?>
        'CONVERSION_CONFIG_DICT': {"uid": "4QobU6STFB0P71PMvOGN5A", "rmktPingThreshold": 0, "rmktEnabled": false, "vid": "<?php echo $video_id ?>", "baseUrl": ""},
      'VIDEO_ID': "<?php echo $video_id ?>"    });
    <?php /* архивная разметка обнуляла токен (в захвате 2012 юзер был разлогинен);
             оставляем ЖИВОЙ токен, иначе like/dislike уходит с пустым и ловит "Bad token" */ ?>
    yt.setAjaxToken('watch_actions_ajax', "<?php echo yt_session_token() ?>");

    if (window['gYouTubePlayerReady']) {
      yt.registerGlobal('gYouTubePlayerReady');
    }
  </script>

    <?php if($videoisLive): ?>
  <script  src="//s.ytimg.com/yt/jsbin/www-watch-livestreaming-vfltcl4g5.js" data-loaded="true"></script>

    <script>
        yt.pubsub.subscribe('init', function() {
          new yt.www.livestreaming.ConcurrentViewers(30000)
          });
    </script>
    <?php endif; ?>

  <script>
    yt = yt || {};
      yt.playerConfig = <?php echo json_encode($ytPlayerConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
      yt.setConfig({
    'EMBED_HTML_TEMPLATE': "\u003ciframe width=\"__width__\" height=\"__height__\" src=\"__url__\" frameborder=\"0\" allowfullscreen\u003e\u003c\/iframe\u003e",
    'EMBED_HTML_URL': "http:\/\/www.youtube.com\/embed\/__videoid__"
  });
    yt.setMsg('FLASH_UPGRADE', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e  \u003cdiv class=\"yt-alert-icon\"\u003e\n    \u003cimg s\u0072c=\"\/yts\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\n  \u003c\/div\u003e\n\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\" role=\"alert\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n            You need to upgrade your Adobe Flash Player to watch this video. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eDownload it from Adobe.\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");
  yt.setMsg('PLAYER_FALLBACK', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e  \u003cdiv class=\"yt-alert-icon\"\u003e\n    \u003cimg s\u0072c=\"\/yts\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\n  \u003c\/div\u003e\n\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\" role=\"alert\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n            The Adobe Flash Player or an HTML5 supported browser is required for video playback. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eGet the latest Flash Player\u003c\/a\u003e \u003cbr\u003e \u003ca href=\"\/html5\"\u003eLearn more about upgrading to an HTML5 browser\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");
  yt.setMsg('QUICKTIME_FALLBACK', "\u003cdiv class=\"yt-alert yt-alert-default yt-alert-error  yt-alert-player\"\u003e  \u003cdiv class=\"yt-alert-icon\"\u003e\n    \u003cimg s\u0072c=\"\/yts\/img\/pixel-vfl3z5WfW.gif\" class=\"icon master-sprite\" alt=\"Alert icon\"\u003e\n  \u003c\/div\u003e\n\u003cdiv class=\"yt-alert-buttons\"\u003e\u003c\/div\u003e\u003cdiv class=\"yt-alert-content\" role=\"alert\"\u003e    \u003cspan class=\"yt-alert-vertical-trick\"\u003e\u003c\/span\u003e\n    \u003cdiv class=\"yt-alert-message\"\u003e\n            The Adobe Flash Player or QuickTime is required for video playback. \u003cbr\u003e \u003ca href=\"http:\/\/get.adobe.com\/flashplayer\/\"\u003eGet the latest Flash Player\u003c\/a\u003e \u003cbr\u003e \u003ca href=\"http:\/\/www.apple.com\/quicktime\/download\/\"\u003eGet the latest version of QuickTime\u003c\/a\u003e\n    \u003c\/div\u003e\n\u003c\/div\u003e\u003c\/div\u003e");


    (function() {
      var forceUpdate = yt.www.watch.player.updateConfig(yt.playerConfig);
      var youTubePlayer = yt.player.update('watch-player', yt.playerConfig,
          forceUpdate, gYouTubePlayerReady);
      yt.setConfig({'PLAYER_REFERENCE': youTubePlayer});
    })();
  </script>


<?php if (!$videoisLive): ?> 
      <script>
    yt.pubsub.subscribe("init", function() {
      yt.net.scriptloader.load("\/yts\/jsbin\/www-watch-transcript-vflGexCWv.js", function() {
        yt.www.watch.transcript.init();
      });
    });
  </script>
<?php endif; ?> 

  <script>
    yt.setConfig({
      'SUBSCRIBE_AXC': "",

      'IS_OWNER_VIEWING': <?php echo !empty($ytIsVideoOwner) ? 'true' : 'null' ?>,
      'IS_WIDESCREEN': false,
      'PREFER_LOW_QUALITY': false,
      'ALLOW_EMBED': true,
      'ALLOW_RATINGS': true,

      'LIST_AUTO_PLAY_ON': false,
      'LIST_AUTO_PLAY_VALUE': 1,
      'SHUFFLE_VALUE': 0,
      'SHUFFLE_ENABLED': false,
      'YPC_CAN_RATE_VIDEO': true,
      'YPC_SHOW_VPPA_CONFIRM_RATING': false,

        'TTS_URL': "\/api\/timedtext?sparams=caps%2Cv%2Cexpire\u0026hl=en_US\u0026v=<?php echo $video_id ?>\u0026caps=\u0026expire=1354786049\u0026key=yttt1\u0026signature=083CBA5C29117FFD6F087D63B86A897A5726A0F1.5559FC19BA63F32148836CCF07B8B4346C709966",





      'PLAYBACK_ID': "AATQJdc1-gxnOCVY",
      'PLAY_ALL_MAX': 480    });

    yt.setMsg({
        'HTML5_SUBS_ASR': "automatic captions",
      'LOADING': "Loading...",
      'WATCH_ERROR_MESSAGE': "This feature is not available right now. Please try again later."    });


      yt.setMsg({
    'UNBLOCK_USER': "Are you sure you want to unblock this user?",
    'BLOCK_USER': "Are you sure you want to block this user?"
  });
  yt.setConfig('BLOCK_USER_AJAX_XSRF', '');


    
  yt.setConfig({
    'COMMENTS_SIGNIN_URL': "https:\/\/accounts.google.com\/ServiceLogin?passive=true\u0026continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dcomments%26hl%3Den_US%26next%3D%252Fwatch%253Fv%253D<?php echo $video_id ?>%26nomobiletemp%3D1\u0026uilel=3\u0026hl=en_US\u0026service=youtube",
    'COMMENTS_THRESHHOLD': -5,
    'COMMENTS_PAGE_SIZE': 10,
    'COMMENTS_COUNT': <?php echo $commentsCountInt ?>,
    'COMMENTS_YPC_CAN_POST_OR_REACT_TO_COMMENT': true,
    'COMMENT_SOURCE': "w",
    'COMMENT_OPEN_REPLY_BOX' : false  });

  yt.setAjaxToken('link_ajax', "");
  <?php /* тот же архивный обнуляющий вызов: возвращаем живой токен, иначе
           отправка комментария уходит с пустым session_token → "FAILED" */ ?>
  yt.setAjaxToken('comment_servlet', "<?php echo yt_session_token() ?>");
  yt.setAjaxToken('comment_voting', "");

  yt.setMsg({
    'CHARACTERS_REMAINING': {"case1": "1 character remaining", "case0": "No characters remaining", "other": "# characters remaining"},
    'COMMENT_OK': "OK",
    'COMMENT_BLOCKED': "You have been blocked by the owner of this video.",
    'COMMENT_CAPTCHAFAIL': "The response to the letters on the image was not correct, please try again.",
    'COMMENT_PENDING': "Comment Pending Approval!",
    'COMMENT_ERROR_EMAIL': "Error, account unverified (see email)",
    'COMMENT_ERROR': "Error, try again",
    'COMMENT_FAILED_MAINTENANCE': "We're currently performing site maintenace, please try later.",
    'COMMENT_OWNER_LINKING': "Comments can't contain links, please put the link in your video description and refer to it in the comment.",
    'SECONDS_REMAINING': {"case1": "1 second remaining before you can post", "case0": "You can post again", "other": "# seconds remaining before you can post"}
  });

    yt.pubsub.subscribe('init', yt.www.comments.init);




      yt.setConfig('ENABLE_AUTO_LARGE', true);
      yt.www.watch.watch5.updatePlayerSize();
      yt.pubsub.subscribe('init', function() {
        yt.events.listen(window, 'resize',
            yt.www.watch.watch5.handleResize);
      });

    yt.pubsub.subscribe('init', yt.www.watch.activity.init);
    yt.pubsub.subscribe('init', yt.www.watch.player.init);
    yt.pubsub.subscribe('init', yt.www.watch.actions.init);
    yt.pubsub.subscribe('init', yt.www.watch.shortcuts.init);


    yt.pubsub.subscribe('init', function() {
      var description = _gel('watch-description');
      if (!_hasclass(description, 'yt-uix-expander-collapsed')) {
        yt.www.watch.watch5.handleToggleDescription(description);
      }
    });

      yt.pubsub.subscribe('init', yt.tracking.resolution);


    













    
    
  </script>

  

  <script>
    yt.setConfig('PYV_REQUEST', false);
  </script>
    <script>
        yt.www.ads.pyv.showPpvOnWatch();
    </script>






  

      <script>
if (window.yt.timing) {yt.timing.tick("js_page");}    </script>

        <script>
yt.setConfig('TIMING_ACTION', "watch5");    </script>





  <script>yt.setConfig('THUMB_DELAY_LOAD_BUFFER', 0);</script>

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
    'DRAGDROP_BINARY_URL': "\/yts\/jsbin\/www-dragdrop-vflF34jmY.js",
    'PLAYLIST_BAR_PLAYING_INDEX': -1  });

    yt.setAjaxToken('addto_ajax_logged_out', "3lwuCuo5xCDgoTtgJxjNsVkouKB8MTM1NDg0NzI0OUAxMzU0NzYwODQ5");

    yt.www.lists.init();









    yt.setConfig({'SBOX_JS_URL': "\/yts\/jsbin\/www-searchbox-vfl0q4fjj.js",'SBOX_SETTINGS': {"CLOSE_ICON_URL": "\/yts\/img\/icons\/close-vflrEJzIW.png", "SHOW_CHIP": false, "PSUGGEST_TOKEN": null, "REQUEST_DOMAIN": "us", "EXPERIMENT_ID": -1, "SESSION_INDEX": null, "HAS_ON_SCREEN_KEYBOARD": false, "CHIP_PARAMETERS": {}, "REQUEST_LANGUAGE": "en"},'SBOX_LABELS': {"SUGGESTION_DISMISS_LABEL": "Dismiss", "SUGGESTION_DISMISSED_LABEL": "Suggestion dismissed"}});





        yt.setConfig('FEED_PRIVACY_CSS_URL', "\/yts\/cssbin\/www-feedprivacydialog-vflUOqWgf.css");
  yt.setAjaxToken('feed_privacy_ajax', "");
    yt.pubsub.subscribe('init', yt.www.account.FeedPrivacyDialog.init);

  </script>

  <script>
    yt.setMsg({
      'ADDTO_WATCH_LATER_ADDED': "Added",
      'ADDTO_WATCH_LATER_ERROR': "Error"
    });
  </script>

  

      <script>
if (window.yt.timing) {yt.timing.tick("js_foot");}    </script>



  <div id="debug">
    
  </div>





<?php if (!empty($ytIsVideoOwner)): ?>
        <link rel="stylesheet" href="/yts/cssbin/www-watch-inlineedit-vflwS811J.css">
    <script src="/yts/jsbin/www-watch-edit-vflGVJE5d.js"></script>
  <script>
    yt.setConfig({
      'IS_OWNER_VIEWING': true
    });

    /* Exactly as in 2012 watch page: new yt.www.watch.Edit() on init */
    function ytWatchEditInit() {
      if (yt.www && yt.www.watch && typeof yt.www.watch.Edit === 'function') {
        new yt.www.watch.Edit();
        return;
      }
      /* Fallback polyfill matching www-watch-edit-vflGVJE5d.js behavior */
      function $(id) { return document.getElementById(id); }
      function showEl(el) {
        if (!el) return;
        el.style.display = '';
        el.className = (el.className || '').replace(/\bhid\b/g, '').replace(/\s+/g, ' ').trim();
      }
      function hideEl(el) {
        if (!el) return;
        el.style.display = 'none';
        if (!/\bhid\b/.test(el.className || '')) el.className = ((el.className || '') + ' hid').trim();
      }
      function q(cls, root) {
        root = root || document;
        return root.querySelector ? root.querySelector('.' + cls) : null;
      }

      var titleH1 = $('watch-headline-title');
      var titleForm = $('watch-headline-title-form');
      var titleReset = $('watch-headline-title-reset');
      var descBox = $('watch-description');
      var descClip = $('watch-description-clip');
      var metaForm = $('watch-video-info-form');
      var metaReset = $('watch-video-info-reset');
      var privacyIcon = $('watch-privacy-icon');

      if (titleH1 && titleForm) {
        titleH1.style.cursor = 'pointer';
        titleH1.onclick = function() {
          hideEl(titleH1);
          showEl(titleForm);
          var inp = q('yt-uix-form-input-text', titleForm);
          if (inp) { try { inp.focus(); inp.select(); } catch (e) {} }
        };
      }
      if (titleReset && titleForm && titleH1) {
        titleReset.onclick = function() {
          hideEl(titleForm);
          showEl(titleH1);
          return false;
        };
      }
      function openMeta() {
        if (!metaForm) return;
        hideEl(descBox);
        showEl(metaForm);
        try { metaForm.scrollIntoView(); } catch (e) {}
        var ta = q('yt-uix-form-textarea', metaForm);
        if (ta) { try { ta.focus(); ta.select(); } catch (e) {} }
      }
      if (descClip) {
        descClip.style.cursor = 'pointer';
        descClip.onclick = function(e) {
          /* don't open when clicking expand/collapse buttons */
          var t = e.target || e.srcElement;
          if (t && (t.closest && (t.closest('#watch-description-toggle') || t.closest('button')))) return;
          openMeta();
        };
      }
      if (privacyIcon) {
        privacyIcon.onclick = function(e) {
          if (e && e.stopPropagation) e.stopPropagation();
          openMeta();
        };
      }
      if (metaReset && metaForm && descBox) {
        metaReset.onclick = function() {
          showEl(descBox);
          hideEl(metaForm);
          return false;
        };
      }
      if (titleForm) {
        titleForm.onsubmit = function() {
          /* let browser POST to /watch_inlineedit_ajax — no preventDefault in polyfill */
          return true;
        };
      }
    }

    if (yt.pubsub && yt.pubsub.subscribe) {
      yt.pubsub.subscribe('init', ytWatchEditInit);
    } else {
      /* if pubsub already fired or missing */
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ytWatchEditInit);
      } else {
        ytWatchEditInit();
      }
    }
  </script>
<?php endif; ?>

</body>
</html>