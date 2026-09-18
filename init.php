<?php
session_start();

// 2. Подключаем файл с функцией автоматической загрузки кук
require_once __DIR__ . '/auto_auth.php';

// 3. Проверяем: если сессия еще не заполнена, пытаемся подтянуть из cookies.txt
if (empty($_SESSION['yt_auth'])) {
    $cookieFile = __DIR__ . '/cookies.txt';
    $loadedCookies = yt_load_cookies_from_file($cookieFile);
    
    if ($loadedCookies) {
        $_SESSION['yt_auth'] = $loadedCookies;
    }
}
/**
 * Global Initialization File (в стиле Rehike)
 */

// Базовые настройки отображения ошибок для разработки
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Определение корневых путей
define('ROOT_PATH', __DIR__);
define('VENDOR_PATH', ROOT_PATH . '/vendor');
define('INCLUDES_PATH', ROOT_PATH . '/includes');

if (file_exists(VENDOR_PATH . '/autoload.php')) {
    require_once VENDOR_PATH . '/autoload.php';
} else {
    die('Critical Error: Composer autoloader not found. Run "composer install".');
}

if (file_exists(INCLUDES_PATH . '/functions.php')) {
    require_once INCLUDES_PATH . '/functions.php';
}

require_once INCLUDES_PATH . '/Nameserver.php';