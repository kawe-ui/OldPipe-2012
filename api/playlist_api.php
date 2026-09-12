<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  playlist_api.php — данные плейлиста для playlist.php (InnerTube /browse)
//  Экспортирует переменные для существующей разметки playlist.php.
//  Вход: $playlist_id или ?list=PL… (у плейлиста browseId = 'VL' + listId).
//
//  Плейлист приватный / удалён / не существует → $playlistError != '' и
//  $playlistExists = false (playlist.php решает, что показать). Точную причину
//  берём из alertRenderer YouTube, как это делает channel_api.php.
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

// ─── Defaults (экспортируемые переменные для playlist.php) ─────────────────────
$playlistExists      = false;
$playlistError       = '';   // '' | 'not_found' | 'unavailable'
$playlistErrorText   = '';   // точный текст алерта YouTube
$playlistId          = '';
$playlistTitle       = '';
$playlistDescription = '';
$playlistAuthor      = '';   // название канала-владельца
$playlistAuthorId    = '';   // UCxxx
$playlistAuthorUrl   = '';   // /channel/UCxxx
$playlistVideoCount  = '';   // «150»
$playlistViews       = '';   // «1,234,567»
$playlistUpdated     = '';   // «Updated 2 days ago»
$playlistDuration    = '';   // суммарная длительность загруженных видео, «58:38:03»
$playlistThumbnail   = '';
$playlistVideos      = [];    // [['id','title','author','authorId','duration','thumbnail','index','views','ago'], …]

// ─── Определяем идентификатор ─────────────────────────────────────────────────
if (empty($playlist_id)) {
    $playlist_id = $_GET['list'] ?? ($_GET['p'] ?? ($_GET['id'] ?? ''));
}
$playlist_id = trim((string)$playlist_id);

// ─── Мелкие помощники ─────────────────────────────────────────────────────────
/** simpleText | runs | content (viewModel) → плоская строка */
function _pl_text($node): string {
    if (!is_array($node)) return '';
    if (isset($node['simpleText'])) return (string)$node['simpleText'];
    if (isset($node['runs']))       return runs_to_text($node['runs']);
    if (isset($node['content']))    return (string)$node['content'];
    return '';
}

/** Суммарная длительность списка видео («M:SS»/«H:MM:SS») из строк «20:17». */
function _pl_sum_duration(array $videos): string {
    $sec = 0; $any = false;
    foreach ($videos as $v) {
        $d = trim((string)($v['duration'] ?? ''));
        if ($d === '' || !preg_match('/^(?:(\d+):)?(\d{1,2}):(\d{2})$/', $d, $m)) continue;
        $sec += ((int)($m[1] ?? 0)) * 3600 + ((int)$m[2]) * 60 + (int)$m[3];
        $any  = true;
    }
    if (!$any) return '';
    $h = intdiv($sec, 3600); $mm = intdiv($sec % 3600, 60); $ss = $sec % 60;
    return $h > 0 ? sprintf('%d:%02d:%02d', $h, $mm, $ss) : sprintf('%d:%02d', $mm, $ss);
}

// Алерт-ошибка плейлиста («The playlist does not exist.» и т.п.).
function _pl_alert_error(array $raw): ?string {
    foreach ($raw['alerts'] ?? [] as $a) {
        $r = $a['alertRenderer'] ?? ($a['alertWithButtonRenderer'] ?? null);
        if (!is_array($r) || ($r['type'] ?? '') !== 'ERROR') continue;
        $t = trim(_pl_text($r['text'] ?? []));
        if ($t !== '') return $t;
    }
    return null;
}

function _pl_classify_error(string $text): string {
    $t = mb_strtolower($text);
    if (str_contains($t, 'does not exist') || str_contains($t, 'not found')
        || str_contains($t, 'no longer') || str_contains($t, 'removed')
        || str_contains($t, 'private')) {
        return 'not_found';
    }
    return 'unavailable';
}

// ─── Шапка плейлиста: заголовок, автор, счётчики, обложка ──────────────────────
// browse VL… обычно кладёт их в header.playlistHeaderRenderer; для «новых»
// ответов без него — фолбэк на microformatDataRenderer.
function _pl_header(array $raw): array {
    $out = ['title' => '', 'description' => '', 'author' => '', 'authorId' => '',
            'views' => '', 'count' => '', 'updated' => '', 'thumb' => ''];

    $h = _sm_find_renderer($raw, 'playlistHeaderRenderer');
    if ($h !== null) {
        $out['title']       = _pl_text($h['title'] ?? []);
        $out['description'] = _pl_text($h['descriptionText'] ?? []);

        if (preg_match('/([\d][\d.,]*)/', _pl_text($h['numVideosText'] ?? []), $m)) $out['count'] = $m[1];
        if (preg_match('/([\d][\d.,]*)/', _pl_text($h['viewCountText'] ?? []), $m)) $out['views'] = $m[1];

        $own = $h['ownerText']['runs'][0] ?? null;
        if (is_array($own)) {
            $out['author']   = $own['text'] ?? '';
            $out['authorId'] = $own['navigationEndpoint']['browseEndpoint']['browseId'] ?? '';
        }
        $out['thumb'] = best_thumb(
            $h['playlistHeaderBanner']['heroPlaylistThumbnailRenderer']['thumbnail']['thumbnails'] ?? []
        );
    }

    // «Updated …» — точного поля нет, ищем строку по всей шапке.
    $updated = _pl_find_updated($h ?? $raw);
    if ($updated !== '') $out['updated'] = $updated;

    // Фолбэк на microformat, если заголовок так и не нашёлся.
    if ($out['title'] === '') {
        $mf = _sm_find_renderer($raw, 'microformatDataRenderer');
        if ($mf !== null) {
            $out['title']       = $mf['title'] ?? '';
            $out['description'] = $out['description'] !== '' ? $out['description'] : ($mf['description'] ?? '');
            if ($out['thumb'] === '') $out['thumb'] = best_thumb($mf['thumbnail']['thumbnails'] ?? []);
        }
    }
    return $out;
}

/** Рекурсивно ищет фразу «Updated …» целиком (собирая runs, а не первый кусок). */
function _pl_find_updated($node, int $depth = 0): string {
    if ($depth > 12 || !is_array($node)) return '';
    // Полная строка из runs («Updated » + «today» = «Updated today»)
    if (isset($node['runs']) && is_array($node['runs'])) {
        $t = trim(runs_to_text($node['runs']));
        if (preg_match('/^(Updated|Last updated)\b/i', $t)) return $t;
    }
    if (isset($node['content']) && is_string($node['content'])) {
        $t = trim($node['content']);
        if (preg_match('/^(Updated|Last updated)\b/i', $t)) return $t;
    }
    if (isset($node['simpleText']) && is_string($node['simpleText'])) {
        $t = trim($node['simpleText']);
        if (preg_match('/^(Updated|Last updated)\b/i', $t)) return $t;
    }
    foreach ($node as $v) {
        if (is_array($v)) {
            $r = _pl_find_updated($v, $depth + 1);
            if ($r !== '') return $r;
        }
    }
    return '';
}

// ─── Видео плейлиста (playlistVideoRenderer) ──────────────────────────────────
function _pl_collect_videos(array $node, array &$out, int $depth = 0): void {
    if ($depth > 40 || count($out) >= 200) return;
    foreach ($node as $k => $v) {
        if (!is_array($v)) continue;
        if ($k === 'playlistVideoRenderer') {
            $id = $v['videoId'] ?? '';
            if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) continue;
            $title = _pl_text($v['title'] ?? []);
            if (trim($title) === '') continue;

            $author = ''; $authorId = '';
            $by = $v['shortBylineText']['runs'][0] ?? null;
            if (is_array($by)) {
                $author   = $by['text'] ?? '';
                $authorId = $by['navigationEndpoint']['browseEndpoint']['browseId'] ?? '';
            }

            $thumb = best_thumb($v['thumbnail']['thumbnails'] ?? []) ?: "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";

            // videoInfo (не всегда есть): «12M views • 3 years ago»
            $views = ''; $ago = '';
            $vi = _pl_text($v['videoInfo'] ?? []);
            if ($vi !== '') {
                if (preg_match('/([\d][\d.,]*\s*[KMB]?)\s+views?/i', $vi, $m)) $views = expand_count($m[1] . ' views');
                if (preg_match('/(\d+\s+\w+\s+ago)/i', $vi, $m))               $ago   = $m[1];
            }

            $out[] = [
                'id'        => $id,
                'title'     => $title,
                'author'    => $author,
                'authorId'  => $authorId,
                'duration'  => _pl_text($v['lengthText'] ?? []),
                'thumbnail' => $thumb,
                'index'     => _pl_text($v['index'] ?? []),
                'views'     => $views,
                'ago'       => $ago,
            ];
        } elseif ($k === 'lockupViewModel' && ($v['contentType'] ?? '') === 'LOCKUP_CONTENT_TYPE_VIDEO') {
            // Новый формат: видео плейлиста приходят view-моделями (contentId =
            // videoId), автор/просмотры/возраст лежат в metadataRows, длительность —
            // бейджем поверх обложки.
            $id = $v['contentId'] ?? '';
            if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) continue;
            $md    = $v['metadata']['lockupMetadataViewModel'] ?? [];
            $title = trim($md['title']['content'] ?? '');
            if ($title === '') continue;

            $author = ''; $authorId = ''; $views = ''; $ago = '';
            foreach (($md['metadata']['contentMetadataViewModel']['metadataRows'] ?? []) as $row) {
                foreach ($row['metadataParts'] ?? [] as $part) {
                    $txt = trim($part['text']['content'] ?? '');
                    if ($txt === '') continue;
                    $bid = $part['text']['commandRuns'][0]['onTap']['innertubeCommand']['browseEndpoint']['browseId'] ?? '';
                    if ($author === '' && preg_match('/^UC[A-Za-z0-9_-]{22}$/', $bid)) {
                        $author = $txt; $authorId = $bid;
                    } elseif ($views === '' && stripos($txt, 'view') !== false) {
                        $views = expand_count($txt);
                    } elseif ($ago === '' && preg_match('/\bago$/i', $txt)) {
                        $ago = $txt;
                    }
                }
            }

            $tvm   = $v['contentImage']['thumbnailViewModel'] ?? [];
            $thumb = best_thumb($tvm['image']['sources'] ?? []) ?: "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";
            $dur   = '';
            foreach ($tvm['overlays'] ?? [] as $ov) {
                foreach ($ov['thumbnailBottomOverlayViewModel']['badges'] ?? [] as $b) {
                    $t = $b['thumbnailBadgeViewModel']['text'] ?? '';
                    if (preg_match('/^\d+:\d{2}(:\d{2})?$/', $t)) { $dur = $t; break 2; }
                }
            }

            $out[] = [
                'id'        => $id,
                'title'     => $title,
                'author'    => $author,
                'authorId'  => $authorId,
                'duration'  => $dur,
                'thumbnail' => $thumb,
                'index'     => (string)(count($out) + 1),
                'views'     => $views,
                'ago'       => $ago,
            ];
        } else {
            _pl_collect_videos($v, $out, $depth + 1);
        }
    }
}

/** Токен продолжения списка (плейлист отдаёт видео страницами по ~100). */
// Новый ответ кладёт его в continuationItemViewModel, старый — в
// continuationItemRenderer. Токен лежит матрёшкой (continuationCommand →
// innertubeCommand → continuationCommand → token), поэтому берём его глубоким
// поиском, а не первым попавшимся continuationCommand.
function _pl_continuation(array $node): ?string {
    foreach (['continuationItemViewModel', 'continuationItemRenderer'] as $rk) {
        $r = _sm_find_renderer($node, $rk);
        if ($r === null) continue;
        $t = _pl_deep_token($r);
        if ($t !== null) return $t;
    }
    return null;
}

/** Первый непустой 'token' в поддереве (внутренний continuationCommand.token). */
function _pl_deep_token($node): ?string {
    if (!is_array($node)) return null;
    if (isset($node['token']) && is_string($node['token']) && $node['token'] !== '') {
        return $node['token'];
    }
    foreach ($node as $v) {
        $t = _pl_deep_token($v);
        if ($t !== null) return $t;
    }
    return null;
}

// ═══════════════════════════════════════════════════════════════════════════════
//  Сборка данных плейлиста
// ═══════════════════════════════════════════════════════════════════════════════
function pl_fetch(string $rawId): array {
    $fail = fn(string $err, string $text = '') => ['exists' => false, 'error' => $err, 'errorText' => $text];

    if (!preg_match('/^[A-Za-z0-9_-]{10,50}$/', $rawId)) {
        return $fail('not_found', 'This playlist does not exist.');
    }

    $raw = innertube_post('browse', ['browseId' => 'VL' . $rawId]);
    if ($raw === null) return $fail('unavailable', 'This playlist is currently unavailable.');

    $alert = _pl_alert_error($raw);
    if ($alert !== null) return $fail(_pl_classify_error($alert), $alert);

    $hdr    = _pl_header($raw);
    $videos = [];
    _pl_collect_videos($raw, $videos);

    // Ни заголовка, ни видео — считаем, что плейлиста нет.
    if ($hdr['title'] === '' && empty($videos)) {
        return $fail('not_found', 'This playlist does not exist.');
    }

    // Дотягиваем следующие страницы видео (с потолком, чтобы не висеть вечно).
    $token = _pl_continuation($raw);
    for ($guard = 0; $token !== null && count($videos) < 200 && $guard < 3; $guard++) {
        $page = innertube_post('browse', ['continuation' => $token]);
        if ($page === null) break;
        _pl_collect_videos($page, $videos);
        $token = _pl_continuation($page);
    }

    if ($hdr['count'] === '')  $hdr['count'] = (string)count($videos);
    if ($hdr['author'] === '' && !empty($videos)) {
        $hdr['author']   = $videos[0]['author'];
        $hdr['authorId'] = $videos[0]['authorId'];
    }

    return [
        'exists'      => true,
        'error'       => '',
        'errorText'   => '',
        'id'          => $rawId,
        'title'       => $hdr['title'] !== '' ? $hdr['title'] : 'Playlist',
        'description' => $hdr['description'],
        'author'      => $hdr['author'],
        'authorId'    => $hdr['authorId'],
        'views'       => $hdr['views'],
        'count'       => $hdr['count'],
        'updated'     => $hdr['updated'],
        'thumbnail'   => $hdr['thumb'] !== '' ? $hdr['thumb'] : (!empty($videos) ? $videos[0]['thumbnail'] : ''),
        'videos'      => $videos,
    ];
}

// ─── Кэш собранных данных плейлиста ───────────────────────────────────────────
// Версия схемы: поднимать при изменении набора полей pl_fetch().
define('PL_CACHE_VER', 1);

function pl_fetch_cached(string $rawId): array {
    $key = CACHE_DIR . '/playlist_v' . PL_CACHE_VER . '_' . sha1($rawId) . '.json';

    if (is_file($key) && (time() - filemtime($key)) < CACHE_TTL_CHANNEL) {
        $c = json_decode((string)file_get_contents($key), true);
        if (is_array($c) && isset($c['exists'])) return $c;
    }

    $data = pl_fetch($rawId);
    @file_put_contents($key, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Ошибку кэшируем ненадолго: плейлист могли открыть/восстановить.
    if (empty($data['exists'])) {
        @touch($key, time() - CACHE_TTL_CHANNEL + CACHE_TTL_METADATA);
    }
    return $data;
}

// ═══════════════════════════════════════════════════════════════════════════════
//  Экспорт переменных для playlist.php
// ═══════════════════════════════════════════════════════════════════════════════
if ($playlist_id === '') {
    $playlistError     = 'not_found';
    $playlistErrorText = 'This playlist does not exist.';
} else {
    $d = pl_fetch_cached($playlist_id);

    if (empty($d['exists'])) {
        $playlistError     = $d['error']     ?? 'unavailable';
        $playlistErrorText = $d['errorText'] ?? '';
    } else {
        $playlistExists      = true;
        $playlistId          = $d['id'];
        $playlistTitle       = $d['title'];
        $playlistDescription = $d['description'];
        $playlistAuthor      = $d['author'];
        $playlistAuthorId    = $d['authorId'];
        $playlistAuthorUrl   = $d['authorId'] !== '' ? '/channel/' . $d['authorId'] : '';
        $playlistVideoCount  = $d['count'];
        $playlistViews       = $d['views'];
        $playlistUpdated     = $d['updated'];
        $playlistThumbnail   = $d['thumbnail'];
        $playlistVideos      = $d['videos'];
        $playlistDuration    = _pl_sum_duration($playlistVideos);
    }
}

// Заголовок страницы (как в channel_api.php).
$title = $playlistExists ? $playlistTitle : 'YouTube';
