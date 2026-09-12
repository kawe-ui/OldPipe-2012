<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  results_api.php — поисковая выдача для results.php (InnerTube /search)
//  Экспортирует переменные для разметки results.php.
//  Вход: $search_query или ?search_query=… (форма мачхеда постит его на /results).
//
//  Нормализует три типа результатов в единый список $searchResults, у каждого
//  есть поле 'type' = video | channel | playlist.
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

// ─── Defaults (экспортируемые переменные для results.php) ──────────────────────
$searchQuery       = '';
$searchResults     = [];   // [['type'=>'video|channel|playlist', …], …]
$searchEstimated   = '';   // «9,699,085»
$searchError       = '';   // '' | 'unavailable'
$searchSort        = '';   // '' | video_date_uploaded | video_view_count | video_avg_rating
$searchRefinements = [];   // [['label'=>'Roblox','query'=>'Roblox'], …] — связанные запросы

// ─── Запрос ───────────────────────────────────────────────────────────────────
if (empty($search_query)) {
    $search_query = $_GET['search_query'] ?? ($_GET['q'] ?? ($_GET['search'] ?? ''));
}
$search_query = trim((string)$search_query);

// ─── Сортировка (имена параметров 2012 → protobuf-params InnerTube) ───────────
const SR_SORT_PARAMS = [
    'video_date_uploaded' => 'CAI=',   // Upload date
    'video_view_count'    => 'CAM=',   // View count
    'video_avg_rating'    => 'CAE=',   // Rating
];
$searchSort = (string)($_GET['search_sort'] ?? '');
if (!isset(SR_SORT_PARAMS[$searchSort])) $searchSort = '';   // relevance

// ─── Хелперы ──────────────────────────────────────────────────────────────────
/** simpleText | runs | content → плоская строка */
function _sr_text($node): string {
    if (!is_array($node)) return '';
    if (isset($node['simpleText'])) return (string)$node['simpleText'];
    if (isset($node['runs']))       return runs_to_text($node['runs']);
    if (isset($node['content']))    return (string)$node['content'];
    return '';
}

/** //host/… → https://host/… (аватары каналов приходят протокол-относительными) */
function _sr_https(string $url): string {
    $url = trim($url);
    if ($url === '') return '';
    if (str_starts_with($url, '//')) return 'https:' . $url;
    return $url;
}

// ─── videoRenderer → видео ────────────────────────────────────────────────────
function _sr_video(array $v): ?array {
    $id = $v['videoId'] ?? '';
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) return null;
    $title = _sr_text($v['title'] ?? []);
    if ($title === '') return null;

    $author = ''; $authorId = '';
    $own = $v['ownerText']['runs'][0] ?? ($v['longBylineText']['runs'][0] ?? null);
    if (is_array($own)) {
        $author   = $own['text'] ?? '';
        $authorId = $own['navigationEndpoint']['browseEndpoint']['browseId'] ?? '';
    }

    // Сниппет описания: detailedMetadataSnippets (новый) или descriptionSnippet (старый)
    $desc = '';
    $runs = $v['detailedMetadataSnippets'][0]['snippetText']['runs']
        ?? ($v['descriptionSnippet']['runs'] ?? []);
    foreach ($runs as $r) $desc .= $r['text'] ?? '';

    return [
        'type'        => 'video',
        'id'          => $id,
        'title'       => $title,
        'author'      => $author,
        'authorId'    => $authorId,
        'duration'    => _sr_text($v['lengthText'] ?? []),
        'views'       => _sr_text($v['viewCountText'] ?? []),        // «973,303,709 views»
        'ago'         => _sr_text($v['publishedTimeText'] ?? []),    // «10 years ago»
        'description' => trim($desc),
        'thumbnail'   => best_thumb($v['thumbnail']['thumbnails'] ?? []) ?: "https://i.ytimg.com/vi/{$id}/mqdefault.jpg",
    ];
}

// ─── channelRenderer → канал ──────────────────────────────────────────────────
// У нового InnerTube поля перепутаны: subscriberCountText часто = «@handle», а
// videoCountText = «20M subscribers». Поэтому раскладываем по смыслу текста.
function _sr_channel(array $c): ?array {
    $id = $c['channelId'] ?? '';
    if (!preg_match('/^UC[A-Za-z0-9_-]{22}$/', $id)) return null;
    $title = _sr_text($c['title'] ?? []);
    if ($title === '') return null;

    $subs = ''; $handle = ''; $videoCount = '';
    foreach ([$c['subscriberCountText'] ?? [], $c['videoCountText'] ?? []] as $f) {
        $t = trim(_sr_text($f));
        if ($t === '') continue;
        if ($handle === '' && $t[0] === '@')                       $handle     = $t;
        elseif ($subs === '' && stripos($t, 'subscriber') !== false) $subs       = $t;
        elseif ($videoCount === '' && stripos($t, 'video') !== false) $videoCount = $t;
    }

    $desc = '';
    foreach ($c['descriptionSnippet']['runs'] ?? [] as $r) $desc .= $r['text'] ?? '';

    $avatar = _sr_https(best_thumb($c['thumbnail']['thumbnails'] ?? []));

    return [
        'type'        => 'channel',
        'id'          => $id,
        'title'       => $title,
        'handle'      => $handle,
        'subscribers' => $subs,
        'videoCount'  => $videoCount,
        'description' => trim($desc),
        'avatar'      => $avatar !== '' ? $avatar : DEFAULT_CHANNEL_AVATAR,
    ];
}

// ─── playlistRenderer (старый) → плейлист ─────────────────────────────────────
function _sr_playlist_renderer(array $p): ?array {
    $id = $p['playlistId'] ?? '';
    if ($id === '') return null;
    $title = _sr_text($p['title'] ?? []);
    if ($title === '') return null;

    $author = ''; $authorId = '';
    $own = $p['shortBylineText']['runs'][0] ?? ($p['longBylineText']['runs'][0] ?? null);
    if (is_array($own)) {
        $author   = $own['text'] ?? '';
        $authorId = $own['navigationEndpoint']['browseEndpoint']['browseId'] ?? '';
    }
    $thumb = best_thumb($p['thumbnails'][0]['thumbnails'] ?? ($p['thumbnail']['thumbnails'] ?? []));

    // Старый рендерер сам несёт первые видео (childVideoRenderer) — берём даром
    $videos = [];
    foreach ($p['videos'] ?? [] as $cv) {
        $c = $cv['childVideoRenderer'] ?? null;
        if (!is_array($c)) continue;
        $vid = $c['videoId'] ?? '';
        $vt  = _sr_text($c['title'] ?? []);
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $vid) && $vt !== '') {
            $videos[] = ['id' => $vid, 'title' => $vt];
        }
        if (count($videos) >= 4) break;
    }

    return [
        'type'     => 'playlist',
        'id'       => $id,
        'title'    => $title,
        'author'   => $author,
        'authorId' => $authorId,
        'count'    => _sr_text($p['videoCount'] ?? []),
        'thumbnail'=> $thumb,
        'videos'   => $videos,
    ];
}

// ─── Первые видео плейлиста (для ленты обложек и списка названий 2012) ────────
// Новый lockupViewModel названий видео не несёт — дотягиваем одним browse VL…
// с файловым кэшем: плейлисты в выдаче повторяются, сеть дёргается раз в сутки.
function _sr_playlist_first_videos(string $plId): array {
    $cacheFile = CACHE_DIR . '/plfirst_' . sha1($plId) . '.json';
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL_CHANNEL) {
        $c = json_decode((string)file_get_contents($cacheFile), true);
        if (is_array($c)) return $c;
    }

    $videos = [];
    $raw = innertube_post('browse', ['browseId' => 'VL' . $plId]);
    if ($raw !== null) {
        $walk = function ($node, int $depth = 0) use (&$walk, &$videos): void {
            if ($depth > 40 || count($videos) >= 4 || !is_array($node)) return;
            foreach ($node as $k => $v) {
                if (!is_array($v)) continue;
                if ($k === 'playlistVideoRenderer') {
                    $vid = $v['videoId'] ?? '';
                    $vt  = _sr_text($v['title'] ?? []);
                    if (preg_match('/^[A-Za-z0-9_-]{11}$/', $vid) && $vt !== '') $videos[] = ['id' => $vid, 'title' => $vt];
                } elseif ($k === 'lockupViewModel' && ($v['contentType'] ?? '') === 'LOCKUP_CONTENT_TYPE_VIDEO') {
                    $vid = $v['contentId'] ?? '';
                    $vt  = trim($v['metadata']['lockupMetadataViewModel']['title']['content'] ?? '');
                    if (preg_match('/^[A-Za-z0-9_-]{11}$/', $vid) && $vt !== '') $videos[] = ['id' => $vid, 'title' => $vt];
                } else {
                    $walk($v, $depth + 1);
                }
            }
        };
        $walk($raw);
        // null-ответ сети не кэшируем; пустой список у реального ответа — кэшируем
        @file_put_contents($cacheFile, json_encode($videos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    return $videos;
}

// ─── lockupViewModel (PLAYLIST) → плейлист (новый формат) ──────────────────────
function _sr_playlist_lockup(array $v): ?array {
    $id = $v['contentId'] ?? '';
    if ($id === '') return null;
    $md    = $v['metadata']['lockupMetadataViewModel'] ?? [];
    $title = trim($md['title']['content'] ?? '');
    if ($title === '') return null;

    $author = ''; $authorId = '';
    foreach (($md['metadata']['contentMetadataViewModel']['metadataRows'] ?? []) as $row) {
        foreach ($row['metadataParts'] ?? [] as $part) {
            $txt = trim($part['text']['content'] ?? '');
            $bid = $part['text']['commandRuns'][0]['onTap']['innertubeCommand']['browseEndpoint']['browseId'] ?? '';
            if ($author === '' && preg_match('/^UC[A-Za-z0-9_-]{22}$/', $bid)) { $author = $txt; $authorId = $bid; }
        }
    }

    $tvm = $v['contentImage']['collectionThumbnailViewModel']['primaryThumbnail']['thumbnailViewModel']
        ?? ($v['contentImage']['thumbnailViewModel'] ?? []);
    $thumb = best_thumb($tvm['image']['sources'] ?? []);

    $count = '';
    foreach ($tvm['overlays'] ?? [] as $ov) {
        $b = $ov['thumbnailOverlayBadgeViewModel']['thumbnailBadges'][0]['thumbnailBadgeViewModel']['text'] ?? '';
        if ($b !== '') { $count = $b; break; }
    }

    return [
        'type'     => 'playlist',
        'id'       => $id,
        'title'    => $title,
        'author'   => $author,
        'authorId' => $authorId,
        'count'    => $count,
        'thumbnail'=> $thumb,
        'videos'   => [],   // дотягиваются в sr_fetch (_sr_playlist_first_videos)
    ];
}

// ─── Обход выдачи: собирает video/channel/playlist в порядке появления ─────────
function _sr_collect(array $node, array &$out, int $depth = 0): void {
    if ($depth > 40 || count($out) >= 40) return;
    foreach ($node as $k => $v) {
        if (!is_array($v)) continue;
        if ($k === 'videoRenderer')          { $r = _sr_video($v);             if ($r) $out[] = $r; }
        elseif ($k === 'channelRenderer')    { $r = _sr_channel($v);           if ($r) $out[] = $r; }
        elseif ($k === 'playlistRenderer')   { $r = _sr_playlist_renderer($v); if ($r) $out[] = $r; }
        elseif ($k === 'lockupViewModel' && str_contains($v['contentType'] ?? '', 'PLAYLIST')) {
            $r = _sr_playlist_lockup($v); if ($r) $out[] = $r;
        } else {
            _sr_collect($v, $out, $depth + 1);
        }
    }
}

// ─── Связанные запросы (searchRefinementCardRenderer) — «лего»-чипсы 2012 ─────
function _sr_collect_refinements(array $node, array &$out, int $depth = 0): void {
    if ($depth > 40 || count($out) >= 18) return;
    foreach ($node as $k => $v) {
        if (!is_array($v)) continue;
        if ($k === 'searchRefinementCardRenderer') {
            $query = trim((string)($v['searchEndpoint']['searchEndpoint']['query'] ?? ''));
            $label = trim(_sr_text($v['query'] ?? []));
            if ($label === '') $label = $query;
            if ($query !== '' && !in_array($query, array_column($out, 'query'), true)) {
                $out[] = ['label' => $label, 'query' => $query];
            }
        } else {
            _sr_collect_refinements($v, $out, $depth + 1);
        }
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
//  Сборка выдачи (короткий кэш: результаты меняются, но не ежесекундно)
// ═══════════════════════════════════════════════════════════════════════════════
function sr_fetch(string $q, string $sort = ''): array {
    $payload = ['query' => $q];
    if (isset(SR_SORT_PARAMS[$sort])) $payload['params'] = SR_SORT_PARAMS[$sort];

    $raw = innertube_post('search', $payload);
    if ($raw === null) return ['error' => 'unavailable', 'estimated' => '', 'results' => [], 'refinements' => []];

    // Основная колонка выдачи (не боковые шелфы/реклама)
    $primary = $raw['contents']['twoColumnSearchResultsRenderer']['primaryContents']
        ?? ($raw['contents'] ?? $raw);

    $results = [];
    _sr_collect($primary, $results);

    // Плейлистам без списка видео дотягиваем первые 3-4 (лента обложек и
    // названия в плитке 2012). На промахе кэша это по одному browse на плейлист.
    foreach ($results as &$r) {
        if ($r['type'] === 'playlist' && empty($r['videos'])) {
            $r['videos'] = _sr_playlist_first_videos($r['id']);
        }
    }
    unset($r);

    // Чипсы-подсказки лежат вне primaryContents — собираем по всему ответу
    $refinements = [];
    _sr_collect_refinements($raw, $refinements);

    $est = (string)($raw['estimatedResults'] ?? '');
    return [
        'error'       => '',
        'estimated'   => $est !== '' ? number_format((int)$est) : '',
        'results'     => $results,
        'refinements' => $refinements,
    ];
}

define('SR_CACHE_VER', 3);   // v3: + первые видео плейлистов (лента обложек)

function sr_fetch_cached(string $q, string $sort = ''): array {
    $key = CACHE_DIR . '/search_v' . SR_CACHE_VER . '_' . sha1(mb_strtolower($q) . '|' . $sort) . '.json';
    if (is_file($key) && (time() - filemtime($key)) < CACHE_TTL_METADATA) {
        $c = json_decode((string)file_get_contents($key), true);
        if (is_array($c) && isset($c['results'])) return $c;
    }
    $data = sr_fetch($q, $sort);
    if ($data['error'] === '') {   // ошибку сети не кэшируем
        @file_put_contents($key, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
    return $data;
}

// ═══════════════════════════════════════════════════════════════════════════════
//  Экспорт переменных для results.php
// ═══════════════════════════════════════════════════════════════════════════════
$searchQuery = $search_query;
if ($search_query !== '') {
    $d = sr_fetch_cached($search_query, $searchSort);
    $searchError       = $d['error'];
    $searchEstimated   = $d['estimated'];
    $searchResults     = $d['results'];
    $searchRefinements = $d['refinements'] ?? [];
}

// Заголовок страницы (как в channel_api.php / playlist_api.php).
$title = $search_query !== '' ? $search_query . ' - YouTube' : 'YouTube';
