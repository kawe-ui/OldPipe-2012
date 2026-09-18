<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  get_video.php — отдача видеопотока флеш-плееру 2012
//  /get_video?video_id=XXXXXXXXXXX&itag=22
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

ignore_user_abort(true);
set_time_limit(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
header('Cache-Control: max-age=31536000');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
$video_id = $_GET['video_id'] ?? ($_GET['v'] ?? '');
$itag     = (int)($_GET['itag'] ?? ($_GET['fmt'] ?? 0));

if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id)) {
    http_response_code(400);
    exit('bad video_id');
}

function _gv_pick(?array $streams, int $itag): ?array {
    $map = yt_quality_map($streams);
    if (empty($map)) return null;
    if ($itag && isset($map[$itag])) return $map[$itag];
    return reset($map) ?: null;
}

$streams = innertube_get_streams($video_id);
$q       = _gv_pick($streams, $itag);
if ($q === null) {
    // повтор без «ложного» второго аргумента (сигнатура — 1 параметр)
    $streams = innertube_get_streams($video_id);
    $q       = _gv_pick($streams, $itag);
}
if ($q === null) {
    http_response_code(404);
    exit('no stream');
}

$ua = $streams['ua'] ?? 'Mozilla/5.0';

function _gv_proxy(array $fmt, string $ua): int {
    $range        = $_SERVER['HTTP_RANGE'] ?? '';
    $headersSent  = false;
    $upstreamCode = 0;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $fmt['url'],
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_BUFFERSIZE     => 256 * 1024,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_HEADERFUNCTION => function ($ch, $line) use (&$headersSent, &$upstreamCode, $fmt) {
            $trim = trim($line);
            if ($trim === '') return strlen($line);
            if (preg_match('#^HTTP/[\d.]+\s+(\d+)#', $trim, $m)) {
                $upstreamCode = (int)$m[1];
                return strlen($line);
            }
            if ($upstreamCode >= 400) return strlen($line);
            if (!$headersSent && $upstreamCode > 0) {
                http_response_code($upstreamCode);
                header('Content-Type: ' . (preg_replace('/;.*$/', '', $fmt['mimeType']) ?: 'video/mp4'));
                header('Accept-Ranges: bytes');
                $headersSent = true;
            }
            foreach (['content-length', 'content-range'] as $h) {
                if (stripos($trim, $h . ':') === 0) header($trim, true);
            }
            return strlen($line);
        },
        CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$headersSent, &$upstreamCode) {
            if ($upstreamCode >= 400) return strlen($data);
            if (!$headersSent) { $headersSent = true; }
            echo $data;
            flush();
            return connection_aborted() ? 0 : strlen($data);
        },
    ]);
    if ($range !== '' && preg_match('/bytes=([\d\-,]+)/', $range, $m)) {
        curl_setopt($ch, CURLOPT_RANGE, $m[1]);
    }
    _itube_ssl_opts($ch);
    _itube_proxy_opts($ch);
    curl_exec($ch);
    curl_close($ch);
    return $upstreamCode;
}

while (ob_get_level() > 0) ob_end_clean();

// ─── 1. Нативный muxed (обычно itag 18) ───────────────────────────────────────
if (!empty($q['native'])) {
    $fmt = $streams['formats'][$q['itag']] ?? ($streams['formats'][18] ?? null);
    if ($fmt === null || empty($fmt['url'])) {
        http_response_code(404);
        exit('no native muxed url');
    }
    $code = _gv_proxy($fmt, $ua);
    if ($code === 403) {
        $streams = innertube_get_streams($video_id);
        $fmt = $streams['formats'][$q['itag']] ?? ($streams['formats'][18] ?? null);
        if ($fmt !== null && !empty($fmt['url'])) {
            $code = _gv_proxy($fmt, $ua);
        }
    }
    if ($code >= 400 && !headers_sent()) {
        http_response_code(502);
        echo 'upstream error ' . $code;
    }
    exit;
}

// ─── 2. Склейка ffmpeg ────────────────────────────────────────────────────────
if (FFMPEG_BIN === '') {
    http_response_code(501);
    exit('ffmpeg not available - only 360p can be served');
}

$vUrl = $streams['videoOnly'][$q['height']]['url'] ?? '';
$aUrl = $streams['audioOnly']['url'] ?? '';
if ($vUrl === '' || $aUrl === '') {
    http_response_code(404);
    exit('no adaptive streams');
}

define('GV_CHUNK', 1048576);

function _gv_download_chunked(string $url, string $ua, string $dest, int $clen): bool {
    $fp = @fopen($dest, 'wb');
    if ($fp === false) return false;

    $pos = 0;
    while ($clen === 0 || $pos < $clen) {
        $end = $pos + GV_CHUNK - 1;
        if ($clen > 0 && $end > $clen - 1) $end = $clen - 1;

        $ok = false;
        for ($try = 0; $try < 3 && !$ok; $try++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RANGE          => $pos . '-' . $end,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_USERAGENT      => $ua,
            ]);
            _itube_ssl_opts($ch);
            _itube_proxy_opts($ch);
            $body = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if (($code === 206 || $code === 200) && is_string($body) && $body !== '') {
                fwrite($fp, $body);
                $pos += strlen($body);
                $ok   = true;
            } elseif ($try < 2) {
                usleep(300000);
            }
        }
        if (!$ok) { fclose($fp); @unlink($dest); return false; }
        if ($clen === 0) break;
    }
    fclose($fp);
    return filesize($dest) > 0;
}

function _gv_prune_cache(): void {
    $files = glob(CACHE_DIR . '/mux_*.mp4') ?: [];
    if (empty($files)) return;

    $total = 0;
    $list  = [];
    foreach ($files as $f) {
        $sz = @filesize($f);
        if ($sz === false) continue;
        $list[] = ['f' => $f, 'sz' => $sz, 't' => @filemtime($f) ?: 0];
        $total += $sz;
    }

    $limit = FFMPEG_CACHE_MAX_MB * 1024 * 1024;
    if ($total <= $limit) return;

    usort($list, fn($a, $b) => $a['t'] <=> $b['t']);
    foreach ($list as $e) {
        if ($total <= $limit) break;
        if (@unlink($e['f'])) $total -= $e['sz'];
    }
}

$cacheName = CACHE_DIR . '/mux_' . $video_id . '_' . $q['itag'] . '.mp4';
$lockName  = $cacheName . '.lock';

if (!is_file($cacheName)) {
    $lock = @fopen($lockName, 'c');
    if ($lock === false) { http_response_code(500); exit('cache not writable'); }
    if (!flock($lock, LOCK_EX)) { fclose($lock); http_response_code(500); exit('lock failed'); }

    if (!is_file($cacheName)) {
        $vTmp = $cacheName . '.v';
        $aTmp = $cacheName . '.a';
        $tmp  = $cacheName . '.part';

        $vLen = (int)($streams['videoOnly'][$q['height']]['contentLength'] ?? 0);
        $aLen = (int)($streams['audioOnly']['contentLength'] ?? 0);

        $okV = _gv_download_chunked($vUrl, $ua, $vTmp, $vLen);
        $okA = $okV && _gv_download_chunked($aUrl, $ua, $aTmp, $aLen);

        if (!$okV || !$okA) {
            @unlink($vTmp); @unlink($aTmp);
            flock($lock, LOCK_UN); fclose($lock); @unlink($lockName);
            http_response_code(502);
            exit('stream download failed');
        }

        $args = [
            FFMPEG_BIN, '-loglevel', 'error', '-y',
            '-i', $vTmp, '-i', $aTmp,
            '-map', '0:v:0', '-map', '1:a:0',
            '-c', 'copy', '-movflags', '+faststart',
            '-f', 'mp4', $tmp,
        ];
        $proc = proc_open($args, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

        $err = ''; $rc = -1;
        if (is_resource($proc)) {
            fclose($pipes[1]);
            $err = (string)stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $rc  = proc_close($proc);
        }
        @unlink($vTmp); @unlink($aTmp);

        if ($rc !== 0 || !is_file($tmp) || filesize($tmp) === 0) {
            @unlink($tmp);
            flock($lock, LOCK_UN); fclose($lock); @unlink($lockName);
            http_response_code(500);
            exit('mux failed: ' . substr(trim($err), -200));
        }
        @rename($tmp, $cacheName);
        _gv_prune_cache();
    }
    flock($lock, LOCK_UN);
    fclose($lock);
    @unlink($lockName);
}

@touch($cacheName);

$size  = filesize($cacheName);
$start = 0;
$end   = $size - 1;
$range = $_SERVER['HTTP_RANGE'] ?? '';

header('Content-Type: video/mp4');
header('Accept-Ranges: bytes');

if ($range !== '' && preg_match('/bytes=(\d*)-(\d*)/', $range, $m)) {
    if ($m[1] !== '') $start = (int)$m[1];
    if ($m[2] !== '') $end   = (int)$m[2];
    if ($start > $end || $start >= $size) {
        http_response_code(416);
        header("Content-Range: bytes */{$size}");
        exit;
    }
    $end = min($end, $size - 1);
    http_response_code(206);
    header("Content-Range: bytes {$start}-{$end}/{$size}");
}
header('Content-Length: ' . ($end - $start + 1));

$fp = fopen($cacheName, 'rb');
fseek($fp, $start);
$left = $end - $start + 1;
while ($left > 0 && !feof($fp)) {
    $chunk = fread($fp, min(65536, $left));
    if ($chunk === false) break;
    echo $chunk;
    flush();
    $left -= strlen($chunk);
    if (connection_aborted()) break;
}
fclose($fp);