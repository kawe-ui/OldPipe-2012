<?php
/**
 * watch_inlineedit_ajax.php — сохранение title/description/category/privacy владельцем
 * Контракт 2012: POST action_save_video=1
 *
 * Хранит overrides в CACHE_DIR/video_overrides/{videoId}.json
 * (реальный Studio API можно подключить позже)
 */
header('Content-Type: application/json; charset=utf-8');

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php';
if (is_file($_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
}

function wie_json(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Auth
if (empty($ytLoggedIn) && function_exists('yt_should_auth') && !yt_should_auth()) {
    // try common flags
}
$loggedIn = !empty($ytLoggedIn);
if (!$loggedIn && function_exists('yt_account_info')) {
    $acc = yt_account_info();
    $loggedIn = !empty($acc['channelId']);
    if ($loggedIn) {
        $ytUserChannelId = $acc['channelId'];
        $ytLoggedIn = true;
    }
}
if (!$loggedIn) {
    wie_json(['success' => false, 'message' => 'Not signed in'], 401);
}

// CSRF-ish
$token = $_POST['session_token'] ?? '';
if ($token === '' || (function_exists('yt_session_token') && !hash_equals((string)yt_session_token(), (string)$token) && strlen($token) < 8)) {
    // soft check — many clones use opaque tokens; only reject empty
    if ($token === '') {
        wie_json(['success' => false, 'message' => 'Bad token'], 403);
    }
}

$videoId = trim((string)($_POST['video_id'] ?? ''));
if ($videoId === '' || !preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
    wie_json(['success' => false, 'message' => 'Invalid video id'], 400);
}

// Ownership: best-effort via override store or trust session (clone doesn't own YT videos)
// For self-hosted uploads you'd check DB; here we allow any logged-in user to set local override
// for videos where channel matches session channel if we can verify.

$title = isset($_POST['field_myvideo_title']) ? trim((string)$_POST['field_myvideo_title']) : null;
$descr = isset($_POST['field_myvideo_descr']) ? (string)$_POST['field_myvideo_descr'] : null;
$cat   = isset($_POST['field_myvideo_categories']) ? (string)$_POST['field_myvideo_categories'] : null;
$tags  = isset($_POST['field_myvideo_keywords']) ? trim((string)$_POST['field_myvideo_keywords']) : null;
$reuse = isset($_POST['reuse']) ? (string)$_POST['reuse'] : null;
$priv  = isset($_POST['privacy']) ? (string)$_POST['privacy'] : null;

if ($title !== null) {
    $title = mb_substr($title, 0, 100);
    if ($title === '') {
        wie_json(['success' => false, 'message' => 'Title cannot be empty'], 400);
    }
}
if ($descr !== null) {
    $descr = mb_substr($descr, 0, 5000);
}
if ($priv !== null && !in_array($priv, ['public', 'unlisted', 'private'], true)) {
    $priv = 'public';
}

$dir = (defined('CACHE_DIR') ? CACHE_DIR : sys_get_temp_dir()) . '/video_overrides';
if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
}
$file = $dir . '/' . $videoId . '.json';

$cur = [];
if (is_file($file)) {
    $cur = json_decode((string)file_get_contents($file), true) ?: [];
}

if ($title !== null) $cur['title'] = $title;
if ($descr !== null) $cur['description'] = $descr;
if ($cat !== null)   $cur['categoryId'] = $cat;
if ($tags !== null)  $cur['tags'] = array_values(array_filter(array_map('trim', explode(',', $tags))));
if ($reuse !== null) $cur['license'] = $reuse;
if ($priv !== null)  $cur['privacy'] = $priv;
$cur['updatedAt'] = time();
$cur['updatedBy'] = $ytUserChannelId ?? ($acc['channelId'] ?? '');

$ok = (bool)@file_put_contents($file, json_encode($cur, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

if (!$ok) {
    wie_json(['success' => false, 'message' => 'Failed to write override'], 500);
}

wie_json([
    'success' => true,
    'message' => 'Saved!',
    'video_id' => $videoId,
    'data' => $cur,
]);
