<?php
/**
 * /dynamic/pfp/pfp.php?u=<url>  — прокси аватарок (YouTube / Google / любые http)
 * /dynamic/pfp/default.png      — раздаётся как статика или этим же скриптом ?default=1
 *
 * Кэш: /dynamic/pfp/cache/<hash>
 */
declare(strict_types=1);

$cacheDir = __DIR__ . '/cache';
if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0755, true);
}

// default.png
if (isset($_GET['default']) || (isset($_SERVER['REQUEST_URI']) && (substr(strtok($_SERVER['REQUEST_URI'], '?'), -12) === '/default.png'))) {
    $defaultFile = __DIR__ . '/default.png';
    if (is_file($defaultFile)) {
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=86400');
        readfile($defaultFile);
        exit;
    }
    // 1x1 transparent fallback
    header('Content-Type: image/png');
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    exit;
}

$u = isset($_GET['u']) ? (string)$_GET['u'] : '';
$u = trim($u);
if ($u === '' || !preg_match('#^https?://#i', $u)) {
    http_response_code(400);
    header('Content-Type: text/plain');
    echo 'bad url';
    exit;
}

// Только картинки с доверенных хостов + общий https
$host = strtolower((string)(parse_url($u, PHP_URL_HOST) ?? ''));
$allow = (
    (substr($host, -10) === '.ggpht.com' || $host === 'ggpht.com')
    || (substr($host, -21) === '.googleusercontent.com' || $host === 'googleusercontent.com')
    || (substr($host, -9) === '.ytimg.com' || $host === 'ytimg.com')
    || (substr($host, -11) === '.google.com' || $host === 'google.com')
    || (substr($host, -11) === '.gstatic.com' || $host === 'gstatic.com')
    || preg_match('#^[a-z0-9.-]+$#', $host)
);
if (!$allow || $host === '') {
    http_response_code(403);
    exit;
}

$hash = sha1($u);
$cacheFile = $cacheDir . '/' . $hash;
$metaFile  = $cacheFile . '.meta';
$ttl = 7 * 86400;

if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
    $ctype = 'image/jpeg';
    if (is_file($metaFile)) {
        $m = json_decode((string)file_get_contents($metaFile), true);
        if (!empty($m['ctype'])) $ctype = $m['ctype'];
    }
    header('Content-Type: ' . $ctype);
    header('Cache-Control: public, max-age=' . $ttl);
    header('X-Pfp-Cache: HIT');
    readfile($cacheFile);
    exit;
}

$ch = curl_init($u);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
]);
$body = curl_exec($ch);
$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
$ctype = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

if ($code < 200 || $code >= 300 || !is_string($body) || $body === '') {
    // fallback default
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=3600');
    $defaultFile = __DIR__ . '/default.png';
    if (is_file($defaultFile)) {
        readfile($defaultFile);
    } else {
        echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    }
    exit;
}

// Нормализуем content-type
if ($ctype === '' || stripos($ctype, 'image/') === false) {
    $ctype = 'image/jpeg';
} else {
    $ctype = explode(';', $ctype)[0];
}

@file_put_contents($cacheFile, $body);
@file_put_contents($metaFile, json_encode(['ctype' => $ctype, 'src' => $u]));

header('Content-Type: ' . $ctype);
header('Cache-Control: public, max-age=' . $ttl);
header('X-Pfp-Cache: MISS');
echo $body;
