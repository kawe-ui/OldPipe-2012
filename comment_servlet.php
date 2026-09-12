<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  /comment_servlet — добавление комментария, контракт 2012-фронтенда:
//    query: add_comment=1&return_ajax=true&len=..&wc=..[&reply=1]
//    body:  session_token, video_id, comment, reply_parent_id, source, screen
//    ответ: XML <root><return_code>0</return_code><str_code>OK</str_code>
//               <html_content><![CDATA[<li class="comment">…]]></html_content></root>
//    str_code ≠ OK → фронтенд показывает сообщение об ошибке.
// ═══════════════════════════════════════════════════════════════════════════════

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php';

function cs_xml(int $code, string $strCode, string $html = ''): never {
    header('Content-Type: text/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?><root>'
       . '<return_code>' . $code . '</return_code>'
       . '<str_code>' . htmlspecialchars($strCode) . '</str_code>'
       . ($html !== '' ? '<html_content><![CDATA[' . $html . ']]></html_content>' : '')
       . '</root>';
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') cs_xml(1, 'FAILED');
if (!isset($_GET['add_comment']))                  cs_xml(1, 'FAILED');
if (!yt_session_token_valid($_POST['session_token'] ?? null)) cs_xml(1, 'FAILED');

$videoId  = trim((string)($_POST['video_id'] ?? ''));
$text     = trim((string)($_POST['comment'] ?? ''));
$parentId = trim((string)($_POST['reply_parent_id'] ?? ''));

if ($videoId === '' || $text === '') cs_xml(1, 'FAILED');
if (mb_strlen($text) > 500)          cs_xml(1, 'FAILED');

$err = yt_action_comment($videoId, $text, $parentId);
if ($err !== null) cs_xml(1, 'FAILED');

// ── Успех: рендерим <li> в разметке watch-страницы ────────────────────────────
$acct      = yt_account_info();
$author    = $acct['name'] ?? 'You';
$authorUrl = !empty($acct['channelId']) ? '/channel/' . $acct['channelId'] : '/';

$html =
  '<li class="comment yt-tile-default" data-author-id="' . htmlspecialchars($acct['channelId'] ?? '') . '" data-id="">'
. '<div class="comment-body"><div class="content-container"><div class="content">'
. '<div class="comment-text" dir="ltr"><p>' . htmlspecialchars($text) . '</p></div>'
. '<p class="metadata"><span class="author ">'
. '<a href="' . htmlspecialchars($authorUrl) . '" class="yt-uix-sessionlink yt-user-name " dir="ltr">' . htmlspecialchars($author) . '</a>'
. '</span><span class="time" dir="ltr">just now</span></p>'
. '</div></div></div></li>';

cs_xml(0, 'OK', $html);
