<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  share_ajax.php — панель «Share» страницы watch (протокол 2012)
//  Разметка взята ДОСЛОВНО из архивного ответа youtube.com (20121109083235),
//  шаблонизированы только динамические значения (video_id, title, thumb…).
//  ВАЖНО: оригинал адресует элементы КЛАССАМИ (share-panel-embed, share-panel-url
//  и т.д.), а не id — www-core ищет их через K(className, container).
//
//    ?action_get_share_box=1&video_id=ID  → {share_html, url_short, url_long, lang}
//    ?action_get_embed=1&video_id=ID      → {embed_html, legacy_url, legacy_code,
//                                            iframe_url, iframe_code}
//    ?action_get_email=1&video_id=ID      → {email_html}
//    ?action_get_share_urls=1&video_id=ID → {url_short, url_long, share_services_html}
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$req      = array_merge($_GET, $_POST);
$video_id = $req['video_id'] ?? '';
if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id)) $video_id = '';

$urlShort = 'http://youtu.be/' . $video_id;
$urlLong  = 'http://www.youtube.com/watch?v=' . $video_id;
$pixel    = '/yts/img/pixel-vfl3z5WfW.gif';

// ─── Метаданные видео (для заголовков/описаний в ссылках сервисов) ────────────
$shareTitle = '';
$shareDesc  = '';
$shareThumb = 'http://i3.ytimg.com/vi/' . $video_id . '/hqdefault.jpg';
if ($video_id !== '') {
    $p = innertube_post('player', [
        'videoId' => $video_id, 'racyCheckOk' => true, 'contentCheckOk' => true,
    ]);
    if ($p !== null) {
        $shareTitle = $p['videoDetails']['title'] ?? '';
        $shareDesc  = $p['videoDetails']['shortDescription'] ?? '';
    }
}
if ($shareTitle === '') $shareTitle = 'YouTube';

$signInUrl = 'https://accounts.google.com/ServiceLogin?passive=true&continue='
    . rawurlencode('http://www.youtube.com/signin?action_handle_signin=true&feature=email&hl=en_US&next='
        . rawurlencode('http://www.youtube.com/watch?v=' . $video_id . '&feature=share_email'))
    . '&nomobiletemp=1&service=youtube&hl=en_US&uilel=3';

// ─── Кнопка сервиса (разметка 1:1 с архивом) ─────────────────────────────────
function share_service_button(string $service, string $label, string $iconClass,
                              string $popupUrl, string $vid, array $opt = []): string {
    $h       = $opt['height'] ?? 650;
    $w       = $opt['width']  ?? 1024;
    $title   = $opt['title']  ?? null;
    $tooltip = $title !== null ? ' title="' . htmlspecialchars($title, ENT_QUOTES) . '" class="yt-uix-tooltip share-service-button"'
                              : ' class="share-service-button"';
    $pixel   = '/yts/img/pixel-vfl3z5WfW.gif';

    $onclick = 'yt.tracking.shareVideo(&quot;' . $service . '&quot;, &quot;' . $vid . '&quot;,&quot;en_US&quot;, &quot;sharepanel&quot;);'
        . 'yt.window.popup(&quot;' . htmlspecialchars($popupUrl, ENT_QUOTES) . '&quot;, '
        . "{'height': {$h},'width': {$w},'scrollbars': true});return false;";

    return '<button onclick="' . $onclick . '" data-service-name="' . $service . '"' . $tooltip . '>'
        . '<img src="' . $pixel . '" alt="' . htmlspecialchars($label, ENT_QUOTES) . '" class="share-service-icon share-service-icon-' . $iconClass . '">'
        . '<span>' . htmlspecialchars($label) . '</span>'
        . '</button>';
}

// ─── Набор сервисов (как в архиве: 3 основных + 7 в «More») ──────────────────
function share_services_all(string $vid, string $title, string $desc, string $thumb): array {
    $wUrl   = 'http://www.youtube.com/watch?v=' . $vid . '&feature=share';
    $wUrlE  = rawurlencode($wUrl);
    $shortE = rawurlencode('http://youtu.be/' . $vid);
    $titleE = rawurlencode($title);
    $thumbE = rawurlencode($thumb);
    $descE  = rawurlencode($desc);

    $primary = ''
        . '  <li>' . "\n    "
        . share_service_button('FACEBOOK', 'Facebook', 'facebook',
            'http:\/\/www.facebook.com\/dialog\/feed?app_id=87741124305&link=' . $wUrlE
            . '&display=popup&redirect_uri=' . rawurlencode('https://www.youtube.com/facebook_redirect'),
            $vid, ['height' => 306, 'width' => 650, 'title' => 'Share to Facebook'])
        . "\n  </li>\n"
        . '  <li>' . "\n    "
        . share_service_button('TWITTER', 'Twitter', 'twitter',
            'http:\/\/twitter.com\/intent\/tweet?url=' . $shortE . '&text=' . $titleE
            . '%3A&via=youtube&related=Youtube%2CYouTubeTrends%2CYTCreators',
            $vid, ['title' => 'Share to Twitter'])
        . "\n  </li>\n"
        . '  <li>' . "\n    "
        . share_service_button('GOOGLEPLUS', 'Google+', 'googleplus',
            'https:\/\/plus.google.com\/share?url=' . $wUrlE . '&source=yt&hl=en',
            $vid, ['height' => 620, 'width' => 620, 'title' => 'Share to Google+'])
        . "\n  </li>\n";

    // «More» — в архиве двумя колонками (ul + ul), каждый <li> дублирует <span>
    $mk = function (string $svc, string $label, string $icon, string $url, array $o = []) use ($vid): string {
        return '                  <li>' . "\n    "
            . share_service_button($svc, $label, $icon, $url, $vid, $o)
            . "\n      <span>" . htmlspecialchars($label) . "</span>\n  </li>\n";
    };

    $col1 = $mk('TUMBLR', 'tumblr.', 'tumblr',
            'http:\/\/www.tumblr.com\/share?v=3&u=' . $wUrlE)
        . $mk('PINTEREST', 'pinterest', 'pinterest',
            ' http:\/\/pinterest.com\/pin\/create\/button\/?url=' . $wUrlE
            . '&description=' . $titleE . '&is_video=true&media=' . $thumbE)
        . $mk('BLOGGER', 'Blogger', 'blogger',
            'http:\/\/www.blogger.com\/blog-this.g?n=' . $titleE
            . '&source=youtube&b=' . rawurlencode('<iframe width="459" height="344" src="//www.youtube.com/embed/' . $vid . '" frameborder="0" allowfullscreen></iframe>')
            . '&eurl=' . $thumbE, ['height' => 468, 'width' => 768])
        . $mk('STUMBLEUPON', 'StumbleUpon', 'stumbleupon',
            'http:\/\/www.stumbleupon.com\/submit?url=' . $wUrlE . '&title=' . $titleE);

    $col2 = $mk('LINKEDIN', 'LinkedIn', 'linkedin',
            'http:\/\/www.linkedin.com\/shareArticle?url=' . $wUrlE . '&title=' . $titleE
            . '&summary=' . $descE . '&source=Youtube')
        . $mk('MYSPACE', 'Myspace', 'myspace',
            'http:\/\/www.myspace.com\/Modules\/PostTo\/Pages\/?t=' . $titleE . '&u=' . $wUrlE . '&l=1')
        . $mk('REDDIT', 'reddit', 'reddit',
            'http:\/\/reddit.com\/submit?url=' . $wUrlE . '&title=' . $titleE);

    return [$primary, $col1, $col2];
}

// ─── action_get_share_box ─────────────────────────────────────────────────────
if (isset($req['action_get_share_box'])) {
    [$primary, $col1, $col2] = share_services_all($video_id, $shareTitle, $shareDesc, $shareThumb);
    $shortEsc = htmlspecialchars($urlShort, ENT_QUOTES);
    $vidEsc   = htmlspecialchars($video_id, ENT_QUOTES);
    $signEsc  = htmlspecialchars($signInUrl, ENT_QUOTES);

    $shareHtml = <<<HTML
  <div class="share-panel">
    <div class="share-option-container ytg-box">
        <div class="share-panel-buttons yt-uix-expander yt-uix-expander-collapsed">
    <span class="share-panel-main-buttons">
<button type="button" class="share-panel-embed yt-uix-button yt-uix-button-default" onclick=";return false;" role="button"><span class="yt-uix-button-content">Embed </span></button><button type="button" class="share-panel-email yt-uix-button yt-uix-button-default" onclick=";return false;" role="button"><span class="yt-uix-button-content">Email </span></button>    </span>
  </div>

          <div class="share-panel-url-container ">
    <span class=" yt-uix-form-input-container yt-uix-form-input-text-container  yt-uix-form-input-non-empty">    <input class="yt-uix-form-input-text share-panel-url" name="share_url" value="{$shortEsc}" data-video-id="{$vidEsc}">
</span>
        <div class="share-panel-url-options yt-uix-expander yt-uix-expander-collapsed">
    <div class="yt-uix-expander-head">
      <a class="share-panel-show-url-options">
        <span class="collapsed-message">
Options
          <img class="arrow" src="{$pixel}" alt="">
        </span>
        <span class="expanded-message">
Close
          <img class="arrow" src="{$pixel}" alt="">
        </span>
      </a>
    </div>
    <ul class="yt-uix-expander-body share-options">
      <li>
        <label>
          <input class="share-panel-start-at" type="checkbox">
Start at:
        </label>
        <input type="text" value="0:00" class="yt-uix-form-input-text share-panel-start-at-time">
      </li>
        <li>
          <label>
            <input class="share-panel-long-url" type="checkbox">
Long link
          </label>
        </li>
    </ul>
  </div>

  </div>

                <div class="share-panel-services yt-uix-expander yt-uix-expander-collapsed clearfix">
    <ul class="share-group ytg-box">
{$primary}    </ul>

        <div class="yt-uix-expander-head clearfix">
    <a class="share-panel-show-more">
      <span class="collapsed-message">
More
        <img class="arrow" src="{$pixel}" alt="">
      </span>
      <span class="expanded-message">
Less
        <img class="arrow" src="{$pixel}" alt="">
      </span>
    </a>
  </div>

      <div class="yt-uix-expander-body share-options-secondary">
        <div class="secondary">
          <div class="share-groups">
            <ul>
{$col1}            </ul>
            <ul>
{$col2}            </ul>
          </div>
        </div>
      </div>
  </div>




        <div class="share-panel-embed-container hid">
      <div>
Loading...
      </div>
  </div>


          <div class="share-panel-email-container hid" data-disabled="true">
        <strong><a href="{$signEsc}">Sign in</a> now!
</strong>

  </div>

    </div>

      <span class="share-panel-hangout">
        <img src="https://ssl.gstatic.com/s2/oz/images/stars/hangout/1/gplus-hangout-24x100-normal.png" alt="Video call" class="share-panel-hangout-button" title="Watch with your friends.">
      </span>
  </div>
HTML;

    echo json_encode([
        'share_html' => $shareHtml,
        'url_short'  => $urlShort,
        'url_long'   => $urlLong,
        'lang'       => 'en_US',
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// ─── action_get_embed ─────────────────────────────────────────────────────────
if (isset($req['action_get_embed'])) {
    $iframeUrl  = '//www.youtube.com/embed/' . $video_id;
    $iframeCode = '<iframe width="__width__" height="__height__" src="__url__" frameborder="0" allowfullscreen></iframe>';
    $legacyUrl  = 'http://www.youtube.com/v/' . $video_id . '?version=3&hl=en_US';
    $legacyCode = '<object width="__width__" height="__height__"><param name="movie" value="__url__"></param>'
        . '<param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>'
        . '<embed src="__url__" type="application/x-shockwave-flash" width="__width__" height="__height__" '
        . 'allowscriptaccess="always" allowfullscreen="true"></embed></object>';

    // Разметка embed-панели в стиле архива: классы (не id), список размеров
    $embedHtml = <<<HTML
  <div class="share-embed">
    <textarea class="share-embed-code yt-uix-form-input-textarea" readonly></textarea>
    <div class="share-embed-size-list">
      <ul>
        <li class="share-embed-size selected"><label><input type="radio" class="share-embed-size-radio" name="embed-size" value="560x315" data-width="560" data-height="315" checked> 560 &times; 315</label></li>
        <li class="share-embed-size"><label><input type="radio" class="share-embed-size-radio" name="embed-size" value="640x360" data-width="640" data-height="360"> 640 &times; 360</label></li>
        <li class="share-embed-size"><label><input type="radio" class="share-embed-size-radio" name="embed-size" value="853x480" data-width="853" data-height="480"> 853 &times; 480</label></li>
        <li class="share-embed-size"><label><input type="radio" class="share-embed-size-radio" name="embed-size" value="1280x720" data-width="1280" data-height="720"> 1280 &times; 720</label></li>
        <li class="share-embed-size">
          <label><input type="radio" class="share-embed-size-radio share-embed-size-radio-custom" name="embed-size" value="custom" data-width="560" data-height="315"> Custom size:</label>
          <span class="share-embed-customize">
            <input class="share-embed-size-custom-width yt-uix-form-input-text" type="text" maxlength="4"> &times;
            <input class="share-embed-size-custom-height yt-uix-form-input-text" type="text" maxlength="4">
          </span>
        </li>
      </ul>
    </div>
    <div class="share-embed-options">
      <div><label><input type="checkbox" class="share-embed-option" name="show-related" checked> Show suggested videos when the video finishes</label></div>
      <div><label><input type="checkbox" class="share-embed-option" name="delayed-cookies"> Enable privacy-enhanced mode</label></div>
      <div><label><input type="checkbox" class="share-embed-option" name="use-https"> Use HTTPS</label></div>
      <div><label><input type="checkbox" class="share-embed-option" name="use-flash-code"> Use old embed code</label></div>
    </div>
  </div>
HTML;

    echo json_encode([
        'embed_html'  => $embedHtml,
        'legacy_url'  => $legacyUrl,
        'legacy_code' => $legacyCode,
        'iframe_url'  => $iframeUrl,
        'iframe_code' => $iframeCode,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// ─── action_get_email ─────────────────────────────────────────────────────────
// В архиве для разлогиненного email-панель = приглашение войти (data-disabled).
if (isset($req['action_get_email'])) {
    $signEsc = htmlspecialchars($signInUrl, ENT_QUOTES);
    echo json_encode([
        'email_html' => "        <strong><a href=\"{$signEsc}\">Sign in</a> now!\n</strong>\n",
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// ─── action_get_share_urls ────────────────────────────────────────────────────
if (isset($req['action_get_share_urls'])) {
    [$primary] = share_services_all($video_id, $shareTitle, $shareDesc, $shareThumb);
    echo json_encode([
        'url_short'           => $urlShort,
        'url_long'            => $urlLong,
        'share_services_html' => $primary,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['errors' => ['Unknown action.']]);
