<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  token_ajax.php — выдача ajax-токенов (протокол 2012)
//  www-core: GET /token_ajax?action_get_watch_actions_token=1, format RAW,
//  ответ парсится функцией Je() как QUERYSTRING (не JSON!):
//      watch_actions_ajax_token=…&addto_ajax_token=…
//  Токен настоящий (HMAC из ytactions) — эндпоинты действий его проверяют.
// ═══════════════════════════════════════════════════════════════════════════════

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php';

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache');

$token = yt_session_token();

echo http_build_query([
    'watch_actions_ajax_token' => $token,
    'html5_ajax_token'         => $token,
    'addto_ajax_token'         => $token,
]);
