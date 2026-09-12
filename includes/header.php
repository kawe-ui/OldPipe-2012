<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/config.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php');
$ytAccount = yt_account_info();

$ytLoggedIn    = $ytLoggedIn    ?? ($ytAccount !== null);
$ytUserName    = $ytUserName    ?? ($ytAccount['name'] ?? '');
$ytUserEmail   = $ytUserEmail   ?? ($ytAccount['email'] ?? '');
$ytUserChannel = $ytUserChannel ?? (!empty($ytAccount['channelId']) ? '/channel/' . $ytAccount['channelId'] : '/');
$ytUserChannelId = $ytUserChannelId ?? ($ytAccount['channelId'] ?? '');
$ytUserAvatar  = $ytUserAvatar  ?? ($ytAccount['avatar'] ?? '');
$ytUserAvatar  = default_avatar($ytUserAvatar);

$ytSignInUrl   = $ytSignInUrl ?? yt_signin_url((string)($_SERVER['REQUEST_URI'] ?? '/'));
$ytOtherAccounts = [];
foreach (($ytAccount['accounts'] ?? []) as $acc) {
    if (!empty($acc['selected']) || empty($acc['switchUrl'])) continue;
    $ytOtherAccounts[] = $acc;
}
$ytCsrfToken   = yt_session_token();
$ytCsrfTokenJs = json_encode($ytCsrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

?>
<script>
(function () {
  var token = <?php echo $ytCsrfTokenJs ?>;
  if (!token) return;

  var CSRF_ENDPOINTS = /\/(watch_actions_ajax|subscription_ajax|comment_servlet|set_safety_mode|picker_ajax)\b/;
  function isCsrfPost(method, url) {
    if (!method || String(method).toUpperCase() !== 'POST') return false;
    try {
      var u = new URL(url, window.location.href);
      return u.origin === window.location.origin && CSRF_ENDPOINTS.test(u.pathname);
    } catch (e) { return CSRF_ENDPOINTS.test(String(url || '')); }
  }
  function withToken(body) {
    if (typeof body === 'string') {
      if (/(^|&)session_token=/.test(body)) {
        return body.replace(/(^|&)session_token=[^&]*/, '$1session_token=' + encodeURIComponent(token));
      }
      return (body ? body + '&' : '') + 'session_token=' + encodeURIComponent(token);
    }
    if (typeof FormData !== 'undefined' && body instanceof FormData) {
      body.set('session_token', token);
    }
    return body;
  }

  if (window.XMLHttpRequest) {
    var xopen = XMLHttpRequest.prototype.open;
    var xsend = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.open = function (method, url) {
      this.__ytCsrf = isCsrfPost(method, url);
      return xopen.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function (body) {
      if (this.__ytCsrf) { try { body = withToken(body); } catch (e) {} }
      return xsend.call(this, body);
    };
  }

  if (window.fetch) {
    var origFetch = window.fetch;
    window.fetch = function (input, init) {
      try {
        init = init || {};
        var url = (typeof input === 'string') ? input : (input && input.url);
        var method = init.method || (input && input.method) || 'GET';
        if (isCsrfPost(method, url) && typeof init.body === 'string') init.body = withToken(init.body);
      } catch (e) {}
      return origFetch.call(this, input, init);
    };
  }

  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || String(form.tagName).toUpperCase() !== 'FORM') return;
    var method = form.getAttribute('method') || form.method || 'GET';
    var action = form.getAttribute('action') || form.action || window.location.href;
    if (!isCsrfPost(method, action)) return;
    var input = form.querySelector('input[name="session_token"]');
    if (!input) {
      input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'session_token';
      form.appendChild(input);
    }
    input.value = token;
  }, true);
  function applyCsrfToken() {
    document.querySelectorAll('input[name="session_token"]').forEach(function (input) {
      input.value = token;
    });
    if (!window.yt) return;
    if (typeof window.yt.setConfig === 'function') window.yt.setConfig('XSRF_TOKEN', token);
    if (typeof window.yt.setAjaxToken === 'function') {
      ['watch_actions_ajax', 'subscription_ajax', 'comments_ajax', 'comment_servlet', 'playlist_bar_ajax'].forEach(function (name) {
        window.yt.setAjaxToken(name, token);
      });
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyCsrfToken);
  } else {
    applyCsrfToken();
  }
}());
</script>
<?php if ($ytLoggedIn): ?>
<form id="logoutForm" method="post" action="/auth/google/logout" style="display:none">
  <input type="hidden" name="session_token" value="<?php echo htmlspecialchars($ytCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
</form>
<?php endif; ?>
<?php if ($ytLoggedIn): ?>
        <div id="masthead-expanded-acct-sw-container" class="with-sandbar hid" style="top: 60px;">
    <iframe id="masthead-expanded-acct-sw-iframe" frameborder="0" src="javascript:&quot;&quot;" style="height: 150px;"></iframe><ul id="masthead-expanded-menu-acct-sw-list" style="height: 150px;">
      <li class="masthead-expanded-menu-item">
        <a href="" onclick="yt.www.masthead.accountswitch.toggle(); return false;">
‹ Back
        </a>
      </li>

          <li class="masthead-expanded-acct-sw-sel">
    <p class="masthead-expanded-acct-sw-id1">
        <?php echo htmlspecialchars($ytUserName) ?><img class="masthead-expanded-acct-sw-sel-arrow" src="yts/img/pixel-vfl3z5WfW.gif" alt="">
    </p>
    <p class="masthead-expanded-acct-sw-id2">
      <?php echo htmlspecialchars($ytUserEmail) ?>
    </p>
      <p class="masthead-expanded-acct-sw-id2"><img class="masthead-expanded-acct-sw-img" src="<?php echo htmlspecialchars($ytUserAvatar) ?>" width="12" height="12" alt=""><?php echo htmlspecialchars($ytUserName) ?></p>
  </li>


      <li class="masthead-expanded-menu-item">
        <a class="end" href="https://accounts.google.com/AddSession?passive=false&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26authuser%3D-1%26hl%3Den_US%26next%3D%252Fwatch%253Fv%253DngzCpd94AwA%26nomobiletemp%3D1&amp;uilel=0&amp;service=youtube&amp;hl=en_US">Sign in to another account...</a>
      </li>
      <li class="masthead-expanded-menu-item">
        <a class="end" href="http://www.youtube.com/watch?v=ngzCpd94AwA#" onclick="document.logoutForm.submit(); return false;">Sign out of all accounts</a>
      </li>
    </ul>
  </div>
<?php endif; ?>
<div id="masthead-container">
    <!-- begin masthead -->
          <div id="masthead" class="" dir="ltr">    <a id="logo-container" href="/" title="YouTube home"><img id="logo" src="/yts/img/pixel-vfl3z5WfW.gif" alt="YouTube home"></a>
<?php if ($ytLoggedIn): ?>
<div id="masthead-user-bar-container">
      <div id="masthead-user-bar">
        <div id="masthead-user">
          <span id="masthead-gaia-user-expander" class="masthead-user-menu-expander masthead-expander" onclick="yt.www.masthead.toggleExpandedMasthead()"><span id="masthead-gaia-user-wrapper" class="yt-rounded" tabindex="0"><?php echo htmlspecialchars($ytUserName) ?></span></span>
    <button type="button" class="sb-button sb-notif-off yt-uix-button" onclick=";return false;" id="sb-button-notify" role="button"><span class="yt-uix-button-content">  </span></button>
    <button type="button" class="sb-button yt-uix-button" onclick=";return false;" id="sb-button-share" role="button"><span class="yt-uix-button-icon-wrapper"><img class="yt-uix-button-icon yt-uix-button-icon-share-plus" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""><span class="yt-uix-button-valign"></span></span><span class="yt-uix-button-content">  </span></button>

<span id="masthead-gaia-photo-expander" class="masthead-user-menu-expander masthead-expander" onclick="yt.www.masthead.toggleExpandedMasthead()"><span id="masthead-gaia-photo-wrapper" class="yt-rounded"><span id="masthead-gaia-user-image"><span class="clip"><span class="clip-center"><img src="<?php echo htmlspecialchars($ytUserAvatar) ?>" alt="" onerror="this.onerror=null;this.src='/dynamic/pfp/default.png'"><span class="vertical-center"></span></span></span></span><span class="masthead-expander-arrow"></span></span></span>
        </div>
      </div>
    </div>
<?php else: ?>
<div id="masthead-user-bar-container" dir="ltr"><div id="masthead-user-bar"><div id="masthead-user"><div id="masthead-user-display"><span id="masthead-user-wrapper"><button href="<?php echo htmlspecialchars($ytSignInUrl) ?>" type="button" id="masthead-user-button" onclick=";window.location.href=this.getAttribute('href');return false;" class=" yt-uix-button yt-uix-button-text" role="button"><span class="yt-uix-button-content"><span id="masthead-user-image"><span class="clip"><span class="clip-center"><img src="/yts/img/silhouette48-vflLdu7sh.png" alt=""><span class="vertical-center"></span></span></span></span><span class="masthead-user-username">Sign In</span> </span></button></span></div></div></div></div>
<?php endif; ?>
<div id="masthead-search-bar-container"<?php echo $ytLoggedIn ? ' class="with-sandbar"' : '' ?>><div id="masthead-search-bar"><div id="masthead-nav"><a href="/videos?feature=mh">Browse</a><span class="masthead-link-separator">|</span><a href="/movies?feature=mh">Movies</a><span class="masthead-link-separator">|</span><a id="masthead-upload-link" class="" data-upsell="upload" href="//www.youtube.com/my_videos_upload">Upload</a></div><form id="masthead-search" class="search-form consolidated-form" action="/results" onsubmit="if (_gel('masthead-search-term').value == '') return false;"><button class="search-btn-component search-button yt-uix-button yt-uix-button-default" onclick="if (_gel('masthead-search-term').value == '') return false; _gel('masthead-search').submit(); return false;;return true;" type="submit" id="search-btn" dir="ltr" tabindex="2" role="button"><span class="yt-uix-button-content">Search </span></button><div id="masthead-search-terms" class="masthead-search-terms-border " dir="ltr"><label><input id="masthead-search-term" class="search-term" name="search_query" value="" type="text" tabindex="1" onkeyup="goog.i18n.bidi.setDirAttribute(event,this)" title="Search" dir="ltr" autocomplete="off" spellcheck="false" style="outline: none;"></label></div><input type="hidden" name="oq"><input type="hidden" name="gs_l"></form></div></div></div>
<?php if ($ytLoggedIn): ?>
          <div id="masthead-expanded" class="hid">
    <div id="masthead-expanded-container" class="with-sandbar">
      <div id="masthead-expanded-menus-container">
        <span id="masthead-expanded-menu-shade"></span>
          <div id="masthead-expanded-google-menu">
    <span class="masthead-expanded-menu-header">
Google account
    </span>
    <div id="masthead-expanded-menu-google-container">
            <img id="masthead-expanded-menu-gaia-photo" alt="" src="<?php echo htmlspecialchars($ytUserAvatar) ?>" onerror="this.onerror=null;this.src='/dynamic/pfp/default.png'">
  <div id="masthead-expanded-menu-account-info" class="email-only">
      <p><?php echo htmlspecialchars($ytUserName) ?></p>
    <p id="masthead-expanded-menu-email"><?php echo htmlspecialchars($ytUserEmail) ?></p>
  </div>

      <div id="masthead-expanded-menu-google-column1">
        <ul>
          <li class="masthead-expanded-menu-item"><a href="<?php echo htmlspecialchars($ytUserChannel) ?>">Profile</a></li>
          <li class="masthead-expanded-menu-item"><a href="https://plus.google.com/u/0/stream">Google+</a></li>
          <li class="masthead-expanded-menu-item"><a href="https://myaccount.google.com/data-and-privacy">Privacy</a></li>
        </ul>
      </div>
      <div id="masthead-expanded-menu-google-column2">
        <div id="masthead-expanded-menu-account-container">
        </div>
        <ul>
            <li class="masthead-expanded-menu-item">
              <a href="https://myaccount.google.com">
Settings
              </a>
            </li>
          <li class="masthead-expanded-menu-item">
            <a class="end" href="#" onclick="document.getElementById('logoutForm').submit(); return false;">
Sign out
            </a>
          </li>
            <li class="masthead-expanded-menu-item">
              <a href="#" onclick="yt.www.masthead.accountswitch.toggle(); return false;">
Switch account
              </a>
            </li>
        </ul>
      </div>
    </div>
  </div>

          <div id="masthead-expanded-menu">
    <span class="masthead-expanded-menu-header">
YouTube
    </span>
    <ul id="masthead-expanded-menu-list">
      <li class="masthead-expanded-menu-item">
        <a href="<?php echo htmlspecialchars($ytUserChannel) ?>?feature=mhee">
My channel
        </a>
      </li>
      <li class="masthead-expanded-menu-item">
        <a href="/my_videos?feature=mhee">
Video Manager
        </a>
      </li>
      <li class="masthead-expanded-menu-item">
        <a href="/my_subscriptions?feature=mhee">Subscriptions</a>
      </li>
      <li class="masthead-expanded-menu-item">
        <a href="/account?feature=mhee">
YouTube settings
        </a>
      </li>
    </ul>
  </div>

      </div>
      <div id="masthead-expanded-sandbar">
        <div id="masthead-expanded-lists-container">
          <div id="masthead-expanded-loading-message">Loading...</div>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
<?php endif; ?>



      <div id="alerts">


</div>

    <!-- end masthead -->
  </div>
