<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  timedtext.php — субтитры для плееров 2012 (flashvar ttsurl)
//
//  Эмулирует легаси-API http://video.google.com/timedtext (он мёртв):
//    ?v=ID&type=list[&tlangs=1&fmts=1&asrs=1]      → <transcript_list> (XML)
//    ?v=ID&lang=en[&kind=asr][&tlang=de][&fmt=…]   → сам трек
//
//  Источник — InnerTube /player от имени ANDROID-клиента: WEB без аттестации
//  получает UNPLAYABLE и поле captions не отдаёт вовсе, ANDROID же возвращает
//  captionTracks с подписанными baseUrl (живут лишь ~5 часов) — список треков
//  поэтому не кэшируется, каждый запрос получает свежие ссылки.
//  В baseUrl уже зашит fmt=srv3, поэтому fmt не дописывается, а ЗАМЕНЯЕТСЯ:
//  флеш-модуль 2012_subtitles3_module понимает только легаси <transcript>
//  (srv1) и числовые коды формата, HTML5-плеер 2012 просит fmt=vtt.
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

header('Access-Control-Allow-Origin: *');

const TT_EMPTY_LIST  = '<?xml version="1.0" encoding="utf-8" ?><transcript_list></transcript_list>';
const TT_EMPTY_TRACK = '<?xml version="1.0" encoding="utf-8" ?><transcript></transcript>';

/** Отдаёт тело и завершает запрос */
function tt_output(string $body, string $ctype = 'text/xml; charset=UTF-8'): never {
    header('Content-Type: ' . $ctype);
    header('Content-Length: ' . strlen($body));
    echo $body;
    exit;
}

$video_id = (string)($_GET['v'] ?? ($_GET['video_id'] ?? ''));
if (preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id) !== 1) {
    tt_output(TT_EMPTY_LIST);
}

// ─── Список треков из InnerTube (без кэша — baseUrl подписаны и недолговечны) ─
// Возвращает [['baseUrl','lang','kind','name','cantran'], …].
function tt_tracks(string $vid): array {
    $d = innertube_post_as('player',
        ['videoId' => $vid, 'racyCheckOk' => true, 'contentCheckOk' => true],
        ['clientName' => 'ANDROID', 'clientVersion' => '20.10.38',
         'androidSdkVersion' => 30, 'osName' => 'Android', 'osVersion' => '11'],
        ['User-Agent: com.google.android.youtube/20.10.38 (Linux; U; Android 11) gzip',
         'X-YouTube-Client-Name: 3', 'X-YouTube-Client-Version: 20.10.38']);
    if ($d === null) return [];

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
// числовые коды флеша (fmt=1 и т.п.) и всё непонятное → легаси srv1
if (!in_array($fmt, ['srv1', 'srv2', 'srv3', 'ttml', 'vtt', 'json3'], true)) $fmt = 'srv1';
if (preg_match('/^[A-Za-z0-9-]{2,10}$/', $tlang) !== 1) $tlang = '';

$ctype = $fmt === 'vtt' ? 'text/vtt; charset=UTF-8'
       : ($fmt === 'json3' ? 'application/json; charset=UTF-8' : 'text/xml; charset=UTF-8');

$cacheFile = CACHE_DIR . '/timedtext/'
    . preg_replace('/[^A-Za-z0-9_.-]/', '', "{$video_id}.{$lang}.{$kind}.{$tlang}.{$fmt}") . '.txt';
if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL_TIMEDTEXT) {
    $cached = @file_get_contents($cacheFile);
    if ($cached !== false && $cached !== '') tt_output($cached, $ctype);
}

// Ищем трек: язык+kind, потом просто язык, потом дефолтный первый
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
yt_curl_ssl_opts($ch);
yt_curl_proxy_opts($ch);
$body = curl_exec($ch);
$code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

if ($code !== 200 || $body === false || $body === '') {
    tt_output(TT_EMPTY_TRACK, $ctype);
}

@file_put_contents($cacheFile, $body, LOCK_EX);
tt_output($body, $ctype);
