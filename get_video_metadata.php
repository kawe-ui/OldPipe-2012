<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  get_video_metadata.php — панель «More info» плеера 2012
//  /get_video_metadata?video_id=XXXXXXXXXXX[&html5=1]
//
//  html5=1 → JSON (html5player-vflfWlguy.js, метод Tj), иначе XML (флеш-плеер).
//  Источник данных — InnerTube (/player + /next) + RYD, через yt_video_metadata().
//  Никаких сторонних инстансов Invidious: всё берётся напрямую у YouTube.
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

header('Cache-Control: no-cache');

$video_id = trim($_GET['video_id'] ?? ($_GET['v'] ?? ''));
$html5    = !empty($_GET['html5']);

$meta = yt_video_metadata($video_id);
$ok   = ($meta !== null && ($meta['status'] ?? '') === 'OK');

// ─── Строки в формате 2012 ────────────────────────────────────────────────────
$views      = $ok ? (int)$meta['viewCount'] : 0;
$likes      = $ok ? (int)($meta['likes']    ?? 0) : 0;
$dislikes   = $ok ? (int)($meta['dislikes'] ?? 0) : 0;
$author     = $ok ? $meta['author']          : '';
$authorId   = $ok ? $meta['authorId']        : '';
$avatar     = $ok ? $meta['authorAvatar']    : DEFAULT_CHANNEL_AVATAR;
$subCount   = $ok ? $meta['subscriberCount'] : '';
$title      = $ok ? $meta['title']           : '';
$descr      = $ok ? $meta['description']     : '';
$has_cc = $ok ? (!empty($meta['has_cc']) || !empty($meta['captions'])) : false;

$viewsFmt        = number_format($views, 0, '.', ',');
$likesDislikes   = number_format($likes, 0, '.', ',') . ' likes, '
                 . number_format($dislikes, 0, '.', ',') . ' dislikes';
$viewCountString = '<strong>' . $viewsFmt . '</strong> views';
$subCountString  = $subCount !== '' ? '<strong>' . $subCount . '</strong> subscribers' : '';

// Кнопка подписки — разметка 2012 с подставленным id канала
$subscribeButtonHtml =
    '<span class=" yt-uix-button-subscription-container" >'
  . '<button type="button" class="yt-uix-subscription-button yt-uix-button yt-uix-button-subscribe-branded yt-uix-button-size-default"'
  . ' aria-live="polite" aria-busy="false" onclick=";return false;" aria-role="button"'
  . ' data-channel-external-id="' . htmlspecialchars($authorId, ENT_QUOTES, 'UTF-8') . '"'
  . ' data-style-type="branded" role="button">'
  . '<span class="yt-uix-button-icon-wrapper">'
  . '<img class="yt-uix-button-icon yt-uix-button-icon-subscribe" src="' . PIXEL_GIF . '" alt="" title="">'
  . '<span class="yt-uix-button-valign"></span></span>'
  . '<span class="yt-uix-button-content">'
  . '<span class="subscribe-label" aria-label="Subscribe">Subscribe</span>'
  . '<span class="subscribed-label" aria-label="Unsubscribe">Subscribed</span>'
  . '<span class="unsubscribe-label" aria-label="Unsubscribe">Unsubscribe</span>'
  . '</span></button>'
  . '<span class="yt-subscription-button-subscriber-count-branded-horizontal" >'
  . htmlspecialchars($subCount, ENT_QUOTES, 'UTF-8') . '</span>'
  . '<span class="yt-subscription-button-disabled-mask" title=""></span>'
  . '</span>';

// ─── html5=1 → JSON ───────────────────────────────────────────────────────────
if ($html5) {
    header('Content-Type: application/json; charset=utf-8');

    if (!$ok) {
        echo json_encode(['errorcode' => 100, 'reason' => $meta['reason'] ?? 'This video is unavailable.'],
            JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        'user_info' => [
            'channel_logo_url'          => $avatar,
            'username'                  => $author,
            'subscriber_count'          => $subCount,
            'external_id'               => $authorId,
            'subscription_button_html'  => $subscribeButtonHtml,
            'image_url'                 => $avatar,
            'public_name'               => $author,
            'channel_title'             => $author,
            'subscriber_count_string'   => $subCountString,
            'channel_banner_url'        => '',
        ],
        'video_info' => [
            'view_count_string'         => $viewCountString,
            'description'               => $descr,
            'dislikes_count_unformatted' => $dislikes,
            'likes_count_unformatted'   => $likes,
            'likes_dislikes_string'     => $likesDislikes,
            'view_count'                => $viewsFmt,
            'subscription_ajax_token'   => '',
            'has_cc'                    => $has_cc ? '1' : '0',
        ],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ─── Иначе → XML для флеш-плеера ──────────────────────────────────────────────
header('Content-Type: text/xml; charset=utf-8');

// CDATA не экранирует ничего, кроме собственного терминатора «]]>»
function _gvm_cdata(string $s): string {
    return '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $s) . ']]>';
}

$xml  = '<?xml version="1.0" encoding="utf-8"?>';
$xml .= '<root><html_content>';

$xml .= '<video_info>';
$xml .= '<subscription_ajax_token>' . _gvm_cdata('') . '</subscription_ajax_token>';
$xml .= '<likes_dislikes_string>'   . _gvm_cdata($likesDislikes) . '</likes_dislikes_string>';
$xml .= '<view_count>'              . _gvm_cdata($viewsFmt) . '</view_count>';
$xml .= '<dislikes_count_unformatted>' . _gvm_cdata((string)$dislikes) . '</dislikes_count_unformatted>';
$xml .= '<likes_count_unformatted>' . _gvm_cdata((string)$likes) . '</likes_count_unformatted>';
$xml .= '<description>'             . _gvm_cdata($descr) . '</description>';
$xml .= '<view_count_string>'       . _gvm_cdata($viewsFmt . ' views') . '</view_count_string>';
$xml .= '</video_info>';

$xml .= '<user_info>';
$xml .= '<public_name>'         . _gvm_cdata($author) . '</public_name>';
$xml .= '<channel_logo_url>'    . _gvm_cdata($avatar) . '</channel_logo_url>';
$xml .= '<channel_title>'       . _gvm_cdata($author) . '</channel_title>';
$xml .= '<username>'            . _gvm_cdata($author) . '</username>';
$xml .= '<subscriber_count>'    . _gvm_cdata($subCount) . '</subscriber_count>';
$xml .= '<channel_paid>'        . _gvm_cdata('0') . '</channel_paid>';
$xml .= '<channel_external_id>' . _gvm_cdata($authorId) . '</channel_external_id>';
$xml .= '<external_id>'         . _gvm_cdata($authorId) . '</external_id>';
$xml .= '<channel_url>'         . _gvm_cdata($authorId !== '' ? '/channel/' . $authorId : '') . '</channel_url>';
$xml .= '<channel_banner_url>'  . _gvm_cdata('') . '</channel_banner_url>';
$xml .= '<external_channel_id>' . _gvm_cdata($authorId) . '</external_channel_id>';
$xml .= '<image_url>'           . _gvm_cdata($avatar) . '</image_url>';
$xml .= '</user_info>';

$xml .= '<watch_next>';
$xml .= '<view_count>' . _gvm_cdata($viewsFmt) . '</view_count>';
$xml .= '<title>'      . _gvm_cdata($title) . '</title>';
$xml .= '<id>'         . _gvm_cdata($video_id) . '</id>';
$xml .= '</watch_next>';

$xml .= '</html_content>';
$xml .= '<return_code>' . _gvm_cdata($ok ? '0' : '1') . '</return_code>';
$xml .= '</root>';

echo $xml;
