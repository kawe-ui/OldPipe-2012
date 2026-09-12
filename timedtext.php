<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

header('Access-Control-Allow-Origin: *');

// Fail loudly and *visibly* if servermain.php didn't define what we need,
// instead of letting an "Undefined constant" fatal produce a blank 500.
foreach (['CACHE_DIR', 'CACHE_TTL_STREAMS', 'CACHE_TTL_TIMEDTEXT'] as $const) {
    if (!defined($const)) {
        header('Content-Type: text/plain', true, 500);
        echo "timedtext.php: missing required constant {$const} (check servermain.php)";
        exit;
    }
}

const TT_EMPTY_LIST  = '<?xml version="1.0" encoding="utf-8" ?><transcript_list></transcript_list>';
const TT_EMPTY_TRACK = '<?xml version="1.0" encoding="utf-8" ?><transcript></transcript>';

/** Отдаёт тело и завершает запрос */
function tt_output(string $body, string $ctype = 'text/xml; charset=UTF-8') {
    header('Content-Type: ' . $ctype);
    header('Content-Length: ' . strlen($body));
    echo $body;
    exit;
}

/** Гарантирует существование каталога кэша, возвращает false вместо фатала при неудаче */
function tt_cache_dir(): string|false {
    $dir = CACHE_DIR . '/timedtext';
    if (is_dir($dir)) return $dir;
    if (@mkdir($dir, 0775, true) || is_dir($dir)) return $dir;
    error_log("timedtext.php: failed to create cache dir {$dir}");
    return false;
}

$video_id = (string)($_GET['v'] ?? ($_GET['video_id'] ?? ''));
if (preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id) !== 1) {
    tt_output(TT_EMPTY_LIST);
}

// ─── Список треков из InnerTube, с кэшем ─────────────────────────────────────
function tt_tracks(string $vid): array {
    $dir = tt_cache_dir();
    $cf = $dir !== false ? $dir . '/' . $vid . '.json' : false;

    if ($cf !== false && is_file($cf) && (time() - filemtime($cf)) < CACHE_TTL_STREAMS) {
        $raw = @file_get_contents($cf);
        if ($raw !== false && $raw !== '') {
            $c = json_decode($raw, true);
            if (is_array($c) && isset($c['tracks']) && is_array($c['tracks'])) {
                return $c['tracks'];
            }
            // corrupt cache file — don't trust it, fall through to refetch
            error_log("timedtext.php: corrupt cache json for {$vid}, refetching");
        }
    }

    try {
        $d = innertube_post_as('player',
            ['videoId' => $vid, 'racyCheckOk' => true, 'contentCheckOk' => true],
            ['clientName' => 'ANDROID', 'clientVersion' => '20.10.38',
             'androidSdkVersion' => 30, 'osName' => 'Android', 'osVersion' => '11'],
            ['User-Agent: com.google.android.youtube/20.10.38 (Linux; U; Android 11) gzip',
             'X-YouTube-Client-Name: 3', 'X-YouTube-Client-Version: 20.10.38']);
    } catch (\Throwable $e) {
        error_log('timedtext.php innertube_post_as failed: ' . $e->getMessage());
        $d = null;
    }
    if ($d === null || !is_array($d)) return [];

    $tracks = [];
    foreach (($d['captions']['playerCaptionsTracklistRenderer']['captionTracks'] ?? []) as $t) {
        if (empty($t['baseUrl']) || empty($t['languageCode'])) continue;
        $tracks[] = [
            'baseUrl' => $t['baseUrl'],
            'lang'    => $t['languageCode'],
            'kind'    => $t['kind'] ?? '',
            'name'    => $t['name']['simpleText'] ?? ($t['name']['runs'][0]['text'] ?? ''),
            'cantran' => !empty($t['isTranslatable']),
        ];
    }

    if ($cf !== false) {
        $encoded = json_encode(['tracks' => $tracks], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($encoded !== false) {
            @file_put_contents($cf, $encoded, LOCK_EX);
        }
    }
    return $tracks;
}

// ─── type=list → <transcript_list> ───────────────────────────────────────────
if (($_GET['type'] ?? '') === 'list') {
    $xml = '<?xml version="1.0" encoding="utf-8" ?>'
         . '<transcript_list docid="' . sprintf('%u', crc32($video_id)) . '">';
    foreach (tt_tracks($video_id) as $i => $t) {
        $name = htmlspecialchars($t['name'], ENT_QUOTES | ENT_XML1, 'UTF-8');
        $lang = htmlspecialchars($t['lang'], ENT_QUOTES | ENT_XML1, 'UTF-8');
        $xml .= '<track id="' . $i . '" name="' . $name . '"'
              . ' lang_code="' . $lang . '"'
              . ' lang_original="' . $name . '" lang_translated="' . $name . '"'
              . ($i === 0 ? ' lang_default="true"' : '')
              . ($t['kind'] !== '' ? ' kind="' . htmlspecialchars($t['kind'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '"' : '')
              . ($t['cantran'] ? ' cantran="true"' : '')
              . '/>';
    }
    tt_output($xml . '</transcript_list>');
}

// ─── Запрос трека ────────────────────────────────────────────────────────────
$lang  = (string)($_GET['lang'] ?? '');
if ($lang === '') tt_output(TT_EMPTY_LIST);
$kind  = (string)($_GET['kind'] ?? '');
$tlang = (string)($_GET['tlang'] ?? '');
$fmt   = (string)($_GET['fmt'] ?? ($_GET['format'] ?? ''));
if (!in_array($fmt, ['srv1', 'srv2', 'srv3', 'ttml', 'vtt', 'json3'], true)) $fmt = 'srv1';
if (preg_match('/^[A-Za-z0-9-]{2,10}$/', $tlang) !== 1) $tlang = '';

$ctype = $fmt === 'vtt' ? 'text/vtt; charset=UTF-8'
       : ($fmt === 'json3' ? 'application/json; charset=UTF-8' : 'text/xml; charset=UTF-8');

$dir = tt_cache_dir();
$cacheFile = $dir !== false
    ? $dir . '/' . preg_replace('/[^A-Za-z0-9_.-]/', '', "{$video_id}.{$lang}.{$kind}.{$tlang}.{$fmt}") . '.txt'
    : false;

if ($cacheFile !== false && is_file($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL_TIMEDTEXT) {
    $cached = @file_get_contents($cacheFile);
    if ($cached !== false && $cached !== '') tt_output($cached, $ctype);
}

$track = null;
$tracks = tt_tracks($video_id);
foreach ($tracks as $t) {
    if ($t['lang'] === $lang && $t['kind'] === $kind) { $track = $t; break; }
}
if ($track === null) foreach ($tracks as $t) {
    if ($t['lang'] === $lang) { $track = $t; break; }
}
if ($track === null) $track = $tracks[0] ?? null;
if ($track === null) tt_output(TT_EMPTY_TRACK, $ctype);

$url = preg_replace('/([?&])fmt=[^&]*/', '$1fmt=' . $fmt, $track['baseUrl'], 1, $replaced);
if ($replaced === 0) $url .= '&fmt=' . $fmt;
if ($tlang !== '') $url .= '&tlang=' . rawurlencode($tlang);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_ENCODING       => '',
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
]);
if (function_exists('yt_curl_ssl_opts'))   yt_curl_ssl_opts($ch);
if (function_exists('yt_curl_proxy_opts')) yt_curl_proxy_opts($ch);
$body = curl_exec($ch);
$curlErr = curl_error($ch);
$code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

if ($curlErr) error_log("timedtext.php curl error for {$video_id}: {$curlErr}");

if ($code !== 200 || $body === false || $body === '') {
    tt_output(TT_EMPTY_TRACK, $ctype);
}

if ($cacheFile !== false) {
    @file_put_contents($cacheFile, $body, LOCK_EX);
}
tt_output($body, $ctype);