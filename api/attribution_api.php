<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  attribution_api.php — данные для attribution.php (InnerTube player / next)
//  Вход: ?v=VIDEO_ID  или  $video_id
//  Экспортирует переменные для разметки 2012-стиля.
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

// ─── Defaults ─────────────────────────────────────────────────────────────────
$attrExists      = false;
$attrError       = '';
$attrVideoId     = '';
$attrTitle       = '';
$attrDescription = '';
$attrThumbnail   = DEFAULT_VIDEO_THUMB ?? 'https://i.ytimg.com/vi/default/hqdefault.jpg';
$attrDuration    = '';
$attrAuthor      = '';
$attrAuthorId    = '';
$attrAuthorUrl   = '';
$attrViewCount   = '';
$attrPublished   = '';
$attrMusic       = [];   // [['title','artist','album','licensers'], …]
$attrCredits     = [];   // свободные строки кредитов
$attrIsOwner     = false;

// ─── ID видео ─────────────────────────────────────────────────────────────────
if (empty($video_id)) {
    $video_id = $_GET['v'] ?? $_GET['video_id'] ?? '';
}
$video_id = trim($video_id);

if ($video_id === '' || !preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id)) {
    $attrError = 'invalid_id';
} else {
    $d = attr_fetch_cached($video_id);

    if (empty($d['exists'])) {
        $attrError = $d['error'] ?? 'not_found';
    } else {
        $attrExists      = true;
        $attrVideoId     = $d['id'];
        $attrTitle       = $d['title'];
        $attrDescription = $d['description'];
        $attrThumbnail   = $d['thumbnail'];
        $attrDuration    = $d['duration'];
        $attrAuthor      = $d['author'];
        $attrAuthorId    = $d['authorId'];
        $attrAuthorUrl   = $d['authorUrl'];
        $attrViewCount   = $d['viewCount'];
        $attrPublished   = $d['published'];
        $attrMusic       = $d['music'] ?? [];
        $attrCredits     = $d['credits'] ?? [];
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
//  Fetch
// ═══════════════════════════════════════════════════════════════════════════════

function attr_fetch(string $id): array {
    $fail = fn(string $err) => ['exists' => false, 'error' => $err];

    // 1) player — даёт videoDetails + microformat + иногда music
    $player = innertube_post('player', [
        'videoId' => $id,
        'contentCheckOk' => true,
        'racyCheckOk'    => true,
    ]);
    if ($player === null) return $fail('unavailable');

    $playability = $player['playabilityStatus']['status'] ?? '';
    if ($playability === 'ERROR' || $playability === 'LOGIN_REQUIRED') {
        $reason = $player['playabilityStatus']['reason'] ?? 'Video unavailable';
        return $fail('not_found');
    }

    $vd   = $player['videoDetails'] ?? [];
    $mf   = $player['microformat']['playerMicroformatRenderer'] ?? [];
    $title = $vd['title'] ?? $mf['title']['simpleText'] ?? '';
    if ($title === '') return $fail('not_found');

    $author   = $vd['author'] ?? '';
    $authorId = $vd['channelId'] ?? '';
    $thumb    = '';
    if (!empty($vd['thumbnail']['thumbnails'])) {
        $thumb = best_thumb($vd['thumbnail']['thumbnails']);
    }
    if ($thumb === '') {
        $thumb = "https://i.ytimg.com/vi/{$id}/hqdefault.jpg";
    }

    $duration = '';
    if (!empty($vd['lengthSeconds'])) {
        $duration = _attr_fmt_duration((int)$vd['lengthSeconds']);
    }

    $out = [
        'exists'      => true,
        'error'       => '',
        'id'          => $id,
        'title'       => $title,
        'description' => $vd['shortDescription'] ?? $mf['description']['simpleText'] ?? '',
        'thumbnail'   => $thumb,
        'duration'    => $duration,
        'author'      => $author,
        'authorId'    => $authorId,
        'authorUrl'   => $authorId !== '' ? '/channel/' . $authorId : '',
        'viewCount'   => isset($vd['viewCount']) ? number_format((int)$vd['viewCount']) : '',
        'published'   => $mf['publishDate'] ?? $mf['uploadDate'] ?? '',
        'music'       => [],
        'credits'     => [],
    ];

    // 2) next — engagement panels / structured description / music shelf
    $next = innertube_post('next', ['videoId' => $id]);
    if (is_array($next)) {
        _attr_collect_music($next, $out['music']);
        _attr_collect_credits($next, $out['credits']);
    }

    // Иногда музыка лежит прямо в player
    if ($out['music'] === [] && is_array($player)) {
        _attr_collect_music($player, $out['music']);
    }

    return $out;
}

function attr_fetch_cached(string $id): array {
    $key = (defined('CACHE_DIR') ? CACHE_DIR : sys_get_temp_dir()) . '/attr_v1_' . $id . '.json';
    $ttl = defined('CACHE_TTL_METADATA') ? CACHE_TTL_METADATA : 3600;

    if (is_file($key) && (time() - filemtime($key)) < $ttl) {
        $c = json_decode((string)file_get_contents($key), true);
        if (is_array($c) && isset($c['exists'])) return $c;
    }

    $data = attr_fetch($id);
    @file_put_contents($key, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    return $data;
}

// ─── helpers ──────────────────────────────────────────────────────────────────

function _attr_fmt_duration(int $sec): string {
    $h = intdiv($sec, 3600);
    $m = intdiv($sec % 3600, 60);
    $s = $sec % 60;
    if ($h > 0) return sprintf('%d:%02d:%02d', $h, $m, $s);
    return sprintf('%d:%02d', $m, $s);
}

function _attr_collect_music(array $node, array &$out, int $depth = 0): void {
    if ($depth > 25 || count($out) >= 20) return;

    foreach ($node as $k => $v) {
        if (!is_array($v)) continue;

        // carouselLockupRenderer / music responsive list items
        if ($k === 'carouselLockupRenderer' || $k === 'musicResponsiveListItemRenderer') {
            $title = '';
            $artist = '';
            $album = '';

            // title
            $title = $v['itemTitle']['simpleText']
                  ?? runs_to_text($v['itemTitle']['runs'] ?? [])
                  ?? $v['title']['simpleText']
                  ?? runs_to_text($v['title']['runs'] ?? [])
                  ?? '';

            // artists
            foreach ($v['itemSubtitle']['runs'] ?? $v['flexColumns'][1]['musicResponsiveListItemFlexColumnRenderer']['text']['runs'] ?? [] as $run) {
                $t = $run['text'] ?? '';
                if ($t !== '' && $t !== ' • ' && $t !== '·') {
                    $artist = $artist === '' ? $t : $artist . ', ' . $t;
                }
            }

            if ($title !== '') {
                $out[] = [
                    'title'  => trim($title),
                    'artist' => trim($artist),
                    'album'  => $album,
                ];
            }
        }

        // structured description music section
        if ($k === 'expandableVideoDescriptionMusicSectionRenderer' || $k === 'videoDescriptionMusicSectionRenderer') {
            foreach ($v['carouselLockups'] ?? [] as $lock) {
                _attr_collect_music($lock, $out, $depth + 1);
            }
        }

        _attr_collect_music($v, $out, $depth + 1);
    }
}

function _attr_collect_credits(array $node, array &$out, int $depth = 0): void {
    if ($depth > 20 || count($out) >= 30) return;

    foreach ($node as $k => $v) {
        if (!is_array($v)) continue;

        // Простые текстовые кредиты из description body / info rows
        if ($k === 'videoDescriptionInfocardsSectionRenderer' || $k === 'infoPanelContentRenderer') {
            $text = $v['title']['simpleText'] ?? runs_to_text($v['title']['runs'] ?? []) ?? '';
            if ($text !== '') $out[] = $text;
        }

        _attr_collect_credits($v, $out, $depth + 1);
    }
}
