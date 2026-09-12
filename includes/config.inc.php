<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  config.inc.php — глобальная конфигурация YT2012-реставрации
// ═══════════════════════════════════════════════════════════════════════════════

if (defined('YT2012_CONFIG_LOADED')) return;
define('YT2012_CONFIG_LOADED', true);

/*
 * Secrets never belong in the web root or in version control. Production can
 * provide these values with Apache SetEnv / process environment variables. A
 * local PHP array can also live at C:/xampp/private/yt2012-secrets.php (one
 * level above the document root); see security-setup.md.
 */
function yt_secret_config(): array {
    static $values = null;
    if ($values !== null) return $values;

    $values = [];
    $file = dirname(__DIR__, 1) . '/private/yt2012-secrets.php';
    if (is_file($file)) {
        $loaded = require $file;
        if (is_array($loaded)) $values = $loaded;
    }
    return $values;
}

function yt_env(string $name, string $default = ''): string {
    $local = yt_secret_config();
    $value = $local[$name] ?? getenv($name);
    if ($value === false || !is_scalar($value)) $value = $default;
    return trim((string)$value);
}

define('APP_URL', rtrim(yt_env('YT_APP_URL', 'http://localhost:5000/'), '/') . '/');
define('APP_ENV', yt_env('YT_APP_ENV', 'development'));

// ─── Режимы ───────────────────────────────────────────────────────────────────
define('DEBUG_MODE',           false); // true → показывать ошибки PHP + ?apidebug
define('SHOW_PRIVATE_CONTENT', false); // выводить ли приватные/скрытые сущности

if (DEBUG_MODE) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
}

function yt_send_security_headers(): void {
    if (headers_sent()) return;
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
    // Compatible with the legacy inline JavaScript while blocking risky embeds.
    header("Content-Security-Policy: base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'");
}
yt_send_security_headers();

// ─── Прокси для внешних запросов (InnerTube, RYD, googlevideo) ────────────────
// Пусто = без прокси. Тип: CURLPROXY_HTTP | CURLPROXY_SOCKS5.
define('PROXY_HOST', '');
define('PROXY_PORT', 0);
define('PROXY_USER', '');
define('PROXY_PASS', '');
define('PROXY_TYPE', CURLPROXY_HTTP);

// ─── Общие cURL-опции для внешних запросов ────────────────────────────────────
// Единая точка для SSL и прокси: ими пользуются и InnerTube-слой (servermain.php),
// и аннотации, и pfp-эндпоинт. У PHP под Windows curl.cainfo обычно пуст, поэтому
// бандл ищется вручную — без него cURL падает на «unable to get local issuer
// certificate» там, где file_get_contents ещё работал.

/** Путь к cacert.pem или false, если не найден */
function yt_ca_bundle(): string|false {
    static $bundle = null;
    if ($bundle !== null) return $bundle;

    $ini = (string)ini_get('curl.cainfo');
    if ($ini !== '' && is_file($ini)) return $bundle = $ini;

    foreach ([
        'C:/Program Files/Ampps/php/extras/ssl/cacert.pem',
        'C:/Program Files/Ampps/php82/extras/ssl/cacert.pem',
        'C:/Ampps/php/extras/ssl/cacert.pem',
        'C:/xampp/apache/bin/curl-ca-bundle.crt',
        'C:/xampp/php/extras/ssl/cacert.pem',
        '/etc/ssl/certs/ca-certificates.crt',
        '/etc/pki/tls/certs/ca-bundle.crt',
    ] as $c) {
        if (@is_file($c)) return $bundle = $c;
    }
    return $bundle = false;
}

function yt_curl_ssl_opts(CurlHandle $ch): void {
    $bundle = yt_ca_bundle();
    // Never turn TLS verification off: a missing CA bundle must fail closed,
    // not expose OAuth tokens, cookies or API responses to a MITM.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    if ($bundle !== false) curl_setopt($ch, CURLOPT_CAINFO, $bundle);
}

function yt_curl_proxy_opts(CurlHandle $ch): void {
    if (PROXY_HOST === '' || PROXY_PORT <= 0) return;
    curl_setopt($ch, CURLOPT_PROXY,     PROXY_HOST);
    curl_setopt($ch, CURLOPT_PROXYPORT, PROXY_PORT);
    curl_setopt($ch, CURLOPT_PROXYTYPE, PROXY_TYPE);
    if (PROXY_USER !== '') curl_setopt($ch, CURLOPT_PROXYUSERPWD, PROXY_USER . ':' . PROXY_PASS);
}

// ─── ffmpeg (склейка video-only + audio-only для качеств выше 360p) ───────────
// YouTube отдаёт muxed только 360p (itag 18); 480p/720p/1080p и 60fps приходят
// раздельными потоками и склеиваются ремуксом (-c copy, без перекодирования).
$__ff = null;
foreach ([
    'C:/ffmpeg/bin/ffmpeg.exe',
    'C:/Program Files/ffmpeg/bin/ffmpeg.exe',
    'C:/Program Files/Ampps/ffmpeg/bin/ffmpeg.exe',
    '/usr/bin/ffmpeg',
    '/usr/local/bin/ffmpeg',
    'C:\Users\USER\AppData\Local\Microsoft\WinGet\Packages\Gyan.FFmpeg_Microsoft.Winget.Source_8wekyb3d8bbwe\ffmpeg-7.1.1-full_build\bin\ffmpeg.exe'
] as $__c) {
    if (@is_file($__c)) { $__ff = $__c; break; }
}
define('FFMPEG_BIN', $__ff ?? '');           // '' → доступен только 360p
define('FFMPEG_CACHE_MAX_MB', 4096);         // лимит кэша склеенных MP4

// ─── yt-dlp (источник ссылок на потоки) ───────────────────────────────────────
// Зачем он нужен, хотя InnerTube и так отдаёт URL: googlevideo без валидной
// аттестации обрывает adaptive-потоки на ~7-9% файла (403 на большом смещении),
// поэтому качества выше 360p через сырой InnerTube отдать нельзя — плеер встанет
// посреди ролика. Проверено: ANDROID/IOS → 403 на хвосте; ANDROID_VR, TVHTML5,
// ANDROID_UNPLUGGED → LOGIN_REQUIRED; MWEB/WEB_EMBEDDED → UNPLAYABLE; muxed
// itag 22 больше не выдаётся ни для одного видео; PoToken от BotGuard (WEB)
// для ANDROID не подходит — там DroidGuard. yt-dlp решает это сам и отдаёт
// ссылки с полным доступом (проверено: HTTP 206 на хвосте 246-МБ потока).
// Пусто → работает прежний путь через InnerTube, т.е. только 360p.
$__ydl = null;
foreach ([
    'C:/Windows/System32/yt-dlp.exe',
    'C:/Program Files/yt-dlp/yt-dlp.exe',
    'C:/yt-dlp/yt-dlp.exe',
    '/usr/bin/yt-dlp',
    '/usr/local/bin/yt-dlp',
    'C:\nvm4w\nodejs\node_modules\youtube-dl-exec\bin\yt-dlp.exe'
] as $__c) {
    if (@is_file($__c)) { $__ydl = $__c; break; }
}
define('YTDLP_BIN', $__ydl ?? '');
define('YTDLP_TIMEOUT', 90);                 // сек на разбор одного видео

// ─── Кэш ──────────────────────────────────────────────────────────────────────
define('CACHE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/cache');
define('CACHE_TTL_STREAMS',  3 * 3600);   // прямые URL стримов
define('CACHE_TTL_CHANNEL', 24 * 3600);   // метаданные каналов (гайд, шапки)
define('CACHE_TTL_RATINGS', 30 * 86400);  // последние известные лайки/дизлайки
define('CACHE_TTL_COMMENTS',     1800);   // токены/страницы комментариев
define('CACHE_TTL_METADATA',      600);   // /get_video_metadata (панель More info)
define('CACHE_TTL_ANNOTATIONS', 30 * 86400); // XML аннотаций из архива (не меняется)
define('CACHE_TTL_PFP',         30 * 86400); // вердикт «монограмма / живая ава»

if (!is_dir(CACHE_DIR)) {
    @mkdir(CACHE_DIR, 0775, true);
}

// ─── Дефолтные ассеты-заглушки (стиль 2012) ───────────────────────────────────
// Дефолтный аватар канала — локальная копия синей «силуэт»-заглушки 2012 года
define('DEFAULT_CHANNEL_AVATAR', '/dynamic/pfp/default.png');
define('DEFAULT_VIDEO_THUMB',    '/yts/img/pixel-vfl3z5WfW.gif');
define('PIXEL_GIF',              '/yts/img/pixel-vfl3z5WfW.gif');

// Эндпоинт, решающий «монограмма → заглушка 2012 / живая ава → отдать как есть»
define('PFP_API_URL', '/dynamic/pfp/pfp.php');

// Хосты, с которых YouTube отдаёт аватары (allow-list против SSRF в pfp.php)
const PFP_ALLOWED_HOSTS = [
    'yt3.ggpht.com', 'yt4.ggpht.com', 'lh3.ggpht.com',
    'yt3.googleusercontent.com', 'yt4.googleusercontent.com',
    'lh3.googleusercontent.com', 'yt3.gstatic.com',
];

/** Приводит //host/… и http://host/… к https://host/…; '' если это не http(s)-URL */
function pfp_normalize_url(string $url): string {
    $url = trim($url);
    if ($url === '') return '';
    if (str_starts_with($url, '//')) $url = 'https:' . $url;
    if (!preg_match('~^https?://~i', $url)) return '';
    return preg_replace('~^http://~i', 'https://', $url);
}

/** Аватар ли это с googleusercontent/ggpht (только такие имеет смысл проверять) */
function pfp_is_google_avatar(string $url): bool {
    $host = strtolower((string)parse_url($url, PHP_URL_HOST));
    return $host !== '' && in_array($host, PFP_ALLOWED_HOSTS, true);
}

// Нормализует URL аватара для вывода в <img src>.
//
// Пусто → заглушка 2012 сразу. Иначе — прогоняем через PFP_API_URL: у каналов
// без аватара YouTube не отдаёт «пустоту», а генерирует монограмму (буква на
// цветном фоне) и присылает её обычным ytc/AIdro_…-URL, неотличимым от живой
// авы. Поэтому решение принимается по самой картинке, а не по строке URL, и
// вынесено в эндпоинт: рендер страницы остаётся без сетевых запросов, а браузер
// тянет аватары параллельно (см. dynamic/pfp/pfp.php).
function default_avatar(?string $url): string {
    $raw = trim((string)$url);
    if ($raw === '') return DEFAULT_CHANNEL_AVATAR;

    $norm = pfp_normalize_url($raw);
    // Не http(s) — это локальный путь вроде /yts/img/…: отдаём как есть
    if ($norm === '') return $raw;
    // Внешний, но не аватар Google — проверять нечего
    if (!pfp_is_google_avatar($norm)) return $norm;

    return PFP_API_URL . '?u=' . urlencode($norm);
}

// ─── Google APIs (существующая интеграция) ────────────────────────────────────
define('GOOGLE_API_KEY',       yt_env('YT_GOOGLE_API_KEY'));
define('GOOGLE_CLIENT_ID',     yt_env('YT_GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', yt_env('YT_GOOGLE_CLIENT_SECRET'));// rtrim: APP_URL заканчивается на «/», без обрезки получился бы «localhost//auth…»,
// а Google сверяет redirect_uri посимвольно с тем, что зарегистрирован в консоли.
define('GOOGLE_REDIRECT_URI',  rtrim(APP_URL, '/') . '/auth/google/callback');

define('GOOGLE_API_BASE_URL',            'https://www.googleapis.com');

define('GOOGLE_REQUEST_TIMEOUT',          10);   // seconds
define('GOOGLE_REQUEST_CONNECT_TIMEOUT',   5);   // seconds

define('GOOGLE_MAX_RETRIES',               3);
define('GOOGLE_RETRY_DELAY_MS',          500);   // milliseconds between retries

define('GOOGLE_REQUESTS_PER_SECOND',      10);
define('GOOGLE_DAILY_QUOTA_LIMIT',     10000);

define('GOOGLE_RESPONSE_FORMAT',       'json');  // 'json' | 'xml'
define('GOOGLE_SSL_VERIFY', APP_ENV === 'production');
define('GOOGLE_SCOPES', implode(' ', [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile',
    'openid',
    'https://www.googleapis.com/auth/youtube.readonly',   // узнать канал юзера (My channel)
    'https://www.googleapis.com/auth/youtube.force-ssl',  // лайки/подписки/комменты через Data API
    // drive.readonly / calendar.readonly убраны: в коде не используются, а как
    // «restricted»-scopes они ужесточают проверки Google на экране согласия.
]));

// ─── Сессия (нужна OAuth-входу) ───────────────────────────────────────────────
// Поднимаем сессию в режиме read-and-close: masthead на любой странице узнаёт,
// залогинен ли юзер, но лок сессии не держится — стриминг видео и AJAX-и не
// сериализуются. Пишут в сессию только /auth/google/* (там свой read/write старт).
require_once __DIR__ . '/session.inc.php';
yt_session_start_readonly();
// Page templates render the CSRF token after their first HTML markup, so create
// it here while headers are still available. API/media requests remain stateless.
if (str_contains(strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? '')), 'text/html')) {
    yt_session_ensure_csrf_token();
}

// Ленивая дорезолвка канала OAuth-юзера — именно ЗДЕСЬ, пока HTML ещё не
// начал выводиться: результат дописывается в сессию, а переоткрыть её на
// запись PHP разрешает только до отправки заголовков. Из header.php (он
// рендерится посреди страницы) сделать это уже нельзя.
if (!empty($_SESSION['google_user']) && empty($_SESSION['google_user']['channel_id'])) {
    require_once __DIR__ . '/auth.inc.php';
    // function_exists — на случай включения auth.inc.php раньше config.inc.php
    // (циклический require_once оставил бы функцию ещё не объявленной).
    if (function_exists('yt_google_fill_channel')) {
        yt_google_fill_channel($_SESSION['google_user']);
    }
}
