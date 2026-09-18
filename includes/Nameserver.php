<?php
namespace OldPipe\Includes\Nameserver;
use BlueLibraries\Dns\Facade\DNS;

class Nameserver
{
    public static function resolveAndRequest(string $url, string $targetIp = '', string $cookieHeader = '')
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? 'www.youtube.com';
        $scheme = $parsedUrl['scheme'] ?? 'https';
        $path = $parsedUrl['path'] ?? '/';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        
        if (empty($targetIp)) {
            try {
                if (class_exists('BlueLibraries\Dns\Facade\DNS')) {
                    $targetIp = DNS::resolve($host);
                } elseif (class_exists('BlueLibraries\DNS\Nameserver')) {
                    $targetIp = \BlueLibraries\DNS\Nameserver::resolve($host);
                }
            } catch (\Throwable $e) {
                $targetIp = '';
            }
        }
        
        if (empty($targetIp)) {
            $targetIp = gethostbyname($host);
            if ($targetIp === $host) {
                $targetIp = '172.217.16.14';
            }
        }
        
        $customUrl = "{$scheme}://{$targetIp}{$path}{$query}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $customUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            "Host: {$host}",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36",
            "Accept-Language: en-US,en;q=0.9",
            "Origin: https://www.youtube.com",
            "Referer: https://www.youtube.com/"
        ];

        if (!empty($cookieHeader)) {
            $headers[] = "Cookie: {$cookieHeader}";
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RESOLVE, ["{$host}:443:{$targetIp}"]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}