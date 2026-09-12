<?php
// в”Ђв”Ђв”Ђ Backend: РґР°РЅРЅС‹Рµ Р°РєРєР°СѓРЅС‚Р° С‡РµСЂРµР· InnerTube (auth.inc: getAccountSwitcherEndpoint
//     + resolve_url РґР»СЏ РєР°РЅР°Р»Р° вЂ” РєСѓРєРё-РїСѓС‚СЊ; РґР»СЏ OAuth-РІС…РѕРґР° вЂ” СЃРµСЃСЃРёСЏ Google).
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/config.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php');

$ytAccount = yt_account_info();
if ($ytAccount === null) {
    // РЎС‚СЂР°РЅРёС†Р° РЅР°СЃС‚СЂРѕРµРє Р±РµР· РІС…РѕРґР° РЅРµ СЃСѓС‰РµСЃС‚РІСѓРµС‚ вЂ” РєР°Рє РІ 2012 СѓРІРѕРґРёРј РЅР° Р»РѕРіРёРЅ.
    header('Location: /auth/google/login?return=' . urlencode('/account'));
    exit;
}

$accName      = $ytAccount['name']  ?? '';
$accEmail     = $ytAccount['email'] ?? '';
$accChannelId = $ytAccount['channelId'] ?? '';
$accChannel   = $accChannelId !== '' ? '/channel/' . $accChannelId : '/';
$accAvatar    = default_avatar($ytAccount['avatar'] ?? '');
?>
<html dir="ltr"><head><script id="scriptload-559385626" src="//s.ytimg.com/yt/jsbin/www-searchbox-vflsHyn9f.js" data-loaded="true"></script>
        <title><?php echo htmlspecialchars($accName) ?> - Overview</title>
 <meta name="keywords" content="video, sharing, camera phone, video phone, free, upload">
  <meta property="og:image" content="/yts/img/youtube_logo_stacked-vfl225ZTx.png">
  <meta property="fb:app_id" content="87741124305">
    <meta property="og:image" content="https://cdn.eracast.cc/yt/imgbin/full-size-logo.png">



<script>
 var yt = yt || {}; yt.timing = yt.timing || {}; yt.timing.tick = function (label, opt_time) { var timer = yt.timing['timer'] || {}; if (opt_time) { timer[label] = opt_time; } else { timer[label] = new Date().getTime(); } yt.timing['timer'] = timer; }; yt.timing.info = function (label, value) { var info_args = yt.timing['info_args'] || {}; info_args[label] = value; yt.timing['info_args'] = info_args; }; yt.timing.info('e', "904821,919006,922401,920704,912806,913419,913546,913556,919349,919351,925109,919003,920201,912706"); if (document.webkitVisibilityState == 'prerender') { document.addEventListener('webkitvisibilitychange', function () { yt.timing.tick('start'); }, false); } yt.timing.tick('start'); yt.timing.info('li', '0'); try { yt.timing['srt'] = window.gtbExternal && window.gtbExternal.pageT() || window.external && window.external.pageT; } catch (e) { } if (window.chrome && window.chrome.csi) { yt.timing['srt'] = Math.floor(window.chrome.csi().pageT); } if (window.msPerformance && window.msPerformance.timing) { yt.timing['srt'] = window.msPerformance.timing.responseStart - window.msPerformance.timing.navigationStart; }    
</script>
<link rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">
<link id="www-core-css" rel="stylesheet" href="/yts/cssbin/www-guide-vflAio4Bl.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><meta http-equiv="origin-trial" content="AlK2UR5SkAlj8jjdEc9p3F3xuFYlF6LYjAML3EOqw1g26eCwWPjdmecULvBH5MVPoqKYrOfPhYVL71xAXI1IBQoAAAB8eyJvcmlnaW4iOiJodHRwczovL2RvdWJsZWNsaWNrLm5ldDo0NDMiLCJmZWF0dXJlIjoiV2ViVmlld1hSZXF1ZXN0ZWRXaXRoRGVwcmVjYXRpb24iLCJleHBpcnkiOjE3NTgwNjcxOTksImlzU3ViZG9tYWluIjp0cnVlfQ=="><meta http-equiv="origin-trial" content="Amm8/NmvvQfhwCib6I7ZsmUxiSCfOxWxHayJwyU1r3gRIItzr7bNQid6O8ZYaE1GSQTa69WwhPC9flq/oYkRBwsAAACCeyJvcmlnaW4iOiJodHRwczovL2dvb2dsZXN5bmRpY2F0aW9uLmNvbTo0NDMiLCJmZWF0dXJlIjoiV2ViVmlld1hSZXF1ZXN0ZWRXaXRoRGVwcmVjYXRpb24iLCJleHBpcnkiOjE3NTgwNjcxOTksImlzU3ViZG9tYWluIjp0cnVlfQ=="><meta http-equiv="origin-trial" content="A9nrunKdU5m96PSN1XsSGr3qOP0lvPFUB2AiAylCDlN5DTl17uDFkpQuHj1AFtgWLxpLaiBZuhrtb2WOu7ofHwEAAACKeyJvcmlnaW4iOiJodHRwczovL2RvdWJsZWNsaWNrLm5ldDo0NDMiLCJmZWF0dXJlIjoiQUlQcm9tcHRBUElNdWx0aW1vZGFsSW5wdXQiLCJleHBpcnkiOjE3NzQzMTA0MDAsImlzU3ViZG9tYWluIjp0cnVlLCJpc1RoaXJkUGFydHkiOnRydWV9"><meta http-equiv="origin-trial" content="A93bovR+QVXNx2/38qDbmeYYf1wdte9EO37K9eMq3r+541qo0byhYU899BhPB7Cv9QqD7wIbR1B6OAc9kEfYCA4AAACQeyJvcmlnaW4iOiJodHRwczovL2dvb2dsZXN5bmRpY2F0aW9uLmNvbTo0NDMiLCJmZWF0dXJlIjoiQUlQcm9tcHRBUElNdWx0aW1vZGFsSW5wdXQiLCJleHBpcnkiOjE3NzQzMTA0MDAsImlzU3ViZG9tYWluIjp0cnVlLCJpc1RoaXJkUGFydHkiOnRydWV9"><meta http-equiv="origin-trial" content="A1S5fojrAunSDrFbD8OfGmFHdRFZymSM/1ss3G+NEttCLfHkXvlcF6LGLH8Mo5PakLO1sCASXU1/gQf6XGuTBgwAAACQeyJvcmlnaW4iOiJodHRwczovL2dvb2dsZXRhZ3NlcnZpY2VzLmNvbTo0NDMiLCJmZWF0dXJlIjoiQUlQcm9tcHRBUElNdWx0aW1vZGFsSW5wdXQiLCJleHBpcnkiOjE3NzQzMTA0MDAsImlzU3ViZG9tYWluIjp0cnVlLCJpc1RoaXJkUGFydHkiOnRydWV9">

<script>
    </script>		<script>
			var yt = yt || {};yt.timing = yt.timing || {};yt.timing.tick = function(label, opt_time) {var timer = yt.timing['timer'] || {};if(opt_time) {timer[label] = opt_time;}else {timer[label] = new Date().getTime();}yt.timing['timer'] = timer;};yt.timing.info = function(label, value) {var info_args = yt.timing['info_args'] || {};info_args[label] = value;yt.timing['info_args'] = info_args;};yt.timing.info('e', "904821,919006,922401,920704,912806,913419,913546,913556,919349,919351,925109,919003,920201,912706");if (document.webkitVisibilityState == 'prerender') {document.addEventListener('webkitvisibilitychange', function() {yt.timing.tick('start');}, false);}yt.timing.tick('start');yt.timing.info('li','0');try {yt.timing['srt'] = window.gtbExternal && window.gtbExternal.pageT() ||window.external && window.external.pageT;} catch(e) {}if (window.chrome && window.chrome.csi) {yt.timing['srt'] = Math.floor(window.chrome.csi().pageT);}if (window.msPerformance && window.msPerformance.timing) {yt.timing['srt'] = window.msPerformance.timing.responseStart - window.msPerformance.timing.navigationStart;}    
		</script>
  <link id="css-617957165" rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">
    <link id="css-1807079883" rel="stylesheet" href="/yts/cssbin/www-guide-vflAio4Bl.css">
        <link rel="stylesheet" href="/ytС‹/cssbin/www-extra.css">
		<link rel="stylesheet" href="/yts/cssbin/www-videos-nav-vflYGt27y.css">
                    <link rel="stylesheet" href="//s.ytimg.com/yt/cssbin/www-refresh-static-vfl5mtBQc.css">
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
            <link rel="stylesheet" href="https://unpkg.com/leaflet.choropleth@1.0.1/dist/leaflet.choropleth.css">
            <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
            <script src="https://unpkg.com/leaflet.choropleth@1.0.1/dist/leaflet.choropleth.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/chroma-js/2.1.0/chroma.min.js"></script>
        		<script src="//s.ytimg.com/yt/jsbin/www-browse-vflu1nggJ.js" data-loaded="true"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
			if (window.yt.timing) {yt.timing.tick("ct");}   
        </script> 
        <style>
            .master-myaccount-top {
                border-bottom: 1px solid #CACACA;
            }

            .www-home-left a {
                padding-bottom: 3px;
                padding-top: 4px;
                font-weight: 700;
                text-align: left;
                color: black;
                padding-left: 2px;
                width: 193px;
                display: inline-block;
            }

            .www-home-left {
                width: 200px;
                border-right: 1px solid #aaa;
            }

            .www-home-right {
                width: 754px;
                padding: 5px;
            }

            .www-home-left a:hover {
                background-color: rgb(239, 239, 239);
                background: -moz-linear-gradient(0deg,rgb(192,192,192,1)0%,rgb(239,239,239,1)115%);
                background: -webkit-linear-gradient(0deg,rgb(192,192,192,1)0%,rgb(239,239,239,1)115%);
                background: linear-gradient(0deg,rgb(192,192,192)0%, rgb(239,239,239)115%);
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="c0c0c0",endColorstr="#efefef",GradientType=1);
            }

            a[href="/inbox/send"] {
                margin: 0px !important;
                padding: 0px !important;
                position: relative;
                top: 0px !important;
            }

            #search-button {
                margin: 0px;
                margin-bottom: 7px;
                margin-top: 4px;
            }
            
            .video-manager-info {
                width: 462px;
            }

            .video-filter-options a {
                margin-left: 5px;
                margin-right: 5px;
            }

            .selected {
                font-weight: bold;
                color: black;
            }

            .yt-dashboard-user {
                background:no-repeat url(/yts/imgbin/dashboard-user-1.png);
                width:40px;
                height:40px
            }

            .yt-dashboard-views {
                background:no-repeat url(/yts/imgbin/dashboard-views.png);
                width:40px;
                height:40px
            }

            .yt-dashboard-calendar {
                background:no-repeat url(/yts/imgbin/dashboard-calendar.png);
                width:40px;
                height:40px
            }

            .yt-dashboard-edit {
                display: ;
            }

            .yt-dashboard-edit:hover {
                display: block;
            }

            button>.yt-dashboard-arrow-left {
                background:no-repeat url(https://cdn.eracast.cc/yt/imgbin/www-refresh-vflMLqC23.png) -72px 0px;
                width:6px;
                height:9px
            }

            button>.yt-dashboard-arrow-right {
                background:no-repeat url(https://cdn.eracast.cc/yt/imgbin/www-refresh-vflMLqC23.png) -177px -77px;
                width:6px;
                height:9px
            }

            button>.yt-uix-button-arrow {
                border: none !important;
                background: no-repeat url(//s.ytimg.com/yt/imgbin/www-guide-vfl1t2Sk-.png) 0 -300px !important;
                width: 5px;
                height: 6px
            }

            
            .item-highlight {background:#fff;text-decoration:none;-moz-border-radius:0;-webkit-border-radius:0;border-radius:0;background-image:-moz-linear-gradient(right,#efefef 0,rgba(255,255,255,.9) 50px);background-image:-ms-linear-gradient(right,#efefef 0,rgba(255,255,255,.9) 50px);background-image:-o-linear-gradient(right,#efefef 0,rgba(255,255,255,.9) 50px);background-image:-webkit-gradient(linear,right top,left top,color-stop(0,#efefef),color-stop(50px,rgba(255,255,255,.9)));background-image:-webkit-linear-gradient(right,#efefef 0,rgba(255,255,255,.9) 50px);background-image:linear-gradient(to left,#efefef 0,rgba(255,255,255,.9) 50px)}

            .green-dot {
                width: 15px;
                height: 15px;
                border-radius: 50%;
                background: linear-gradient(#7ca46c, #496140);
                box-shadow: 0 0 0 3px #d1dad3;
                margin-right: 10px;
            }

            .yellow-dot {
                width: 15px;
                height: 15px;
                border-radius: 50%;
                background: linear-gradient(#FFFF66, #999900);
                box-shadow: 0 0 0 3px #d1dad3;
                margin-right: 10px;
            }
            
            .red-dot {
                width: 15px;
                height: 15px;
                border-radius: 50%;
                background: linear-gradient(#FF8080, #AA0000);
                box-shadow: 0 0 0 3px #d1dad3;
                margin-right: 10px;
            }

            .yt-vertical-rule-main-alt {
                position: absolute;
                height: 94px;
                width: 30px;
                margin-top: 6px;
                border: 0;
                background: url(//s.ytimg.com/yt/img/refresh/vertical_rule_ltr-vflkNhm_8.png) repeat-y;
                background: -webkit-linear-gradient(left,rgba(0,0,0,.12),rgba(0,0,0,.08) 1px,rgba(0,0,0,.08) 1px,rgba(0,0,0,0) 30px,transparent 100%);
                background: -moz-linear-gradient(left,rgba(0,0,0,.12),rgba(0,0,0,.08) 1px,rgba(0,0,0,.08) 1px,rgba(0,0,0,0) 30px,transparent 100%);
                background: -webkit-linear-gradient(left,rgba(0,0,0,.12),rgba(0,0,0,.08) 1px,rgba(0,0,0,.08) 1px,rgba(0,0,0,0) 30px,transparent 100%);
                background: -o-linear-gradient(left,rgba(0,0,0,.12),rgba(0,0,0,.08) 1px,rgba(0,0,0,.08) 1px,rgba(0,0,0,0) 30px,transparent 100%);
            }

            #ex2 .close-modal, #ex3 .close-modal, #ex4 .close-modal, #ex5 .close-modal {
                display: none;
            }

            #ex2, #ex3, #ex4, #ex5 {
                padding: 0;
                border-radius: 0 0 8px 8px;
            }

            #ex2>div, #ex3>div, #ex4>div, #ex5>div {
                padding: 20px 30px;
            }

            #watch7-headline #watch-privacy-icon {
                float: left;
                margin-right: 5px
            }

            #watch7-headline #watch-privacy-icon .privacy-icon {
                vertical-align: middle
            }

            #watch7-headline #watch-privacy-icon.unlisted .privacy-icon {
                background: no-repeat url(//s.ytimg.com/yts/imgbin/www-hitchhiker-vfllYIUv0.png) -123px -278px;
                background-size: auto;
                width: 24px;
                height: 20px
            }

            #watch7-headline #watch-privacy-icon.private .privacy-icon {
                background: no-repeat url(//s.ytimg.com/yts/imgbin/www-hitchhiker-vfllYIUv0.png) -100px -511px;
                background-size: auto;
                width: 24px;
                height: 20px
            }

            #watch7-headline #watch-privacy-icon.public .privacy-icon {
                background: no-repeat url(//s.ytimg.com/yts/imgbin/www-hitchhiker-vfllYIUv0.png) -204px -511px;
                background-size: auto;
                width: 24px;
                height: 20px
            }

            #watch7-headline #watch-privacy-icon.blocked .privacy-icon {
                background: no-repeat url(https://cdn.eracast.cc/yt/imgbin/blocked.jpg);
                background-size: auto;
                width: 24px;
                height: 20px
            }

            #watch7-headline #watch-privacy-icon.process .privacy-icon {
                background: no-repeat url(https://cdn.eracast.cc/s/img/spinner.gif);
                background-size: auto;
                width: 24px;
                height: 24px
            }

            .yt-sprite{display:inline-block}.yt-thumb{overflow:hidden;background:#f1f1f1;font-size:0;vertical-align:middle;display:inline-block}
            .yt-sprite{position:relative;vertical-align:middle}

            
            .yt-analytics-calendar {
                background: no-repeat url(/yt/imgbin/calender-31.png);
                background-size: contain;
                width: 20px;
                height: 20px;
            }

            .custom-list {
                list-style-type: none;
            }
            .custom-list li {
                display: flex;
                align-items: center;
            }
            .custom-list li::before {
                content: "вЂў";
                margin-right: 5px;
                margin-bottom: 20px;
                font-size: 30px;
                height: 10px;
            }
            .custom-list li:first-child::before {
                color: lightblue;
            }
            .custom-list li:nth-child(2)::before {
                color: orange;
            }
            .custom-list li:nth-child(3)::before {
                color: green;
            }
            .custom-list li:nth-child(4)::before {
                color: lightgray;
            }
                    </style>
	<style type="text/css">.gssb_c{border:0;position:absolute;z-index:989}.gssb_e{border:1px solid #ccc;border-top-color:#d9d9d9;box-shadow:0 2px 4px rgba(0,0,0,0.2);-webkit-box-shadow:0 2px 4px rgba(0,0,0,0.2);cursor:default}.gssb_f{visibility:hidden;white-space:nowrap}.gssb_k{border:0;display:block;position:absolute;top:0;z-index:988}.gsdd_a{border:none!important}.gsib_a{width:100%;padding:4px 6px 0}.gsib_a,.gsib_b{vertical-align:top}.gssb_a{padding:0 7px}.gssb_a,.gssb_a td{white-space:nowrap;overflow:hidden;line-height:22px}#gssb_b{font-size:11px;color:#36c;text-decoration:none}#gssb_b:hover{font-size:11px;color:#36c;text-decoration:underline}.gssb_m{color:#000;background:#fff}.gssb_g{text-align:center;padding:8px 0 7px;position:relative}.gssb_h{font-size:15px;height:28px;margin:0.2em;-webkit-appearance:button}.gssb_i{background:#eee}.gss_ifl{visibility:hidden;padding-left:5px}.gssb_i .gss_ifl{visibility:visible}a.gssb_j{font-size:13px;color:#36c;text-decoration:none;line-height:100%}a.gssb_j:hover{text-decoration:underline}.gssb_l{height:1px;background-color:#e5e5e5}.gscp_a,.gscp_c,.gscp_d,.gscp_e,.gscp_f{display:inline-block;vertical-align:bottom}.gscp_f{border:none}.gscp_a{background:#d9e7fe;border:1px solid #9cb0d8;cursor:default;outline:none;text-decoration:none!important;user-select:none;-webkit-user-select:none;}.gscp_a:hover{border-color:#869ec9}.gscp_a.gscp_b{background:#4787ec;border-color:#3967bf}.gscp_c{color:#444;font-size:13px;font-weight:bold}.gscp_d{color:#aeb8cb;cursor:pointer;font:21px arial,sans-serif;line-height:inherit;padding:0 7px}.gscp_d{position:relative;top:1px}.gscp_a:hover .gscp_d{color:#575b66}.gscp_c:hover,.gscp_a .gscp_d:hover{color:#222}.gscp_a.gscp_b .gscp_c,.gscp_a.gscp_b .gscp_d{color:#fff}.gscp_e{height:100%;padding:0 4px}a.gspqs_a{padding:0 3px 0 8px}.gspqs_b{color:#666;line-height:22px}.gspr_a{padding-right:1px}.gsq_a{padding:0}.gsfe_a{border:1px solid #b9b9b9;border-top-color:#a0a0a0;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);-moz-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.1);}.gsfe_b{border:1px solid #4d90fe;outline:none;box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-moz-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow:inset 0px 1px 2px rgba(0,0,0,0.3);}.gsok_a{background:url(data:image/gif;base64,R0lGODlhEwALAKECAAAAABISEv///////yH5BAEKAAIALAAAAAATAAsAAAIdDI6pZ+suQJyy0ocV3bbm33EcCArmiUYk1qxAUAAAOw==) no-repeat center;display:inline-block;height:11px;line-height:0;width:19px}.gsok_a img{border:none;visibility:hidden}.gsst_a{display:inline-block}.gsst_a{cursor:pointer;padding:0 4px}.gsst_a:hover{text-decoration:none!important}.gsst_b{font-size:16px;padding:0 2px;user-select:none;-webkit-user-select:none;white-space:nowrap}.gsst_e{opacity:0.55;}.gsst_a:hover .gsst_e,.gsst_a:focus .gsst_e{opacity:0.72;}.gsst_a:active .gsst_e{opacity:1;}.gsst_f{background:white;text-align:left}.gsst_g{background-color:white;border:1px solid #ccc;border-top-color:#d9d9d9;box-shadow:0 2px 4px rgba(0,0,0,0.2);-webkit-box-shadow:0 2px 4px rgba(0,0,0,0.2);margin:-1px -3px;padding:0 6px}.gsst_h{background-color:white;height:1px;margin-bottom:-1px;position:relative;top:-1px}.gsfi{font-size:16px}.gsfs{font-size:16px}a.gssb_j{font-size:12px;color:#03c}.gssb_a,.gssb_a td{line-height:20px}.gssb_a{padding:0 6px}.gssb_c{z-index:3000001}.gssb_i td{background:#eee}.gssb_k{z-index:3000000}.gssb_l{margin:2px 0}.gsib_a{padding:0 4px}.gsok_a{padding:0}.gsok_a img{display:block}.gsfe_b{border:1px solid #1c62b9;box-shadow:inset 0 1px 2px rgba(0,0,0,0.3);-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,0.3);outline:none;}a.gscp_a{position:relative;background:#e2e2e2;border:1px solid #bbb;border-radius:3px}.gsfe_a a.gscp_a{border-width:1px;border-style:solid;border-color:#bbb}a.gscp_a.gscp_b{border-color:#777!important;background:#999;outline:none}.gscp_c{color:#666;font-size:11px;font-weight:bold;padding-right:20px;text-shadow:0 1px 0 rgba(255, 255, 255, 0.5);-ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80ffffff,Positive=true)";zoom:1;filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80ffffff,Positive=true)}.gsfe_a a.gscp_a .gscp_c{color:#444}a.gscp_a.gscp_b .gscp_c,.gsfe_a a.gscp_a.gscp_b .gscp_c{color:#fff;text-shadow:0 1px 0 rgba(100, 100, 100, 0.5);-ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80646464,Positive=true)";zoom:1;filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#80646464,Positive=true)}.gscp_d{position:absolute;padding:0;background:url(//s.ytimg.com/yt/img/icons/close-vflrEJzIW.png);background-repeat:no-repeat;background-position-y:0;right:3px;top:6px;font-size:0;width:13px;height:13px}.gscp_d:hover{background-position-y:-13px}a.gscp_a.gscp_b .gscp_d{background-position-y:-26px}.gsfe_a a.gscp_a.gscp_b .gscp_d:hover{background-position-y:-39px}.gscp_f{background:#000}</style></head>
	<body id="" class="date-20120930 en_US ltr   ytg-old-clearfix guide-feed-v2 " dir="ltr">
		<ins class="adsbygoogle adsbygoogle-noablate" data-ad-hi="true" data-adsbygoogle-status="done" style="display: none !important;" data-ad-status="unfilled"><div id="aswift_0_host" style="border-width: medium; border-style: none; border-color: currentcolor; border-image: none; height: 0px; width: 0px; margin: 0px; padding: 0px; position: relative; visibility: visible; background-color: transparent; display: inline-block;"><iframe id="aswift_0" name="aswift_0" style="left:0;position:absolute;top:0;border:0;width:undefinedpx;height:undefinedpx;min-height:auto;max-height:none;min-width:auto;max-width:none;" sandbox="allow-forms allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts allow-top-navigation-by-user-activation" frameborder="0" marginwidth="0" marginheight="0" vspace="0" hspace="0" allowtransparency="true" scrolling="no" allow="attribution-reporting; run-ad-auction" src="https://googleads.g.doubleclick.net/pagead/ads?gdpr=0&amp;us_privacy=1---&amp;gpp_sid=-1&amp;client=ca-pub-6054359075708811&amp;output=html&amp;adk=1812271804&amp;adf=3025194257&amp;abgtt=6&amp;lmt=1784190353&amp;plaf=1%3A2%2C2%3A2%2C7%3A2&amp;plat=1%3A128%2C2%3A128%2C3%3A128%2C4%3A128%2C8%3A128%2C9%3A32776%2C16%3A8388608%2C17%3A32%2C24%3A32%2C25%3A32%2C30%3A1081344%2C32%3A32%2C41%3A32%2C42%3A32%2C43%3A32%2C44%3A32&amp;format=0x0&amp;url=https%3A%2F%2Fwww.eracast.cc%2Faccount&amp;pra=5&amp;asro=0&amp;aimartd=4&amp;aieuf=1&amp;aicrs=1&amp;uach=WyJXaW5kb3dzIiwiMTUuMC4wIiwieDg2IiwiIiwiMTUwLjAuNDA3OC42NSIsbnVsbCwwLG51bGwsIjY0IixbWyJOb3Q7QT1CcmFuZCIsIjguMC4wLjAiXSxbIkNocm9taXVtIiwiMTUwLjAuNzg3MS4xMTUiXSxbIk1pY3Jvc29mdCBFZGdlIiwiMTUwLjAuNDA3OC42NSJdXSwwXQ..&amp;dt=1784190353231&amp;bpp=373&amp;bdt=63&amp;idt=429&amp;shv=r20260715&amp;mjsv=m202607140101&amp;ptt=9&amp;saldr=aa&amp;abxe=1&amp;cookie=ID%3De84ee9be781543a5%3AT%3D1774119413%3ART%3D1784190146%3AS%3DALNI_MblGbfAzQuEy8Iexvof-sv4sOhiUA&amp;gpic=UID%3D0000139436d11097%3AT%3D1774119413%3ART%3D1784190146%3AS%3DALNI_MafaIrc5gXdKMoitR978wYhdebhyQ&amp;eo_id_str=ID%3Dab5aeff906140dc7%3AT%3D1774119413%3ART%3D1784190146%3AS%3DAA-AfjZxk9b0FIIzW5gIEtFx3lvY&amp;nras=1&amp;correlator=723296665515&amp;frm=20&amp;pv=2&amp;u_tz=180&amp;u_his=3&amp;u_h=1080&amp;u_w=1920&amp;u_ah=1040&amp;u_aw=1920&amp;u_cd=24&amp;u_sd=1&amp;dmc=16&amp;adx=-12245933&amp;ady=-12245933&amp;biw=1897&amp;bih=922&amp;scr_x=0&amp;scr_y=0&amp;eid=31099682%2C95396101%2C31099754%2C95373849%2C95393485&amp;oid=2&amp;pvsid=7619824111700169&amp;tmod=1760303696&amp;uas=0&amp;nvt=1&amp;fsapi=1&amp;fc=1920&amp;brdim=0%2C0%2C0%2C0%2C1920%2C0%2C1920%2C1040%2C1912%2C922&amp;vis=1&amp;rsz=%7C%7Cs%7C&amp;abl=NS&amp;fu=32768&amp;bc=31&amp;bz=1&amp;ifi=1&amp;uci=a!1&amp;fsb=1&amp;dtd=436" data-google-container-id="a!1" tabindex="0" title="Advertisement" aria-label="Advertisement" data-load-complete="true"></iframe></div></ins><iframe name="googlefcPresent" style="width: 0px; height: 0px; border-width: medium; border-style: none; border-color: currentcolor; border-image: none; z-index: -1000; left: -1000px; top: -1000px; display: none;"></iframe><form name="logoutForm" method="POST" action="https://www.eracast.cc/logout">
			<input type="hidden" name="action_logout" value="1">
		</form>
		<!-- begin page -->
		<div id="page" class="browse-base">
			<!-- begin pagetop -->
			<style>
   .content-region {
   position:absolute;
   top:10px;
   left:97px;
   color:#999;
   font-size:11px;
   text-decoration:none;
   font-weight:400 
   }
   .content-region-footer {
   position:absolute;
   top:25px;
   left:95px;
   color:#999;
   font-size:11px;
   text-decoration:none;
   font-weight:400 
   }

   </style>


<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?>
			<div id="content-container">
				<div id="baseDiv" class="date-20120930 video-info   browse-base browse-videos">
					<div id="alerts"></div>
					<div id="masthead-subnav" class="yt-nav yt-nav-dark ">
						<ul>
                            <a href="/my_videos">
                                <li class="">
                                    <span class="yt-nav-item">
                                        Video Manager
                                    </span>
                                </li>
                            </a>
                            <li class="">
								<span class="yt-nav-item">
								    Video Editor
								</span>
							</li>
                            <a href="/my_subscriptions">
                                <li class="">
                                    <span class="yt-nav-item">
                                        Subscriptions
                                    </span>
                                </li>
							</a>
                            <a href="/analytics">
                                <li class="">
                                    <span class="yt-nav-item">
                                        Analytics
                                    </span>
                                </li>
							</a>
                            <a href="/account">
                                <li class="selected" style="float:right;">
                                    <span class="yt-nav-item">
                                        Settings
                                    </span>
                                </li>
                            </a>
                            <a href="/inbox">
                                <li class="" style="float:right;">
                                    <span class="yt-nav-item">
                                        Inbox
                                    </span>
                                </li>
                            </a>
						</ul>
					</div>
					<div class="ytg-base">
						<div class="ytg-wide">
                                                            <div id="yts-nav" class="ytg-1col">
                                    <ol style="margin-top:0px;border-left: 1px solid lightgray;">
                                        <li class="top-level">
                                            <a>
                                                Account settings
                                            </a>
                                        </li>
                                        <ol class="indented">
                                            <li class="sub-level">
                                                <a class="item-highlight" href="/account">
                                                    Overview
                                                </a>
                                            </li>
                                            <li class="sub-level">
                                                <a class="1" href="/account/sharing">
                                                    Sharing
                                                </a>
                                            </li>
                                            <li class="sub-level">
                                                <a class="1" href="/account/privacy">
                                                    Privacy
                                                </a>
                                            </li>
                                            <li class="sub-level">
                                                <a class="1" href="/account/email">
                                                    Email                                                 </a>
                                            </li>
                                            <li class="sub-level">
                                                <a class="1" href="/account/playback">
                                                    Playback
                                                </a>
                                            </li>
                                        </ol>
                                        <li class="top-level">
                                            <a>
                                                Channel settings
                                            </a>
                                        </li>
                                        <ol class="indented">
                                            <li class="sub-level">
                                                <a class="1" href="/account/monetization">
                                                    Monetization
                                                </a>
                                            </li>
                                        </ol>
                                    </ol>
                                </div>
                                                                                        <form method="post" action="">
   <input type="hidden" name="_token" value="gDQzQmC0VXfhvVBrxZC4xTk6M2njLUdiAmwBr48Q">
   <div style="width:73.5%;float:right;border-left: 1px solid lightgray;padding:25px;">
      <div style="display:flex;flex-direction:row;margin-bottom:10px;">
         <h1>Overview</h1>
         <input class="yt-uix-button yt-uix-button-primary " type="submit" value="Save" style="margin-left:auto;padding:0 20px">
      </div>
      <div class="yt-horizontal-rule "></div>
      <div style="margin-top:10px;">
         <h2><b>Account Information</b></h2>
         <div style="margin-top:10px;">
            <div style="margin-top:20px;display:flex;flex-direction:row;white-space:nowrap;">
               <div style="width:30%;">
                  <p>Name</p>
               </div>
               <div style="display:flex;flex-direction:row;">
                  <div style="margin-right:10px;">
                     <img src="<?php echo htmlspecialchars($accAvatar) ?>" height="100" width="100" alt="<?php echo htmlspecialchars($accName) ?>" onerror="this.onerror=null;this.src='/dynamic/pfp/default.png'">
                  </div>
                  <div style="display:flex;flex-direction:column">
                     <a href="<?php echo htmlspecialchars($accChannel) ?>" style="margin-top:10px;"><b><?php echo htmlspecialchars($accName) ?></b></a>
                     <div style="display:flex;flex-direction:row;margin-top:5px;">
                        <b><?php echo htmlspecialchars($accEmail) ?></b>
                        <a href="/channel_editor" style="margin-left:10px;">Edit profile</a>
                     </div>
                     <a style="margin-top:5px;">Advanced</a>
                  </div>
               </div>
            </div>
            <div style="margin-top:20px;display:flex;flex-direction:row;white-space:nowrap;">
               <div style="width:30%;">
                  <p>Password</p>
               </div>
               <div style="display:flex;flex-direction:column">
                  <a href="#">Change password</a>
                  <p style="margin-top:5px;color:grey;">You will be redirected to your Aesthetiful account page</p>
               </div>
            </div>
            <div style="margin-top:20px;display:flex;flex-direction:row;white-space:nowrap;">
               <div style="width:30%;">
                  <p>Mobile uploads</p>
               </div>
               <div style="display:flex;flex-direction:column">
                  <p>n/a</p>
                  <p style="margin-top:5px;color:grey;">Upload videos from your phone by emailing this address. Want a different address? <a href="#">Click Here</a></p>
               </div>
            </div>
         </div>
      </div>
      <div style="margin-top:40px;">
         <h2><b>Account Status</b></h2>
         <div style="margin-top:20px;margin-bottom:10px;display:flex;flex-direction:row;white-space:nowrap;">
            <div style="width:30%;">
               <p>Community guidelines</p>
            </div>
            <div style="display:flex;flex-direction:row">
                               <div class="green-dot"></div>
                <p>Good standing</p>
                           </div>
         </div>
         <div class="yt-horizontal-rule "></div>
         <div style="margin-top:10px;margin-bottom:10px;display:flex;flex-direction:row;white-space:nowrap;">
            <div style="width:30%;">
               <p>Copyright strikes</p>
            </div>
            <div style="display:flex;flex-direction:row">
               <div class="green-dot"></div>
               <p>Good standing</p>
            </div>
         </div>
         <div class="yt-horizontal-rule "></div>
         <div style="margin-top:10px;margin-bottom:10px;display:flex;flex-direction:row;white-space:nowrap;">
            <div style="width:30%;">
               <p>Content ID claims</p>
            </div>
            <div style="display:flex;flex-direction:row">
                               <div class="green-dot"></div>
                <p>Good standing</p>
                           </div>
         </div>
      </div>
      <div style="margin-top:40px;">
         <h2><b>Links</b></h2>
         <br>
         <a href="#">Learn how to promote your videos</a>
      </div>
      <div style="margin-top:40px;">
         <h2><b>Video Player</b></h2>
         <br>
         <select name="player">
            <option value="1" selected="">2012 Legacy (HTML5/flash)</option>
            <option value="2">Plyr (HTML5)</option>
            <option value="3">VideoJS (HTML5)</option>
         </select>
      </div>
   </div>
</form>                            						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<!-- end pagemiddle -->
			<!-- begin pagebottom -->
			<?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>
			<div id="playlist-bar" class="hid passive editable" data-video-url="/watch?v=&amp;feature=BFql&amp;playnext=1&amp;list=QL" data-list-id="" data-list-type="QL">
				<div id="playlist-bar-bar-container">
					<div id="playlist-bar-bar">
						<div class="yt-alert yt-alert-naked yt-alert-success hid " id="playlist-bar-notifications">
							<div class="yt-alert-icon">
								<img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
							</div>
							<div class="yt-alert-content" role="alert"></div>
						</div>
						<span id="playlist-bar-info"><span class="playlist-bar-active playlist-bar-group"><button onclick=";return false;" title="Previous video" type="button" id="playlist-bar-prev-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-prev" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Previous video"><span class="yt-valign-trick"></span></span></button><span class="playlist-bar-count"><span class="playing-index">0</span> / <span class="item-count">0</span></span><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-next-button" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-next" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span></button></span><span class="playlist-bar-active playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-autoplay-button" data-button-toggle="true" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-autoplay" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span></button><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-button-empty" onclick=";return false;" id="playlist-bar-shuffle-button" data-button-toggle="true" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-shuffle" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span></button></span><span class="playlist-bar-passive playlist-bar-group"><button onclick=";return false;" title="Play videos" type="button" id="playlist-bar-play-button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-default yt-uix-tooltip yt-uix-button-empty" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-play" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Play videos"><span class="yt-valign-trick"></span></span></button><span class="playlist-bar-count"><span class="item-count">0</span></span></span><span id="playlist-bar-title" class="yt-uix-button-group"><span class="playlist-title">Unsaved Playlist</span></span></span>
						<a id="playlist-bar-lists-back" href="#">
						Return to active list
						</a>
						<span id="playlist-bar-controls"><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked  yt-uix-button yt-uix-button-text yt-uix-button-empty" onclick=";return false;" id="playlist-bar-toggle-button" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-playlist-bar-toggle" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-valign-trick"></span></span></button></span><span class="playlist-bar-group"><button type="button" class="yt-uix-tooltip yt-uix-tooltip-masked yt-uix-button-reverse flip yt-uix-button yt-uix-button-text" onclick=";return false;" data-button-menu-id="playlist-bar-options-menu" data-button-has-sibling-menu="true" role="button"><span class="yt-uix-button-content">Options </span><img class="yt-uix-button-arrow" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt=""></button></span></span>      
					</div>
				</div>
				<div id="playlist-bar-tray-container">
					<div id="playlist-bar-tray" class="yt-uix-slider yt-uix-slider-fluid">
						<button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-prev" onclick="return false;"><img class="yt-uix-slider-prev-arrow" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Previous video"></button><button class="yt-uix-button playlist-bar-tray-button yt-uix-button-default yt-uix-slider-next" onclick="return false;"><img class="yt-uix-slider-next-arrow" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Next video"></button>
						<div class="yt-uix-slider-body">
							<div id="playlist-bar-tray-content" class="yt-uix-slider-slide">
								<ol class="video-list"></ol>
								<ol id="playlist-bar-help">
									<li class="empty playlist-bar-help-message">Your queue is empty. Add videos to your queue using this button: <img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="addto-button-help"><br> or <a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fvideos%253Ffeature%253Dmh&amp;uilel=3&amp;hl=en_US&amp;service=youtube">sign in</a> to load a different list.</li>
								</ol>
							</div>
							<div class="yt-uix-slider-shade-left"></div>
							<div class="yt-uix-slider-shade-right"></div>
						</div>
					</div>
					<div id="playlist-bar-save"></div>
					<div id="playlist-bar-lists" class="dark-lolz"></div>
					<div id="playlist-bar-loading"><img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Loading..."><span id="playlist-bar-loading-message">Loading...</span><span id="playlist-bar-saving-message" class="hid">Saving...</span></div>
					<div id="playlist-bar-template" style="display: none;" data-video-thumb-url="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg">
						<!--<li class="playlist-bar-item yt-uix-slider-slide-unit __classes__" data-video-id="__video_encrypted_id__"><a href="__video_url__" title="__video_title__" class="yt-uix-sessionlink" data-sessionlink="ei=CPjwu5ji3bICFS4RIQod9j-M-A%3D%3D&amp;feature=BFa"><span class="video-thumb ux-thumb yt-thumb-default-106 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="http://s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="__video_title__" data-thumb-manual="true" data-thumb="__video_thumb_url__" width="106" ><span class="vertical-align"></span></span></span></span><span class="screen"></span><span class="count"><strong>__list_position__</strong></span><span class="play"><img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif"></span><span class="yt-uix-button yt-uix-button-default delete"><img class="yt-uix-button-icon-playlist-bar-delete" src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" alt="Delete"></span><span class="now-playing">Now playing</span><span dir="ltr" class="title"><span>__video_title__  <span class="uploader">by __video_display_name__</span>
							</span></span><span class="dragger"></span></a></li>-->
					</div>
					<div id="playlist-bar-next-up-template" style="display: none;">
						<!--<div class="playlist-bar-next-thumb"><span class="video-thumb ux-thumb yt-thumb-default-74 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="//i4.ytimg.com/vi/__video_encrypted_id__/default.jpg" alt="Thumbnail" onerror="this.onerror=null;this.src='/dynamic/thumbs/default.jpg';" width="74" ><span class="vertical-align"></span></span></span></span></div>-->
					</div>
				</div>
				<div id="playlist-bar-options-menu" class="hid">
					<div id="playlist-bar-extras-menu">
						<ul>
							<li><span class="yt-uix-button-menu-item" data-action="clear">
								Clear all videos from this list
								</span>
							</li>
						</ul>
					</div>
					<ul>
						<li><span class="yt-uix-button-menu-item" onclick="window.location.href='//support.google.com/youtube/bin/answer.py?answer=146749&amp;hl=en-US'">Learn more</span></li>
					</ul>
				</div>
			</div>
			<div id="shared-addto-watch-later-login" class="hid">
				<a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fvideos%253Ffeature%253Dmh&amp;uilel=3&amp;hl=en_US&amp;service=youtube" class="sign-in-link">Sign in</a> to add this to a playlist
			</div>
			<div id="shared-addto-menu" style="display: none;" class="hid sign-in">
				<div class="addto-menu">
					<div id="addto-list-panel" class="menu-panel active-panel">
						<span class="yt-uix-button-menu-item yt-uix-tooltip sign-in" data-possible-tooltip="" data-tooltip-show-delay="750"><a href="https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26feature%3Dplaylist%26nomobiletemp%3D1%26hl%3Den_US%26next%3D%252Fvideos%253Ffeature%253Dmh&amp;uilel=3&amp;hl=en_US&amp;service=youtube" class="sign-in-link">Sign in</a> to add this to a playlist
						</span>
					</div>
					<div id="addto-list-saved-panel" class="menu-panel">
						<div class="panel-content">
							<div class="yt-alert yt-alert-naked yt-alert-success  ">
								<div class="yt-alert-icon">
									<img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
								</div>
								<div class="yt-alert-content" role="alert">
									<span class="yt-alert-vertical-trick"></span>
									<div class="yt-alert-message">
										<span class="message">Added to <span class="addto-title yt-uix-tooltip yt-uix-tooltip-reverse" title="More information about this playlist" data-tooltip-show-delay="750"></span></span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="addto-list-error-panel" class="menu-panel">
						<div class="panel-content">
							<img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
							<span class="error-details"></span>
							<a class="show-menu-link">Back to list</a>
						</div>
					</div>
					<div id="addto-note-input-panel" class="menu-panel">
						<div class="panel-content">
							<div class="yt-alert yt-alert-naked yt-alert-success  ">
								<div class="yt-alert-icon">
									<img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon">
								</div>
								<div class="yt-alert-content" role="alert">
									<span class="yt-alert-vertical-trick"></span>
									<div class="yt-alert-message">
										<span class="message">Added to playlist:</span>
										<span class="addto-title yt-uix-tooltip" title="More information about this playlist" data-tooltip-show-delay="750"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="yt-uix-char-counter" data-char-limit="150">
							<div class="addto-note-box addto-text-box"><textarea id="addto-note" class="addto-note yt-uix-char-counter-input" maxlength="150"></textarea><label for="addto-note" class="addto-note-label">Add an optional note</label></div>
							<span class="yt-uix-char-counter-remaining">150</span>
						</div>
						<button disabled="disabled" type="button" class="playlist-save-note yt-uix-button yt-uix-button-default" onclick=";return false;" role="button"><span class="yt-uix-button-content">Add note </span></button>
					</div>
					<div id="addto-note-saving-panel" class="menu-panel">
						<div class="panel-content loading-content">
							<img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
							<span>Saving note...</span>
						</div>
					</div>
					<div id="addto-note-saved-panel" class="menu-panel">
						<div class="panel-content">
							<img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
							<span class="message">Note added to:</span>
						</div>
					</div>
					<div id="addto-note-error-panel" class="menu-panel">
						<div class="panel-content">
							<img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif">
							<span class="message">Error adding note:</span>
							<ul class="error-details"></ul>
							<a class="add-note-link">Click to add a new note</a>
						</div>
					</div>
					<div class="close-note hid">
						<img src="//s.ytimg.com/yt/img/pixel-vfl3z5WfW.gif" class="close-button">
					</div>
				</div>
			</div>
			<!-- end pagebottom -->
		</div>
		<!-- end page -->
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