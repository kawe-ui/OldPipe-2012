<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  /watch_actions_ajax — лайк/дизлайк/снять оценку, контракт 2012-фронтенда:
//    query:  action_like_video=1 | action_dislike_video=1 | action_indifferent_video=1
//            + video_id, plid
//    body:   session_token, screen
//    ответ:  XML <root><return_code>0</return_code></root> (0 = успех)
// ═══════════════════════════════════════════════════════════════════════════════

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php';

function wa_xml(int $code, string $msg = '', string $html = ''): never {
    header('Content-Type: text/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?><root><return_code>' . $code . '</return_code>'
       . ($msg !== ''  ? '<error_message><![CDATA[' . $msg . ']]></error_message>' : '')
       . ($html !== '' ? '<html_content><![CDATA[' . $html . ']]></html_content>' : '')
       . '</root>';
    exit;
}

/** Панель подтверждения: успех-обработчик лайка вставляет html_content в #watch-actions-ajax */
function wa_confirm_html(string $message): string {
    return '<div class="yt-alert yt-alert-naked yt-alert-success  ">'
         . '<div class="yt-alert-icon"><img src="/yts/img/pixel-vfl3z5WfW.gif" class="icon master-sprite" alt="Alert icon"></div>'
         . '<div class="yt-alert-content" role="alert"><span class="yt-alert-vertical-trick"></span>'
         . '<div class="yt-alert-message">' . htmlspecialchars($message) . '</div>'
         . '</div></div>';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') wa_xml(1, 'POST only');
if (!yt_session_token_valid($_POST['session_token'] ?? null)) wa_xml(1, 'Bad token');

$videoId = (string)($_GET['video_id'] ?? '');
$rating  = isset($_GET['action_like_video'])    ? 'like'
        : (isset($_GET['action_dislike_video']) ? 'dislike'
        : (isset($_GET['action_indifferent_video']) ? 'none' : ''));

if ($videoId === '' || $rating === '') wa_xml(1, 'Bad request');

if (!yt_action_rate($videoId, $rating)) {
    wa_xml(1, 'Could not save your rating. Sign in and try again.');
}

$confirm = [
    'like'    => 'You like this video. Added to your Liked videos.',
    'dislike' => 'You dislike this video.',
    'none'    => '',
][$rating];

wa_xml(0, '', $confirm !== '' ? wa_confirm_html($confirm) : '');
