<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  pfp.php — «default pfp API»: возвращает аватар 2012-стиля вместо монограммы
//
//  Задача: у канала без аватара YouTube отдаёт не пустоту, а СГЕНЕРИРОВАННУЮ
//  монограмму (буква на плоском фоне) — обычным URL вида
//  //yt3.googleusercontent.com/ytc/AIdro_…, который по строке неотличим от
//  живой авы. Поэтому решаем по картинке.
//
//  Признак монограммы — число уникальных цветов. Замеры на реальных каналах:
//      монограммы : 248…443
//      фотографии : 3896…6229
//  Разрыв на порядок, поэтому порог 1200 берётся с большим запасом в обе стороны.
//
//  Вердикт кэшируется по ID аватара (часть URL до «=s176-…»), так что размер
//  запрошенной картинки на кэш не влияет и сеть дёргается один раз на канал.
//
//  Fail-open: любая ошибка (сеть, таймаут, не картинка) → отдаём исходный аватар.
//  Ошибка проверки не должна ломать аватары на всём сайте.
// ═══════════════════════════════════════════════════════════════════════════════

require_once __DIR__ . '/../../includes/config.inc.php';

// Порог уникальных цветов: ниже — монограмма/плоская заглушка, выше — фото
const PFP_FLAT_COLOR_LIMIT = 1200;
// Размер, на котором считаем цвета (на нём же снимались пороги выше)
const PFP_PROBE_SIZE = 176;

/** 302 на итоговую картинку + разрешение браузеру кэшировать */
function pfp_redirect(string $location): never {
    header('Cache-Control: public, max-age=86400');
    header('Location: ' . $location, true, 302);
    exit;
}

$src = pfp_normalize_url((string)($_GET['u'] ?? ''));

// Пусто / чужой хост → заглушка (чужие хосты не проксируем: SSRF)
if ($src === '' || !pfp_is_google_avatar($src)) {
    pfp_redirect(DEFAULT_CHANNEL_AVATAR);
}

// ─── ID аватара: всё до «=s176-c-k-…» — стабилен между размерами ──────────────
$avatarId = preg_replace('/=.*$/', '', $src);
$cacheDir = CACHE_DIR . '/pfp';
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
$cacheFile = $cacheDir . '/' . sha1($avatarId) . '.txt';

// ─── Кэш вердикта ─────────────────────────────────────────────────────────────
if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL_PFP) {
    $verdict = trim((string)@file_get_contents($cacheFile));
    if ($verdict === 'monogram') pfp_redirect(DEFAULT_CHANNEL_AVATAR);
    if ($verdict === 'real')     pfp_redirect($src);
}

/** Скачивает картинку (с ограничением размера) */
function pfp_fetch(string $url): ?string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        // The initial URL passed the Google-CDN allow-list, but a redirect from
        // that host could otherwise point the fetch at an internal address
        // (SSRF). Confine redirects to HTTP(S) and cap how far they can chain.
        CURLOPT_PROTOCOLS      => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        // аватар 176px — десятки КБ; больше 2 МБ качать незачем
        CURLOPT_NOPROGRESS     => false,
        CURLOPT_PROGRESSFUNCTION => fn($c, $dlTotal, $dlNow) => $dlNow > 2_000_000 ? 1 : 0,
    ]);
    yt_curl_ssl_opts($ch);
    yt_curl_proxy_opts($ch);

    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    return ($body === false || $code !== 200 || $body === '') ? null : $body;
}

/**
 * Монограмма ли это.
 * null — определить не удалось (сеть/битая картинка), решение не кэшируем.
 *
 * Монограмма устроена жёстко: РОВНЫЙ несветлый фон до самых краёв, а поверх —
 * только БЕЛАЯ буква, занимающая малую долю площади. Проверяем именно это.
 * Одного подсчёта цветов мало: у плоских живых ав (логотип @YouTube, арт на
 * однотонном фоне) цветов так же мало, и они ложно превращались в дефолт.
 *
 * Замеры на s176 (сетка через пиксель):
 *                        цветов  рамка   фон%   не-фон:светлота  не-фон:белые%
 *   монограмма             65    1.000   95.1        243             82.7
 *   арт на сером фоне     429    1.000   87.5        177             59.6   ← real
 *   лого @YouTube         458    1.000   71.8         62              0.3   ← real
 *   арт во весь кадр     3693    0.045    6.5         47              0.0   ← real
 *   фото                 1276    0.006   12.8         90              9.8   ← real
 */
function pfp_is_monogram(string $imageData): ?bool {
    // Нет GD (extension=gd в php.ini) → определить нельзя. Возвращаем null
    // (fail-open: покажется исходная ава), а не роняем фаталом ВСЕ авы сайта.
    if (!function_exists('imagecreatefromstring')) return null;

    $im = @imagecreatefromstring($imageData);
    if ($im === false) return null;

    $w = imagesx($im);
    $h = imagesy($im);
    if ($w < 8 || $h < 8) return null;

    // ── 1. Фон = средний цвет рамки по периметру; рамка обязана быть однородной
    $edge = [];
    for ($x = 0; $x < $w; $x += 2) { $edge[] = imagecolorat($im, $x, 0); $edge[] = imagecolorat($im, $x, $h - 1); }
    for ($y = 0; $y < $h; $y += 2) { $edge[] = imagecolorat($im, 0, $y); $edge[] = imagecolorat($im, $w - 1, $y); }

    $n = count($edge);
    $sr = $sg = $sb = 0;
    foreach ($edge as $c) { $sr += ($c >> 16) & 0xFF; $sg += ($c >> 8) & 0xFF; $sb += $c & 0xFF; }
    $ar = $sr / $n; $ag = $sg / $n; $ab = $sb / $n;

    // Однородность: почти все пиксели рамки около среднего цвета
    // (сравнение с допуском, а не на равенство — JPEG шумит на ±пару единиц).
    $near = 0;
    foreach ($edge as $c) {
        if (abs((($c >> 16) & 0xFF) - $ar) < 26
         && abs((($c >>  8) & 0xFF) - $ag) < 26
         && abs(( $c        & 0xFF) - $ab) < 26) $near++;
    }
    if ($near / $n < 0.96) return false;   // рамка пёстрая → лого/арт/фото

    // Фон монограммы — всегда средний тон (буква-то белая). Светлый фон → лого.
    $edgeLum = 0.2126 * $ar + 0.7152 * $ag + 0.0722 * $ab;
    if ($edgeLum > 200) return false;

    // ── 2. Полный проход: цветов мало, фон доминирует, не-фон — белая буква
    $colors = [];
    $tot = 0; $bg = 0; $nb = 0; $nbLumSum = 0.0; $nbWhite = 0;
    for ($y = 0; $y < $h; $y += 2) {
        for ($x = 0; $x < $w; $x += 2) {
            $c = imagecolorat($im, $x, $y);
            $colors[$c] = true;
            if (count($colors) > PFP_FLAT_COLOR_LIMIT) return false; // явно фото
            $tot++;
            $r = ($c >> 16) & 0xFF; $g = ($c >> 8) & 0xFF; $b = $c & 0xFF;
            if (abs($r - $ar) < 26 && abs($g - $ag) < 26 && abs($b - $ab) < 26) { $bg++; continue; }
            $nb++;
            $nbLumSum += 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
            if ($r >= 225 && $g >= 225 && $b >= 225) $nbWhite++;
        }
    }

    if ($nb < 8)              return true;   // сплошная заливка без буквы — заглушка
    if ($bg / $tot < 0.88)    return false;  // рисунок крупнее буквы → живой арт
    if ($nbLumSum / $nb < 210) return false; // «буква» не белая → живой арт
    if ($nbWhite / $nb < 0.70) return false;
    return true;
}

// ─── Проверка ─────────────────────────────────────────────────────────────────
// Тянем фиксированный размер: пороги сняты именно на нём, и от размера в
// исходном URL вердикт зависеть не должен.
//
// Параметр «=sN-c» (размер + квадратный кроп) — универсальный для всего Google-CDN.
// Расширенный «-k-c0x00ffffff-no-rj» понимают ТОЛЬКО каналы YouTube (yt3/ytc), а
// аватары Google-аккаунта (lh3.googleusercontent.com/a/…, откуда берётся ава
// залогиненного через OAuth пользователя) на него отвечают 400 → проба падала,
// fail-open отдавал сырой url, и монограмма аккаунта не превращалась в дефолт 2012.
// На фото/монограммах результат тот же (проверено: байты картинки идентичны).
$probeUrl = $avatarId . '=s' . PFP_PROBE_SIZE . '-c';
$data     = pfp_fetch($probeUrl);
$verdict  = $data === null ? null : pfp_is_monogram($data);

if ($verdict === null) {
    pfp_redirect($src);  // fail-open, вердикт не кэшируем — попробуем позже
}

@file_put_contents($cacheFile, $verdict ? 'monogram' : 'real', LOCK_EX);
pfp_redirect($verdict ? DEFAULT_CHANNEL_AVATAR : $src);
