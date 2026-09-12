<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

$CustomDomain    = $_GET['Domain']    ?? null;
$CustomUrlLength = $_GET['UrlLength'] ?? null;

$Trim = "http:// https://";

$currentPath = $_SERVER['PHP_SELF'];
$pathInfo    = pathinfo($currentPath);
$hostName    = $_SERVER['HTTP_HOST'];

if ($CustomDomain && ltrim($CustomDomain, $Trim) !== $hostName) {
    $hostName = ltrim($CustomDomain, $Trim);
}

if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = 'https';
} else {
    $protocol = 'http';
}

$UrlLength = $CustomUrlLength ?: 'Full';

if ($UrlLength === 'Short') {
    $CompiledDomainUrl   = $hostName;
    $CompiledApiUrl      = 'www.youtube.com';
} else {
    $UrlLength           = 'Full';
    $CompiledDomainUrl   = $protocol . '://' . $hostName;
    $CompiledApiUrl      = 'https://www.youtube.com';
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'ServerInfo' => [
        'Short'    => $hostName,
        'Full'     => $protocol . '://' . $hostName,
        'Url'      => $CompiledDomainUrl,
        'Protocol' => $protocol,
        'Protocol_Version' => trim($_SERVER['SERVER_PROTOCOL'] ?? '', 'HTTP/ HTTPS/'),
        'Path' => [
            'Short' => $pathInfo['dirname'],
            'Full'  => $currentPath,
        ],
        'File' => [
            'Name' => [
                'Full'      => $pathInfo['basename'],
                'Short'     => $pathInfo['filename'],
                'Extension' => $pathInfo['extension'] ?? '',
            ],
            'Arguments' => [
                'Domain'    => $CustomDomain,
                'UrlLength' => $CustomUrlLength,
            ],
        ],
    ],
    'Api' => [
        'Engine'    => 'InnerTube',
        'Base'      => INNERTUBE_BASE_URL,
        'Url'       => $CompiledApiUrl,
        'UrlLength' => $UrlLength,
        'Client' => [
            'Name'    => INNERTUBE_CLIENT_NAME,
            'Version' => INNERTUBE_CLIENT_VER,
        ],
        'Key'       => substr(INNERTUBE_API_KEY, 0, 10) . '...',
        'Endpoints' => [
            'player' => INNERTUBE_BASE_URL . 'player',
            'next'   => INNERTUBE_BASE_URL . 'next',
            'browse' => INNERTUBE_BASE_URL . 'browse',
            'search' => INNERTUBE_BASE_URL . 'search',
        ],
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);