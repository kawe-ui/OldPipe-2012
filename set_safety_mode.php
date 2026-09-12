<?php
// Receives the archived safety-picker form.
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php';

function safety_mode_return_path(mixed $url): string {
    $url = (string)$url;
    if ($url === '' || str_starts_with($url, '//')) return '/';

    $parts = parse_url($url);
    if ($parts === false || isset($parts['scheme'], $parts['host'], $parts['user'], $parts['pass'])) return '/';

    $path = (string)($parts['path'] ?? '/');
    if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) return '/';
    return $path . (isset($parts['query']) ? '?' . $parts['query'] : '');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}
if (!yt_session_token_valid($_POST['session_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid CSRF token.');
}

if (isset($_POST['safety_mode'])) {
    yt_set_preference_cookie('PREF_safety', $_POST['safety_mode'] === 'true' ? '1' : '0');
}

header('Location: ' . safety_mode_return_path($_POST['next_url'] ?? ($_SERVER['HTTP_REFERER'] ?? '/')), true, 303);
exit;