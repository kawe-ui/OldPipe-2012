<?php
/**
 * guide_ajax.php — ленты гайда 2012
 * Auth: auth.inc.php + ytactions.inc.php (SAPISID → yt_innertube_authed), как Rehike.
 */
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

$ytAccount = null;
$ytAccessToken = '';
$ytLoggedIn = false;
$oldera = true;
$__ga_done = false;

register_shutdown_function(static function () {
    if (!empty($GLOBALS['__ga_done'])) return;
    $err = error_get_last();
    if ($err === null) return;
    if (!in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) return;
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);
    }
    echo json_encode([
        'feed_html' => '<div class="feed-header no-metadata"><div class="feed-header-details"><h2>From YouTube</h2></div></div>'
            . '<div class="feed-container"><div class="feed-page"><p class="feed-message">'
            . 'We were unable to complete the request, please try again later.</p></div></div>',
        'error' => $err['message'] ?? 'fatal',
        'file'  => isset($err['file']) ? basename((string)$err['file']) : '',
        'line'  => $err['line'] ?? 0,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
});

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php';
    if (is_file($_SERVER['DOCUMENT_ROOT'] . '/includes/config.inc.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config.inc.php';
    }
    if (is_file($_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php';
    }
    if (is_file($_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/ytactions.inc.php';
    }

    if (function_exists('yt_account_info')) {
        try { $ytAccount = yt_account_info(); } catch (Throwable $e) { $ytAccount = null; }
    }
    if (function_exists('yt_google_access_token')) {
        try { $ytAccessToken = (string)yt_google_access_token(); } catch (Throwable $e) { $ytAccessToken = ''; }
    }
    $ytLoggedIn = ($ytAccount !== null)
        || (function_exists('yt_should_auth') && yt_should_auth());

} catch (Throwable $e) {
    error_log('guide_ajax bootstrap: ' . $e->getMessage());
}

if (!function_exists('default_avatar')) {
    function default_avatar(?string $url): string {
        $url = trim((string)$url);
        if ($url === '') return '/dynamic/pfp/default.png';
        if (strpos($url, '/dynamic/pfp/') === 0) return $url;
        if (preg_match('#^https?://#i', $url)) {
            return '/dynamic/pfp/pfp.php?u=' . rawurlencode($url);
        }
        if (strpos($url, '//') === 0) {
            return '/dynamic/pfp/pfp.php?u=' . rawurlencode('https:' . $url);
        }
        return $url;
    }
}

if (!function_exists('expand_count')) {
    function expand_count($raw): string {
        if ($raw === null || $raw === '') return '';
        if (is_numeric($raw)) return ((int)$raw > 0) ? (string)(int)$raw : '';
        $raw = trim((string)$raw);
        if (preg_match('/([\d.,]+)\s*([KMB])/iu', $raw, $m)) {
            $mult = ['K' => 1000, 'M' => 1000000, 'B' => 1000000000][strtoupper($m[2])];
            $n = (int)round((float)str_replace(',', '', $m[1]) * $mult);
            return $n > 0 ? (string)$n : '';
        }
        $digits = preg_replace('/[^\d]/', '', $raw);
        return ($digits !== '' && (int)$digits > 0) ? (string)(int)$digits : '';
    }
}

if (!function_exists('format_view_count')) {
    function format_view_count($raw): string {
        $n = expand_count($raw);
        if ($n === '' || $n === '0') return '';
        $count = (int)$n;
        if ($count === 1) return '1 view';
        return number_format($count, 0, '.', ',') . ' views';
    }
}

if (!function_exists('runs_to_text')) {
    function runs_to_text(array $runs): string {
        $t = '';
        foreach ($runs as $r) $t .= $r['text'] ?? '';
        return $t;
    }
}

if (!function_exists('best_thumb')) {
    function best_thumb(array $thumbs, int $minWidth = 0): string {
        if ($thumbs === []) return '';
        usort($thumbs, function ($a, $b) {
            return (int)($b['width'] ?? 0) - (int)($a['width'] ?? 0);
        });
        foreach ($thumbs as $t) {
            if ((int)($t['width'] ?? 0) >= $minWidth && !empty($t['url'])) return $t['url'];
        }
        return $thumbs[0]['url'] ?? '';
    }
}

try {

$req = array_merge($_GET, $_POST);

foreach (['action_dismiss_video', 'action_dismiss_channel', 'action_dismiss_item'] as $a) {
    if (isset($req[$a])) {
        $__ga_done = true;
        $GLOBALS['__ga_done'] = true;
        echo json_encode(['success' => 1]);
        exit;
    }
}

function _ga_parse_video(array $vr): ?array {
    $id = $vr['videoId'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) return null;

    foreach ($vr['thumbnailOverlays'] ?? [] as $ov) {
        $style = $ov['thumbnailOverlayTimeStatusRenderer']['style'] ?? '';
        if (in_array($style, ['LIVE', 'SHORTS'], true)) return null;
    }

    $title = $vr['title']['simpleText'] ?? '';
    if ($title === '' && !empty($vr['title']['runs'])) $title = runs_to_text($vr['title']['runs']);
    if (trim($title) === '') return null;

    $author = ''; $authorId = '';
    foreach (['ownerText', 'shortBylineText', 'longBylineText'] as $f) {
        if (!empty($vr[$f]['runs'][0])) {
            $author   = $vr[$f]['runs'][0]['text'] ?? '';
            $authorId = $vr[$f]['runs'][0]['navigationEndpoint']['browseEndpoint']['browseId'] ?? '';
            break;
        }
    }

    $avatar = best_thumb($vr['channelThumbnailSupportedRenderers']['channelThumbnailWithLinkRenderer']['thumbnail']['thumbnails']
        ?? ($vr['channelThumbnail']['thumbnails'] ?? []));

    $thumb = best_thumb($vr['thumbnail']['thumbnails'] ?? []);
    if ($thumb === '') $thumb = "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";

    $duration = $vr['lengthText']['simpleText'] ?? '';
    if ($duration === '') {
        foreach ($vr['thumbnailOverlays'] ?? [] as $ov) {
            $duration = $ov['thumbnailOverlayTimeStatusRenderer']['text']['simpleText'] ?? '';
            if ($duration !== '') break;
        }
    }

    $vcRuns = '';
    if (!empty($vr['viewCountText']['runs'])) {
        foreach ($vr['viewCountText']['runs'] as $r) $vcRuns .= $r['text'] ?? '';
    }
    $viewsRaw = $vr['viewCountText']['accessibility']['accessibilityData']['label']
        ?? ($vr['viewCountText']['simpleText']
        ?? ($vr['shortViewCountText']['simpleText'] ?? $vcRuns));
    $views = $viewsRaw !== '' ? expand_count($viewsRaw) : '';

    $desc = '';
    if (!empty($vr['descriptionSnippet']['runs'])) $desc = runs_to_text($vr['descriptionSnippet']['runs']);
    elseif (!empty($vr['detailedMetadataSnippets'][0]['snippetText']['runs'])) {
        $desc = runs_to_text($vr['detailedMetadataSnippets'][0]['snippetText']['runs']);
    }
    if (function_exists('mb_strlen') && mb_strlen($desc) > 120) {
        $desc = mb_substr($desc, 0, 117) . '...';
    } elseif (strlen($desc) > 120) {
        $desc = substr($desc, 0, 117) . '...';
    }

    return [
        'id' => $id, 'title' => $title, 'author' => $author, 'authorId' => $authorId,
        'authorAvatar' => $avatar, 'thumbnail' => $thumb, 'duration' => $duration,
        'views' => $views, 'publishedAgo' => $vr['publishedTimeText']['simpleText'] ?? '',
        'description' => $desc,
    ];
}

function _ga_parse_lockup(array $lv): ?array {
    if (($lv['contentType'] ?? '') !== 'LOCKUP_CONTENT_TYPE_VIDEO') return null;
    $id = $lv['contentId'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) return null;

    $md    = $lv['metadata']['lockupMetadataViewModel'] ?? [];
    $title = trim($md['title']['content'] ?? '');
    if ($title === '') return null;

    $author = ''; $authorId = ''; $views = ''; $ago = '';
    foreach (($md['metadata']['contentMetadataViewModel']['metadataRows'] ?? []) as $row) {
        foreach ($row['metadataParts'] ?? [] as $part) {
            $txt = trim((string)($part['text']['content'] ?? ''));
            if ($txt === '' && !empty($part['text']['runs'])) {
                foreach ($part['text']['runs'] as $r) $txt .= $r['text'] ?? '';
                $txt = trim($txt);
            }
            if ($txt === '') continue;
            if (stripos($txt, 'view') !== false || stripos($txt, 'watching') !== false) {
                if ($views === '') $views = expand_count($txt);
            } elseif (preg_match('/\bago$|^Streamed|^Scheduled|^Premiere/i', $txt)) {
                if ($ago === '') $ago = preg_replace('/^Streamed\s+/i', '', $txt);
            } elseif ($author === '') {
                $author   = $txt;
                $authorId = $part['text']['commandRuns'][0]['onTap']['innertubeCommand']['browseEndpoint']['browseId'] ?? '';
            }
        }
    }

    // Avatar канала из lockup (разные варианты разметки 2024–2026)
    $avatar = '';
    $imgCandidates = [
        $md['image']['decoratedAvatarViewModel']['avatar']['avatarViewModel']['image']['sources'] ?? null,
        $md['image']['avatarViewModel']['image']['sources'] ?? null,
        $md['image']['sources'] ?? null,
        $lv['metadata']['image']['decoratedAvatarViewModel']['avatar']['avatarViewModel']['image']['sources'] ?? null,
    ];
    foreach ($imgCandidates as $sources) {
        if (is_array($sources) && $sources !== []) {
            $avatar = best_thumb($sources);
            if ($avatar !== '') break;
        }
    }
    // Иногда аватар лежит в contentMetadata / leading image
    if ($avatar === '') {
        $avatar = best_thumb($md['image']['decoratedAvatarViewModel']['avatar']['avatarViewModel']['image']['sources'] ?? []);
    }

    $thumb = "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";
    $tvm   = $lv['contentImage']['thumbnailViewModel'] ?? [];
    $best  = best_thumb($tvm['image']['sources'] ?? []);
    if ($best !== '') $thumb = $best;

    $duration = '';
    foreach ($tvm['overlays'] ?? [] as $ov) {
        foreach ($ov['thumbnailBottomOverlayViewModel']['badges'] ?? [] as $b) {
            $t = $b['thumbnailBadgeViewModel']['text'] ?? '';
            if (preg_match('/^\d+:\d{2}(:\d{2})?$/', $t)) { $duration = $t; break 2; }
        }
    }

    return [
        'id' => $id, 'title' => $title, 'author' => $author, 'authorId' => $authorId,
        'authorAvatar' => $avatar, 'thumbnail' => $thumb, 'duration' => $duration,
        'views' => $views, 'publishedAgo' => $ago, 'description' => '',
    ];
}

function _ga_collect(array $node, array &$out, int $depth = 0): void {
    if ($depth > 30 || count($out) >= 40) return;
    foreach ($node as $key => $value) {
        if (!is_array($value)) continue;
        switch ($key) {
            case 'videoRenderer':
            case 'gridVideoRenderer':
                $p = _ga_parse_video($value);
                if ($p !== null) $out[] = $p;
                break;
            case 'richItemRenderer':
                $inner = $value['content'] ?? [];
                if (!empty($inner['videoRenderer'])) {
                    $p = _ga_parse_video($inner['videoRenderer']);
                    if ($p !== null) $out[] = $p;
                } else {
                    _ga_collect($inner, $out, $depth + 1);
                }
                break;
            case 'lockupViewModel':
                $p = _ga_parse_lockup($value);
                if ($p !== null) $out[] = $p;
                break;
            default:
                _ga_collect($value, $out, $depth + 1);
        }
    }
}

/** Есть ли SAPISID (Rehike-style) */
function _ga_can_auth(): bool {
    if (function_exists('yt_should_auth') && yt_should_auth()) return true;
    return !empty($_COOKIE['SAPISID']) || !empty($_COOKIE['__Secure-3PAPISID']);
}

function _ga_videos_from_browse(string $browseId, string $params = ''): array {
    $out = [];
    try {
        if (!function_exists('innertube_post')) return $out;
        $payload = ['browseId' => $browseId];
        if ($params !== '') $payload['params'] = $params;
        $raw = innertube_post('browse', $payload);
        if ($raw !== null) _ga_collect($raw, $out);
    } catch (Throwable $e) {
        error_log('guide_ajax browse: ' . $e->getMessage());
    }
    return $out;
}

/**
 * Авторизованный browse: yt_innertube_authed (SAPISID из $_COOKIE).
 * Fallback — обычный innertube_post.
 */
function _ga_videos_from_browse_auth(string $browseId, string $params = ''): array {
    $out = [];
    $payload = ['browseId' => $browseId];
    if ($params !== '') $payload['params'] = $params;
    try {
        $raw = null;
        if (function_exists('yt_innertube_authed') && _ga_can_auth()) {
            $raw = yt_innertube_authed('browse', $payload);
        }
        if ($raw === null && function_exists('innertube_post')) {
            $raw = innertube_post('browse', $payload);
        }
        if ($raw !== null) _ga_collect($raw, $out);
    } catch (Throwable $e) {
        error_log('guide_ajax browse_auth ' . $browseId . ': ' . $e->getMessage());
    }
    return $out;
}

/**
 * Кэш аватарок каналов (файл, 24ч) + дозаполнение пустых authorAvatar по authorId.
 * Нужно для FEsubscriptions / likes / history и т.п., где InnerTube часто не кладёт аватар.
 */
function _ga_avatar_cache_get(string $channelId): string {
    static $mem = [];
    if (isset($mem[$channelId])) return $mem[$channelId];
    $dir = defined('CACHE_DIR') ? CACHE_DIR : (sys_get_temp_dir() . '/yt_cache');
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $f = $dir . '/ch_avatar_' . sha1($channelId) . '.txt';
    if (is_file($f) && (time() - filemtime($f)) < 86400) {
        $u = trim((string)@file_get_contents($f));
        if ($u !== '') return $mem[$channelId] = $u;
    }
    return '';
}

function _ga_avatar_cache_set(string $channelId, string $url): void {
    if ($channelId === '' || $url === '') return;
    $dir = defined('CACHE_DIR') ? CACHE_DIR : (sys_get_temp_dir() . '/yt_cache');
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    @file_put_contents($dir . '/ch_avatar_' . sha1($channelId) . '.txt', $url);
}

function _ga_fetch_channel_avatar(string $channelId): string {
    $cached = _ga_avatar_cache_get($channelId);
    if ($cached !== '') return $cached;
    if (!function_exists('innertube_post')) return '';
    try {
        $raw = null;
        if (function_exists('yt_innertube_authed') && _ga_can_auth()) {
            $raw = yt_innertube_authed('browse', ['browseId' => $channelId]);
        }
        if ($raw === null) {
            $raw = innertube_post('browse', ['browseId' => $channelId]);
        }
        if (!is_array($raw)) return '';
        $url = best_thumb($raw['metadata']['channelMetadataRenderer']['avatar']['thumbnails'] ?? []);
        if ($url === '') {
            // pageHeaderViewModel
            $ph = null;
            $stack = [$raw];
            $d = 0;
            while ($stack && $d < 40) {
                $n = array_pop($stack); $d++;
                if (!is_array($n)) continue;
                if (isset($n['pageHeaderViewModel'])) { $ph = $n['pageHeaderViewModel']; break; }
                foreach ($n as $v) if (is_array($v)) $stack[] = $v;
            }
            if (is_array($ph)) {
                $url = best_thumb($ph['image']['sources'] ?? ($ph['avatar']['thumbnails'] ?? []));
            }
        }
        if ($url !== '') {
            _ga_avatar_cache_set($channelId, $url);
            return $url;
        }
    } catch (Throwable $e) {
        error_log('guide_ajax avatar: ' . $e->getMessage());
    }
    return '';
}

/** Дозаполнить authorAvatar для видео без аватарки (макс. $limit уникальных каналов) */
function _ga_fill_avatars(array &$videos, int $limit = 25): void {
    $need = [];
    foreach ($videos as $i => $v) {
        $av = trim((string)($v['authorAvatar'] ?? ''));
        $cid = (string)($v['authorId'] ?? '');
        if ($av !== '' || $cid === '' || !preg_match('/^UC[A-Za-z0-9_-]{22}$/', $cid)) continue;
        if (!isset($need[$cid])) $need[$cid] = [];
        $need[$cid][] = $i;
        if (count($need) >= $limit) break;
    }
    if ($need === []) return;

    foreach ($need as $cid => $indexes) {
        $url = _ga_fetch_channel_avatar($cid);
        if ($url === '') continue;
        foreach ($indexes as $i) {
            $videos[$i]['authorAvatar'] = $url;
        }
        // остальные видео с тем же authorId
        foreach ($videos as $j => $v) {
            if (($v['authorId'] ?? '') === $cid && trim((string)($v['authorAvatar'] ?? '')) === '') {
                $videos[$j]['authorAvatar'] = $url;
            }
        }
    }
}

function _ga_videos_from_search(string $query): array {
    $out = [];
    try {
        if (!function_exists('innertube_post')) return $out;
        $raw = innertube_post('search', ['query' => $query, 'params' => 'EgIQAQ%3D%3D']);
        if ($raw !== null) _ga_collect($raw, $out);
        if (empty($out)) {
            $raw = innertube_post('search', ['query' => $query]);
            if ($raw !== null) _ga_collect($raw, $out);
        }
    } catch (Throwable $e) {
        error_log('guide_ajax search: ' . $e->getMessage());
    }
    return $out;
}

$feedTitle = 'From YouTube';
$feedKey   = 'youtube';
$videos    = [];

if (isset($req['action_load_main_feed'])) {
    $videos = _ga_videos_from_browse('FEwhat_to_watch');
    if (empty($videos)) $videos = _ga_videos_from_search('popular videos');
    $feedTitle = 'From YouTube';

} elseif (isset($req['action_load_system_feed']) || isset($req['action_load_personal_feed']) || isset($req['action_load_social_feed'])) {
    $feedKey = strtolower(trim($req['feed_name'] ?? 'youtube'));

    switch ($feedKey) {
        case 'trending':
            $videos = _ga_videos_from_search('trending videos today' . ($oldera ? ' before:2012' : ''));
            if (empty($videos)) $videos = _ga_videos_from_search('trending videos today');
            $feedTitle = 'Trending';
            break;

        case 'subscriptions':
        case 'all':
            // Реальный API подписок (нужен SAPISID в cookies — прокси www.youtube.com)
            $videos = _ga_videos_from_browse_auth('FEsubscriptions');
            $feedTitle = 'Subscriptions';
            break;

        case 'social_all':
        case 'social':
            $videos = _ga_videos_from_browse_auth('FEsubscriptions');
            $feedTitle = 'Social';
            break;

        case 'music':
            $videos = _ga_videos_from_search('popular music videos' . ($oldera ? ' before:2012' : ''));
            if (empty($videos)) $videos = _ga_videos_from_search('popular music videos');
            $feedTitle = 'Music';
            break;

        case 'popular':
            $videos = _ga_videos_from_search('popular videos this week' . ($oldera ? ' before:2012' : ''));
            if (empty($videos)) $videos = _ga_videos_from_search('popular videos this week');
            $feedTitle = 'Popular';
            break;

        case 'uploads':
            $feedTitle = 'Videos';
            // channelId из auth.inc (cookie/OAuth) — оба ключа на всякий
            $cid = '';
            $accName = '';
            $accAvatar = '';
            if (is_array($ytAccount)) {
                $cid = (string)($ytAccount['channelId'] ?? ($ytAccount['channel_id'] ?? ''));
                $accName = (string)($ytAccount['name'] ?? ($ytAccount['channel_title'] ?? ''));
                $accAvatar = (string)($ytAccount['avatar'] ?? ($ytAccount['channel_avatar'] ?? ($ytAccount['photo'] ?? '')));
            }
            if ($cid !== '' && preg_match('/^UC[A-Za-z0-9_-]{22}$/', $cid)) {
                // вкладка Videos канала (params EgZ2aWRlb3M…)
                $videos = _ga_videos_from_browse_auth($cid, 'EgZ2aWRlb3PyBgQKAjoA');
                if (empty($videos)) {
                    $videos = _ga_videos_from_browse_auth($cid, 'EgZ2aWRlb3M%3D');
                }
                if (empty($videos)) {
                    $videos = _ga_videos_from_browse_auth($cid);
                }
                // На вкладке канала InnerTube часто НЕ отдаёт author/avatar на каждом видео
                if (!empty($videos)) {
                    // имя канала из browse metadata если нужно
                    if ($accName === '' && function_exists('innertube_post')) {
                        $meta = innertube_post('browse', ['browseId' => $cid]);
                        if (is_array($meta)) {
                            $t = $meta['metadata']['channelMetadataRenderer']['title'] ?? '';
                            if ($t !== '') $accName = $t;
                            if ($accAvatar === '') {
                                $accAvatar = best_thumb($meta['metadata']['channelMetadataRenderer']['avatar']['thumbnails'] ?? []);
                            }
                        }
                    }
                    foreach ($videos as &$v) {
                        if ($v['author'] === '' && $accName !== '') $v['author'] = $accName;
                        if ($v['authorId'] === '') $v['authorId'] = $cid;
                        if ($v['authorAvatar'] === '' && $accAvatar !== '') $v['authorAvatar'] = $accAvatar;
                    }
                    unset($v);
                }
            }
            break;

        case 'likes':
            $feedTitle = 'Likes';
            $videos = _ga_videos_from_browse_auth('FElikes');
            break;

        case 'history':
            $feedTitle = 'History';
            $videos = _ga_videos_from_browse_auth('FEhistory');
            break;

        case 'watch_later':
            $feedTitle = 'Watch Later';
            $videos = _ga_videos_from_browse_auth('WL');
            break;

        case 'youtube':
        default:
            $videos = _ga_videos_from_search('popular videos' . ($oldera ? ' before:2012' : ''));
            if (empty($videos)) $videos = _ga_videos_from_search('popular videos');
            $feedTitle = ($feedKey === 'youtube') ? 'From YouTube' : ucfirst($feedKey);
    }

} elseif (isset($req['action_load_chart_feed'])) {
    $feedKey = strtolower(trim($req['chart_name'] ?? ($req['feed_name'] ?? 'entertainment')));
    $chartQueries = [
        'entertainment' => 'entertainment videos',
        'sports'        => 'sports highlights',
        'comedy'        => 'comedy videos',
        'film'          => 'film and animation',
        'gadgets'       => 'tech gadgets review',
        'music'         => 'popular music videos',
        'gaming'        => 'gaming videos',
        'news'          => 'news today',
    ];
    $feedTitle = ucfirst($feedKey);
    $q = $chartQueries[$feedKey] ?? ($feedKey . ' videos');
    $videos = _ga_videos_from_search($q . ($oldera ? ' before:2012' : ''));
    if (empty($videos)) $videos = _ga_videos_from_search($q);

} elseif (isset($req['action_load_user_feed']) || isset($req['action_load_browse_feed'])) {
    $uid = trim($req['user_id'] ?? ($req['browse_channel_id'] ?? ''));
    $browseId = (strpos($uid, 'UC') === 0) ? $uid : 'UC' . $uid;
    $feedKey = 'user';
    if (preg_match('/^UC[A-Za-z0-9_-]{22}$/', $browseId)) {
        $videos = _ga_videos_from_browse($browseId, 'EgZ2aWRlb3PyBgQKAjoA');
        $feedTitle = !empty($videos[0]['author']) ? $videos[0]['author'] : 'Channel';
        if (function_exists('innertube_post')) {
            $chanMeta = innertube_post('browse', ['browseId' => $browseId]);
            if ($chanMeta !== null) {
                $t = $chanMeta['metadata']['channelMetadataRenderer']['title'] ?? '';
                if ($t !== '') $feedTitle = $t;
                $av = best_thumb($chanMeta['metadata']['channelMetadataRenderer']['avatar']['thumbnails'] ?? []);
                foreach ($videos as &$v) {
                    if ($v['author'] === '') { $v['author'] = $feedTitle; $v['authorId'] = $browseId; }
                    if ($v['authorAvatar'] === '' && $av !== '') $v['authorAvatar'] = $av;
                }
                unset($v);
            }
        }
    }
} else {
    $videos = _ga_videos_from_search('popular videos');
}

function _ga_render_feed(string $feedTitle, string $feedKey, array $videos): string {
    $pixel = '/yts/img/pixel-vfl3z5WfW.gif';
    $e = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };

    $items = '';
    foreach ($videos as $fi) {
        $vid    = $e($fi['id']);
        $title  = $e($fi['title']);
        $author = $e($fi['author']);
        $chan   = $e($fi['authorId']);
        $thumb  = $e($fi['thumbnail']);
        $avatar = $e(default_avatar((string)($fi['authorAvatar'] ?? '')));
        $dur    = $e($fi['duration']);
        $viewsFmt = format_view_count($fi['views'] ?? '');
        $views  = $e($viewsFmt);
        $ago    = $e($fi['publishedAgo']);
        $desc   = $e($fi['description']);

        $viewsHtml = $viewsFmt !== ''
            ? '<span class="bull">•</span><span class="view-count">' . $views . '</span>'
            : '';
        $descHtml = ($fi['description'] ?? '') !== ''
            ? '<div class="description"><p>' . $desc . '</p></div>'
            : '';

        $items .= <<<HTML
      <li>
          <div class="feed-item-container first">
          <div class="feed-author-bubble-container">
<a href="/channel/{$chan}?feature=g-logo-xit" class="feed-author-bubble">  <span class="feed-item-author">
      <span class="video-thumb ux-thumb yt-thumb-square-28"><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner">
        <img src="{$avatar}" alt="{$author}" data-thumb="{$avatar}" width="28" onerror="this.onerror=null;this.src='/dynamic/pfp/default.png'">
        <span class="vertical-align"></span></span></span></span>
  </span>
</a>  </div>
    <div class="feed-item-main">
      <div class="feed-item-header">
        <span class="feed-item-actions-line">
          <span class="feed-item-owner"><a href="/channel/{$chan}?feature=g-logo-xit" class="yt-uix-sessionlink yt-user-name" dir="ltr">{$author}</a></span>
 uploaded a video
                <span class="feed-item-time">{$ago}</span>
        </span>
      </div>
            <div class="feed-item-content-wrapper clearfix context-data-item"
                data-context-item-actionverb="uploaded"
                data-context-item-title="{$title}"
                data-context-item-type="video"
                data-context-item-time="{$dur}"
                data-context-item-user="{$author}"
                data-context-item-id="{$vid}"
                data-context-item-views="{$views}">
    <div class="feed-item-thumb">
          <a class="ux-thumb-wrap contains-addto yt-uix-contextlink yt-uix-sessionlink" href="/watch?v={$vid}&amp;feature=g-logo-xit">
      <span class="video-thumb ux-thumb yt-thumb-default-185"><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="{$thumb}" alt="Thumbnail" data-thumb="{$thumb}" width="185"><span class="vertical-align"></span></span></span></span>
    <span class="video-time">{$dur}</span>
  </a>
    </div>
    <div class="feed-item-content">
      <h4>
        <a class="feed-video-title title yt-uix-contextlink yt-uix-sessionlink secondary" href="/watch?v={$vid}&amp;feature=g-logo-xit">{$title}</a>
      </h4>
        <div class="metadata">
      <a href="/channel/{$chan}?feature=g-logo-xit" class="yt-uix-sessionlink yt-user-name" dir="ltr">{$author}</a>
        {$viewsHtml}
      {$descHtml}
  </div>
    </div>
  </div>
    </div>
  </div>
  <div class="feed-item-dismissal-notices"></div>
      </li>
HTML;
    }

    $feedTitleE = $e($feedTitle);
    $feedKeyE = $e($feedKey);
    return <<<HTML
        <div class="feed-header no-metadata">
      <div class="feed-header-thumb">
          <img class="feed-header-icon {$feedKeyE}" src="{$pixel}" alt="">
      </div>
      <div class="feed-header-details context-source-container" data-context-source="{$feedTitleE}">
        <h2>    {$feedTitleE}
</h2>
      </div>
  </div>
      <div class="feed-container" data-filter-type="" data-view-type="">
    <div class="feed-page">
          <ul class="context-data-container">
{$items}
      </ul>
    </div>
  </div>
HTML;
}

// Дозаполняем аватары для личных/подписочных лент
if (!empty($videos) && in_array($feedKey, [
    'subscriptions', 'all', 'social', 'social_all',
    'likes', 'history', 'watch_later', 'uploads',
], true)) {
    _ga_fill_avatars($videos, 30);
}

$GLOBALS['__ga_done'] = true;
$__ga_done = true;

if (empty($videos)) {
    $msg = 'No videos found.';
    if (in_array($feedKey, ['subscriptions', 'all', 'social', 'social_all', 'likes', 'history', 'watch_later', 'uploads'], true)
        && !_ga_can_auth()) {
        $msg = 'Sign in with a YouTube session (open via www.youtube.com proxy while logged in) to see this feed.';
    }
    echo json_encode([
        'feed_html' => '<div class="feed-header no-metadata"><div class="feed-header-details"><h2>' .
            htmlspecialchars($feedTitle) . '</h2></div></div>' .
            '<div class="feed-container"><div class="feed-page"><p class="feed-message">' .
            htmlspecialchars($msg) . '</p></div></div>',
        'authed' => _ga_can_auth(),
        'feed'   => $feedKey,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'feed_html' => _ga_render_feed($feedTitle, $feedKey, $videos),
    'authed'    => _ga_can_auth(),
    'feed'      => $feedKey,
    'count'     => count($videos),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    error_log('guide_ajax error: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(200);
    $GLOBALS['__ga_done'] = true;
    echo json_encode([
        'feed_html' => '<div class="feed-header no-metadata"><div class="feed-header-details"><h2>From YouTube</h2></div></div>'
            . '<div class="feed-container"><div class="feed-page"><p class="feed-message">'
            . 'We were unable to complete the request, please try again later.</p></div></div>',
        'error' => $e->getMessage(),
        'file'  => basename($e->getFile()),
        'line'  => $e->getLine(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}