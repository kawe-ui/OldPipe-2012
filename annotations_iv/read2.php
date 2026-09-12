<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  read2.php — источник аннотаций для Flash-плеера (flashvar iv_read_url)
//
//  ВАЖНО: InnerTube аннотации НЕ отдаёт. YouTube удалил их в январе 2019 года;
//  в /youtubei/v1/player поля annotations больше нет ни для одного видео.
//  Единственный источник классических аннотаций — архив Archive Team,
//  поэтому XML тянется оттуда. Остальное (кэш, cURL, конфиг) переведено
//  на инфраструктуру проекта — от Invidious тут больше ничего не осталось.
//
//  Контракт: ВСЕГДА валидный XML. Модуль 2012_iv3_module парсит ответ как XML,
//  и любой мусор в теле (PHP-warning, пустая строка, HTML 404-страницы) ломает
//  разбор — из-за этого плеер ругался ошибкой и не показывал кнопку аннотаций.
//  Нет аннотаций → пустой <document/>, но валидный.
// ═══════════════════════════════════════════════════════════════════════════════

require_once __DIR__ . '/../includes/config.inc.php';

header('Content-Type: text/xml; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

// Пустой, но валидный документ — то, что плеер понимает как «аннотаций нет»
const IV_EMPTY_DOC = '<?xml version="1.0" encoding="UTF-8" ?><document><annotations></annotations></document>';

/** Отдаёт XML и завершает запрос */
function iv_output(string $xml): never {
    header('Content-Length: ' . strlen($xml));
    echo $xml;
    exit;
}

// ─── 1. Валидация video_id ────────────────────────────────────────────────────
// Без неё $videoId[0] на пустой строке = warning, а сам id уходил в URL как есть
// (path traversal: ?video_id=../../foo).
$video_id = (string)($_GET['video_id'] ?? '');
if (preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id) !== 1) {
    iv_output(IV_EMPTY_DOC);
}

// ─── 2. Кэш (в т.ч. отрицательный: у большинства видео аннотаций нет) ────────
$cacheDir = CACHE_DIR . '/annotations';
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
$cacheFile = $cacheDir . '/' . $video_id . '.xml';

if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL_ANNOTATIONS) {
    $cached = @file_get_contents($cacheFile);
    if ($cached !== false && $cached !== '') iv_output($cached);
}

// ─── 3. Путь в архиве: <первая буква>/<первые 3 символа>/<id>.xml.gz ─────────
// Идентификаторы на «-» лежат в отдельной ветке -/ar-/.
function iv_archive_url(string $videoId): string {
    $bucketDir = $videoId[0] === '-' ? '-/ar-' : $videoId[0];
    return 'https://storage.googleapis.com/biggest_bucket/annotations/'
         . $bucketDir . '/' . substr($videoId, 0, 3) . '/' . $videoId . '.xml.gz';
}

// ─── 4. Загрузка (cURL вместо file_get_contents: таймаут, коды ответа, прокси) ─
// Возвращает ['xml' => ?string, 'definitive' => bool].
// definitive=true  — архив ответил по существу (200 с XML либо честный 404);
//                    результат можно класть в кэш надолго.
// definitive=false — сеть/SSL/таймаут: ответ неизвестен, кэшировать НЕЛЬЗЯ,
//                    иначе разовый сбой замораживает «аннотаций нет» на месяц.
function iv_fetch(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 15,
        // GCS хранит .gz и отдаёт его decompressive transcoding'ом; просим
        // gzip явно и разжимаем сами, если придёт сырой поток.
        CURLOPT_ENCODING       => 'gzip',
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
    ]);
    yt_curl_ssl_opts($ch);
    yt_curl_proxy_opts($ch);

    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $net  = $body === false || $code === 0;

    if ($net)          return ['xml' => null, 'definitive' => false];
    // 404 — нормальная ситуация: у видео просто нет аннотаций в архиве
    if ($code === 404)  return ['xml' => null, 'definitive' => true];
    if ($code !== 200 || $body === '') return ['xml' => null, 'definitive' => false];

    // Если транскодинг не сработал — на входе сырой gzip (\x1f\x8b)
    if (substr($body, 0, 2) === "\x1f\x8b") {
        $plain = @gzdecode($body);
        if ($plain === false) return ['xml' => null, 'definitive' => false];
        $body = $plain;
    }

    // Архив может отдать XML-ошибку GCS вместо документа аннотаций
    if (stripos(ltrim($body), '<document') === false) {
        return ['xml' => null, 'definitive' => false];
    }
    return ['xml' => $body, 'definitive' => true];
}

$res = iv_fetch(iv_archive_url($video_id));
$xml = $res['xml'] ?? IV_EMPTY_DOC;

if ($res['definitive']) {
    @file_put_contents($cacheFile, $xml, LOCK_EX);
}
iv_output($xml);
