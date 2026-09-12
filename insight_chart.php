<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  insight_chart.php — график «Views and discovery» панели статистики 2012
//  GET /insight_chart?v=ID → PNG 460×100 (как chart.apis.google.com, который
//  Google выключил в 2019-м; параметры оригинала: bg F4F4F4, линия 5F8FC9,
//  заливка DCE7EE, подпись максимума слева сверху, три даты снизу).
//
//  Кривая — НАСТОЯЩАЯ история просмотров: счётчики выковыриваются из архивных
//  копий watch-страницы в Wayback Machine (CDX-индекс → до 12 равномерно
//  разбросанных капчур), плюс точный текущий счётчик из InnerTube и ноль в
//  день публикации. Первый рендер поэтому медленный (десяток запросов к
//  web.archive.org); серия кэшируется на 30 дней, скудная (<3 точек) — на 6
//  часов, чтобы дособрать при следующем открытии. Если точек так и нет
//  (видео ни разу не архивировалось) — плашка «Views history is not available».
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

const IC_W = 460, IC_H = 100;

$video_id = (string)($_GET['v'] ?? '');
$validId  = preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id) === 1;

// ─── HTTP GET с проектными SSL/прокси-опциями ────────────────────────────────
// Wayback агрессивно троттлит (2 из 3 запросов — 503 или пустое тело),
// поэтому ретраи обязательны, а не для красоты.
function ic_get(string $url, int $timeout = 15, int $attempts = 2): ?string {
    for ($i = 0; $i < $attempts; $i++) {
        if ($i > 0) usleep(1500000);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_ENCODING       => '',
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);
        yt_curl_ssl_opts($ch);
        yt_curl_proxy_opts($ch);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($code === 200 && is_string($body) && $body !== '') return $body;
    }
    return null;
}

// ─── Счётчик просмотров из архивной копии watch-страницы (2006–2026) ─────────
function ic_extract_views(string $html): ?int {
    static $patterns = [
        '/itemprop="interactionCount"\s+content="(\d+)"/',      // микроданные ~2011–2018
        '/"viewCount"\s*:\s*"(\d+)"/',                          // ytInitialData / videoDetails 2017+
        '/"view_count"\s*:\s*"?(\d+)/',                         // flashvars-конфиги
        '/class="watch-view-count[^"]*"[^>]*>\s*(?:<strong>\s*)?([\d.,\s\x{00a0}]+)/u', // 2010–2013
        '/watchViewCount[^>]*>\s*([\d.,]+)/',                   // 2008–2010
        '/Views:\s*(?:<[^>]*>\s*)*([\d.,]+)/',                  // 2006–2008
    ];
    foreach ($patterns as $re) {
        if (preg_match($re, $html, $m) === 1) {
            $n = (int)preg_replace('/\D/u', '', $m[1]);
            if ($n > 0) return $n;
        }
    }
    return null;
}

// ─── Серия [unix_ts => views] из Wayback + кэш ───────────────────────────────
function ic_series(string $vid): array {
    $dir = CACHE_DIR . '/insight';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    $cf = $dir . '/' . $vid . '.json';

    if (is_file($cf)) {
        $c = json_decode((string)@file_get_contents($cf), true);
        if (is_array($c) && isset($c['points'])) {
            $ttl = count($c['points']) >= 3 ? CACHE_TTL_INSIGHT : 6 * 3600;
            if (time() - filemtime($cf) < $ttl) return $c['points'];
        }
    }

    set_time_limit(180); // первый сбор — это до дюжины запросов к медленному архиву

    $points = [];
    // Не больше одной капчуры на месяц (collapse по YYYYMM), только успешные
    $cdx = ic_get('https://web.archive.org/cdx/search/cdx?url='
        . rawurlencode('youtube.com/watch?v=' . $vid)
        . '&filter=statuscode:200&collapse=timestamp:6&fl=timestamp&limit=400', 20, 4);

    if ($cdx !== null) {
        $stamps = preg_split('/\s+/', trim($cdx), -1, PREG_SPLIT_NO_EMPTY);
        $n = count($stamps);
        if ($n > 12) { // равномерная выборка, первый и последний обязательно
            $picked = [];
            for ($i = 0; $i < 12; $i++) $picked[] = $stamps[(int)round($i * ($n - 1) / 11)];
            $stamps = array_values(array_unique($picked));
        }
        $deadline = time() + 90;
        foreach ($stamps as $ts) {
            if (time() > $deadline) break;
            $html = ic_get('https://web.archive.org/web/' . $ts . 'id_/http://www.youtube.com/watch?v=' . $vid, 12, 2);
            if ($html === null) continue;
            $views = ic_extract_views($html);
            if ($views === null) continue;
            $t = DateTime::createFromFormat('YmdHis', $ts, new DateTimeZone('UTC'));
            if ($t !== false) $points[$t->getTimestamp()] = $views;
        }
    }

    ksort($points);
    // Счётчик не убывает; редкие аномалии капчур выбрасываем
    $clean = []; $prev = -1;
    foreach ($points as $t => $v) {
        if ($v >= $prev) { $clean[$t] = $v; $prev = $v; }
    }

    @file_put_contents($cf, json_encode(['points' => $clean]), LOCK_EX);
    return $clean;
}

// ─── Точки: [публикация, 0] + архив + [сейчас, точный счётчик] ───────────────
$points = $validId ? ic_series($video_id) : [];
if ($validId) {
    $meta = yt_video_metadata($video_id);
    if ($meta !== null && ($meta['status'] ?? '') === 'OK') {
        if (!empty($meta['publishDate'])) {
            $pubTs = strtotime($meta['publishDate']);
            if ($pubTs !== false && (empty($points) || $pubTs < array_key_first($points))) {
                $points = [$pubTs => 0] + $points;
            }
        }
        if ((int)$meta['viewCount'] > 0) $points[time()] = (int)$meta['viewCount'];
    }
}
ksort($points);

// ─── Рендер ──────────────────────────────────────────────────────────────────
$im = imagecreatetruecolor(IC_W, IC_H);
$bg     = imagecolorallocate($im, 0xF4, 0xF4, 0xF4);
$line   = imagecolorallocate($im, 0x5F, 0x8F, 0xC9);
$fill   = imagecolorallocate($im, 0xDC, 0xE7, 0xEE);
$text   = imagecolorallocate($im, 0x33, 0x33, 0x33);
$grid   = imagecolorallocate($im, 0xDD, 0xDD, 0xDD);
imagefilledrectangle($im, 0, 0, IC_W - 1, IC_H - 1, $bg);

if (count($points) < 3) {
    $msg = 'Views history is not available';
    imagestring($im, 2, (int)((IC_W - strlen($msg) * imagefontwidth(2)) / 2), (int)(IC_H / 2 - 7), $msg, $text);
} else {
    $padT = 14; $padB = 14; $padL = 6; $padR = 6;
    $plotW = IC_W - $padL - $padR;
    $plotH = IC_H - $padT - $padB;

    $times = array_keys($points);
    $t0 = $times[0]; $t1 = end($times);
    $vMax = max($points) ?: 1;
    $span = max(1, $t1 - $t0);

    $px = fn(int $t): int => $padL + (int)round(($t - $t0) / $span * ($plotW - 1));
    $py = fn(int $v): int => $padT + $plotH - 1 - (int)round($v / $vMax * ($plotH - 1));

    // сетка-базис как в оригинале (chg): нижняя граница плюс верхний уровень
    imageline($im, $padL, $padT, IC_W - $padR - 1, $padT, $grid);
    imageline($im, $padL, IC_H - $padB - 1, IC_W - $padR - 1, IC_H - $padB - 1, $grid);

    // заливка под кривой
    $poly = [];
    foreach ($points as $t => $v) { $poly[] = $px($t); $poly[] = $py($v); }
    $poly[] = $px($t1); $poly[] = IC_H - $padB - 1;
    $poly[] = $px($t0); $poly[] = IC_H - $padB - 1;
    imagefilledpolygon($im, $poly, $fill);

    // сама кривая
    imagesetthickness($im, 2);
    $prevX = null; $prevY = null;
    foreach ($points as $t => $v) {
        $x = $px($t); $y = $py($v);
        if ($prevX !== null) imageline($im, $prevX, $prevY, $x, $y, $line);
        $prevX = $x; $prevY = $y;
    }
    imagesetthickness($im, 1);

    // подпись максимума сверху слева и три даты снизу (позиции 5/50/95%)
    imagestring($im, 2, $padL + 2, 1, number_format($vMax, 0, '.', ','), $text);
    // NB: float-ключи массива PHP превратил бы в int 0 — только пары
    foreach ([[0.05, $t0], [0.50, (int)(($t0 + $t1) / 2)], [0.95, $t1]] as [$pos, $t]) {
        $label = date('m/d/y', $t);
        $x = $padL + (int)($plotW * $pos) - (int)(strlen($label) * imagefontwidth(2) / 2);
        imagestring($im, 2, max($padL, min($x, IC_W - $padR - strlen($label) * imagefontwidth(2))), IC_H - 13, $label, $text);
    }
}

header('Content-Type: image/png');
header('Cache-Control: public, max-age=3600');
imagepng($im);
imagedestroy($im);
