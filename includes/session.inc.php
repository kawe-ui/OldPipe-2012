<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  session.inc.php — единая точка старта PHP-сессии для OAuth-входа
//
//  Две функции намеренно разделены:
//    yt_session_start()          — read/write, держит файловый лок сессии до конца
//                                  запроса. Нужен ТОЛЬКО там, где мы пишем в сессию
//                                  (login / callback / logout).
//    yt_session_start_readonly() — читает сессию и СРАЗУ отпускает лок
//                                  (session_start(['read_and_close' => true])).
//                                  Вызывается из config.inc.php на каждой странице,
//                                  чтобы masthead знал, залогинен ли юзер, но при
//                                  этом не сериализовать параллельные запросы
//                                  (важно для стриминга видео и AJAX-ов).
// ═══════════════════════════════════════════════════════════════════════════════

if (defined('YT2012_SESSION_LOADED')) return;
define('YT2012_SESSION_LOADED', true);

const YT_SESSION_NAME = 'YT2012SESS';

function yt_request_is_https(): bool {
    return (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
}

function yt_session_cookie_setup(): void {
    // Refuse attacker-supplied session ids and avoid URL-based session ids.
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_name(YT_SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        // Lax keeps OAuth's top-level callback working while protecting posts.
        'samesite' => 'Lax',
        'httponly' => true,
        'secure'   => yt_request_is_https(),
    ]);
}

/** Полноценная сессия для записи. true — сессия активна и её можно писать. */
/** Cookie for a UI preference: inaccessible to JavaScript and protected on HTTPS. */
function yt_set_preference_cookie(string $name, string $value): void {
    setcookie($name, $value, [
        'expires'  => time() + 86400 * 365,
        'path'     => '/',
        'secure'   => yt_request_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/** Create the per-session CSRF token while headers can still be sent. */
function yt_session_ensure_csrf_token(): void {
    if (!yt_session_start()) return;
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
    session_write_close();
}

function yt_session_start(): bool {
    if (PHP_SAPI === 'cli') return false;
    if (session_status() === PHP_SESSION_ACTIVE) return true;
    if (headers_sent()) return false;
    yt_session_cookie_setup();
    return @session_start();
}

/**
 * Только чтение: поднимает $_SESSION и тут же снимает лок. Анонимным
 * посетителям (без куки сессии) не создаёт пустую сессию и не шлёт Set-Cookie.
 */
function yt_session_start_readonly(): bool {
    if (PHP_SAPI === 'cli') return false;
    if (session_status() === PHP_SESSION_ACTIVE) return true;
    if (headers_sent()) return false;
    if (empty($_COOKIE[YT_SESSION_NAME])) return false;   // гость — сессии нет
    yt_session_cookie_setup();
    return @session_start(['read_and_close' => true]);
}

/**
 * Дозапись после read_and_close: заново открывает ту же сессию на запись.
 * Возможна ТОЛЬКО до отправки заголовков — после начала вывода PHP уже не
 * даёт выставить имя/куки сессии (поэтому дорезолвка канала живёт в
 * config.inc.php, а не в header.php). После правки $_SESSION обязательно
 * вызвать session_write_close(), чтобы не держать файловый лок.
 */
function yt_session_reopen_write(): bool {
    if (PHP_SAPI === 'cli') return false;
    if (session_status() === PHP_SESSION_ACTIVE) return true;  // уже открыта на запись
    if (headers_sent()) return false;                          // поздно — вывод уже пошёл
    if (empty($_COOKIE[YT_SESSION_NAME])) return false;        // гость — писать некуда
    yt_session_cookie_setup();
    session_cache_limiter('');                                 // не слать cache-заголовки повторно
    return @session_start();
}
