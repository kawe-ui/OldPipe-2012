<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  home_api.php  —  InnerTube browse  |  May 2026
//  browseId: FEwhat_to_watch (главная), FEtrending (фолбэк)
//  Описания: batch /player — shortDescription присутствует в videoDetails
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

// ─── Defaults ─────────────────────────────────────────────────────────────────
$homeFeedVideos  = [];
$spotlightVideos = [];
$featuredVideos  = [];

// ─── InnerTube browse ─────────────────────────────────────────────────────────
$browseRaw = null;
foreach (['FEwhat_to_watch', 'FEtrending'] as $bid) {
    $res = innertube_post('browse', ['browseId' => $bid]);
    if ($res !== null && !empty($res['contents'])) {
        $browseRaw = $res;
        break;
    }
}

// ─── Парсер одного videoRenderer ─────────────────────────────────────────────
function _itube_parse_video_renderer(array $vr): ?array {
    $videoId = $vr['videoId'] ?? '';
    if (empty($videoId)) return null;

    // Пропускаем Shorts и прямые эфиры
    foreach ($vr['thumbnailOverlays'] ?? [] as $ov) {
        $style = $ov['thumbnailOverlayTimeStatusRenderer']['style'] ?? '';
        if (in_array($style, ['LIVE', 'SHORTS'], true)) return null;
    }

    // Заголовок
    $title = '';
    if (!empty($vr['title']['runs'])) {
        foreach ($vr['title']['runs'] as $r) $title .= $r['text'] ?? '';
    } elseif (!empty($vr['title']['simpleText'])) {
        $title = $vr['title']['simpleText'];
    }
    if (empty(trim($title))) return null;

    // Канал
    $channelTitle = '';
    $channelId    = '';
    foreach (['ownerText', 'shortBylineText', 'longBylineText'] as $f) {
        if (!empty($vr[$f]['runs'][0])) {
            $channelTitle = $vr[$f]['runs'][0]['text'] ?? '';
            $channelId    = $vr[$f]['runs'][0]['navigationEndpoint']['browseEndpoint']['browseId'] ?? '';
            break;
        }
    }

    // Avatar канала
    $chArr = $vr['channelThumbnailSupportedRenderers']['channelThumbnailWithLinkRenderer']['thumbnail']['thumbnails']
        ?? $vr['channelThumbnail']['thumbnails']
        ?? [];
    $channelThumb = '';
    if (!empty($chArr[0]['url'])) {
        $channelThumb = preg_replace('/=s\d+(-c-k.*)?$/', '=s88-c-k-c0x00ffffff-no-rj', $chArr[0]['url']);
    }
    // Пусто → /dynamic/pfp/default.png; реальная ава → через pfp-прокси (как watch/channel).
    $channelThumb = default_avatar($channelThumb);

    // Thumbnail видео
    $thumbArr = $vr['thumbnail']['thumbnails'] ?? [];
    $thumb    = "https://i.ytimg.com/vi/{$videoId}/mqdefault.jpg";
    if (!empty($thumbArr)) {
        usort($thumbArr, fn($a, $b) => (int)($b['width'] ?? 0) - (int)($a['width'] ?? 0));
        if (!empty($thumbArr[0]['url'])) $thumb = $thumbArr[0]['url'];
    }

    // Длительность
    $durationText = $vr['lengthText']['simpleText'] ?? '';
    if (empty($durationText)) {
        foreach ($vr['thumbnailOverlays'] ?? [] as $ov) {
            $durationText = $ov['thumbnailOverlayTimeStatusRenderer']['text']['simpleText'] ?? '';
            if (!empty($durationText)) break;
        }
    }

    // Просмотры: «1,234,567 views» → «1,234,567»
    $viewsRaw = $vr['viewCountText']['simpleText']
        ?? ($vr['viewCountText']['runs'][0]['text'] ?? '');
    $views = '';
    if (preg_match('/([\d][\d\s,\.]*[\d])/', $viewsRaw, $m)) {
        $views = trim($m[1]);
    } elseif (!empty($viewsRaw)) {
        $views = $viewsRaw;
    }

    // Дата
    $publishedAgo = $vr['publishedTimeText']['simpleText'] ?? '';

    // Описание из browse (descriptionSnippet / shortDescription)
    // Может быть пустым — будет заполнено позже через _fetch_descriptions()
    $description = '';
    if (!empty($vr['descriptionSnippet']['runs'])) {
        foreach ($vr['descriptionSnippet']['runs'] as $r) $description .= $r['text'] ?? '';
    } elseif (!empty($vr['shortDescription'])) {
        $description = $vr['shortDescription'];
    }
    // Обрезаем если есть
    if (mb_strlen($description) > 120) {
        $description = mb_substr($description, 0, 117) . '...';
    }

    return [
        'id'           => $videoId,
        'title'        => $title,
        'author'       => $channelTitle,
        'authorId'     => $channelId,
        'authorAvatar' => $channelThumb,
        'thumbnail'    => $thumb,
        'duration'     => $durationText,
        'views'        => $views,
        'description'  => $description,   // '' если не пришло из browse
        'publishedAgo' => $publishedAgo,
    ];
}

// ─── Рекурсивный сборщик videoRenderer ───────────────────────────────────────
function _collect_video_renderers(array $node, array &$out, int $depth = 0): void {
    if ($depth > 30 || count($out) >= 60) return;
    foreach ($node as $key => $value) {
        if (!is_array($value)) continue;
        switch ($key) {
            case 'videoRenderer':
            case 'gridVideoRenderer':
                $p = _itube_parse_video_renderer($value);
                if ($p !== null) $out[] = $p;
                break;
            case 'richItemRenderer':
                $inner = $value['content'] ?? [];
                if (!empty($inner['videoRenderer'])) {
                    $p = _itube_parse_video_renderer($inner['videoRenderer']);
                    if ($p !== null) $out[] = $p;
                }
                break;
            default:
                _collect_video_renderers($value, $out, $depth + 1);
        }
    }
}

// ─── Batch /player — получаем shortDescription для всех видео ─────────────────
//
// InnerTube /player возвращает videoDetails.shortDescription — полное описание.
// Browse/FEwhat_to_watch часто НЕ присылает descriptionSnippet, поэтому
// запрашиваем /player параллельно через curl_multi для нужных видео.
// Лимит: первые 20 видео (у остальных описание скорее всего не показывается
// в видимой части ленты без скролла).
//
function _fetch_descriptions(array &$videos, int $limit = 20): void {
    $targets = [];
    foreach ($videos as $i => $v) {
        if (empty($v['description']) && !empty($v['id'])) {
            $targets[$i] = $v['id'];
        }
        if (count($targets) >= $limit) break;
    }
    if (empty($targets)) return;

    // Строим payload для каждого запроса
    $context = innertube_context();
    $handles = [];
    $mh      = curl_multi_init();

    foreach ($targets as $idx => $vid) {
        $payload = json_encode([
            'videoId'        => $vid,
            'racyCheckOk'    => true,
            'contentCheckOk' => true,
            'context'        => $context,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $url = INNERTUBE_BASE_URL . 'player?key=' . INNERTUBE_API_KEY . '&prettyPrint=false';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,        15);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'Origin: '  . INNERTUBE_ORIGIN,
            'Referer: ' . INNERTUBE_ORIGIN . '/',
            'X-YouTube-Client-Name: 1',
            'X-YouTube-Client-Version: ' . INNERTUBE_CLIENT_VER,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
        ]);

        // SSL
        $caBundle = null;
        foreach ([
            'C:/Program Files/Ampps/php/extras/ssl/cacert.pem',
            'C:/Ampps/php/extras/ssl/cacert.pem',
            '/etc/ssl/certs/ca-certificates.crt',
        ] as $c) {
            if (file_exists($c)) { $caBundle = $c; break; }
        }
        if ($caBundle) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        curl_multi_add_handle($mh, $ch);
        $handles[$idx] = $ch;
    }

    // Выполняем параллельно
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    // Собираем результаты
    foreach ($handles as $idx => $ch) {
        $body = curl_multi_getcontent($ch);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        if (empty($body)) continue;
        $data = json_decode($body, true);
        if (!is_array($data)) continue;

        $desc = $data['videoDetails']['shortDescription'] ?? '';
        if (empty($desc)) continue;

        // Обрезаем до 120 символов
        if (mb_strlen($desc) > 120) {
            $desc = mb_substr($desc, 0, 117) . '...';
        }
        $videos[$idx]['description'] = $desc;
    }

    curl_multi_close($mh);
}

// ─── Парсим ответ browse ──────────────────────────────────────────────────────
if ($browseRaw !== null) {
    _collect_video_renderers($browseRaw['contents'], $homeFeedVideos);
}

// Фолбэк — search
if (empty($homeFeedVideos)) {
    $searchFallback = innertube_post('search', ['query' => 'popular videos before:2012']);
    if ($searchFallback !== null) {
        _collect_video_renderers($searchFallback, $homeFeedVideos);
    }
}

if (empty($spotlightVideos)) {
    $searchFallback = innertube_post('search', ['query' => 'The 2012 U.S. Presidential Campaign before:2012']); // keep it for temporary next time
    if ($searchFallback !== null) {
        _collect_video_renderers($searchFallback, $spotlightVideos);
    }
}

// Дедупликация
$seen = [];
$homeFeedVideos = array_values(array_filter($homeFeedVideos, function ($v) use (&$seen) {
    if (empty($v['id']) || isset($seen[$v['id']])) return false;
    $seen[$v['id']] = true;
    return true;
}));

// ─── Получаем описания для видео у которых их нет ─────────────────────────────
// Параллельные запросы /player для первых 20 видео без описания
_fetch_descriptions($homeFeedVideos, 20);

// ─── Sidebar sections ─────────────────────────────────────────────────────────
$spotlightVideos = array_slice($spotlightVideos, 0, 4);
$featuredVideos  = array_slice($homeFeedVideos, 4, 3);

// ═══════════════════════════════════════════════════════════════════════════════
//  Популярные каналы для гайда (боковое меню) — живые, с фильтрацией мёртвых
// ═══════════════════════════════════════════════════════════════════════════════

// Резолвит @handle → browseId (UCxxx) через navigation/resolve_url
function _resolve_channel_handle(string $handle): ?string {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => INNERTUBE_BASE_URL . 'navigation/resolve_url?key=' . INNERTUBE_API_KEY . '&prettyPrint=false',
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode([
            'url'     => 'https://www.youtube.com/' . ltrim($handle, '/'),
            'context' => innertube_context(),
        ], JSON_UNESCAPED_SLASHES),
    ]);
    _itube_ssl_opts($ch);
    _itube_proxy_opts($ch);
    $res = curl_exec($ch);
    curl_close($ch);
    $d = json_decode((string)$res, true);
    $id = $d['endpoint']['browseEndpoint']['browseId'] ?? null;
    return (is_string($id) && preg_match('/^UC[A-Za-z0-9_-]{22}$/', $id)) ? $id : null;
}

// Метаданные канала; null если канал удалён/приватен/недоступен (фильтрация)
function _channel_meta(string $browseId): ?array {
    $raw = innertube_post('browse', ['browseId' => $browseId]);
    if ($raw === null) return null;
    $m = $raw['metadata']['channelMetadataRenderer'] ?? null;
    if ($m === null || empty($m['title'])) return null; // мёртвый/приватный → пропускаем

    // Реальная ава → показываем; нет авы (моно­грамма) → default.png (см. default_avatar/pfp)
    $avatar = default_avatar(best_thumb($m['avatar']['thumbnails'] ?? []));

    $subs = '';
    array_walk_recursive($raw, function ($val, $key) use (&$subs) {
        if ($subs !== '' || $key !== 'subscriberCountText') return;
        $subs = is_array($val) ? ($val['simpleText'] ?? '') : (string)$val;
    });

    return [
        'id'     => $browseId,
        'title'  => $m['title'],
        'avatar' => $avatar,
        'subs'   => $subs,
    ];
}

// Куратор популярных каналов (по хэндлам) с кэшем на сутки.
function get_popular_channels(int $limit = 5): array {
    $cacheFile = CACHE_DIR . '/guide_channels.json';
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL_CHANNEL) {
        $c = json_decode((string)file_get_contents($cacheFile), true);
        if (is_array($c) && !empty($c)) return array_slice($c, 0, $limit);
    }

    // живые каналы уровня 2012→2026 (актуальные крупные)
    $handles = ['@MrBeast', '@markiplier', '@PewDiePie', '@LinusTechTips', '@vanoss',
                '@JackSepticEye', '@Vsauce', '@smosh'];

    $channels = [];
    foreach ($handles as $h) {
        $id = _resolve_channel_handle($h);
        if ($id === null) continue;                 // не резолвится → пропуск
        $meta = _channel_meta($id);
        if ($meta === null) continue;               // мёртвый/приватный → пропуск
        $channels[] = $meta;
        if (count($channels) >= 8) break;
    }

    if (!empty($channels)) {
        @file_put_contents($cacheFile, json_encode($channels, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
    return array_slice($channels, 0, $limit);
}

$guideChannels = get_popular_channels(5);