<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  auth.inc.php — определение входа по куки, как в Rehike (modules/Rehike/Signin)
//
//  Идея та же, что там: страница работает под доменом www.youtube.com (у нас —
//  через Fiddler, у Rehike — через прямой деплой), поэтому браузер сам присылает
//  настоящие куки аккаунта в каждом запросе. Логин отдельным шагом не нужен —
//  «залогинен» определяется присутствием куки SAPISID на каждой загрузке
//  страницы (AuthManager::determineShouldAuth в оригинале).
//
//  Источник данных аккаунта — /getAccountSwitcherEndpoint, тот же первостатейный
//  (не InnerTube) эндпоинт, что использует Rehike (Signin/AuthManager.php,
//  requestSigninData). Ответ — JSON с XSSI-префixом )]}' и структурой, разобранной
//  1:1 по Signin/Switcher.php.
//
//  Google OAuth удалён: это ЕДИНСТВЕННЫЙ путь входа. На голом localhost кук
//  youtube.com не бывает, поэтому там сайт всегда в состоянии «гость».
// ═══════════════════════════════════════════════════════════════════════════════

require_once(__DIR__ . '/config.inc.php');
require_once(__DIR__ . '/session.inc.php');

const YT_AUTH_ORIGIN = 'https://www.youtube.com';

/**
 * Эпохально-корректная ссылка «Sign in» — та же, что в статичной разметке 2012:
 * настоящий accounts.google.com/ServiceLogin с continue на www.youtube.com/signin.
 * Под Fiddler'ом www.youtube.com — это наш сервер, так что после входа Google
 * вернёт человека на локальный /signin (см. signin.php) уже с куками аккаунта.
 * $next — локальный путь, куда вернуться после входа.
 */
function yt_signin_url(string $next = '/'): string {
    if ($next === '' || $next[0] !== '/' || str_starts_with($next, '//')) $next = '/';
    $continue = 'https://www.youtube.com/signin?' . http_build_query([
        'action_handle_signin' => 'true',
        'nomobiletemp'         => '1',
        'hl'                   => 'en_US',
        'next'                 => $next,
    ]);
    return 'https://accounts.google.com/ServiceLogin?' . http_build_query([
        'passive'  => 'true',
        'continue' => $continue,
        'uilel'    => '3',
        'hl'       => 'en_US',
        'service'  => 'youtube',
    ]);
}

/**
 * «Sign in to another account…» — добавляет ещё одну сессию рядом с уже
 * залогиненными (authuser=-1 просит Google предложить выбор/новый вход),
 * возвращает на $next, а не на зашитую страницу. Тот же принцип, что и в
 * yt_signin_url(), только AddSession вместо ServiceLogin.
 */
function yt_add_session_url(string $next = '/'): string {
    if ($next === '' || $next[0] !== '/' || str_starts_with($next, '//')) $next = '/';
    $continue = 'https://www.youtube.com/signin?' . http_build_query([
        'action_handle_signin' => 'true',
        'authuser'             => '-1',
        'hl'                   => 'en_US',
        'next'                 => $next,
        'nomobiletemp'         => '1',
    ]);
    return 'https://accounts.google.com/AddSession?' . http_build_query([
        'passive'  => 'false',
        'continue' => $continue,
        'uilel'    => '0',
        'service'  => 'youtube',
        'hl'       => 'en_US',
    ]);
}

/** Залогинен ли пользователь (по кукам YouTube, проброшенным через Fiddler) */
function yt_is_logged_in(): bool {
    return yt_account_info() !== null;
}

/** Сериализует $_COOKIE в HTTP-заголовок Cookie — как Network::getCurrentRequestCookie() */
function yt_current_request_cookie(): string {
    if (empty($_COOKIE)) return '';
    $out = '';
    foreach ($_COOKIE as $k => $v) $out .= $k . '=' . $v . '; ';
    return $out;
}

/** determineShouldAuth(): наличие SAPISID — единственный критерий «залогинен» */
function yt_should_auth(): bool {
    return isset($_COOKIE['SAPISID']) && $_COOKIE['SAPISID'] !== '';
}

/** getAuthHeader(): подпись SAPISIDHASH для InnerTube-запросов от имени юзера */
function yt_sapisid_hash(string $origin = YT_AUTH_ORIGIN): string {
    $sapisid = $_COOKIE['SAPISID'] ?? '';
    $ts = time();
    return 'SAPISIDHASH ' . $ts . '_' . sha1($ts . ' ' . $sapisid . ' ' . $origin);
}

/** useAuthService(): заголовки для авторизованного InnerTube-запроса */
function yt_auth_headers(): array {
    if (!yt_should_auth()) return [];
    return [
        'Authorization: ' . yt_sapisid_hash(),
        'Origin: '        . YT_AUTH_ORIGIN,
        'Cookie: '        . yt_current_request_cookie(),
    ];
}

/** Первостатейный (не InnerTube) запрос к youtube.com с прокинутыми куками */
function yt_first_party_request(string $url): ?string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER     => ['Cookie: ' . yt_current_request_cookie()],
    ]);
    yt_curl_ssl_opts($ch);
    yt_curl_proxy_opts($ch);
    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    return ($body === false || $code !== 200) ? null : $body;
}

/** accountItem() — разбор одного элемента переключателя аккаунтов */
function _yt_switcher_account_item(array $account, string $groupEmail = ''): array {
    $gaiaId = '';
    $clientCacheKey = '';
    $signinUrl = '';
    foreach ($account['serviceEndpoint']['selectActiveIdentityEndpoint']['supportedTokens'] ?? [] as $token) {
        if (isset($token['pageIdToken']['pageId'])) $gaiaId = $token['pageIdToken']['pageId'];
        if (isset($token['offlineCacheKeyToken']['clientCacheKey'])) {
            $clientCacheKey = $token['offlineCacheKeyToken']['clientCacheKey'];
        }
        // Настоящая ссылка переключения на этот аккаунт — как в Rehike
        // (Signin/Switcher.php::accountItem() -> switchUrl), без выдумывания
        // своей логики: Google уже подписал переход нужным authuser/pageId.
        if (isset($token['accountSigninToken']['signinUrl'])) $signinUrl = $token['accountSigninToken']['signinUrl'];
    }
    // offlineCacheKeyToken.clientCacheKey — это UC-идентификатор канала БЕЗ
    // префикса «UC» (сверено вручную с независимым резолвом @handle на
    // нескольких реальных аккаунтах — совпадает 1:1). Он уже лежит в этом же
    // ответе свитчера, так что резолвить канал отдельным сетевым запросом
    // (resolve_url) в норме вообще не требуется.
    $hasChannel = (bool)($account['hasChannel'] ?? false);
    $channelId  = ($clientCacheKey !== '' && $hasChannel) ? 'UC' . $clientCacheKey : '';
    $switchUrl  = $signinUrl !== '' ? YT_AUTH_ORIGIN . $signinUrl : '';

    return [
        'name'       => $account['accountName']['simpleText'] ?? '',
        'photo'      => $account['accountPhoto']['thumbnails'][0]['url'] ?? '',
        // accountByline — это НЕ @handle (часто счётчик подписчиков вроде
        // «141 подписчик»); настоящий @handle — отдельное поле channelHandle.
        'byline'     => $account['accountByline']['simpleText'] ?? '',
        'handle'     => $account['channelHandle']['simpleText'] ?? '',
        'channelId'  => $channelId,
        'selected'   => (bool)($account['isSelected'] ?? false),
        'hasChannel' => $hasChannel,
        'gaiaId'     => $gaiaId,
        'switchUrl'  => $switchUrl,
        // email гугл-аккаунта, под которым лежит этот канал (для «своих»
        // каналов первой секции — пусто, группа подписана общим заголовком).
        'groupEmail' => $groupEmail,
    ];
}

/**
 * Switcher::parseResponse() — разбор ответа /getAccountSwitcherEndpoint.
 *
 * Ответ состоит из НЕСКОЛЬКИХ секций (sections[]), а не одной: первая — каналы
 * текущего гугл-аккаунта (googleAccountHeaderRenderer с именем/почтой),
 * следующие — каналы КАЖДОГО ДРУГОГО залогиненного гугл-аккаунта в браузере,
 * каждая со своим accountItemSectionHeaderRenderer.title = его email. Старая
 * версия читала только sections[0] и поэтому не видела остальные аккаунты
 * вообще — из-за этого «Switch account» ничего, кроме текущего, не находил.
 */
function yt_parse_switcher_response(string $raw): ?array {
    // XSSI-защита Google: первые 4 байта — )]}'
    $json = json_decode(substr($raw, 4), true);
    if (!is_array($json)) return null;

    $sections = $json['data']['actions'][0]['getMultiPageMenuAction']['menu']
        ['multiPageMenuRenderer']['sections'] ?? null;
    if (!is_array($sections) || $sections === []) return null;

    $googleHeader = $sections[0]['accountSectionListRenderer']['header']['googleAccountHeaderRenderer'] ?? [];

    $channels = [];
    foreach ($sections as $sec) {
        $sl = $sec['accountSectionListRenderer'] ?? null;
        if ($sl === null) continue;

        foreach ($sl['contents'] ?? [] as $group) {
            $renderer = $group['accountItemSectionRenderer'] ?? null;
            if ($renderer === null) continue;

            // Заголовок секции — email гугл-аккаунта, к которому относятся
            // каналы ниже. У первой (текущей) секции такого заголовка нет —
            // там вместо него googleAccountHeaderRenderer уровнем выше.
            $groupEmailRuns = $renderer['header']['accountItemSectionHeaderRenderer']['title']['runs'] ?? [];
            $groupEmail = $groupEmailRuns[0]['text'] ?? '';

            foreach ($renderer['contents'] ?? [] as $it) {
                if (isset($it['accountItem'])) $channels[] = _yt_switcher_account_item($it['accountItem'], $groupEmail);
            }
        }
    }

    $active = null;
    foreach ($channels as $ch) if ($ch['selected']) { $active = $ch; break; }
    if ($active === null) $active = $channels[0] ?? null;
    if ($active === null) return null;

    return [
        'name'      => $googleHeader['name']['simpleText']  ?? $active['name'],
        'email'     => $googleHeader['email']['simpleText'] ?? '',
        'avatar'    => $active['photo'],
        'gaiaId'    => $active['gaiaId'],
        'hasChannel'=> $active['hasChannel'],
        'channelId' => $active['channelId'],
        'handle'    => $active['handle'],
        'byline'    => $active['byline'],
        // Полный список переключаемых аккаунтов (включая текущий, с
        // selected=true) — для реальной панели «Switch account».
        'accounts'  => $channels,
    ];
}

/**
 * Константы InnerTube живут в api/servermain.php, который подключается не на
 * каждой странице, — без этих фолбэков обращение к ним было бы фатальной
 * ошибкой PHP 8 при «неудачном» порядке include.
 */
function _yt_innertube_const(string $name, string $fallback): string {
    return defined($name) ? (string)constant($name) : $fallback;
}

/** navigation/resolve_url("/profile") → browseId текущего канала (UC...) */
function yt_resolve_own_channel_id(array $authHeaders): string {
    if ($authHeaders === []) return '';

    $base = _yt_innertube_const('INNERTUBE_BASE_URL',   'https://www.youtube.com/youtubei/v1/');
    $key  = _yt_innertube_const('INNERTUBE_API_KEY',    'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8');
    $ver  = _yt_innertube_const('INNERTUBE_CLIENT_VER', '2.20260709.01.00');

    $ch = curl_init($base . 'navigation/resolve_url?key=' . $key . '&prettyPrint=false');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode([
            'context' => ['client' => ['clientName' => 'WEB', 'clientVersion' => $ver, 'hl' => 'en', 'gl' => 'US']],
            'url'     => 'https://www.youtube.com/profile',
        ], JSON_UNESCAPED_SLASHES),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        // Авторизованному InnerTube-запросу кроме SAPISIDHASH нужны X-Origin
        // и X-Goog-AuthUser — без них YouTube отвечает 401.
        CURLOPT_HTTPHEADER     => array_merge($authHeaders, [
            'Content-Type: application/json',
            'X-Origin: '                . YT_AUTH_ORIGIN,
            'X-Goog-AuthUser: 0',
            'X-YouTube-Client-Name: 1',
            'X-YouTube-Client-Version: ' . $ver,
        ]),
    ]);
    yt_curl_ssl_opts($ch);
    yt_curl_proxy_opts($ch);
    $res  = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err  = curl_error($ch);
    if ($res === false || $code !== 200) {
        error_log('[yt-auth] resolve_url(/profile) failed: HTTP ' . $code
            . ($err !== '' ? ' curl: ' . $err : '')
            . ' ' . substr((string)$res, 0, 300));
        return '';
    }
    $j = json_decode($res, true);
    $browseId = $j['endpoint']['browseEndpoint']['browseId'] ?? '';
    if (!is_string($browseId) || !str_starts_with($browseId, 'UC')) {
        error_log('[yt-auth] resolve_url(/profile): нет browseEndpoint, ответ: ' . substr($res, 0, 300));
        return '';
    }
    return $browseId;
}

/**
 * Запасной резолв: @handle → UC-идентификатор через InnerTube resolve_url,
 * без ключа Data API и без авторизации (тот же приём, что и в
 * api/channel_api.php::_ch_resolve() — там резолвятся ЧУЖИЕ каналы, здесь тем
 * же способом достаём id, когда авторизованный resolve_url(/profile) выше
 * не смог его вернуть).
 */
function yt_channel_id_by_handle(string $handle): string {
    if ($handle === '' || $handle[0] !== '@') {
        error_log('[yt-auth] channel_id_by_handle: не похоже на @handle: "' . $handle . '"');
        return '';
    }

    $base = _yt_innertube_const('INNERTUBE_BASE_URL',   'https://www.youtube.com/youtubei/v1/');
    $key  = _yt_innertube_const('INNERTUBE_API_KEY',    'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8');
    $ver  = _yt_innertube_const('INNERTUBE_CLIENT_VER', '2.20260709.01.00');

    $ch = curl_init($base . 'navigation/resolve_url?key=' . $key . '&prettyPrint=false');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode([
            'context' => ['client' => ['clientName' => 'WEB', 'clientVersion' => $ver, 'hl' => 'en', 'gl' => 'US']],
            'url'     => 'https://www.youtube.com/' . $handle,
        ], JSON_UNESCAPED_SLASHES),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ]);
    yt_curl_ssl_opts($ch);
    yt_curl_proxy_opts($ch);
    $res  = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    if ($res === false || $code !== 200) {
        error_log('[yt-auth] resolve_url(' . $handle . ') failed: HTTP ' . $code
            . ' ' . substr((string)$res, 0, 200));
        return '';
    }
    $id = json_decode($res, true)['endpoint']['browseEndpoint']['browseId'] ?? '';
    if (!is_string($id) || !str_starts_with($id, 'UC')) {
        error_log('[yt-auth] resolve_url(' . $handle . '): нет browseEndpoint, ответ: ' . substr((string)$res, 0, 300));
        return '';
    }
    return $id;
}

/**
 * use($yt): данные текущего аккаунта, либо null, если SAPISID нет или ответ
 * не удалось разобрать. Один сетевой запрос на страницу (статический кэш).
 */
function yt_account_info(): ?array {
    static $cached = false;
    if ($cached !== false) return $cached;

    // Единственный критерий входа — куки YouTube (через Fiddler), как в Rehike.
    if (!yt_should_auth()) return $cached = null;

    $raw = yt_first_party_request(YT_AUTH_ORIGIN . '/getAccountSwitcherEndpoint');
    if ($raw === null) return $cached = null;

    $acct = yt_parse_switcher_response($raw);
    if ($acct === null) return $cached = null;

    $acct['channelId'] = yt_own_channel_id_cached($acct);

    return $cached = $acct;
}

/**
 * UC-идентификатор собственного канала для куки-фолбэка, с файловым кэшем
 * (ключ — hash SAPISID, чтобы у разных аккаунтов были разные записи).
 * Порядок: кэш → channelId прямо из ответа свитчера (offlineCacheKeyToken,
 * см. _yt_switcher_account_item) → авторизованный resolve_url(/profile) →
 * @handle (channelHandle из свитчера, НЕ accountByline — тот часто оказывается
 * счётчиком подписчиков вроде «141 подписчик», а не хэндлом).
 */
function yt_own_channel_id_cached(array $acct): string {
    $cacheFile = sys_get_temp_dir() . '/yt_ucid_' . sha1((string)($_COOKIE['SAPISID'] ?? ''));
    if (is_file($cacheFile) && filemtime($cacheFile) > time() - 21600) {   // 6 часов
        $id = trim((string)@file_get_contents($cacheFile));
        if (str_starts_with($id, 'UC')) return $id;
    }

    // 1) Прямо из ответа свитчера — без сетевого запроса, самый надёжный путь.
    $id = (string)($acct['channelId'] ?? '');

    // 2) Фолбэк — авторизованный resolve_url(/profile) (у части аккаунтов
    // почему-то не отдаёт browseEndpoint даже при валидных куках).
    if ($id === '' && !empty($acct['hasChannel'])) {
        $id = yt_resolve_own_channel_id(yt_auth_headers());
    }

    // 3) Последний фолбэк — @handle через InnerTube.
    if ($id === '') {
        $id = yt_channel_id_by_handle((string)($acct['handle'] ?? ''));
    }

    if ($id === '') {
        error_log('[yt-auth] channelId не резолвится ни одним из путей '
            . '(hasChannel=' . (!empty($acct['hasChannel']) ? '1' : '0')
            . ', handle="' . ($acct['handle'] ?? '') . '")');
    }

    if ($id !== '') @file_put_contents($cacheFile, $id);
    return $id;
};

if (!function_exists('yt_is_subscribed')) {

/**
 * Статус подписки на $targetChannelId.
 * $myChannelId можно пустым (нужен только для self-check).
 * Пишет короткий лог в %TEMP%/yt_sub_debug.log
 */
function yt_is_subscribed(string $myChannelId, string $targetChannelId): bool {
    $targetChannelId = trim($targetChannelId);
    if ($targetChannelId === '' || !preg_match('/^UC[A-Za-z0-9_-]{22}$/', $targetChannelId)) {
        return false;
    }

    if ($myChannelId === '' && function_exists('yt_account_info')) {
        $acct = yt_account_info();
        if (is_array($acct)) {
            $myChannelId = (string)($acct['channelId'] ?? $acct['channel_id'] ?? '');
        }
    }
    if ($myChannelId !== '' && $myChannelId === $targetChannelId) {
        return false;
    }

    if (!function_exists('yt_should_auth') || !yt_should_auth()) {
        _yt_sub_debug("no auth / no SAPISID for $targetChannelId");
        return false;
    }

    $sapisid  = (string)($_COOKIE['SAPISID'] ?? '');
    $cacheKey = sys_get_temp_dir() . '/yt_sub_v4_' . sha1($sapisid . '|' . $targetChannelId);
    if (is_file($cacheKey) && (time() - filemtime($cacheKey)) < 600) {
        $v = @file_get_contents($cacheKey);
        if ($v === '1') return true;
        if ($v === '0') return false;
    }

    $result = _yt_fetch_subscribed_status($targetChannelId);
    _yt_sub_debug("fetch $targetChannelId => " . var_export($result, true));
    if ($result === null) {
        // сетевая/парсинг ошибка — не кэшируем
        return false;
    }
    @file_put_contents($cacheKey, $result ? '1' : '0');
    return $result;
}

function _yt_sub_debug(string $msg): void {
    $f = sys_get_temp_dir() . '/yt_sub_debug.log';
    @file_put_contents($f, date('H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
}

/**
 * Авторизованный browse. true/false/null(ошибка).
 */
function _yt_fetch_subscribed_status(string $channelId): ?bool {
    if (!function_exists('yt_auth_headers')) return null;

    $auth = yt_auth_headers();
    if ($auth === []) return null;

    $base = function_exists('_yt_innertube_const')
        ? _yt_innertube_const('INNERTUBE_BASE_URL', 'https://www.youtube.com/youtubei/v1/')
        : (defined('INNERTUBE_BASE_URL') ? INNERTUBE_BASE_URL : 'https://www.youtube.com/youtubei/v1/');
    $key  = function_exists('_yt_innertube_const')
        ? _yt_innertube_const('INNERTUBE_API_KEY', 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8')
        : (defined('INNERTUBE_API_KEY') ? INNERTUBE_API_KEY : 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8');
    $ver  = function_exists('_yt_innertube_const')
        ? _yt_innertube_const('INNERTUBE_CLIENT_VER', '2.20260709.01.00')
        : (defined('INNERTUBE_CLIENT_VER') ? INNERTUBE_CLIENT_VER : '2.20260709.01.00');

    $cookie = function_exists('yt_current_request_cookie') ? yt_current_request_cookie() : '';
    $payload = json_encode([
        'context'  => [
            'client' => [
                'clientName'    => 'WEB',
                'clientVersion' => $ver,
                'hl'            => 'en',
                'gl'            => 'US',
            ],
        ],
        'browseId' => $channelId,
    ], JSON_UNESCAPED_SLASHES);

    $headers = array_merge($auth, [
        'Content-Type: application/json',
        'X-Origin: https://www.youtube.com',
        'X-Goog-AuthUser: 0',
        'X-YouTube-Client-Name: 1',
        'X-YouTube-Client-Version: ' . $ver,
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
    ]);
    // Cookie уже в $auth, но на всякий случай продублируем через CURLOPT_COOKIE
    $ch = curl_init($base . 'browse?key=' . $key . '&prettyPrint=false');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 14,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_COOKIE         => rtrim($cookie, '; '),
    ]);
    if (function_exists('yt_curl_ssl_opts'))   yt_curl_ssl_opts($ch);
    if (function_exists('yt_curl_proxy_opts')) yt_curl_proxy_opts($ch);

    $res  = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $cerr = curl_error($ch);
    if ($res === false || $code !== 200) {
        _yt_sub_debug("browse HTTP $code curl=$cerr body=" . substr((string)$res, 0, 200));
        return null;
    }

    $data = json_decode($res, true);
    if (!is_array($data)) {
        _yt_sub_debug('browse JSON decode fail');
        return null;
    }

    $found = _yt_extract_subscribed_flag($data);
    if ($found === null) {
        // сохраним кусок ответа для отладки (один раз)
        $dump = sys_get_temp_dir() . '/yt_sub_last_browse.json';
        @file_put_contents($dump, json_encode([
            'channelId' => $channelId,
            'hasFrameworkUpdates' => isset($data['frameworkUpdates']),
            'headerKeys' => array_keys($data['header'] ?? []),
            'topKeys' => array_keys($data),
            'mutations_sample' => array_slice($data['frameworkUpdates']['entityBatchUpdate']['mutations'] ?? [], 0, 3),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        _yt_sub_debug("flag not found, dump=$dump");
    }
    return $found;
}

/**
 * Только кнопка ЭТОГО канала (header / pageHeader), не рекомендованные.
 * true/false если нашли, null если нет.
 */
function _yt_extract_subscribed_flag(array $data): ?bool {
    $mutations = $data['frameworkUpdates']['entityBatchUpdate']['mutations'] ?? [];

    // Индекс entityKey → subscribed
    $byKey = [];
    foreach ($mutations as $mut) {
        $ent = $mut['payload']['subscriptionStateEntity'] ?? null;
        if (!is_array($ent) || !array_key_exists('subscribed', $ent)) continue;
        $ek = (string)($mut['entityKey'] ?? '');
        if ($ek !== '') $byKey[$ek] = (bool)$ent['subscribed'];
        // иногда key лежит внутри entity
        $ik = (string)($ent['key'] ?? '');
        if ($ik !== '') $byKey[$ik] = (bool)$ent['subscribed'];
    }

    // ── 1) Header: c4TabbedHeaderRenderer (старый) ───────────────────────────
    $hdr = $data['header']['c4TabbedHeaderRenderer'] ?? null;
    if (is_array($hdr)) {
        $btn = $hdr['subscribeButton']['subscribeButtonRenderer'] ?? null;
        if (is_array($btn) && array_key_exists('subscribed', $btn)) {
            return (bool)$btn['subscribed'];
        }
    }

    // ── 2) pageHeaderViewModel → subscribeButtonViewModel ────────────────────
    $vm = _yt_find_renderer_by_name($data, 'subscribeButtonViewModel', 0, 12);
    if (is_array($vm)) {
        // stateEntityStoreKey → mutations
        $sk = (string)($vm['stateEntityStoreKey'] ?? '');
        if ($sk !== '' && array_key_exists($sk, $byKey)) {
            return $byKey[$sk];
        }
        // inline subscribeState
        foreach ([
            $vm['subscribeState']['subscribed'] ?? null,
            $vm['subscribeButtonContent']['subscribeState']['subscribed'] ?? null,
            $vm['unsubscribeButtonContent']['subscribeState']['subscribed'] ?? null,
        ] as $s) {
            if ($s !== null) return (bool)$s;
        }
        // key внутри subscribeState
        $k2 = (string)($vm['subscribeButtonContent']['subscribeState']['key'] ?? $vm['subscribeState']['key'] ?? '');
        if ($k2 !== '' && array_key_exists($k2, $byKey)) {
            return $byKey[$k2];
        }
    }

    // ── 3) Если в mutations ровно одна subscriptionStateEntity — берём её ───
    if (count($byKey) === 1) {
        return (bool)reset($byKey);
    }

    // ── 4) Старый subscribeButtonRenderer только в header-ветке ──────────────
    if (is_array($hdr)) {
        $found = _yt_find_sub_limited($hdr, 0);
        if ($found !== null) return $found;
    }
    // pageHeader ветка
    $ph = $data['header'] ?? $data['contents'] ?? null;
    if (is_array($ph)) {
        $found = _yt_find_sub_limited($ph, 0);
        if ($found !== null) return $found;
    }

    return null;
}

/** Поиск renderer по имени ключа, ограниченная глубина */
function _yt_find_renderer_by_name(array $node, string $name, int $depth, int $maxDepth) {
    if ($depth > $maxDepth) return null;
    if (isset($node[$name]) && is_array($node[$name])) return $node[$name];
    foreach ($node as $v) {
        if (!is_array($v)) continue;
        $r = _yt_find_renderer_by_name($v, $name, $depth + 1, $maxDepth);
        if ($r !== null) return $r;
    }
    return null;
}

/** Ищем subscribed только в узлах кнопки, без обхода всего JSON */
function _yt_find_sub_limited(array $node, int $depth): ?bool {
    if ($depth > 14) return null;
    if (isset($node['subscribeButtonRenderer']) && is_array($node['subscribeButtonRenderer'])) {
        $b = $node['subscribeButtonRenderer'];
        if (array_key_exists('subscribed', $b)) return (bool)$b['subscribed'];
    }
    if (isset($node['subscribeButtonViewModel']) && is_array($node['subscribeButtonViewModel'])) {
        $vm = $node['subscribeButtonViewModel'];
        foreach ([
            $vm['subscribeState']['subscribed'] ?? null,
            $vm['subscribeButtonContent']['subscribeState']['subscribed'] ?? null,
            $vm['unsubscribeButtonContent']['subscribeState']['subscribed'] ?? null,
        ] as $s) {
            if ($s !== null) return (bool)$s;
        }
    }
    foreach ($node as $v) {
        if (!is_array($v)) continue;
        $r = _yt_find_sub_limited($v, $depth + 1);
        if ($r !== null) return $r;
    }
    return null;
}

} // end if !function_exists
