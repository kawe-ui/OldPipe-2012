<?php
@ob_start();
@ini_set('display_errors', '0');

if (file_exists(__DIR__ . '/api/servermain.php')) {
    @require_once __DIR__ . '/api/servermain.php';
}

if (session_status() === PHP_SESSION_NONE) {
    if (session_name() !== 'YTSESSID') {
        @session_name('YTSESSID');
    }
    @session_start();
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    @setcookie(session_name(), '', time() - 42000, $p['path'] ?: '/', $p['domain'] ?? '', isset($_SERVER['HTTPS']), true);
}
@session_unset();
@session_destroy();

$cookies = [
    'SID', 'HSID', 'SSID', 'APISID', 'SAPISID',
    '__Secure-1PAPISID', '__Secure-3PAPISID',
    '__Secure-1PSID', '__Secure-3PSID',
    '__Secure-3PSIDCC', '__Secure-3PSIDTS',
    'LOGIN_INFO', 'PREF', 'VISITOR_INFO1_LIVE', 'YSC', 'CONSENT',
    'yt_cookie_jar', 'yt_cookie_user', 'yt_local_user', 'user', 'account'
];

$host = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '');
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

foreach ($cookies as $name) {
    unset($_COOKIE[$name]);
    $domains = ['', $host];
    if ($host) $domains[] = '.' . $host;
    
    foreach (array_unique($domains) as $d) {
        $opt = ['expires' => time() - 42000, 'path' => '/', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax'];
        if ($d !== '') $opt['domain'] = $d;
        @setcookie($name, '', $opt);
    }
    @setcookie($name, '', time() - 42000, '/');
}

if (ob_get_length() > 0) @ob_end_clean();

$redirectTo = '/';
if (!empty($_GET['next']) && is_string($_GET['next'])) {
    $next = trim($_GET['next']);
    if ($next !== '' && $next[0] === '/' && (!isset($next[1]) || $next[1] !== '/')) {
        $redirectTo = $next;
    }
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Location: ' . $redirectTo, true, 303);
exit;