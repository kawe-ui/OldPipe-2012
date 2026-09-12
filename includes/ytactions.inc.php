<?php
// Authorized YouTube actions and their CSRF protection.
require_once __DIR__ . '/config.inc.php';
require_once __DIR__ . '/auth.inc.php';

/*
 * A CSRF token is random, server-side and session-bound. It is intentionally
 * independent of the YouTube cookies and all API secrets.
 */
function yt_session_token(): string {
    $token = $_SESSION['csrf_token'] ?? null;
    if (is_string($token) && $token !== '') return $token;

    if (!yt_session_start()) return '';
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
    return $_SESSION['csrf_token'];
}

/** Reject requests that explicitly declare a foreign Origin. */
function yt_request_has_same_origin(): bool {
    $origin = trim((string)($_SERVER['HTTP_ORIGIN'] ?? ''));
    if ($origin === '') return true; // Older same-origin form posts can omit Origin.

    $originHost = strtolower((string)parse_url($origin, PHP_URL_HOST));
    if ($originHost === '') return false;

    // Compare against the host the browser actually addressed for THIS request,
    // not a fixed APP_URL. The site is legitimately reached under several hosts
    // — localhost, 127.0.0.1, and www.youtube.com via the Fiddler path — and a
    // browser cannot be tricked into sending a victim's Host with a foreign
    // Origin, so a cross-site attacker's Origin host still never matches. The
    // port and scheme are intentionally ignored: the Fiddler/reverse-proxy hop
    // terminates TLS and rewrites them, which a strict check wrongly rejected.
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    if ($host === '') return false;
    $requestHost = (string)parse_url('//' . $host, PHP_URL_HOST);

    return $requestHost !== '' && $originHost === $requestHost;
}

function yt_session_token_valid(?string $token): bool {
    $started  = yt_session_start();
    $expected = $started ? ($_SESSION['csrf_token'] ?? null) : null;
    $match    = is_string($expected) && is_string($token) && hash_equals($expected, $token);

    if ($match) {
        // A valid session-bound token is itself proof of same origin: an off-site
        // page is blocked by the same-origin policy from ever reading it, so it
        // cannot forge this value. The Origin/Host check is therefore redundant
        // here and must NOT reject — reverse proxies and the Fiddler path
        // legitimately rewrite Host (browser Origin=www.youtube.com, server
        // Host=localhost), which a hard check wrongly dropped. Just record it.
        if (!yt_request_has_same_origin()) {
            error_log('[csrf] token valid but Origin/Host differ (allowed): origin='
                . ($_SERVER['HTTP_ORIGIN'] ?? '-') . ' host=' . ($_SERVER['HTTP_HOST'] ?? '-'));
        }
        return true;
    }

    // Diagnostics on rejection — lengths only, never the token values themselves.
    error_log(sprintf('[csrf] reject: started=%s recvLen=%s expLen=%s origin=%s host=%s',
        $started ? '1' : '0',
        is_string($token) ? (string)strlen($token) : 'null',
        is_string($expected) ? (string)strlen($expected) : 'null',
        $_SERVER['HTTP_ORIGIN'] ?? '-',
        $_SERVER['HTTP_HOST'] ?? '-'));
    return false;
}

function yt_valid_video_id(string $videoId): bool {
    return (bool)preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId);
}

function yt_valid_channel_id(string $channelId): bool {
    return (bool)preg_match('/^UC[A-Za-z0-9_-]{22}$/', $channelId);
}

/** POST to an authenticated InnerTube endpoint; null on network/non-2xx failure. */
function yt_innertube_authed(string $endpoint, array $payload): ?array {
    if (!yt_should_auth()) return null;

    $base = _yt_innertube_const('INNERTUBE_BASE_URL', 'https://www.youtube.com/youtubei/v1/');
    $key  = _yt_innertube_const('INNERTUBE_API_KEY', 'AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8');
    $ver  = _yt_innertube_const('INNERTUBE_CLIENT_VER', '2.20260709.01.00');

    $payload['context'] = $payload['context'] ?? [
        'client' => ['clientName' => 'WEB', 'clientVersion' => $ver, 'hl' => 'en', 'gl' => 'US'],
    ];

    $ch = curl_init($base . $endpoint . '?key=' . rawurlencode($key) . '&prettyPrint=false');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_HTTPHEADER     => array_merge(yt_auth_headers(), [
            'Content-Type: application/json',
            'X-Origin: ' . YT_AUTH_ORIGIN,
            'X-Goog-AuthUser: 0',
            'X-YouTube-Client-Name: 1',
            'X-YouTube-Client-Version: ' . $ver,
        ]),
    ]);
    yt_curl_ssl_opts($ch);
    yt_curl_proxy_opts($ch);
    $res = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($res === false || $code < 200 || $code >= 300) {
        error_log('[yt-actions] InnerTube ' . $endpoint . ' failed: HTTP ' . $code);
        return null;
    }
    $decoded = json_decode((string)$res, true);
    return is_array($decoded) ? $decoded : [];
}

/** $rating: like | dislike | none */
function yt_action_rate(string $videoId, string $rating): bool {
    $endpoint = [
        'like' => 'like/like',
        'dislike' => 'like/dislike',
        'none' => 'like/removelike',
    ][$rating] ?? null;
    if ($endpoint === null || !yt_valid_video_id($videoId)) return false;

    return yt_innertube_authed($endpoint, ['target' => ['videoId' => $videoId]]) !== null;
}

function yt_action_subscribe(string $channelId): bool {
    if (!yt_valid_channel_id($channelId)) return false;
    return yt_innertube_authed('subscription/subscribe', [
        'channelIds' => [$channelId],
        'params' => 'EgIIAhgA',
    ]) !== null;
}

function yt_action_unsubscribe(string $channelId): bool {
    if (!yt_valid_channel_id($channelId)) return false;
    return yt_innertube_authed('subscription/unsubscribe', ['channelIds' => [$channelId]]) !== null;
}

function yt_action_is_subscribed(string $channelId): bool {
    if (!yt_valid_channel_id($channelId)) return false;

    $response = yt_innertube_authed('next', ['videoId' => $GLOBALS['video_id'] ?? '']);
    $found = null;
    $walk = function (mixed $node) use (&$walk, &$found): void {
        if ($found !== null || !is_array($node)) return;
        if (isset($node['subscribeButtonRenderer']['subscribed'])) {
            $found = (bool)$node['subscribeButtonRenderer']['subscribed'];
            return;
        }
        foreach ($node as $value) $walk($value);
    };
    if ($response !== null) $walk($response);
    return $found ?? false;
}

function _yt_tree_find(mixed $node, string $key): ?string {
    if (!is_array($node)) return null;
    if (isset($node[$key]) && is_string($node[$key])) return $node[$key];
    foreach ($node as $value) {
        $found = _yt_tree_find($value, $key);
        if ($found !== null) return $found;
    }
    return null;
}

/** Страница комментариев видео (continuation-батч секции comment-item-section) */
function _yt_comment_batch(string $videoId): ?array {
    $next = yt_innertube_authed('next', ['videoId' => $videoId]);
    $continuation = null;
    $walk = function (mixed $node) use (&$walk, &$continuation): void {
        if ($continuation !== null || !is_array($node)) return;
        if (($node['sectionIdentifier'] ?? '') === 'comment-item-section') {
            $continuation = _yt_tree_find($node, 'token');
            if ($continuation !== null) return;
        }
        foreach ($node as $value) $walk($value);
    };
    if ($next !== null) $walk($next);

    return $continuation !== null ? yt_innertube_authed('next', ['continuation' => $continuation]) : null;
}

/**
 * createReplyParams, привязанный к конкретному родительскому комментарию:
 * поднимаемся от точного вхождения $parentId к первому предку, в чьём поддереве
 * есть и сам id, и ключ createReplyParams, — так параметры чужого комментария
 * (соседнего в списке) не подхватятся.
 */
function _yt_scoped_reply_params(mixed $node, string $parentId, bool &$contains): ?string {
    $contains = false;
    if (!is_array($node)) {
        $contains = is_string($node) && str_contains($node, $parentId);
        return null;
    }
    $childContains = false;
    foreach ($node as $value) {
        $c = false;
        $found = _yt_scoped_reply_params($value, $parentId, $c);
        if ($found !== null) { $contains = true; return $found; }
        $childContains = $childContains || $c;
    }
    $contains = $childContains;
    if ($childContains) {
        $params = _yt_tree_find($node, 'createReplyParams');
        if ($params !== null) return $params;
    }
    return null;
}

/** Create a top-level comment or reply. Returns null on success. */
function yt_action_comment(string $videoId, string $text, string $parentId = ''): ?string {
    $text = trim($text);
    if (!yt_valid_video_id($videoId) || $text === '' || mb_strlen($text) > 500) return 'invalid_input';
    if (strlen($parentId) > 128 || !preg_match('/^[A-Za-z0-9._-]*$/', $parentId)) return 'invalid_parent';
    if (!yt_should_auth()) return 'not_signed_in';

    if ($parentId !== '') {
        $batch = _yt_comment_batch($videoId);
        $c = false;
        $params = $batch !== null ? _yt_scoped_reply_params($batch, $parentId, $c) : null;
        if ($params === null) {
            error_log('[yt-actions] reply: createReplyParams for ' . $parentId . ' not found');
            return 'no_reply_params';
        }
        $result = yt_innertube_authed('comment/create_comment_reply', [
            'commentText' => $text,
            'createReplyParams' => $params,
        ]);
        return $result !== null ? null : 'innertube_failed';
    }

    $batch  = _yt_comment_batch($videoId);
    $params = $batch !== null ? _yt_tree_find($batch, 'createCommentParams') : null;
    if ($params === null) return 'no_params';

    $result = yt_innertube_authed('comment/create_comment', [
        'commentText' => $text,
        'createCommentParams' => $params,
    ]);
    return $result !== null ? null : 'innertube_failed';
}