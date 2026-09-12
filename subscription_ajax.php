<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  /subscription_ajax — подписка/отписка, контракт 2012-фронтенда:
//    подписка:  query action_create_subscription_to_channel=1&c=<UCID>
//    отписка:   query action_remove_subscriptions=1, body s=<id> (у нас = UCID)
//    body:      session_token
//    ответ:     JSON {"response": {...}} (успех = сам факт JSON-объекта)
// ═══════════════════════════════════════════════════════════════════════════════

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php';

function sub_json(array $data, int $http = 200): never {
    http_response_code($http);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') sub_json(['errors' => ['POST only']], 400);
if (!yt_session_token_valid($_POST['session_token'] ?? null)) sub_json(['errors' => ['Bad token']], 403);

if (isset($_GET['action_create_subscription_to_channel'])) {
    $channelId = (string)($_GET['c'] ?? '');
    if (!yt_action_subscribe($channelId)) sub_json(['errors' => ['Subscribe failed']], 500);
    // subscription_id = UCID: фронтенд вернёт его в body `s` при отписке
    sub_json(['response' => ['show_feed_privacy_dialog' => false, 'subscription_id' => $channelId]]);
}

if (isset($_GET['action_remove_subscriptions'])) {
    $channelId = (string)($_POST['s'] ?? ($_POST['c'] ?? ''));
    if (!yt_action_unsubscribe($channelId)) sub_json(['errors' => ['Unsubscribe failed']], 500);
    sub_json(['response' => ['success' => true]]);
}

sub_json(['errors' => ['Unknown action']], 400);
