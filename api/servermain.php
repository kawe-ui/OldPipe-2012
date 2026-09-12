<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  servermain.php  —  InnerTube WEB client config + helpers  |  May 2026
// ═══════════════════════════════════════════════════════════════════════════════

// ─── Server vars ──────────────────────────────────────────────────────────────
$PHP_Self            = $_SERVER['PHP_SELF'];
$PHP_Self_PathInfo   = pathinfo($PHP_Self);
$ServerAddress       = $_SERVER['SERVER_ADDR']     ?? '';
$ServerName          = $_SERVER['SERVER_NAME']     ?? '';
$ServerSoftware      = $_SERVER['SERVER_SOFTWARE'] ?? '';
$ServerProtocol      = $_SERVER['SERVER_PROTOCOL'] ?? '';
$ServerRequestMethod = $_SERVER['REQUEST_METHOD']  ?? 'GET';
$ServerRequestTime   = $_SERVER['REQUEST_TIME']    ?? time();
$ServerRequestTimeDT = new DateTime("@$ServerRequestTime");
$ServerRequestTimeConverted = $ServerRequestTimeDT->format('F j, Y, g:i a');
$ServerRoot          = $_SERVER['DOCUMENT_ROOT']   ?? '';

if (
    (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) {
    $ServerHTTP = 'https';
} else {
    $ServerHTTP = 'http';
}

$FileName        = $_SERVER['SCRIPT_FILENAME'] ?? '';
$ServerPort      = $_SERVER['SERVER_PORT']     ?? '';
$ServerSignature = $_SERVER['SERVER_SIGNATURE'] ?? '';
$ScriptName      = $_SERVER['SCRIPT_NAME']     ?? '';
$RequestURI      = $_SERVER['REQUEST_URI']     ?? '';
$HTTP_Host       = $_SERVER['HTTP_HOST']       ?? 'localhost';
$HTTP_Host_Full  = $ServerHTTP . '://' . $HTTP_Host;

// ─── InnerTube API — актуальный WEB-клиент июль 2026 ──────────────────────────
define('INNERTUBE_API_KEY',    'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8');
define('INNERTUBE_CLIENT_NAME', 'WEB');
define('INNERTUBE_CLIENT_VER',  '2.20260709.01.00');
define('INNERTUBE_BASE_URL',    'https://www.youtube.com/youtubei/v1/');
define('INNERTUBE_ORIGIN',      'https://www.youtube.com');

// Совместимость со старым кодом
$ApiKey      = INNERTUBE_API_KEY;
$ApiUrl      = 'www.youtube.com';
$ApiUrl_Full = 'https://www.youtube.com';
$Api_Version = 'v1';
$ApiBase     = INNERTUBE_BASE_URL;

// ─── Конфиг (прокси, кэш, дефолтные ассеты) ───────────────────────────────────
$__cfg = ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/includes/config.inc.php';
if (!is_file($__cfg)) $__cfg = __DIR__ . '/../includes/config.inc.php';
require_once($__cfg);

const CACHE_TTL_STREAMS   = 3 * 3600;   // 3h — matches the comment in timedtext.php
const CACHE_TTL_TIMEDTEXT = 6 * 3600;   // pick whatever makes sense for you

// ─── Прокси для внешних запросов ──────────────────────────────────────────────
function _itube_proxy_opts(CurlHandle $ch): void {
    if (defined('PROXY_HOST') && PROXY_HOST !== '' && PROXY_PORT > 0) {
        curl_setopt($ch, CURLOPT_PROXY,     PROXY_HOST);
        curl_setopt($ch, CURLOPT_PROXYPORT, PROXY_PORT);
        curl_setopt($ch, CURLOPT_PROXYTYPE, PROXY_TYPE);
        if (PROXY_USER !== '') {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, PROXY_USER . ':' . PROXY_PASS);
        }
    }
}

// ─── SSL helper ───────────────────────────────────────────────────────────────
// Логика переехала в config.inc.php (yt_curl_ssl_opts), чтобы список cacert
// не расходился между InnerTube, аннотациями и pfp-эндпоинтом.
function _itube_ssl_opts(CurlHandle $ch): void {
    yt_curl_ssl_opts($ch);
}

// ─── Context-блок для каждого InnerTube запроса ───────────────────────────────
function innertube_context(string $hl = 'en', string $gl = 'US'): array {
    return [
        'client' => [
            'hl'               => $hl,
            'gl'               => $gl,
            'clientName'       => INNERTUBE_CLIENT_NAME,
            'clientVersion'    => INNERTUBE_CLIENT_VER,
            'originalUrl'      => INNERTUBE_ORIGIN,
            'platform'         => 'DESKTOP',
            'utcOffsetMinutes' => 0,
        ],
    ];
}

// ─── InnerTube POST (основной запрос) ─────────────────────────────────────────
function innertube_post(string $endpoint, array $payload, string $hl = 'en', string $gl = 'US'): ?array {
    $payload['context'] = innertube_context($hl, $gl);
    $url  = INNERTUBE_BASE_URL . $endpoint . '?key=' . INNERTUBE_API_KEY . '&prettyPrint=false';
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,        25);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json),
        'Origin: '  . INNERTUBE_ORIGIN,
        'Referer: ' . INNERTUBE_ORIGIN . '/',
        'X-YouTube-Client-Name: 1',
        'X-YouTube-Client-Version: ' . INNERTUBE_CLIENT_VER,
        'X-Origin: ' . INNERTUBE_ORIGIN,
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
        'Accept-Language: en-US,en;q=0.9',
        'Accept: application/json',
    ]);
    _itube_ssl_opts($ch);
    _itube_proxy_opts($ch);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($code !== 200 || $res === false || !empty($err)) return null;
    $decoded = json_decode($res, true);
    return is_array($decoded) ? $decoded : null;
}

// ─── InnerTube POST с произвольным клиентом (для /player-стримов) ─────────────
function innertube_post_as(string $endpoint, array $payload, array $client, array $headers): ?array {
    $payload['context'] = ['client' => array_merge($client, ['hl' => 'en', 'gl' => 'US'])];
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            INNERTUBE_BASE_URL . $endpoint . '?prettyPrint=false');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,        25);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json),
        'Accept: application/json',
    ], $headers));
    _itube_ssl_opts($ch);
    _itube_proxy_opts($ch);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || $res === false) return null;
    $decoded = json_decode($res, true);
    return is_array($decoded) ? $decoded : null;
}

// ─── Источник №1: yt-dlp ──────────────────────────────────────────────────────
// Отдаёт ту же структуру, что и нативный разбор ниже, но со ссылками, у которых
// есть полный доступ к adaptive-потокам (см. комментарий к YTDLP_BIN в конфиге).
// Только благодаря ему доступны 480p/720p/1080p и 60fps.
function ytdlp_get_streams(string $videoId): ?array {
    if (YTDLP_BIN === '') return null;

    $args = [
        YTDLP_BIN, '-J', '--no-warnings', '--no-playlist', '--skip-download',
        '--socket-timeout', '15',
        'https://www.youtube.com/watch?v=' . $videoId,
    ];
    if (PROXY_HOST !== '' && PROXY_PORT > 0) {
        $args[] = '--proxy';
        $args[] = (PROXY_TYPE === CURLPROXY_SOCKS5 ? 'socks5://' : 'http://') . PROXY_HOST . ':' . PROXY_PORT;
    }

    // proc_open массивом (без shell): в URL и путях бывают символы, которые
    // escapeshellarg() на Windows портит.
    $proc = @proc_open($args, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (!is_resource($proc)) return null;

    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $out = ''; $deadline = time() + YTDLP_TIMEOUT;
    do {
        $out .= (string)stream_get_contents($pipes[1]);
        $st   = proc_get_status($proc);
        if (!$st['running']) break;
        if (time() > $deadline) { proc_terminate($proc); break; }
        usleep(50000);
    } while (true);
    $out .= (string)stream_get_contents($pipes[1]);

    fclose($pipes[1]); fclose($pipes[2]);
    proc_close($proc);

    $j = json_decode($out, true);
    if (!is_array($j)) return null;

    // Живой эфир: growing HLS без фиксированного размера файла — прогрессивная
    // отдача и ffmpeg-ремукс-в-файл (ниже) тут не подходят в принципе, get_video.php
    // обрабатывает такие потоки отдельной веткой через hlsUrl (см. isLive).
    if (!empty($j['is_live']) && !empty($j['manifest_url'])) {
        return [
            'title'         => $j['title'] ?? '',
            'author'        => $j['uploader'] ?? '',
            'lengthSeconds' => 0,
            'keywords'      => $j['tags'] ?? [],
            'ua'            => 'Mozilla/5.0',
            'isLive'        => true,
            'hlsUrl'        => $j['manifest_url'],
            'formats'            => [],
            'videoOnly'          => [],
            'audioOnly'          => null,
            'adaptiveFullAccess' => false,
        ];
    }

    if (empty($j['formats'])) return null;

    $formats = []; $videoOnly = []; $audioOnly = null; $ua = 'Mozilla/5.0';

    foreach ($j['formats'] as $f) {
        if (empty($f['url'])) continue;
        $vc   = $f['vcodec'] ?? 'none';
        $ac   = $f['acodec'] ?? 'none';
        $itag = (int)($f['format_id'] ?? 0);
        $len  = (int)($f['filesize'] ?? ($f['filesize_approx'] ?? 0));
        if (!empty($f['http_headers']['User-Agent'])) $ua = $f['http_headers']['User-Agent'];

        // muxed itag 18 — единственный, что YouTube ещё отдаёт слитым
        if ($itag === 18 && $vc !== 'none' && $ac !== 'none') {
            $formats[18] = [
                'itag'          => 18,
                'url'           => $f['url'],
                'mimeType'      => 'video/mp4; codecs="avc1.42001E, mp4a.40.2"',
                'width'         => (int)($f['width'] ?? 640),
                'height'        => (int)($f['height'] ?? 360),
                'contentLength' => $len,
                'qualityLabel'  => ($f['height'] ?? 360) . 'p',
            ];
            continue;
        }

        // video-only H.264: Flash декодирует только avc1 (VP9/AV1 — мимо,
        // потому 1440p/2160p принципиально недоступны)
        if ($ac === 'none' && str_starts_with($vc, 'avc1')) {
            $h = (int)($f['height'] ?? 0);
            if ($h <= 0) continue;
            $cand = [
                'itag'          => $itag,
                'url'           => $f['url'],
                'height'        => $h,
                'width'         => (int)($f['width'] ?? 0),
                'fps'           => (int)($f['fps'] ?? 30),
                'qualityLabel'  => $h . 'p' . (((int)($f['fps'] ?? 30)) >= 50 ? (int)$f['fps'] : ''),
                'contentLength' => $len,
                'bitrate'       => (int)(($f['tbr'] ?? 0) * 1000),
            ];
            $cur = $videoOnly[$h] ?? null;
            if ($cur === null
                || $cand['fps'] > $cur['fps']
                || ($cand['fps'] === $cur['fps'] && $cand['bitrate'] > $cur['bitrate'])) {
                $videoOnly[$h] = $cand;
            }
            continue;
        }

        // audio-only AAC.
        // Только стерео: yt-dlp показывает и 5.1-дорожки (itag 256/258,
        // audio_channels=6), а Flash уверенно тянет лишь стерео — на 5.1 звук
        // может пропасть. Из стерео предпочитаем AAC-LC (mp4a.40.2) над
        // HE-AAC (mp4a.40.5), затем берём больший битрейт.
        if ($vc === 'none' && str_starts_with($ac, 'mp4a')) {
            if ((int)($f['audio_channels'] ?? 2) > 2) continue;
            $br   = (int)(($f['tbr'] ?? 0) * 1000);
            $isLC = str_starts_with($ac, 'mp4a.40.2');
            $cand = ['itag' => $itag, 'url' => $f['url'], 'bitrate' => $br,
                     'contentLength' => $len, 'lc' => $isLC];
            $cur  = $audioOnly;
            if ($cur === null
                || ($isLC && !$cur['lc'])
                || ($isLC === $cur['lc'] && $br > $cur['bitrate'])) {
                $audioOnly = $cand;
            }
        }
    }

    if (empty($formats) && empty($videoOnly)) return null;
    krsort($videoOnly);

    return [
        'title'         => $j['title'] ?? '',
        'author'        => $j['uploader'] ?? '',
        'lengthSeconds' => (int)($j['duration'] ?? 0),
        'keywords'      => $j['tags'] ?? [],
        'ua'            => $ua,
        'formats'            => $formats,
        'videoOnly'          => $videoOnly,
        'audioOnly'          => $audioOnly,
        // yt-dlp сам разбирается с аттестацией — его ссылки не обрезаются
        'adaptiveFullAccess' => $audioOnly !== null && !empty($videoOnly),
    ];
}

// ─── Прямые URL стримов ───────────────────────────────────────────────────────
// Сначала yt-dlp (полный доступ → все качества), при неудаче — нативный разбор
// InnerTube (ANDROID/IOS): он даёт рабочий muxed itag 18, т.е. 360p.
// Возвращает ['title','author','lengthSeconds','keywords','ua','formats'=>[itag=>fmt]]
// fmt: itag, url, mimeType, width, height, contentLength, qualityLabel
//
// Без файлового кэша: каждый вызов добывает ссылки заново (yt-dlp/InnerTube).
// Лок остаётся: плеер шлёт range-запросы пачками, и без него на одно и то же
// видео параллельно запускалось бы по yt-dlp'у на каждый запрос —
// эксклюзивный лок на videoId сериализует их в один физический полёт.
function innertube_get_streams(string $videoId): ?array {
    $lockFile = sys_get_temp_dir() . '/itube_streams_' . preg_replace('/[^A-Za-z0-9_-]/', '', $videoId) . '.lock';
    $lock = @fopen($lockFile, 'c');
    if ($lock !== false) flock($lock, LOCK_EX);
    $out = _igs_fetch($videoId);
    if ($lock !== false) {
        flock($lock, LOCK_UN);
        fclose($lock);
        @unlink($lockFile);
    }
    return $out;
}

// Собственно добыча ссылок (yt-dlp → ANDROID/IOS) — только из-под лока выше
function _igs_fetch(string $videoId): ?array {
    $viaYtdlp = ytdlp_get_streams($videoId);
    if ($viaYtdlp !== null && !empty($viaYtdlp['formats'])) {
        return $viaYtdlp;
    }

    $clients = [
        [
            'client'  => ['clientName' => 'ANDROID', 'clientVersion' => '20.10.38',
                          'androidSdkVersion' => 30, 'osName' => 'Android', 'osVersion' => '11'],
            'ua'      => 'com.google.android.youtube/20.10.38 (Linux; U; Android 11) gzip',
            'headers' => ['X-YouTube-Client-Name: 3', 'X-YouTube-Client-Version: 20.10.38'],
        ],
        [
            'client'  => ['clientName' => 'IOS', 'clientVersion' => '20.10.4', 'deviceMake' => 'Apple',
                          'deviceModel' => 'iPhone16,2', 'osName' => 'iPhone', 'osVersion' => '18.3.2.22D82'],
            'ua'      => 'com.google.ios.youtube/20.10.4 (iPhone16,2; U; CPU iOS 18_3_2 like Mac OS X;)',
            'headers' => ['X-YouTube-Client-Name: 5', 'X-YouTube-Client-Version: 20.10.4'],
        ],
    ];

    foreach ($clients as $cl) {
        $d = innertube_post_as('player', [
            'videoId' => $videoId, 'racyCheckOk' => true, 'contentCheckOk' => true,
        ], $cl['client'], array_merge(['User-Agent: ' . $cl['ua']], $cl['headers']));

        if ($d === null) continue;
        if (($d['playabilityStatus']['status'] ?? '') !== 'OK') continue;

        $vd      = $d['videoDetails'] ?? [];

        // Живой эфир: growing HLS без фиксированного размера файла — та же
        // ветка, что и в ytdlp_get_streams(), см. комментарий там.
        if (!empty($vd['isLive']) && !empty($d['streamingData']['hlsManifestUrl'])) {
            return [
                'title'         => $vd['title'] ?? '',
                'author'        => $vd['author'] ?? '',
                'lengthSeconds' => 0,
                'keywords'      => $vd['keywords'] ?? [],
                'ua'            => $cl['ua'],
                'isLive'        => true,
                'hlsUrl'        => $d['streamingData']['hlsManifestUrl'],
                'formats'            => [],
                'videoOnly'          => [],
                'audioOnly'          => null,
                'adaptiveFullAccess' => false,
            ];
        }

        $formats = [];

        // muxed (video+audio) с прямым URL — YouTube всё чаще не отдаёт его
        // вовсе (даже для рабочих видео), поэтому дальше не бросаем попытку
        // из-за пустого $formats — adaptive ниже сам по себе достаточен.
        foreach ($d['streamingData']['formats'] ?? [] as $f) {
            if (empty($f['url']) || empty($f['itag'])) continue;
            $formats[(int)$f['itag']] = [
                'itag'          => (int)$f['itag'],
                'url'           => $f['url'],
                'mimeType'      => $f['mimeType'] ?? 'video/mp4',
                'width'         => (int)($f['width'] ?? 0),
                'height'        => (int)($f['height'] ?? 0),
                'contentLength' => (int)($f['contentLength'] ?? 0),
                'qualityLabel'  => $f['qualityLabel'] ?? '',
            ];
        }

        // ── Adaptive: video-only H.264 (avc1) + audio-only AAC (mp4a) ──────────
        // Всё выше 360p YouTube отдаёт только раздельно. Flash умеет лишь
        // H.264/AAC, поэтому VP9/AV1 (2K/4K) игнорируем — их пришлось бы
        // транскодировать, что не реально-временная задача.
        $videoOnly = [];   // height => лучший avc1-поток (60fps в приоритете)
        $audioOnly = null; // лучший mp4a
        foreach ($d['streamingData']['adaptiveFormats'] ?? [] as $f) {
            if (empty($f['url']) || empty($f['mimeType'])) continue;
            $mime = $f['mimeType'];

            if (str_starts_with($mime, 'video/mp4') && str_contains($mime, 'avc1')) {
                $h   = (int)($f['height'] ?? 0);
                $fps = (int)($f['fps'] ?? 30);
                if ($h <= 0) continue;
                $cand = [
                    'itag'          => (int)$f['itag'],
                    'url'           => $f['url'],
                    'height'        => $h,
                    'width'         => (int)($f['width'] ?? 0),
                    'fps'           => $fps,
                    'qualityLabel'  => $f['qualityLabel'] ?? ($h . 'p'),
                    'contentLength' => (int)($f['contentLength'] ?? 0),
                    'bitrate'       => (int)($f['bitrate'] ?? 0),
                ];
                // при равной высоте предпочитаем больший fps, затем битрейт
                $cur = $videoOnly[$h] ?? null;
                if ($cur === null
                    || $cand['fps'] > $cur['fps']
                    || ($cand['fps'] === $cur['fps'] && $cand['bitrate'] > $cur['bitrate'])) {
                    $videoOnly[$h] = $cand;
                }
            } elseif (str_starts_with($mime, 'audio/mp4') && str_contains($mime, 'mp4a')) {
                $br = (int)($f['bitrate'] ?? 0);
                if ($audioOnly === null || $br > $audioOnly['bitrate']) {
                    $audioOnly = [
                        'itag'          => (int)$f['itag'],
                        'url'           => $f['url'],
                        'bitrate'       => $br,
                        'contentLength' => (int)($f['contentLength'] ?? 0),
                    ];
                }
            }
        }
        krsort($videoOnly);

        // Ни muxed, ни adaptive не дали ничего пригодного — этот клиент бесполезен,
        // пробуем следующего (раньше здесь бросали попытку уже на пустом $formats,
        // хотя adaptive рядом мог быть рабочим — см. yt_quality_map()).
        if (empty($formats) && (empty($videoOnly) || $audioOnly === null)) continue;

        // ── Проверка полного доступа к adaptive-потокам ────────────────────────
        // Сюда попадаем только если yt-dlp недоступен. Сырой googlevideo без
        // валидной аттестации обрывает adaptive-поток примерно на 7-9% файла
        // (замер: 22 МБ из 246 МБ, лимит на СМЕЩЕНИЕ и он накопительный —
        // обновление ссылки не помогает). Если поток обрезан — качества выше
        // 360p не предлагаем, иначе плеер встанет посреди ролика.
        // Muxed itag 18 под ограничение не попадает.
        $adaptiveFullAccess = false;
        $probe = reset($videoOnly);
        if ($probe !== false && !empty($probe['contentLength'])) {
            $clen = (int)$probe['contentLength'];
            if ($clen <= 4 * 1024 * 1024) {
                $adaptiveFullAccess = true;      // поток и так короче лимита
            } else {
                $from = $clen - 65536;           // пробуем хвост файла
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $probe['url'],
                    CURLOPT_RANGE          => $from . '-' . ($clen - 1),
                    CURLOPT_NOBODY         => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 20,
                    CURLOPT_USERAGENT      => $cl['ua'],
                ]);
                _itube_ssl_opts($ch);
                _itube_proxy_opts($ch);
                curl_exec($ch);
                $pc = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $adaptiveFullAccess = ($pc === 206 || $pc === 200);
            }
        }

        $out = [
            'title'         => $vd['title'] ?? '',
            'author'        => $vd['author'] ?? '',
            'lengthSeconds' => (int)($vd['lengthSeconds'] ?? 0),
            'keywords'      => $vd['keywords'] ?? [],
            'ua'            => $cl['ua'],
            'formats'            => $formats,
            'videoOnly'          => $videoOnly,
            'audioOnly'          => $audioOnly,
            'adaptiveFullAccess' => $adaptiveFullAccess,
        ];
        return $out;
    }
    return null;
}

// ─── Карта качеств: классические itag 2012 → реальные потоки ──────────────────
// Плеер 2012 берёт подпись качества из itag, поэтому раскладываем современные
// потоки по «родному» словарю: 5=240p, 18=360p, 35=480p, 22=720p, 37=1080p.
// itag 18 отдаётся как есть (уже muxed), остальные склеиваются ffmpeg'ом.
// FLV (5/35) стримится на лету, MP4 (22/37) — через кэш с faststart.
function yt_quality_map(?array $streams): array {
    if ($streams === null) return [];

    // Живой эфир — единственная запись независимо от запрошенного itag:
    // get_video.php гонит ffmpeg'ом HLS→FLV напрямую в ответ, без выбора
    // качества и без Range (перемотка по live не имеет смысла).
    if (!empty($streams['isLive'])) {
        return [93 => [
            'itag'      => 93,
            'mime'      => 'application/vnd.apple.mpegurl',
            'live'      => true,
            'quality'   => "small",
            'url'       => $streams['hlsUrl'] ?? '',
            'hlsUrl'    => $streams['hlsUrl'] ?? '',
        ]];
    }

    // height => [classicItag, quality-label плеера, контейнер, m       ime]
    // Плеер берёт ПОДПИСЬ качества из itag, а декодер — из поля type в
    // stream_map, поэтому все склеенные потоки отдаём как H.264/AAC в MP4.
    static $classic = [
        240  => [5,  'small',  'mp4', 'video/mp4; codecs="avc1.42001E, mp4a.40.2"'],
        360  => [18, 'medium', 'mp4', 'video/mp4; codecs="avc1.42001E, mp4a.40.2"'],
        480  => [35, 'large',  'mp4', 'video/mp4; codecs="avc1.4D401E, mp4a.40.2"'],
        720  => [22, 'hd720',  'mp4', 'video/mp4; codecs="avc1.64001F, mp4a.40.2"'],
        1080 => [37, 'hd1080', 'mp4', 'video/mp4; codecs="avc1.640028, mp4a.40.2"'],
    ];

    $map   = [];
    $audio = $streams['audioOnly'] ?? null;

    // 360p — нативный muxed itag 18 (без ffmpeg, с полной поддержкой Range)
    if (!empty($streams['formats'][18])) {
        $f = $streams['formats'][18];
        $map[18] = [
            'itag'       => 18,
            'height'     => $f['height'] ?: 360,
            'width'      => $f['width'] ?: 640,
            'fps'        => 30,
            'label'      => '360p',
            'quality'    => 'medium',
            'container'  => 'mp4',
            'mime'       => 'video/mp4; codecs="avc1.42001E, mp4a.40.2"',
            'native'     => true,
            'video_itag' => 18,
            'audio_itag' => 0,
        ];
    }

    // Остальные качества — из adaptive H.264 + AAC (нужен звук для склейки).
    // Предлагаем их только если поток отдаётся целиком: иначе googlevideo
    // оборвёт видео на ~4 МБ и плеер встанет посреди ролика.
    if ($audio !== null && !empty($streams['adaptiveFullAccess'])) {
        // Высота нативного muxed: у старых роликов itag 18 бывает 240p, и тогда
        // adaptive-240p дал бы в меню второе такое же качество.
        $nativeHeight = $map[18]['height'] ?? 0;

        foreach ($streams['videoOnly'] ?? [] as $h => $v) {
            if (!isset($classic[$h])) continue;   // 1440p/2160p — только VP9/AV1, Flash не умеет
            [$itag, $quality, $container, $mime] = $classic[$h];
            if (isset($map[$itag])) continue;     // 360p уже занят нативным
            // Ровно та же высота, что у muxed → это дубль (у старых роликов
            // itag 18 бывает 240p и совпал бы с adaptive-240p → itag 5).
            // Качества НИЖЕ нативного — законный выбор, их оставляем.
            if ($h === $nativeHeight) continue;
            $map[$itag] = [
                'itag'       => $itag,
                'height'     => $h,
                'width'      => $v['width'] ?: (int)round($h * 16 / 9),
                'fps'        => $v['fps'],
                'label'      => $v['qualityLabel'],   // «1080p60» и т.п.
                'quality'    => $quality,
                'container'  => $container,
                'mime'       => $mime,
                'native'     => false,
                'video_itag' => $v['itag'],
                'audio_itag' => $audio['itag'],
            ];
        }
    }

    krsort($map);   // высокие качества первыми (как в fmt_list 2012)
    return $map;
}

// ─── Простой GET (RYD и прочие внешние API) ───────────────────────────────────
function apiGet(string $url): ?array {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,        15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
    ]);
    _itube_ssl_opts($ch);
    _itube_proxy_opts($ch);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200 || $res === false) return null;
    $decoded = json_decode($res, true);
    return is_array($decoded) ? $decoded : null;
}

// ─── Форматтеры ───────────────────────────────────────────────────────────────
function SecondsFormater(int $s): string {
    $d = (int)floor($s / 86400); $s -= $d * 86400;
    $h = (int)floor($s / 3600);  $s -= $h * 3600;
    $m = (int)floor($s / 60);    $s -= $m * 60;
    if ($d > 0) return sprintf('%d:%02d:%02d:%02d', $d, $h, $m, $s);
    if ($h > 0) return sprintf('%d:%02d:%02d', $h, $m, $s);
    return sprintf('%d:%02d', $m, $s);
}

function iso8601ToSeconds(string $iso): int {
    preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/', $iso, $m);
    return ((int)($m[1] ?? 0)) * 3600
         + ((int)($m[2] ?? 0)) * 60
         + ((int)($m[3] ?? 0));
}

// Объединяет массив runs в строку
function runs_to_text(array $runs): string {
    $out = '';
    foreach ($runs as $r) $out .= $r['text'] ?? '';
    return $out;
}

// Лучший thumbnail из массива thumbnails InnerTube
function best_thumb(array $thumbs, int $minWidth = 0): string {
    if (empty($thumbs)) return '';
    usort($thumbs, fn($a, $b) => (int)($b['width'] ?? 0) - (int)($a['width'] ?? 0));
    foreach ($thumbs as $t) {
        if ((int)($t['width'] ?? 0) >= $minWidth && !empty($t['url'])) return $t['url'];
    }
    return $thumbs[0]['url'] ?? '';
}

// Парсит «1,234,567 views» → «1,234,567»
function parse_short_count(string $raw): string {
    if (preg_match('/([\d][\d\s,\.]*[\d]|\d)/', $raw, $m)) return trim($m[1]);
    return $raw;
}

// ─── Комментарии: единый парсер для watch (страница 1) и пагинации ────────────
// Рекурсивный поиск рендерера по ключу (локальная копия для servermain)
function _sm_find_renderer(array $data, string $key, int $depth = 0): ?array {
    if ($depth > 30) return null;
    foreach ($data as $k => $v) {
        if ($k === $key && is_array($v)) return $v;
        if (is_array($v)) {
            $r = _sm_find_renderer($v, $key, $depth + 1);
            if ($r !== null) return $r;
        }
    }
    return null;
}

// Ищет continuation-token секции комментариев в ответе /next
function yt_comments_token(array $nextRaw): ?string {
    $find = function (array $data, int $depth = 0) use (&$find): ?string {
        if ($depth > 30) return null;
        foreach ($data as $k => $v) {
            if (!is_array($v)) continue;
            if ($k === 'itemSectionRenderer' && str_contains($v['sectionIdentifier'] ?? '', 'comment')) {
                foreach ($v['contents'] ?? [] as $c) {
                    $t = $c['continuationItemRenderer']['continuationEndpoint']['continuationCommand']['token'] ?? null;
                    if (is_string($t) && $t !== '') return $t;
                }
            }
            $r = $find($v, $depth + 1);
            if ($r !== null) return $r;
        }
        return null;
    };
    return $find($nextRaw);
}

// Загружает страницу комментариев по токену.
// Возвращает ['comments'=>[...], 'nextToken'=>?string, 'count'=>?string]
function yt_fetch_comments(string $videoId, ?string $token = null): array {
    // Токен первой страницы — из /next
    if ($token === null) {
        $next = innertube_post('next', ['videoId' => $videoId]);
        if ($next === null) return ['comments' => [], 'nextToken' => null, 'count' => null];
        $token = yt_comments_token($next);
        if ($token === null) return ['comments' => [], 'nextToken' => null, 'count' => null];
    }

    $page = innertube_post('next', ['continuation' => $token]);
    if ($page === null) return ['comments' => [], 'nextToken' => null, 'count' => null];

    // Число комментариев из заголовка
    $count = null;
    $chr = _sm_find_renderer($page, 'commentsHeaderRenderer');
    if ($chr !== null) {
        $cText = runs_to_text($chr['countText']['runs'] ?? []);
        if ($cText === '') $cText = $chr['countText']['simpleText'] ?? '';
        if (preg_match('/[\d][\d,\.\s]*/', $cText, $m)) $count = trim($m[0]);
    }

    // Данные комментариев из mutations (формат 2024+)
    $payloadById = [];
    foreach ($page['frameworkUpdates']['entityBatchUpdate']['mutations'] ?? [] as $mut) {
        $pl = $mut['payload']['commentEntityPayload'] ?? null;
        if ($pl === null) continue;
        $cid = $pl['properties']['commentId'] ?? '';
        if ($cid !== '') $payloadById[$cid] = $pl;
    }

    // Порядок вывода — по commentViewModel
    $orderedIds = [];
    $walk = function (array $data, int $depth = 0) use (&$walk, &$orderedIds) {
        if ($depth > 30 || count($orderedIds) >= 40) return;
        foreach ($data as $k => $v) {
            if (!is_array($v)) continue;
            if ($k === 'commentViewModel' && !empty($v['commentId'])) {
                $orderedIds[$v['commentId']] = true;
            } else {
                $walk($v, $depth + 1);
            }
        }
    };
    $walk($page);
    $ids = array_keys($orderedIds);
    if (empty($ids)) $ids = array_keys($payloadById);

    $comments = [];
    foreach ($ids as $cid) {
        $pl = $payloadById[$cid] ?? null;
        if ($pl === null) continue;
        if ((int)($pl['properties']['replyLevel'] ?? 0) > 0) continue;
        $authorId = $pl['author']['channelId'] ?? '';
        $comments[] = [
            'id'        => $cid,
            'author'    => $pl['author']['displayName'] ?? '',
            'authorId'  => $authorId,
            'authorUrl' => $authorId ? '/channel/' . $authorId : '',
            'avatar'    => $pl['author']['avatarThumbnailUrl'] ?? '',
            'text'      => $pl['properties']['content']['content'] ?? '',
            'likes'     => $pl['toolbar']['likeCountNotliked'] ?? '0',
            'date'      => $pl['properties']['publishedTime'] ?? '',
        ];
    }

    // Токен следующей страницы (continuationItemRenderer в самом низу)
    $nextToken = null;
    $findNext = function (array $data, int $depth = 0) use (&$findNext, &$nextToken) {
        if ($nextToken !== null || $depth > 30) return;
        foreach ($data as $k => $v) {
            if (!is_array($v)) continue;
            if ($k === 'continuationItemRenderer') {
                $t = $v['continuationEndpoint']['continuationCommand']['token']
                    ?? ($v['button']['buttonRenderer']['command']['continuationCommand']['token'] ?? null);
                if (is_string($t) && $t !== '') { $nextToken = $t; return; }
            }
            $findNext($v, $depth + 1);
        }
    };
    $findNext($page);

    return ['comments' => $comments, 'nextToken' => $nextToken, 'count' => $count];
}

// ─── Лайки из ответа /next ────────────────────────────────────────────────────
// InnerTube отдаёт счётчик в разных обёртках; проверяем известные формы и берём
// первую с числом. Скрытый автором рейтинг → null.
// «6.2K» и «1,234» → 6200 / 1234 (expand_count разворачивает суффиксы).
function yt_likes_from_next(array $next): ?int {
    $lbvm = _sm_find_renderer($next, 'likeButtonViewModel');
    $label = '';
    if ($lbvm !== null) {
        $label = $lbvm['likeCountEntity']['expandedLikeCountIfIndifferent']['content']
            ?? ($lbvm['likeCountEntity']['likeCountIfLiked']['content'] ?? '');
    }
    // Старый toggleButtonRenderer — как фолбэк
    if ($label === '') {
        $tb = _sm_find_renderer($next, 'toggleButtonRenderer');
        if ($tb !== null) {
            $label = $tb['defaultText']['simpleText']
                ?? ($tb['defaultText']['accessibility']['accessibilityData']['label'] ?? '');
        }
    }
    if ($label === '' || !preg_match('/\d/', $label)) return null;
    $n = str_replace(',', '', expand_count($label));
    return is_numeric($n) ? (int)$n : null;
}

// ─── Лайки / дизлайки: RYD + кэш последних известных значений ─────────────────
// Автор может скрыть рейтинг, а RYD — не ответить. Тогда берём последнее
// известное значение из кэша, чтобы шкала sparkbars не осталась пустой.
// $likesHint — лайки, которые уже дал InnerTube (используются, если RYD молчит).
// Возвращает ['likes'=>?int, 'dislikes'=>?int, 'viewCount'=>?int]
function yt_video_ratings(string $videoId, ?int $likesHint = null): array {
    $cacheFile = CACHE_DIR . '/rating_' . preg_replace('/[^A-Za-z0-9_-]/', '', $videoId) . '.json';

    $likes = null; $dislikes = null; $views = null;

    $ryd = apiGet('https://returnyoutubedislikeapi.com/votes?videoId=' . urlencode($videoId));
    if ($ryd !== null) {
        if (isset($ryd['likes']))     $likes    = (int)$ryd['likes'];
        if (isset($ryd['dislikes']))  $dislikes = (int)$ryd['dislikes'];
        if (isset($ryd['viewCount'])) $views    = (int)$ryd['viewCount'];
    }

    if ($likes === null && $likesHint !== null) $likes = $likesHint;

    // Фолбэк на кэш — только для того, чего не дала сеть
    if (($likes === null || $dislikes === null) && is_file($cacheFile)) {
        $c = json_decode((string)file_get_contents($cacheFile), true);
        if (is_array($c)) {
            if ($likes === null    && isset($c['likes']))    $likes    = (int)$c['likes'];
            if ($dislikes === null && isset($c['dislikes'])) $dislikes = (int)$c['dislikes'];
        }
    }

    // Свежие значения из сети — обновляем кэш
    if ($ryd !== null && isset($ryd['likes'], $ryd['dislikes'])) {
        @file_put_contents($cacheFile, json_encode([
            'likes'    => (int)$ryd['likes'],
            'dislikes' => (int)$ryd['dislikes'],
            'ts'       => time(),
        ]));
    }

    return ['likes' => $likes, 'dislikes' => $dislikes, 'viewCount' => $views];
}

// ─── Метаданные видео: /player + /next + рейтинги ─────────────────────────────
// Единый источник данных для get_video_metadata.php, api.php и get_video_info.php.
// Всегда возвращает массив со 'status': 'OK' — данные заполнены; иначе 'reason'
// содержит текст ошибки YouTube (удалено / приватное / заблокировано).
// null — только при неверном id или полном отказе InnerTube.
function yt_video_metadata(string $videoId, bool $useCache = true): ?array {
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) return null;

    $cacheFile = CACHE_DIR . '/meta_' . $videoId . '.json';
    if ($useCache && is_file($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL_METADATA) {
        $c = json_decode((string)file_get_contents($cacheFile), true);
        if (is_array($c) && isset($c['status'])) return $c;
    }

    $player = innertube_post('player', [
        'videoId'        => $videoId,
        'racyCheckOk'    => true,
        'contentCheckOk' => true,
    ]);
    if ($player === null) return null;

    $ps     = $player['playabilityStatus'] ?? [];
    $status = $ps['status'] ?? 'ERROR';
    $vd     = $player['videoDetails'] ?? [];
    $mf     = $player['microformat']['playerMicroformatRenderer'] ?? [];

    // Видео недоступно и деталей нет — дальше идти незачем
    if (empty($vd['videoId']) && $status !== 'OK') {
        $reason = $ps['reason']
            ?? ($ps['errorScreen']['playerErrorMessageRenderer']['reason']['simpleText'] ?? '');
        return [
            'videoId' => $videoId,
            'status'  => $status,
            'reason'  => $reason !== '' ? $reason : 'This video is unavailable.',
        ];
    }

    // ── Владелец канала: аватар + подписчики (только /next их отдаёт) ──────────
    $authorAvatar   = '';
    $subscriberText = '';
    $likesHint      = null;
    $next = innertube_post('next', ['videoId' => $videoId]);
    if ($next !== null) {
        $vor = _sm_find_renderer($next, 'videoOwnerRenderer');
        if ($vor !== null) {
            $authorAvatar = best_thumb($vor['thumbnail']['thumbnails'] ?? []);
            $scStr = $vor['subscriberCountText']['simpleText']
                ?? runs_to_text($vor['subscriberCountText']['runs'] ?? []);
            // «6.3M subscribers» → «6.3M»: формат 2012 хранит счётчик без слова
            if (preg_match('/^([\d][\d.,]*\s*[KMB]?)/u', trim($scStr), $m)) {
                $subscriberText = trim($m[1]);
            }
        }
        $likesHint = yt_likes_from_next($next);
    }

    $rawViews = (int)($vd['viewCount'] ?? 0);
    $ratings  = yt_video_ratings($videoId, $likesHint);
    if ($rawViews === 0 && !empty($ratings['viewCount'])) $rawViews = (int)$ratings['viewCount'];

    $out = [
        'videoId'         => $videoId,
        'status'          => 'OK',
        'reason'          => '',
        'title'           => $vd['title']            ?? '',
        'description'     => $vd['shortDescription'] ?? '',
        'lengthSeconds'   => (int)($vd['lengthSeconds'] ?? 0),
        'author'          => $vd['author']    ?? '',
        'authorId'        => $vd['channelId'] ?? '',
        'authorAvatar'    => default_avatar($authorAvatar),
        'subscriberCount' => $subscriberText,
        'viewCount'       => $rawViews,
        'likes'           => $ratings['likes'],
        'dislikes'        => $ratings['dislikes'],
        'keywords'        => $vd['keywords'] ?? [],
        'thumbnail'       => best_thumb($vd['thumbnail']['thumbnails'] ?? [])
                             ?: "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg",
        'publishDate'     => $mf['publishDate'] ?? ($mf['uploadDate'] ?? ''),
        'category'        => $mf['category'] ?? '',
    ];

    if ($useCache) @file_put_contents($cacheFile, json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    return $out;
}

// Разворачивает сокращённые счётчики в полные числа с запятыми, как на
// страницах 2012 года: «6.2K views» → «6,200», «1.3M» → «1,300,000»,
// «1,234,567 views» → «1,234,567», «No views» → «0»
function expand_count(string $raw): string {
    $raw = trim($raw);
    if ($raw === '' ) return '';
    if (preg_match('/^([\d.,]+)\s*([KMB])/iu', $raw, $m)) {
        $mult = ['K' => 1000, 'M' => 1000000, 'B' => 1000000000][strtoupper($m[2])];
        return number_format((float)str_replace(',', '', $m[1]) * $mult, 0, '.', ',');
    }
    if (preg_match('/([\d][\d,\.]*)/', $raw, $m)) {
        return number_format((float)str_replace(',', '', $m[1]), 0, '.', ',');
    }
    if (stripos($raw, 'no view') !== false) return '0';
    return $raw;
}