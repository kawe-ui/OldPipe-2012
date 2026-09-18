<?php
/**
 * Core Auth Module (yt2009 style + BebasDNS transport)
 */

if (!function_exists('yt_cookie_names')) {
    function yt_cookie_names(): array {
        return [
            'SID', 'HSID', 'SSID', 'APISID', 'SAPISID',
            '__Secure-1PSID', '__Secure-3PSID',
            '__Secure-1PAPISID', '__Secure-3PAPISID',
            'LOGIN_INFO', 'PREF', 'VISITOR_INFO1_LIVE', 'YSC'
        ];
    }
}

if (!function_exists('yt_cookie_boot_session')) {
    function yt_cookie_boot_session(): void {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        if (session_status() === PHP_SESSION_NONE) {
            if (session_name() !== 'YTSESSID') {
                @session_name('YTSESSID');
            }
            if (!headers_sent()) {
                @session_set_cookie_params([
                    'lifetime' => 86400 * 30, // 30 дней сессия живет стабильно
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            @session_start();
        }
    }
}

// Независимый резолв IP через BebasDNS (обход hosts и блокировок)
if (!function_exists('yt_resolve_domain_ip')) {
    function yt_resolve_domain_ip(string $domain = 'www.youtube.com'): string {
        static $resolvedCache = [];
        if (isset($resolvedCache[$domain])) {
            return $resolvedCache[$domain];
        }

        $ip = '';
        try {
            if (class_exists('BlueLibraries\Dns\Facade\DNS')) {
                $ip = \BlueLibraries\Dns\Facade\DNS::resolve($domain);
            } elseif (class_exists('BlueLibraries\DNS\Nameserver')) {
                $ip = \BlueLibraries\DNS\Nameserver::resolve($domain);
            } elseif (class_exists('Nameserver')) {
                $ip = Nameserver::resolve($domain);
            }
        } catch (\Throwable $e) {
            $ip = '';
        }

        if (empty($ip)) {
            $ip = gethostbyname($domain);
            if ($ip === $domain) $ip = '';
        }

        $resolvedCache[$domain] = $ip ?: '172.217.16.14';
        return $resolvedCache[$domain];
    }
}

// Защищенный cURL без устаревшего curl_close()
if (!function_exists('yt_curl_init_secure')) {
    function yt_curl_init_secure(string $url, string $domain = 'www.youtube.com'): \CurlHandle {
        $targetIp = yt_resolve_domain_ip($domain);
        $parsedUrl = parse_url($url);
        $scheme = $parsedUrl['scheme'] ?? 'https';
        $path = $parsedUrl['path'] ?? '/';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        
        $customUrl = "{$scheme}://{$targetIp}{$path}{$query}";
        $ch = curl_init($customUrl);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_ENCODING => '', 
            CURLOPT_RESOLVE => ["{$domain}:443:{$targetIp}"],
        ]);
        return $ch;
    }
}

// Парсер сырой строки кук в стиле yt2009
if (!function_exists('yt_parse_cookie_string')) {
    function yt_parse_cookie_string(string $raw): array {
        $out = [];
        $raw = str_replace(["\r\n", "\r"], "\n", $raw);
        foreach (explode("\n", $raw) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            if (stripos($line, 'cookie:') === 0) $line = trim(substr($line, 7));
            if (strpos($line, '=') !== false) {
                foreach (explode(';', $line) as $pair) {
                    $pair = trim($pair);
                    if ($pair === '' || strpos($pair, '=') === false) continue;
                    list($k, $v) = explode('=', $pair, 2);
                    $out[trim($k)] = trim($v);
                }
            }
        }
        return $out;
    }
}

if (!function_exists('yt_cookie_header')) {
    function yt_cookie_header(array $jar): string {
        $parts = [];
        foreach ($jar as $k => $v) {
            if ($v === '' || $v === null) continue;
            $parts[] = $k . '=' . $v;
        }
        return implode('; ', $parts);
    }
}

// Генерация подписи SAPISIDHASH
if (!function_exists('yt_sapisidhash')) {
    function yt_sapisidhash(array $jar, string $origin = 'https://www.youtube.com'): ?string {
        $sapisid = $jar['SAPISID'] ?? ($jar['__Secure-3PAPISID'] ?? ($jar['__Secure-1PAPISID'] ?? ''));
        if ($sapisid === '') return null;
        $ts = (string) time();
        return 'SAPISIDHASH ' . $ts . '_' . sha1($ts . ' ' . $sapisid . ' ' . $origin);
    }
}

// Запрос к InnerTube под куками
if (!function_exists('yt_innertube_cookie_post')) {
    function yt_innertube_cookie_post(string $endpoint, array $payload, array $jar): ?array {
        if ($jar === []) return null;
        $key = defined('INNERTUBE_API_KEY') ? INNERTUBE_API_KEY : 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';
        $base = defined('INNERTUBE_BASE_URL') ? INNERTUBE_BASE_URL : 'https://www.youtube.com/youtubei/v1/';
        $url = $base . ltrim($endpoint, '/') . '?key=' . $key;
        
        $clientVer = defined('INNERTUBE_CLIENT_VER') ? INNERTUBE_CLIENT_VER : '2.20260901.01.00';
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        
        if (!isset($payload['context'])) {
            $payload = array_merge(['context' => ['client' => [
                'hl' => 'en', 'gl' => 'US',
                'clientName' => defined('INNERTUBE_CLIENT_NAME') ? INNERTUBE_CLIENT_NAME : 'WEB',
                'clientVersion' => $clientVer,
                'userAgent' => $userAgent,
                'osName' => 'Windows',
                'osVersion' => '10.0',
            ]]], $payload);
        }
        
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $headers = [
            'Host: www.youtube.com',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($body),
            'Origin: https://www.youtube.com',
            'Referer: https://www.youtube.com/',
            'X-YouTube-Client-Name: 1',
            'X-YouTube-Client-Version: ' . $clientVer,
            'User-Agent: ' . $userAgent,
            'Accept: application/json, text/plain, */*',
            'Cookie: ' . yt_cookie_header($jar),
        ];
        
        $auth = yt_sapisidhash($jar);
        if ($auth) {
            $headers[] = 'Authorization: ' . $auth;
            $headers[] = 'X-Origin: https://www.youtube.com';
        }

        $ch = yt_curl_init_secure($url, 'www.youtube.com');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // curl_close убран для совместимости с PHP 8+

        if ($code < 200 || $code >= 300 || !is_string($resp) || $resp === '') return null;
        $j = json_decode($resp, true);
        return is_array($j) ? $j : null;
    }
}

// Верификация и вытягивание реального профиля YouTube
if (!function_exists('yt_cookie_verify_account')) {
    function yt_cookie_verify_account(array $jar): ?array {
        if ($jar === []) return null;

        $hasAuthCookie = false;
        foreach (['SID', 'HSID', 'SSID', 'APISID', 'SAPISID', '__Secure-1PSID', '__Secure-3PSID', 'LOGIN_INFO'] as $ck) {
            if (!empty($jar[$ck])) {
                $hasAuthCookie = true;
                break;
            }
        }
        if (!$hasAuthCookie) return null;

        $name = 'YouTube User'; $channelId = ''; $avatar = '/dynamic/pfp/default.png';

        $raw = yt_innertube_cookie_post('account/account_menu', [
            'deviceTheme' => 'DEVICE_THEME_SUPPORTED',
            'userInterfaceTheme' => 'USER_INTERFACE_THEME_LIGHT',
        ], $jar);

        if (is_array($raw)) {
            $stack = [$raw];
            while ($stack) {
                $node = array_pop($stack);
                if (!is_array($node)) continue;

                if ($name === 'YouTube User' && isset($node['accountName']['simpleText'])) {
                    $name = trim((string)$node['accountName']['simpleText']);
                }
                if ($avatar === '/dynamic/pfp/default.png' && isset($node['accountPhoto']['thumbnails'][0]['url'])) {
                    $avatar = (string)$node['accountPhoto']['thumbnails'][0]['url'];
                }
                if (empty($channelId) && isset($node['serviceEndpoint']['browseEndpoint']['browseId'])) {
                    $bid = (string)$node['serviceEndpoint']['browseEndpoint']['browseId'];
                    if (strpos($bid, 'UC') === 0) $channelId = $bid;
                }

                foreach ($node as $v) {
                    if (is_array($v)) $stack[] = $v;
                }
            }
        }

        return [
            'name'          => $name,
            'channelId'     => $channelId,
            'avatar'        => $avatar,
            'is_logged_in'  => true,
            'logged_in'     => true,
            'auth'          => 'cookie',
        ];
    }
}

// Автозапуск сессии при каждом инклюде
try {
    yt_cookie_boot_session();
    if (!empty($_SESSION['yt_cookie_jar']) && empty($_SESSION['user'])) {
        $user = yt_cookie_verify_account($_SESSION['yt_cookie_jar']);
        if ($user) {
            $_SESSION['user'] = $user;
            $_SESSION['account'] = $user;
            $_SESSION['yt_cookie_user'] = $user;
        }
    }
} catch (\Throwable $e) {}