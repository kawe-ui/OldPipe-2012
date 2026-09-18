<?php
/**
 * ServiceLoginAuth.php — Интеллектуальный обработчик входа (yt2009 style)
 * Перехватывает куки из любых полей формы (включая старые поля email/password)
 */

ini_set('display_errors', '0');
error_reporting(E_ALL);

try {
    if (file_exists(__DIR__ . '/cookie_auth.inc.php')) {
        require_once __DIR__ . '/cookie_auth.inc.php';
    }

    if (function_exists('yt_cookie_boot_session')) {
        yt_cookie_boot_session();
    } else {
        if (session_status() === PHP_SESSION_NONE) {
            @session_name('YTSESSID');
            @session_start();
        }
    }

    $rawCookies = '';

    // Сканируем POST-запрос на наличие кук в ЛЮБОМ поле (email, password, cookies, text и т.д.)
    if (!empty($_POST)) {
        foreach ($_POST as $key => $val) {
            if (is_string($val) && !empty($val)) {
                // Если поле содержит признаки кук YouTube (SID, SAPISID и т.д.) или длинную строку
                if (strpos($val, 'SID=') !== false || strpos($val, 'SAPISID=') !== false || strlen($val) > 20) {
                    $rawCookies = $val;
                    break;
                }
            }
        }
        
        // Если специфичные метки не нашлись, берем самое первое непустое длинное поле
        if (empty($rawCookies)) {
            foreach ($_POST as $val) {
                if (is_string($val) && strlen(trim($val)) > 10) {
                    $rawCookies = $val;
                    break;
                }
            }
        }
    }

    // Если в POST пусто, проверяем сырой ввод (JSON / AJAX)
    if (empty($rawCookies)) {
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $rawCookies = $input;
        }
    }

    $jar = [];
    if (!empty($rawCookies)) {
        if (function_exists('yt_parse_cookie_string')) {
            $jar = yt_parse_cookie_string($rawCookies);
        }
        
        // Ручной запасной парсер для key=value
        if (empty($jar) && strpos($rawCookies, '=') !== false) {
            foreach (explode(';', $rawCookies) as $pair) {
                $pair = trim($pair);
                if (strpos($pair, '=') !== false) {
                    list($k, $v) = explode('=', $pair, 2);
                    $jar[trim($k)] = trim($v);
                }
            }
        }
    }

    // Если свежих кук нет, подтягиваем из сохраненной сессии
    if (empty($jar) && function_exists('yt_cookie_load_jar')) {
        $jar = yt_cookie_load_jar();
    }

    // Если куки успешно собраны — сохраняем сессию и проверяем аккаунт через InnerTube
    if (!empty($jar)) {
        if (function_exists('yt_cookie_store_jar')) {
            yt_cookie_store_jar($jar);
        }
        
        if (function_exists('yt_cookie_verify_account')) {
            $user = yt_cookie_verify_account($jar);
            if ($user && is_array($user)) {
                $_SESSION['user'] = $user;
                $_SESSION['account'] = $user;
                $_SESSION['yt_cookie_user'] = $user;
                $_SESSION['logged_in'] = true;
            }
        }
    }

} catch (\Throwable $e) {
    @error_log('ServiceLoginAuth Error: ' . $e->getMessage());
}

// Отладочный режим: если перейти как /ServiceLoginAuth?debug=1
if (isset($_GET['debug'])) {
    header('Content-Type: text/html; charset=utf-8');
    echo "<h2>ServiceLoginAuth Debug</h2>";
    echo "POST received: " . (!empty($_POST) ? 'YES' : 'NO') . "<br>";
    echo "Raw Cookies Length: " . strlen($rawCookies ?? '') . "<br>";
    echo "Parsed Jar Count: " . count($jar) . "<br>";
    echo "Session User: " . (isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : 'NOT LOGGED IN') . "<br>";
    echo "<pre>"; print_r($jar); echo "</pre>";
    exit;
}

// Редирект обратно на главную или указанную страницу
$redirectUrl = '/';
if (!empty($_GET['continue'])) {
    $redirectUrl = (string)$_GET['continue'];
    if (strpos($redirectUrl, 'http://') === 0 || strpos($redirectUrl, 'https://') === 0) {
        $redirectUrl = '/';
    }
}

if (!headers_sent()) {
    header('Location: ' . $redirectUrl);
    exit;
} else {
    echo '<script>window.location.href = ' . json_encode($redirectUrl) . ';</script>';
    exit;
}