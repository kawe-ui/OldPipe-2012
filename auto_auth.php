<?php
// Функция автоматического чтения кук из файла Netscape формата (cookies.txt)
function yt_load_cookies_from_file($filepath) {
    if (!file_exists($filepath)) {
        return false;
    }

    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $cookieJar = [];

    foreach ($lines as $line) {
        // Пропускаем комментарии
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        $parts = preg_split('/\s+/', $line);
        // Формат Netscape: domain, flag, path, secure, expiration, name, value
        if (count($parts) >= 7) {
            $name = $parts[5];
            $value = $parts[6];
            
            // Нам важны ключевые куки авторизации YouTube/Google
            $targetCookies = ['__Secure-1PSID', 'SAPISID', 'HSID', 'SSID', 'APISID', '__Secure-3PSID'];
            if (in_array($name, $targetCookies)) {
                $cookieJar[$name] = $value;
            }
        }
    }

    return !empty($cookieJar) ? $cookieJar : false;
}

// Пример использования в вашем скрипте инициализации сессии:
$cookieFile = __DIR__ . '/cookies.txt'; // Путь к вашему файлу с куками
$loadedCookies = yt_load_cookies_from_file($cookieFile);

if ($loadedCookies) {
    // Сохраняем в сессию так, как ожидает ваш механизм проверки (например, yt_cookie_verify_account)
    $_SESSION['yt_auth'] = $loadedCookies;
    
    // Если нужно сразу проверить валидность:
    // $isValid = yt_cookie_verify_account($_SESSION['yt_auth']);
    // if ($isValid) { ... }
}