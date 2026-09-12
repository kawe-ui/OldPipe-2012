<?php
// ═══════════════════════════════════════════════════════════════════════════
//  results.php — страница поиска (архивная разметка 20120410, данные InnerTube)
//  Бэкенд: api/results_api.php -> $searchQuery, $searchResults[], $searchEstimated
// ═══════════════════════════════════════════════════════════════════════════
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/results_api.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/config.inc.php');
?>
<!doctype html>
<html lang="en"><head><script>
var yt = yt || {};yt.timing = yt.timing || {};yt.timing.tick = function(label, opt_time) {var timer = yt.timing['timer'] || {};if(opt_time) {timer[label] = opt_time;}else {timer[label] = new Date().getTime();}yt.timing['timer'] = timer;};yt.timing.info = function(label, value) {var info_args = yt.timing['info_args'] || {};info_args[label] = value;yt.timing['info_args'] = info_args;};yt.timing.info('e', '920102');yt.timing.tick('start');yt.timing.info('li','0');try {yt.timing['srt'] = window.gtbExternal && window.gtbExternal.pageT() ||window.external && window.external.pageT;} catch(e) {}if (window.chrome && window.chrome.csi) {yt.timing['srt'] = Math.floor(window.chrome.csi().pageT);}if (window.msPerformance && window.msPerformance.timing) {yt.timing['srt'] = window.msPerformance.timing.responseStart - window.msPerformance.timing.navigationStart;}    </script>

    <script>
        function tn_load(index) {
    if (window.yt.timing && yt.timing.handleThumbnailLoad){
      yt.timing.handleThumbnailLoad(index);
    }
  }

    </script>
  <script>
    var yt = yt || {};
    yt.toTranslate = yt.toTranslate || [];
  </script>

    <title><?php echo $searchQuery !== '' ? htmlspecialchars($searchQuery) . ' - ' : '' ?>YouTube</title>

    
  <link rel="search" type="application/opensearchdescription+xml" href="https://web.archive.org/web/20120410191153/http://www.youtube.com/opensearch?locale=en_US" title="YouTube Video Search">

  <link rel="icon" href="https://web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/favicon-refresh-vfldLzJxy.ico" type="image/x-icon">
  <link rel="shortcut icon" href="https://web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/favicon-refresh-vfldLzJxy.ico" type="image/x-icon"> 
    <link rel="alternate" media="handheld" href="https://web.archive.org/web/20120410191153/http://m.youtube.com/results?desktop_uri=%2Fresults%3Fsearch_query%3Dminecraft&amp;search_query=minecraft&amp;gl=US">
  
    <meta name="description" content="Share your videos with friends, family, and the world">

    <meta name="keywords" content="video, sharing, camera phone, video phone, free, upload">

  

    <link id="css-617957165" rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">

    <link id="www-core-css" rel="stylesheet" href="https://web.archive.org/web/20120410191153cs_/http://s.ytimg.com/yt/cssbin/www-refresh-vflj_nHFo.css">









          <script>
      if (window.yt.timing) {
        yt.timing.tick('resultscss');
      }
    </script>



        <script>
      if (window.yt.timing) {
        yt.timing.tick('ct');
      }
    </script>


<style type="text/css">.gssb_c{border:0;position:absolute;z-index:989}.gssb_e{border:1px solid #ccc;border-top-color:#d9d9d9;box-shadow:0 2px 4px rgba(0,0,0,0.2);-webkit-box-shadow:0 2px 4px rgba(0,0,0,0.2);cursor:default}.gssb_f{visibility:hidden;white-space:nowrap}.gssb_k{border:0;display:block;position:absolute;top:0;z-index:988}.gsib_a{width:100%;padding:4px 6px 0}.gsib_a,.gsib_b{vertical-align:top}.gssb_a{padding:0 7px}.gssb_a,.gssb_a td{white-space:nowrap;overflow:hidden;line-height:22px}#gssb_b{font-size:11px;color:#36c;text-decoration:none}#gssb_b:hover{font-size:11px;color:#36c;text-decoration:underline}.gssb_m{color:#000;background:#fff}.gssb_g{text-align:center;padding:8px 0 7px;position:relative}.gssb_h{font-size:15px;height:28px;margin:0.2em;-webkit-appearance:button}.gssb_i{background:#eee}.gss_ifl{visibility:hidden;padding-left:5px}.gssb_i .gss_ifl{visibility:visible}a.gssb_j{font-size:13px;color:#36c;text-decoration:none;line-height:100%}a.gssb_j:hover{text-decoration:underline}.gssb_l{height:1px;background-color:#e5e5e5}.gscl_a{font-size:10px;color:#03c;text-decoration:underline;white-space:nowrap}.gsq_a{padding:0}.gspr_a{padding-right:1px}a.gspqs_a{padding:0 3px 0 8px}.gspqs_b{color:#666;line-height:22px}.gsfe_a{border:1px solid #b9b9b9;border-top-color:#a0a0a0;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);-moz-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);}.gsfe_b{border:1px solid #4d90fe;outline:none;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-moz-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);}.gsok_a{background:url(data:image/gif;base64,R0lGODlhEwALAKECAAAAABISEv///////yH5BAEKAAIALAAAAAATAAsAAAIdDI6pZ+suQJyy0ocV3bbm33EcCArmiUYk1qxAUAAAOw==) no-repeat center;cursor:pointer;display:inline-block;height:11px;line-height:0;margin:0 3px;width:19px}.gsst_a{display:block;line-height:13px}.gsst_a:hover{text-decoration:none!important}.gsst_b{width:3px}.gsst_c{width:1px}.gsst_d{width:7px}.gsst_e{filter:alpha(opacity=55);opacity:0.55}.gsst_a:hover .gsst_e{filter:alpha(opacity=72);opacity:0.72}.gsst_a:active .gsst_e{filter:alpha(opacity=100);opacity:1}.gsfi{font-size:13px}.gsfs{font-size:13px}a.gssb_j{font-size:12px;color:#03c}.gssb_a,.gssb_a td{line-height:17px}.gssb_a{padding:0 5px}.gssb_c{z-index:3000001}.gssb_i td{background:#eee}.gssb_k{z-index:3000000}.gsfe_b{border:1px solid #1c62b9;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);outline:none;}</style></head>
  <body id="" class="date-20120405 en_US ltr  " dir="ltr">




 

  <form name="logoutForm" method="POST" action="/web/20120410191153/http://www.youtube.com/">
    <input type="hidden" name="action_logout" value="1">
  <input name="session_token" type="hidden" value="tb3HJc_hol5xwRKbcnuBGg6sevh8MTMzNDE3MTUxM0AxMzM0MDg1MTEz"></form>
  <!-- begin page -->
  <div id="page" class="  search-base ">
      
  <div id="masthead-container">
    <!-- begin masthead -->
  <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>
  <div id="content-container">
    <!-- begin content -->
    <div id="content">
      
  <noscript>
      <div class="yt-alert yt-alert-default yt-alert-error  "><div class="yt-alert-icon"><img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon"></div>  <button type="button" onclick="" class="close yt-uix-close" data-close-parent-class="yt-alert">
Close
  </button>
<div class="yt-alert-content">    <span class="yt-alert-vertical-trick"></span>
    <div class="yt-alert-message">
          Hello, you seem to have JavaScript turned off.  Please enable it to see search results properly.
    </div>
</div></div>

  </noscript>

    <div id="search-header">
      <div id="search-header-inner">
            <p class="num-results">
<?php if ($searchEstimated !== ''): ?>About <strong><?php echo htmlspecialchars($searchEstimated) ?></strong> results<?php endif; ?>
    </p>

        <h2>
            Search results for
    <strong class="query"><span class="search-title-lego"><?php echo htmlspecialchars($searchQuery) ?></span></strong>

        </h2>
      </div>
      
      <hr class="yt-horizontal-rule">
  </div>

<?php
// ─── Sort by / Filter / связанные запросы — живые (InnerTube) ─────────────────
$srSortLabels = [
    ''                    => 'Relevance',
    'video_date_uploaded' => 'Upload date',
    'video_view_count'    => 'View count',
    'video_avg_rating'    => 'Rating',
];
$srQEnc  = urlencode($searchQuery);
$srSortHref = function (string $sort) use ($srQEnc): string {
    return '/results?search_type=videos&amp;search_query=' . htmlspecialchars($srQEnc, ENT_QUOTES)
         . ($sort !== '' ? '&amp;search_sort=' . $sort : '');
};
// фильтры 2012 работали дописыванием «, filter» к запросу — сохраняем протокол
$srFilterHref = function (string $filter) use ($srQEnc): string {
    return '/results?search_query=' . htmlspecialchars($srQEnc . urlencode(', ' . $filter), ENT_QUOTES);
};
$srChipHref = function (string $q): string {
    return '/results?search_query=' . htmlspecialchars(urlencode($q), ENT_QUOTES);
};
$srPixel = '/yts/img/pixel-vfl3z5WfW.gif';
$srChipsTop  = array_slice($searchRefinements, 0, 10);
$srChipsMore = array_slice($searchRefinements, 10);
?>
  <div id="search-refinements">
        <div id="lego-refine-block">
        <div class="sort-by floatR">

    <span class="sort-by-title">Sort by:</span>
    <button type="button" class=" yt-uix-button yt-uix-button-text" onclick=";return false;" role="button" aria-pressed="false" aria-expanded="false" aria-haspopup="true"><span class="yt-uix-button-content"><?php echo $srSortLabels[$searchSort] ?> </span><img class="yt-uix-button-arrow" src="<?php echo $srPixel ?>" alt=""><ul class="yt-uix-button-menu yt-uix-button-menu-text" role="menu" aria-haspopup="true" style="display: none;"><?php foreach ($srSortLabels as $srKey => $srLabel): if ($srKey === $searchSort) continue; ?><li role="menuitem"><span href="<?php echo $srSortHref($srKey) ?>" class=" yt-uix-button-menu-item" onclick=";window.location.href=this.getAttribute('href');return false;"><?php echo $srLabel ?></span></li><?php endforeach; ?></ul></button>
  </div>

        <button type="button" id="lego-refine-toggle" onclick="var p=document.getElementById('search-lego-refinements');if(p)p.className=p.className.indexOf('hid')>=0?p.className.replace('hid',''):p.className+' hid';return false;" class=" yt-uix-button yt-uix-button-text" role="button"><span class="yt-uix-button-content">Filter </span><img class="yt-uix-button-arrow" src="<?php echo $srPixel ?>" alt=""></button>

<?php if (!empty($srChipsTop)): ?>
        <ul class="single-line-lego-list">
<?php foreach ($srChipsTop as $srChip):
        $srLbl  = htmlspecialchars($srChip['label'], ENT_QUOTES);
        $srBoth = '/results?search_query=' . htmlspecialchars($srQEnc . urlencode(', ' . $srChip['query']), ENT_QUOTES);
        $srOnly = $srChipHref($srChip['query']);
?><li><span class="lego lego-category " data-lego-name="<?php echo $srLbl ?>"><a class="lego-action" title="Search for <?php echo htmlspecialchars($searchQuery, ENT_QUOTES) ?>, <?php echo $srLbl ?>" href="<?php echo $srBoth ?>"><img src="<?php echo $srPixel ?>"></a><a class="lego-action-placeholder" title="Search for <?php echo htmlspecialchars($searchQuery, ENT_QUOTES) ?>, <?php echo $srLbl ?>" href="<?php echo $srBoth ?>"><img src="<?php echo $srPixel ?>"></a><a class="lego-content" title="Search for <?php echo $srLbl ?>" href="<?php echo $srOnly ?>"><?php echo htmlspecialchars(mb_strtolower($srChip['label'])) ?></a></span></li><?php endforeach; ?>        </ul>
<?php endif; ?>


          <div id="search-lego-refinements" class="hid">
    <div class="search-refinements-block search-refinements-links">
      <div class="search-refinements-block-title">Sort by</div>
<ul><?php foreach ($srSortLabels as $srKey => $srLabel): ?><li><?php if ($srKey === $searchSort): ?>    <span class="lego-content-selected"><?php echo $srLabel ?></span>
<?php else: ?>    <a href="<?php echo $srSortHref($srKey) ?>"><?php echo $srLabel ?></a>
<?php endif; ?></li><?php endforeach; ?></ul>    </div>
        <div class="search-refinements-block filters">
      <div class="search-refinements-block-title">Filter</div>
<ul><?php foreach (['today' => 'uploaded today', 'this week' => 'uploaded this week', 'this month' => 'uploaded this month'] as $srF => $srFL): ?><li><span class="lego lego-property  append-lego" data-lego-name="<?php echo $srF ?>"><a class="lego-content" title="Search for <?php echo htmlspecialchars($searchQuery, ENT_QUOTES) ?>, <?php echo $srF ?>" href="<?php echo $srFilterHref($srF) ?>"><?php echo $srFL ?></a></span></li><?php endforeach; ?></ul>    </div>

        <div class="search-refinements-block filters">
      <div class="search-refinements-block-title">&nbsp;</div>
<ul><?php foreach (['channel' => 'channel', 'playlist' => 'playlist', 'movie' => 'movie', 'show' => 'show', '3d' => '3D'] as $srF => $srFL): ?><li><span class="lego lego-property  append-lego" data-lego-name="<?php echo $srF ?>"><a class="lego-content" title="Search for <?php echo htmlspecialchars($searchQuery, ENT_QUOTES) ?>, <?php echo $srF ?>" href="<?php echo $srFilterHref($srF) ?>"><?php echo $srFL ?></a></span></li><?php endforeach; ?></ul>    </div>

        <div class="search-refinements-block filters">
      <div class="search-refinements-block-title">&nbsp;</div>
<ul><?php foreach (['hd' => 'HD (high definition)', 'cc' => 'CC (closed caption)', 'long' => 'longer than 20 min', 'creativecommons' => 'creative commons', 'live' => 'live'] as $srF => $srFL): ?><li><span class="lego lego-property  append-lego" data-lego-name="<?php echo $srF ?>"><a class="lego-content" title="Search for <?php echo htmlspecialchars($searchQuery, ENT_QUOTES) ?>, <?php echo $srF ?>" href="<?php echo $srFilterHref($srF) ?>"><?php echo $srFL ?></a></span></li><?php endforeach; ?></ul>    </div>

<?php if (!empty($srChipsMore)): ?>
          <div class="search-refinements-block ">
      <div class="search-refinements-block-title">Explore</div>
<ul><?php foreach ($srChipsMore as $srChip): $srLbl = htmlspecialchars($srChip['label'], ENT_QUOTES); ?><li><span class="lego lego-category " data-lego-name="<?php echo $srLbl ?>"><a class="lego-content" title="Search for <?php echo $srLbl ?>" href="<?php echo $srChipHref($srChip['query']) ?>"><?php echo htmlspecialchars(mb_strtolower($srChip['label'])) ?></a></span></li><?php endforeach; ?></ul>    </div>
<?php endif; ?>

    <div class="clearL"></div>   </div>

    </div>

  </div>

      <div class="yt-horizontal-rule ">
    <span class="first"></span>
    <span class="second"></span>
    <span class="third"></span>
  </div>




  <div id="search-base-div">
    <div id="search-main" class="ytg-4col new-snippets">
<div id="translate-checkbox-container" class="hid"><label id="translate-checkbox-label" for="translate-checkbox"><input id="translate-checkbox" onclick="yt.www.translation.translateAll(&quot;en&quot;, &quot;session_token=xyXLe2esa55st_hIlYL_kiljNG58MEAxMzM0MDg1MTEz&quot;)" type="checkbox" style="vertical-align:middle; padding:0px; margin: -1px 3px 0 0;"><b>Translate results</b> into my language</label></div>
      <div id="results-main-content">
                
<?php if (false): /* архивный блок (реклама/пейджер/refinements под чужой запрос) — скрыт */ ?>
    <div class="promoted-videos list-view pyv-promoted-videos">
        <div class="result-item yt-uix-tile yt-tile-default">
    <div class="thumb-container">
            <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCQ5_e-YWET_e2GaXW2QXx57jmDZrZz6EC4sDM8ya7trjlMAgAEAEgnuvFDigCUMy3qVRgyQbIAQGpAge_YJf5urA-qgQeT9A9cwER0MXcg6X1wxJX2jBzk9PbUNZyn-_rVHdIoAYa%26sig%3DAOD64_1-lPkDn9voXAqLsprFVnTwT7ZG9g%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2FGIZWABBY%253Fv%253DxYUCiHjdVnc%26ctype%3D21%26video_id%3DxYUCiHjdVnc&amp;adtype=pyv&amp;event=ad&amp;usg=0u3VVJ3mL6XyAf8hyONroyFuoyY=" class="ux-thumb-wrap result-item-thumb">
    <span class="video-thumb ux-thumb ux-thumb-128 "><span class="clip"><span class="clip-inner"><img src="https://web.archive.org/web/20120410191153im_/http://i1.ytimg.com/vi/xYUCiHjdVnc/1.jpg" alt="Thumbnail
"><span class="vertical-align"></span></span></span></span>
      <span class="video-time">19:52</span>
  </a>


    </div>
    <div class="result-item-main-content">
        <h3 dir="ltr">
    <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCQ5_e-YWET_e2GaXW2QXx57jmDZrZz6EC4sDM8ya7trjlMAgAEAEgnuvFDigCUMy3qVRgyQbIAQGpAge_YJf5urA-qgQeT9A9cwER0MXcg6X1wxJX2jBzk9PbUNZyn-_rVHdIoAYa%26sig%3DAOD64_1-lPkDn9voXAqLsprFVnTwT7ZG9g%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2FGIZWABBY%253Fv%253DxYUCiHjdVnc%26ctype%3D21%26video_id%3DxYUCiHjdVnc&amp;adtype=pyv&amp;event=ad&amp;usg=0u3VVJ3mL6XyAf8hyONroyFuoyY=" title="" class="yt-uix-tile-link"><b>Minecraft</b></a>
  </h3>

            <p class="search-ad-description">
        <span dir="ltr">Noob training with Kentucky Fried</span>
        <br>
        <span dir="ltr">Chaos Part 1</span>
      </p>

      



  <p class="facets">
      <span class="ads-by" dir="ltr">by <a href="/web/20120410191153/http://www.youtube.com/user/GIZWABBY?feature=pvchclk">GIZWABBY</a></span>
<span class="metadata-separator">|</span>    <strong dir="ltr">50,244 views</strong>

  </p>

    </div>
  </div>
  

        <div class="result-item yt-uix-tile yt-tile-default">
    <div class="thumb-container">
            <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCKJyb-YWET_e2GaXW2QXx57jmDeL9rKwDiubbqzu7trjlMAgAEAIgnuvFDigCUMaFt5v8_____wFgyQbIAQGqBBtP0D1zARLQxdyjzNqIDx_a02FUnEJcsHXEE5-gBho%26sig%3DAOD64_3L3NgZdd_eF_J7VGaxRPh5Ev04pw%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2Fvsgamerz%253Fv%253DKw4JAymeKjk%2526feature%253Dpyv%2526ad%253D15840610770%2526kw%253Dminecraft%26ctype%3D21%26video_id%3DKw4JAymeKjk&amp;adtype=pyv&amp;event=ad&amp;usg=pvd-cMXhpNUZTtxiS2YapQt_si4=" class="ux-thumb-wrap result-item-thumb">
    <span class="video-thumb ux-thumb ux-thumb-128 "><span class="clip"><span class="clip-inner"><img src="https://web.archive.org/web/20120410191153im_/https://i4.ytimg.com/vi/Kw4JAymeKjk/3.jpg" alt="Thumbnail
"><span class="vertical-align"></span></span></span></span>
      <span class="video-time">14:09</span>
  </a>


    </div>
    <div class="result-item-main-content">
        <h3 dir="ltr">
    <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCKJyb-YWET_e2GaXW2QXx57jmDeL9rKwDiubbqzu7trjlMAgAEAIgnuvFDigCUMaFt5v8_____wFgyQbIAQGqBBtP0D1zARLQxdyjzNqIDx_a02FUnEJcsHXEE5-gBho%26sig%3DAOD64_3L3NgZdd_eF_J7VGaxRPh5Ev04pw%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2Fvsgamerz%253Fv%253DKw4JAymeKjk%2526feature%253Dpyv%2526ad%253D15840610770%2526kw%253Dminecraft%26ctype%3D21%26video_id%3DKw4JAymeKjk&amp;adtype=pyv&amp;event=ad&amp;usg=pvd-cMXhpNUZTtxiS2YapQt_si4=" title="" class="yt-uix-tile-link"><b>Minecraft</b> Let's Play</a>
  </h3>

            <p class="search-ad-description">
        <span dir="ltr"><b>Minecraft</b> 1.0</span>
        <br>
        <span dir="ltr">Gameplay</span>
      </p>

      



  <p class="facets">
      <span class="ads-by" dir="ltr">by <a href="/web/20120410191153/http://www.youtube.com/user/vsgamerz?feature=pvchclk">vsgamerz</a></span>
<span class="metadata-separator">|</span>    <strong dir="ltr">173,417 views</strong>

  </p>

    </div>
  </div>
  

    <a href="//web.archive.org/web/20120410191153/http://support.google.com/youtube/bin/answer.py?answer=143422&amp;topic=13660&amp;hl=en-US" class="promoted-videos-disclaimer">
      Ads
    </a>
  </div>
<?php endif; ?>





  
    

    

    

      <div id="search-results">
<?php if ($searchQuery === ''): ?>
            <p style="padding:20px">Type a search query above.</p>
<?php elseif ($searchError !== ''): ?>
            <p style="padding:20px">Search is currently unavailable. Please try again later.</p>
<?php elseif (empty($searchResults)): ?>
            <p style="padding:20px">No results found for &quot;<?php echo htmlspecialchars($searchQuery) ?>&quot;.</p>
<?php else: ?>
<?php foreach ($searchResults as $r): ?>
<?php if ($r['type'] === 'video'):
        $vid   = htmlspecialchars($r['id'], ENT_QUOTES);
        $wUrl  = '/watch?v=' . $vid;
        $tEsc  = htmlspecialchars($r['title'], ENT_QUOTES);
        $thumb = htmlspecialchars($r['thumbnail'], ENT_QUOTES);
        $chUrl = $r['authorId'] !== '' ? '/channel/' . htmlspecialchars($r['authorId'], ENT_QUOTES) : '/';
?>
            <div class="result-item yt-uix-tile yt-tile-default *sr">
    <div class="thumb-container">
      <a href="<?php echo $wUrl ?>" class="ux-thumb-wrap contains-addto result-item-thumb"><span class="video-thumb ux-thumb ux-thumb-128 "><span class="clip"><span class="clip-inner"><img alt="Thumbnail" src="<?php echo $thumb ?>"><span class="vertical-align"></span></span></span></span><?php if ($r['duration'] !== ''): ?><span class="video-time"><?php echo htmlspecialchars($r['duration']) ?></span><?php endif; ?>

  <button onclick=";return false;" title="Watch Later" type="button" class="addto-button video-actions addto-watch-later-button-sign-in yt-uix-button yt-uix-button-default yt-uix-button-short yt-uix-tooltip" data-video-ids="<?php echo $vid ?>" role="button"><span class="yt-uix-button-content">  <span class="addto-label">
Watch Later
  </span>
  <img src="/yts/img/pixel-vfl3z5WfW.gif">
 </span></button>
</a>
    </div>
    <div class="result-item-main-content">
  <h3><a href="<?php echo $wUrl ?>" class="yt-uix-tile-link" dir="ltr" title="<?php echo $tEsc ?>"><?php echo $tEsc ?></a></h3>
<?php if ($r['description'] !== ''): ?>
  <p class="description " dir="ltr"><?php echo htmlspecialchars(mb_strimwidth($r['description'], 0, 160, '...')) ?></p>
<?php endif; ?>
    <p class="facets">
    <span class="username-prepend">by</span>     <a href="<?php echo $chUrl ?>" class="yt-user-name " dir="ltr"><?php echo htmlspecialchars($r['author']) ?></a>
<?php if ($r['ago'] !== ''): ?><span class="metadata-separator">|</span>  <span class="date-added"><?php echo htmlspecialchars($r['ago']) ?></span>
<?php endif; ?>
<?php if ($r['views'] !== ''): ?><span class="metadata-separator">|</span>  <span class="viewcount"><?php echo htmlspecialchars($r['views']) ?></span>
<?php endif; ?>
  </p>
    </div>
  </div>

<?php elseif ($r['type'] === 'channel'):
        $chUrl  = '/channel/' . htmlspecialchars($r['id'], ENT_QUOTES);
        $tEsc   = htmlspecialchars($r['title'], ENT_QUOTES);
        $avatar = htmlspecialchars(default_avatar($r['avatar']), ENT_QUOTES);
?>
  <div class="result-item yt-uix-tile yt-tile-default *sr result-item-channel">
    <div class="thumb-container">
        <a href="<?php echo $chUrl ?>" class="ux-thumb-wrap result-item-thumb">  <span class="video-thumb ux-thumb yt-thumb-square-77 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="<?php echo $avatar ?>" alt="Thumbnail" width="77" onerror="this.onerror=null;this.src='/dynamic/pfp/default.png'"><span class="vertical-align"></span></span></span></span>
</a>
    </div>
    <div class="result-item-main-content">
      <h3><a class="yt-uix-tile-link result-item-translation-title" dir="ltr" title="<?php echo $tEsc ?>" href="<?php echo $chUrl ?>"><?php echo $tEsc ?></a></h3>
        <p class="description result-item-translation-description "><?php echo htmlspecialchars(mb_strimwidth($r['description'], 0, 160, '...')) ?></p>
      <ul class="single-line-lego-list">
        <li>  <span class="yt-badge-std">CHANNEL</span>
</li>
    </ul>
    <p class="facets">
      <span class="username-prepend">by     <a href="<?php echo $chUrl ?>" class="yt-user-name " dir="ltr"><?php echo $tEsc ?></a>
</span>
<?php if ($r['videoCount'] !== ''): ?><span class="metadata-separator">|</span>  <span class="video-count">
    <?php echo htmlspecialchars($r['videoCount']) . "\n" ?>
  </span>
<?php endif; ?>
<?php if ($r['subscribers'] !== ''): ?><span class="metadata-separator">|</span>  <span class="channel-subscriber-count"><?php echo htmlspecialchars($r['subscribers']) ?></span>
<?php endif; ?>
  </p>
    </div>
  </div>

<?php elseif ($r['type'] === 'playlist'):
        $plId    = htmlspecialchars($r['id'], ENT_QUOTES);
        $plUrl   = '/playlist?list=' . $plId;
        $tEsc    = htmlspecialchars($r['title'], ENT_QUOTES);
        $chUrl   = $r['authorId'] !== '' ? '/channel/' . htmlspecialchars($r['authorId'], ENT_QUOTES) : '/';
        $plVids  = $r['videos'] ?? [];
        // Плитка 2012: большая обложка = 1-е видео, маленькие = 2-е, 3-е и снова
        // 1-е (со стрелкой). watch-ссылки несут list= для контекста плейлиста.
        $plFirst = $plVids[0]['id'] ?? '';
        $plWatch = function (string $vid) use ($plId): string {
            return '/watch?v=' . htmlspecialchars($vid, ENT_QUOTES) . '&amp;list=' . $plId;
        };
        $plHref  = $plFirst !== '' ? $plWatch($plFirst) . '&amp;feature=results_main&amp;playnext=1' : $plUrl;
        $plThumb = fn(string $vid) => 'https://i.ytimg.com/vi/' . htmlspecialchars($vid, ENT_QUOTES) . '/default.jpg';
        $plBadge = '/results?search_query=' . htmlspecialchars(urlencode($searchQuery . ', playlist'), ENT_QUOTES);
?>
  <div class="result-item playlist yt-uix-tile yt-tile-default *sr">
    <div class="thumb-container">
      <a class="playlist-thumb" href="<?php echo $plHref ?>"><span class="playlist-large-thumb"><span class="video-thumb ux-thumb ux-thumb-128 "><span class="clip"><span class="clip-inner"><img alt="Thumbnail" src="<?php echo $plFirst !== '' ? $plThumb($plFirst) : htmlspecialchars($r['thumbnail'], ENT_QUOTES) ?>"><span class="vertical-align"></span></span></span></span></span></a><?php if (isset($plVids[1])): ?><a href="<?php echo $plWatch($plVids[1]['id']) ?>"><span class="playlist-small-thumb "><span class="video-thumb ux-thumb ux-thumb-54 "><span class="clip"><span class="clip-inner"><img alt="Thumbnail" src="<?php echo $plThumb($plVids[1]['id']) ?>"><span class="vertical-align"></span></span></span></span></span></a><?php endif; ?><?php if (isset($plVids[2])): ?><a href="<?php echo $plWatch($plVids[2]['id']) ?>"><span class="playlist-small-thumb "><span class="video-thumb ux-thumb ux-thumb-54 "><span class="clip"><span class="clip-inner"><img alt="Thumbnail" src="<?php echo $plThumb($plVids[2]['id']) ?>"><span class="vertical-align"></span></span></span></span></span></a><?php endif; ?><?php if ($plFirst !== ''): ?><a href="<?php echo $plWatch($plFirst) ?>"><span class="playlist-small-thumb playlist-small-thumb-last"><span class="video-thumb ux-thumb ux-thumb-54 "><span class="clip"><span class="clip-inner"><img alt="Thumbnail" src="<?php echo $plThumb($plFirst) ?>"><span class="vertical-align"></span></span></span></span><span class="playlist-arrow"><img src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></span></span></a><?php endif; ?>
    </div>
    <div class="result-item-main-content">
      <h3><a href="<?php echo $plHref ?>" class="yt-uix-tile-link" dir="ltr" title="<?php echo $tEsc ?>"><?php echo $tEsc ?></a></h3>
<?php if (!empty($plVids)): ?>
        <ul class="playlist-videos">
<?php foreach (array_slice($plVids, 0, 3) as $plV): ?><li class="playlist-video"><a href="<?php echo $plWatch($plV['id']) ?>&amp;feature=results_video&amp;playnext=1" class="playlist-detail-title"><img class="tiny-video-icon" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><?php echo htmlspecialchars($plV['title']) ?></a></li><?php endforeach; ?>  </ul>
<?php endif; ?>

  <ul class="single-line-lego-list"><li><a href="<?php echo $plBadge ?>" class="yt-badge-std">playlist</a></li></ul>
    <p class="facets">
<?php if ($r['author'] !== ''): ?>    <span class="username-prepend">by</span>     <a href="<?php echo $chUrl ?>" class="yt-user-name " dir="ltr"><?php echo htmlspecialchars($r['author']) ?></a>
<?php endif; ?>
<?php if ($r['count'] !== ''): ?><span class="metadata-separator">|</span>  <a href="<?php echo $plUrl ?>" class="video-count">
    <?php echo htmlspecialchars($r['count']) . "\n" ?>
  </a>
<?php endif; ?>
  </p>
    </div>
  </div>

<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
  </div>

    





      
<?php if (false): /* архивный блок (реклама/пейджер/refinements под чужой запрос) — скрыт */ ?>
    <div class="promoted-videos list-view pyv-promoted-videos">
        <div class="result-item yt-uix-tile yt-tile-default">
    <div class="thumb-container">
            <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCQ5_e-YWET_e2GaXW2QXx57jmDZrZz6EC4sDM8ya7trjlMAgAEAEgnuvFDigCUMy3qVRgyQbIAQGpAge_YJf5urA-qgQeT9A9cwER0MXcg6X1wxJX2jBzk9PbUNZyn-_rVHdIoAYa%26sig%3DAOD64_1-lPkDn9voXAqLsprFVnTwT7ZG9g%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2FGIZWABBY%253Fv%253DxYUCiHjdVnc%26ctype%3D21%26video_id%3DxYUCiHjdVnc&amp;adtype=pyv&amp;event=ad&amp;usg=0u3VVJ3mL6XyAf8hyONroyFuoyY=" class="ux-thumb-wrap result-item-thumb">
    <span class="video-thumb ux-thumb ux-thumb-128 "><span class="clip"><span class="clip-inner"><img src="https://web.archive.org/web/20120410191153im_/http://i1.ytimg.com/vi/xYUCiHjdVnc/1.jpg" alt="Thumbnail
"><span class="vertical-align"></span></span></span></span>
      <span class="video-time">19:52</span>
  </a>


    </div>
    <div class="result-item-main-content">
        <h3 dir="ltr">
    <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCQ5_e-YWET_e2GaXW2QXx57jmDZrZz6EC4sDM8ya7trjlMAgAEAEgnuvFDigCUMy3qVRgyQbIAQGpAge_YJf5urA-qgQeT9A9cwER0MXcg6X1wxJX2jBzk9PbUNZyn-_rVHdIoAYa%26sig%3DAOD64_1-lPkDn9voXAqLsprFVnTwT7ZG9g%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2FGIZWABBY%253Fv%253DxYUCiHjdVnc%26ctype%3D21%26video_id%3DxYUCiHjdVnc&amp;adtype=pyv&amp;event=ad&amp;usg=0u3VVJ3mL6XyAf8hyONroyFuoyY=" title="" class="yt-uix-tile-link"><b>Minecraft</b></a>
  </h3>

            <p class="search-ad-description">
        <span dir="ltr">Noob training with Kentucky Fried</span>
        <br>
        <span dir="ltr">Chaos Part 1</span>
      </p>

      



  <p class="facets">
      <span class="ads-by" dir="ltr">by <a href="/web/20120410191153/http://www.youtube.com/user/GIZWABBY?feature=pvchclk">GIZWABBY</a></span>
<span class="metadata-separator">|</span>    <strong dir="ltr">50,244 views</strong>

  </p>

    </div>
  </div>
  

    <a href="//web.archive.org/web/20120410191153/http://support.google.com/youtube/bin/answer.py?answer=143422&amp;topic=13660&amp;hl=en-US" class="promoted-videos-disclaimer">
      Ads
    </a>
  </div>
<?php endif; ?>







      </div>
    </div>

<?php if (false): /* архивный блок (реклама/пейджер/refinements под чужой запрос) — скрыт */ ?>
    <div id="search-pva-content">

        <div id="search-pva" class="ytg-2col-b ytg-last yt-vertical-rule new-snippets">
      




        <div id="pyv-ads">
              <p class="ads-promoted">
      Ads
  </p>

      <div class="sidebar-ads promoted yt-uix-tile yt-tile-default">
          <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCgmRJ-YWET_e2GaXW2QXx57jmDZ7d7bcChoOd6y-7trjlMBABIJ7rxQ4oAlDMt6lUYMkGyAEBqQIHv2CX-bqwPqoEHk_QPXMBE9DF3L-k17YbV9oMHrns21DWcp_v61R3SKAGGg%26num%3D3%26sig%3DAOD64_0yuBDDd8p2RfvEMs_a4EZ4_fj5hQ%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2FGIZWABBY%253Fv%253DxYUCiHjdVnc%26ctype%3D21%26video_id%3DxYUCiHjdVnc&amp;adtype=pyv&amp;event=ad&amp;usg=qzVxVo_f4AWSowf_ozkD0L6Qo38=" class="ux-thumb-wrap result-item-thumb">
    <span class="video-thumb ux-thumb ux-thumb-128 "><span class="clip"><span class="clip-inner"><img src="https://web.archive.org/web/20120410191153im_/http://i1.ytimg.com/vi/xYUCiHjdVnc/default.jpg" alt="Thumbnail
"><span class="vertical-align"></span></span></span></span>
      <span class="video-time">19:52</span>
  </a>


    <div class="result-item-main-content">
        <h3 dir="ltr">
    <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCgmRJ-YWET_e2GaXW2QXx57jmDZ7d7bcChoOd6y-7trjlMBABIJ7rxQ4oAlDMt6lUYMkGyAEBqQIHv2CX-bqwPqoEHk_QPXMBE9DF3L-k17YbV9oMHrns21DWcp_v61R3SKAGGg%26num%3D3%26sig%3DAOD64_0yuBDDd8p2RfvEMs_a4EZ4_fj5hQ%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2FGIZWABBY%253Fv%253DxYUCiHjdVnc%26ctype%3D21%26video_id%3DxYUCiHjdVnc&amp;adtype=pyv&amp;event=ad&amp;usg=qzVxVo_f4AWSowf_ozkD0L6Qo38=" title="" class="yt-uix-tile-link"><b>Minecraft</b> Series</a>
  </h3>

            <p class="search-ad-description">
        <span dir="ltr">Noob Training Part 1</span>
        <span dir="ltr">Gizwabby teaches his Dad-who fails!</span>
      </p>

      



  <p class="facets">
      <span class="ads-by" dir="ltr">by <a href="/web/20120410191153/http://www.youtube.com/user/GIZWABBY?feature=pvchclk">GIZWABBY</a></span>
<span class="metadata-separator">|</span>    <strong dir="ltr">50,244 views</strong>

  </p>

    </div>
    
  </div>

      <div class="sidebar-ads promoted yt-uix-tile yt-tile-default">
          <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCT-5x-YWET_e2GaXW2QXx57jmDcay0ogCvoTMkCm7trjlMBACIJ7rxQ4oAlCr3qDjB2DJBsgBAakCB79gl_m6sD6qBB5P0D1zARTQxdzvtpDuHFfadGrG19tQ1nKf7-tUd0igBho%26num%3D4%26sig%3DAOD64_2anTIe3-AjLKtEo6RJKPvxiIEK_A%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2FGIZWABBY%253Fv%253D3Bxc0F2AuuA%26ctype%3D21%26video_id%3D3Bxc0F2AuuA&amp;adtype=pyv&amp;event=ad&amp;usg=L5USdF9q90nsLhXQFoX6L9F0uVg=" class="ux-thumb-wrap result-item-thumb">
    <span class="video-thumb ux-thumb ux-thumb-128 "><span class="clip"><span class="clip-inner"><img src="https://web.archive.org/web/20120410191153im_/http://i1.ytimg.com/vi/3Bxc0F2AuuA/default.jpg" alt="Thumbnail
"><span class="vertical-align"></span></span></span></span>
      <span class="video-time">19:25</span>
  </a>


    <div class="result-item-main-content">
        <h3 dir="ltr">
    <a href="/web/20120410191153/http://www.youtube.com/redirect?q=http%3A%2F%2Fwww.google.com%2Faclk%3Fsa%3DL%26ai%3DCT-5x-YWET_e2GaXW2QXx57jmDcay0ogCvoTMkCm7trjlMBACIJ7rxQ4oAlCr3qDjB2DJBsgBAakCB79gl_m6sD6qBB5P0D1zARTQxdzvtpDuHFfadGrG19tQ1nKf7-tUd0igBho%26num%3D4%26sig%3DAOD64_2anTIe3-AjLKtEo6RJKPvxiIEK_A%26adurl%3Dhttp%3A%2F%2Fwww.youtube.com%2Fuser%2FGIZWABBY%253Fv%253D3Bxc0F2AuuA%26ctype%3D21%26video_id%3D3Bxc0F2AuuA&amp;adtype=pyv&amp;event=ad&amp;usg=L5USdF9q90nsLhXQFoX6L9F0uVg=" title="" class="yt-uix-tile-link"><b>Minecraft</b></a>
  </h3>

            <p class="search-ad-description">
        <span dir="ltr"><b>Minecraft</b> with Kentucky Fried Chaos</span>
        <span dir="ltr">Series Part 1</span>
      </p>

      



  <p class="facets">
      <span class="ads-by" dir="ltr">by <a href="/web/20120410191153/http://www.youtube.com/user/GIZWABBY?feature=pvchclk">GIZWABBY</a></span>
<span class="metadata-separator">|</span>    <strong dir="ltr">75,333 views</strong>

  </p>

    </div>
    
  </div>


    <p class="ads-promoted">
      <a href="/web/20120410191153/http://www.youtube.com/advertise/adwords.html">
See your ad here »
      </a>
    </p>


        </div>

              





  

  <div id="ad_creative_1" class="ad-div " style="z-index: 1">
    <iframe id="ad_creative_iframe_1" height="250" width="300" scrolling="no" frameborder="0" style="z-index: 1" src="//web.archive.org/web/20120410191153/http://ad-g.doubleclick.net/adi/com.ytsrc.undef/default;sz=300x250;tile=1;dcopt=ist;klg=en;kt=K;kga=-1;kr=F;kw=minecraft;kgg=-1;kcr=us;dc_dedup=1;kmyd=ad_creative_1;ord=2406548216070813?"></iframe>
      <div style="font-size: 10px; padding-top: 3px;" class="alignC grayText">
          <a href="/web/20120410191153/http://www.youtube.com/t/ads_preferences">
Advertisement
          </a>
      </div>

    <script>
      (function() {
        var addTimestamp = (Math.floor(Math.random() * 1000) == 0);
        if (addTimestamp) {
          var kts = new Date().getTime();
          var iframeSrc = "//web.archive.org/web/20120410191153/http://ad-g.doubleclick.net/adi/com.ytsrc.undef/default;sz=300x250;tile=1;dcopt=ist;klg=en;kt=K;kga=-1;kr=F;kw=minecraft;kgg=-1;kcr=us;dc_dedup=1;kmyd=ad_creative_1;kts=" + kts + ";ord=2406548216070813?";
        } else {
          var iframeSrc = "//web.archive.org/web/20120410191153/http://ad-g.doubleclick.net/adi/com.ytsrc.undef/default;sz=300x250;tile=1;dcopt=ist;klg=en;kt=K;kga=-1;kr=F;kw=minecraft;kgg=-1;kcr=us;dc_dedup=1;kmyd=ad_creative_1;ord=2406548216070813?";
        }
        var adIframe = document.getElementById("ad_creative_iframe_1");
        adIframe.src = iframeSrc;
      })();
    </script>
  </div>

      <span class="yt-vertical-rule-main"></span>
  <span class="yt-vertical-rule-corner-top"></span>
  <span class="yt-vertical-rule-corner-bottom"></span>

  </div>


    </div>
<?php endif; ?>
  </div> 
  <div id="search-footer-box" class="searchFooterBox">
        

<?php if (false): /* архивный блок (реклама/пейджер/refinements под чужой запрос) — скрыт */ ?>
    <div class="yt-uix-pager" role="navigation">

        


<a href="/web/20120410191153/http://www.youtube.com/results?search_query=minecraft&amp;page=1" class="yt-uix-button yt-uix-pager-page-num yt-uix-pager-button yt-uix-button-toggled yt-uix-button-default" data-page="1" aria-label="Go to page 1"><span class="yt-uix-button-content">1</span></a>&nbsp;
        


<a href="/web/20120410191153/http://www.youtube.com/results?search_query=minecraft&amp;page=2" class="yt-uix-button yt-uix-pager-page-num yt-uix-pager-button yt-uix-button-default" data-page="2" aria-label="Go to page 2"><span class="yt-uix-button-content">2</span></a>&nbsp;
        


<a href="/web/20120410191153/http://www.youtube.com/results?search_query=minecraft&amp;page=3" class="yt-uix-button yt-uix-pager-page-num yt-uix-pager-button yt-uix-button-default" data-page="3" aria-label="Go to page 3"><span class="yt-uix-button-content">3</span></a>&nbsp;
        


<a href="/web/20120410191153/http://www.youtube.com/results?search_query=minecraft&amp;page=4" class="yt-uix-button yt-uix-pager-page-num yt-uix-pager-button yt-uix-button-default" data-page="4" aria-label="Go to page 4"><span class="yt-uix-button-content">4</span></a>&nbsp;
        


<a href="/web/20120410191153/http://www.youtube.com/results?search_query=minecraft&amp;page=5" class="yt-uix-button yt-uix-pager-page-num yt-uix-pager-button yt-uix-button-default" data-page="5" aria-label="Go to page 5"><span class="yt-uix-button-content">5</span></a>&nbsp;
        


<a href="/web/20120410191153/http://www.youtube.com/results?search_query=minecraft&amp;page=6" class="yt-uix-button yt-uix-pager-page-num yt-uix-pager-button yt-uix-button-default" data-page="6" aria-label="Go to page 6"><span class="yt-uix-button-content">6</span></a>&nbsp;
        


<a href="/web/20120410191153/http://www.youtube.com/results?search_query=minecraft&amp;page=7" class="yt-uix-button yt-uix-pager-page-num yt-uix-pager-button yt-uix-button-default" data-page="7" aria-label="Go to page 7"><span class="yt-uix-button-content">7</span></a>&nbsp;

        


<a href="/web/20120410191153/http://www.youtube.com/results?search_query=minecraft&amp;page=2" class="yt-uix-button yt-uix-pager-next yt-uix-pager-button yt-uix-button-default" data-page="2"><span class="yt-uix-button-content">Next »</span></a>&nbsp;
    </div>
<?php endif; ?>


  </div>


    </div>
    <!-- end content -->
  </div>
  <div id="footer-container">
    <!-- begin footer -->
        <script>
      if (window.yt.timing) {
        yt.timing.tick('foot_begin');
      }
    </script>

      <div id="footer">
        <div class="yt-horizontal-rule ">
    <span class="first"></span>
    <span class="second"></span>
    <span class="third"></span>
  </div>


    <div id="footer-logo">
      <a href="/web/20120410191153/http://www.youtube.com/" title="YouTube home">
        <img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="YouTube home">
      </a>
      
      <span id="footer-divider"></span>
    </div>
    <div id="footer-main">
        
  <div id="in-product-help" class="yt-uix-clickcard">
    <button type="button" id="help-button" onclick=";return false;" class="yt-uix-clickcard-target yt-uix-button-reverse yt-uix-button yt-uix-button-default" data-iph-anchor-text="More Help" data-orientation="vertical" data-iph-search-input-text="Search YouTube's Help Center" data-iph-search-button-text="Search" data-locale="en_US" data-iph-tracking="iph-questionmark" data-iph-title-text="Need Help on this page?" data-iph-topic-id="1699854" data-iph-css-url="//s.ytimg.com/yt/cssbin/www-helpie-vfl5UBTg9.css" data-iph-js-url="//s.ytimg.com/yt/jsbin/www-help-vflX4RLuS.js" role="button"><span class="yt-uix-button-content">  <img class="questionmark" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
  <span>Help</span>
  <img class="yt-uix-button-arrow" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
 </span></button>
    <div class="yt-uix-clickcard-content" id="help-target">  <p class="loading-spinner">
    <img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="">
Loading...
  </p>
</div>
  </div>

      <ul id="footer-links-primary">
        <li><a href="/web/20120410191153/http://www.youtube.com/t/about_youtube">About</a></li>
        <li><a href="/web/20120410191153/http://www.youtube.com/t/press">Press &amp; Blogs</a></li>
        <li><a href="/web/20120410191153/http://www.youtube.com/t/copyright_center">Copyright</a></li>
        <li><a href="/web/20120410191153/http://www.youtube.com/creators">Creators &amp; Partners</a></li>
        <li><a href="/web/20120410191153/http://www.youtube.com/t/advertising_overview">Advertising</a></li>
        <li><a href="/web/20120410191153/http://www.youtube.com/dev">Developers</a></li>
      </ul>


      <ul id="footer-links-secondary">
        <li><a href="/web/20120410191153/http://www.youtube.com/t/terms">Terms</a></li>
        <li><a href="https://web.archive.org/web/20120410191153/http://www.google.com/intl/en/policies/privacy/">Privacy</a></li>
        <li><a href="//web.archive.org/web/20120410191153/http://support.google.com/youtube/bin/request.py?contact_type=abuse&amp;hl=en-US">Safety</a></li>
        <li><a href="//web.archive.org/web/20120410191153/http://www.google.com/tools/feedback/intl/en/error.html" onclick="return yt.www.feedback.start(yt.getConfig('FEEDBACK_LOCALE_LANGUAGE'), yt.getConfig('FEEDBACK_LOCALE_EXTRAS'));" id="reportbug">Report a bug</a></li>
        <li><a href="/web/20120410191153/http://www.youtube.com/testtube">Try something new!</a></li>
      </ul>
        <ul class="pickers yt-uix-button-group" data-button-toggle-group="required">
      <li>  <button type="button" class=" yt-uix-button yt-uix-button-text" onclick="yt.www.masthead.loadPicker('language-picker', &quot;&quot;); return false;;return false;" data-button-toggle="true" data-button-menu-id="arrow" role="button"><span class="yt-uix-button-content">English </span><img class="yt-uix-button-arrow" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button>
</li>
      <li>  <button type="button" class=" yt-uix-button yt-uix-button-text" onclick="yt.www.masthead.loadPicker('region-picker', &quot;&quot;); return false;;return false;" data-button-toggle="true" data-button-menu-id="arrow" role="button"><span class="yt-uix-button-content">Worldwide </span><img class="yt-uix-button-arrow" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button>
</li>
      <li>  <button type="button" class=" yt-uix-button yt-uix-button-text" onclick="yt.www.masthead.loadPicker('safetymode-picker', &quot;&quot;);return false;" data-button-toggle="true" data-button-menu-id="arrow" role="button"><span class="yt-uix-button-content">Safety:
  <span class="yt-footer-safety-value">
Off
  </span>
 </span><img class="yt-uix-button-arrow" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button>
</li>
  </ul>
    <div id="picker-container"></div>
  <div id="picker-loading" style="display: none">Loading...</div>


    </div>
  </div>

        <script>
      if (window.yt.timing) {
        yt.timing.tick('foot_end');
      }
    </script>

    <!-- end footer -->
  </div>
    



  <div id="playlist-bar" class="hid passive editable" data-video-url="/watch?v=&amp;feature=BFql&amp;playnext=1&amp;list=QL" data-list-id="" data-list-type="QL">
    <div id="playlist-bar-bar-container">
      <div id="playlist-bar-bar">
        <div class="yt-alert yt-alert-naked yt-alert-success hid " id="playlist-bar-notifications"><div class="yt-alert-icon"><img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon"></div><div class="yt-alert-content"></div></div>
<span id="playlist-bar-info"><span class="playlist-bar-active playlist-bar-group"><button onclick=";return false;" title="Previous video" type="button" id="playlist-bar-prev-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" role="button"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-prev" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Previous video"></button><span class="playlist-bar-count"><span class="playing-index">0</span> / <span class="item-count">0</span></span><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-next-button" role="button"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-next" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button></span><span class="playlist-bar-active playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-autoplay-button" data-button-toggle="true" role="button"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-autoplay" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-shuffle-button" data-button-toggle="true" role="button"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-shuffle" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button></span><span class="playlist-bar-passive playlist-bar-group"><button onclick=";return false;" title="Play videos" type="button" id="playlist-bar-play-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" role="button"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-play" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Play videos"></button><span class="playlist-bar-count"><span class="item-count">0</span></span></span><span id="playlist-bar-title" class="yt-uix-button-group"><span class="playlist-title">Unsaved Playlist</span></span></span>
        <a id="playlist-bar-lists-back" href="#">
Return to active list
        </a>

<span id="playlist-bar-controls"><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-text yt-uix-button-empty" onclick=";return false;" id="playlist-bar-toggle-button" role="button"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-toggle" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button></span><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked yt-uix-button-reverse flip yt-uix-button yt-uix-button-text" onclick=";return false;" data-button-menu-id="playlist-bar-options-menu" data-button-has-sibling-menu="true" role="button"><span class="yt-uix-button-content">Options </span><img class="yt-uix-button-arrow" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button></span></span>      </div>
    </div>

<div id="playlist-bar-tray-container"><div id="playlist-bar-tray" class="yt-uix-slider yt-uix-slider-fluid"><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-prev" onclick="return false;"><img class="yt-uix-slider-prev-arrow" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Previous video"></button><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-next" onclick="return false;"><img class="yt-uix-slider-next-arrow" src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Next video"></button><div class="yt-uix-slider-body"><div id="playlist-bar-tray-content" class="yt-uix-slider-slide"><ol class="video-list"></ol><ol id="playlist-bar-help"><li class="empty playlist-bar-help-message">Your queue is empty. Add videos to your queue using this button: <img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="addto-button-help"><br> or <a href="https://web.archive.org/web/20120410191153/https://accounts.google.com/ServiceLogin?uilel=3&amp;service=youtube&amp;passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fresults%253Fsearch_query%253Dminecraft&amp;hl=en_US&amp;ltmpl=sso">sign in</a> to load a different list.</li></ol></div><div class="yt-uix-slider-shade-left"></div><div class="yt-uix-slider-shade-right"></div></div></div><div id="playlist-bar-save"></div><div id="playlist-bar-lists" class="dark-lolz"></div><div id="playlist-bar-loading"><img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Loading..."><span id="playlist-bar-loading-message">Loading...</span><span id="playlist-bar-saving-message" class="hid">Saving...</span></div><div id="playlist-bar-template" style="display: none;" data-video-thumb-url="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg"><!--<li class="playlist-bar-item yt-uix-slider-slide-unit __classes__" data-video-id="__video_encrypted_id__"><a href="__video_url__" title="__video_title__"><span class="video-thumb ux-thumb ux-thumb-96 "><span class="clip"><span class="clip-inner"><img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="__video_title__" data-thumb-manual="true" data-thumb="__video_thumb_url__" ><span class="vertical-align"></span></span></span></span><span class="screen"></span><span class="count"><strong>__list_position__</strong></span><span class="play"><img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif"></span><span class="yt-uix-button yt-uix-button-default delete"><img class="yt-uix-button-icon-playlist-bar-delete" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Delete"></span><span class="now-playing">Now playing</span><span dir="ltr" class="title"><span>__video_title__  <span class="uploader">by __video_display_name__</span>
</span></span><span class="dragger"></span></a></li>--></div><div id="playlist-bar-next-up-template" style="display: none;"><!--<div class="playlist-bar-next-thumb"><span class="video-thumb ux-thumb ux-thumb-64 "><span class="clip"><span class="clip-inner"><img src="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg" alt="Thumbnail
" ><span class="vertical-align"></span></span></span></span></div>--></div></div>      <div id="playlist-bar-options-menu" class="hid">

    <div id="playlist-bar-extras-menu">
        <ul>
      <li><span class="yt-uix-button-menu-item" data-action="clear">
Clear all videos from this list
      </span></li>
  </ul>

    </div>

    <ul>
      <li><span class="yt-uix-button-menu-item" onclick="window.location.href='//web.archive.org/web/20120410191153/http://support.google.com/youtube/bin/answer.py?answer=146749&amp;hl=en-US'">Learn more</span></li>
    </ul>
  </div>

  </div>


  
  <div id="shared-addto-watch-later-login" class="hid">
    <a href="https://web.archive.org/web/20120410191153/https://accounts.google.com/ServiceLogin?uilel=3&amp;service=youtube&amp;passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252F&amp;hl=en_US&lt;mpl=sso" class="sign-in-link">Sign in</a> to add this to a playlist

  </div>

  <div id="shared-addto-menu" style="display: none;" class="hid sign-in">
      <div class="addto-menu">
        <div id="addto-list-panel" class="menu-panel active-panel">
        <span class="yt-uix-button-menu-item yt-uix-tooltip sign-in" data-possible-tooltip="" data-tooltip-show-delay="750"><a href="https://web.archive.org/web/20120410191153/https://accounts.google.com/ServiceLogin?uilel=3&amp;service=youtube&amp;passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252F&amp;hl=en_US&lt;mpl=sso" class="sign-in-link">Sign in</a> to add this to a playlist
</span>

  </div>
  <div id="addto-list-saved-panel" class="menu-panel">
    <div class="panel-content">
      <div class="yt-alert yt-alert-naked yt-alert-success  "><div class="yt-alert-icon"><img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon"></div><div class="yt-alert-content">    <span class="yt-alert-vertical-trick"></span>
    <div class="yt-alert-message">
          
  <span class="message">Added to <span class="addto-title yt-uix-tooltip yt-uix-tooltip-reverse" title="More information about this playlist" data-tooltip-show-delay="750"></span></span>

    </div>
</div></div>
    </div>
  </div>
  <div id="addto-list-error-panel" class="menu-panel">
    <div class="panel-content">
      <img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
      <span class="error-details"></span>
      <a class="show-menu-link">Back to list</a>
    </div>
  </div>

        <div id="addto-note-input-panel" class="menu-panel">
    <div class="panel-content">
      <div class="yt-alert yt-alert-naked yt-alert-success  "><div class="yt-alert-icon"><img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon"></div><div class="yt-alert-content">    <span class="yt-alert-vertical-trick"></span>
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
      <img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
      <span>Saving note...</span>
    </div>
  </div>
  <div id="addto-note-saved-panel" class="menu-panel">
    <div class="panel-content">
      <img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
      <span class="message">Note added to:</span>
    </div>
  </div>
  <div id="addto-note-error-panel" class="menu-panel">
    <div class="panel-content">
      <img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
      <span class="message">Error adding note:</span>
      <ul class="error-details"></ul>
      <a class="add-note-link">Click to add a new note</a>
    </div>
  </div>
  <div class="close-note hid">
    <img src="//web.archive.org/web/20120410191153im_/http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="close-button">
  </div>

  </div>

  </div>



  </div>
  <!-- end page -->
    
    
    <script id="www-core-js" src="//web.archive.org/web/20120410191153js_/http://s.ytimg.com/yt/jsbin/www-core-vflYBNsF7.js"></script>


  <script>
        yt.setConfig({
      'XSRF_TOKEN': 'tb3HJc_hol5xwRKbcnuBGg6sevh8MTMzNDE3MTUxM0AxMzM0MDg1MTEz',
      'XSRF_FIELD_NAME': 'session_token'
    });
    yt.pubsub.subscribe('init', yt.www.xsrf.populateSessionToken);

    yt.setConfig('XSRF_REDIRECT_TOKEN', 'bMgQznLviXZF7rISnhKHuAtEntd8MTMzNDE3MTUxM0AxMzM0MDg1MTEz');

    yt.setConfig('LOGGED_IN', false);
    yt.setConfig('SESSION_INDEX', null);

    yt.setConfig('FEEDBACK_LOCALE_LANGUAGE', "en");
    yt.setConfig('FEEDBACK_LOCALE_EXTRAS', {"experiments": "920102", "accept_language": null});
  </script>

      <script>
      if (window.yt.timing) {
        yt.timing.tick('js_head');
      }
    </script>

          <script>
      if (window.yt.timing) {
        yt.timing.tick('resultsjs');
      }
    </script>



  <script>
    yt.pubsub.subscribe('init', function () {
      yt.www.search.init();
    });
    var gGoogleSuggest = true;

        function tn_load(index) {
    if (window.yt.timing && yt.timing.handleThumbnailLoad){
      yt.timing.handleThumbnailLoad(index);
    }
  }

  </script>


    <script>
    yt.net.ajax.setToken('subscription_ajax', "");
    yt.pubsub.subscribe('init', yt.www.subscriptions.SubscriptionButton.init);
  </script>





      <script>
      if (window.yt.timing) {
        yt.timing.tick('js_page');
      }
    </script>

        <script>
      yt.setConfig('TIMING_ACTION', 'results');
    </script>





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
    'DRAGDROP_BINARY_URL': "\/\/web.archive.org\/web\/20120410191153\/http:\/\/s.ytimg.com\/yt\/jsbin\/www-dragdrop-vflYeX-4D.js",
    'PLAYLIST_BAR_PLAYING_INDEX': -1,
    'LIST_COPY_ON_EDIT_ENABLED': false  });

    yt.net.ajax.setToken('addto_ajax_logged_out', "LcOhOmUfRAFIPfklOvvq_tSias58MEAxMzM0MDg1MTEz");

    yt.pubsub.subscribe('init', yt.www.lists.init);





        yt.www.thumbnaildelayload.init();





      yt.pubsub.subscribe('init', function() {
        yt.net.scriptloader.load("\/\/web.archive.org\/web\/20120410191153\/http:\/\/s.ytimg.com\/yt\/jsbin\/www-searchbox-vflqoCD_h.js", function() {
          
      if (_gel('masthead-search')) {
        yt.setTimeout(function() {
          searchbox.yt.install(_gel('masthead-search'),
              _gel('masthead-search')["search_query"],
              "en",
              "us",
              "close",
              false,
              '',
              '',
              null,
              "Suggestion dismissed",
              "Dismiss",
              -1,
              null);
        }, 100);
      }

        });
      });


  </script>

  <script>

    yt.setMsg({
      'ADDTO_WATCH_LATER_ADDED': "Added",
      'ADDTO_WATCH_LATER_ERROR': "Error"
    });

    yt.pubsub.subscribe('init', yt.www.lists.addtowatchlater.init);
  </script>

  

      <script>
      if (window.yt.timing) {
        yt.timing.tick('js_foot');
      }
    </script>


  



<iframe class="gssb_k" style="display: none; top: 46px; left: 0px; height: 0px;" allow="autoplay 'self'; fullscreen 'self'"></iframe><table cellspacing="0" cellpadding="0" class="gstl_0 gssb_c" style="width: 379px; display: none; top: 46px; left: 204px; position: absolute;"><tbody><tr><td class="gssb_f"></td><td class="gssb_e" style="width: 100%;"></td></tr></tbody></table></body></html>