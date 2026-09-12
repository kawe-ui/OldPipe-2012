<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  picker_ajax.php — пикеры футера (Language / Location / Safety)
//  ОРИГИНАЛЬНЫЕ ответы youtube.com 2012 года из web.archive
//  (includes/pickers/*.html — captures 20121221/20130129), протокол 1:1:
//    GET ?action_language=1&base_url=…   → {"html": …}
//    GET ?action_country=1&base_url=…    → {"html": …}
//    GET ?action_safetymode=1&base_url=… → {"html": …}
//    POST ?action_update_language=1      → redirect на base_url (форма пикера)
// ═══════════════════════════════════════════════════════════════════════════════

// ─── Локальный base_url (никаких редиректов наружу) ───────────────────────────
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php';

$input = (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') ? $_POST : $_GET;
$baseUrl = $input['base_url'] ?? '/';
$bp = parse_url($baseUrl);
$baseHref = ($bp['path'] ?? '/') . (isset($bp['query']) ? '?' . $bp['query'] : '');
if ($baseHref === '' || $baseHref[0] !== '/') $baseHref = '/';
$sep = str_contains($baseHref, '?') ? '&amp;' : '?';

// ─── POST формы выбора языка — просто возвращаемся назад ─────────────────────
if (isset($_GET['action_update_language'])) {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit;
    }
    if (!yt_session_token_valid($_POST['session_token'] ?? null)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
    if (!empty($_POST['hl']) && preg_match('/^[A-Za-z-]{2,10}$/', $_POST['hl'])) {
        yt_set_preference_cookie('PREF_hl', $_POST['hl']);
    }
    header('Location: ' . str_replace('&amp;', '&', $baseHref), true, 303);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

function picker_template(string $name): string {
    $f = $_SERVER['DOCUMENT_ROOT'] . '/includes/pickers/' . $name;
    return is_file($f) ? (string)file_get_contents($f) : '';
}

$html = '';

if (isset($_GET['action_language'])) {
    $html = picker_template('picker_language.html');
    // форма должна вернуть на текущую страницу
    $html = str_replace(
        '<input type="hidden" name="base_url" value="/">',
        '<input type="hidden" name="base_url" value="' . htmlspecialchars(str_replace('&amp;', '&', $baseHref), ENT_QUOTES) . '">',
        $html
    );
    // архивный session_token заменяем локальным
    $html = preg_replace(
        '/name="session_token" value="[^"]*"/',
        'name="session_token" value="' . htmlspecialchars(yt_session_token(), ENT_QUOTES, 'UTF-8') . '"',
        $html
    );
}

if (isset($_GET['action_country'])) {
    $html = picker_template('picker_country.html');
    // ссылки вида href="/?persist_gl=1&gl=XX" → на текущую страницу.
    // base_url приходит от клиента — экранируем перед вставкой в href (XSS).
    $safeHref = htmlspecialchars($baseHref, ENT_QUOTES, 'UTF-8');
    $html = str_replace('href="/?persist_gl', 'href="' . $safeHref . $sep . 'persist_gl', $html);
}

if (isset($_GET['action_safetymode'])) {
    $html = picker_template('picker_safety.html');
    $html = preg_replace(
        '/name="session_token" value="[^"]*"/',
        'name="session_token" value="' . htmlspecialchars(yt_session_token(), ENT_QUOTES, 'UTF-8') . '"',
        $html
    );
}

if ($html === '') {
    http_response_code(400);
    echo json_encode(['errors' => ['Unknown picker action.']]);
    exit;
}

echo json_encode(['html' => $html], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
