<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  channel_api.php — данные канала для channel.php (InnerTube /browse)
//  Экспортирует переменные для существующей разметки channel.php.
//  Вход: $channel_id (UCxxx) или username/@handle (?id=…), .htaccess → /channel/ID
//
//  Если канала нет / он забанен / закрыт — $channelError != '' и channel.php
//  уводит на /oops.php. Точный текст берём из alertRenderer YouTube.
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

// ─── Defaults ─────────────────────────────────────────────────────────────────
// $Subscribed / $isOwner здесь НЕ выставляем — это user-specific данные.
// Их считает только channel.php на основе текущей сессии/куки.
$channelExists       = false;
$channelError        = '';   // '' | 'not_found' | 'terminated' | 'unavailable'
$channelErrorText    = '';   // точный текст алерта YouTube
$channelId           = '';
$channelTitle        = '';
$channelDescription  = '';
$channelAvatar       = DEFAULT_CHANNEL_AVATAR;
$channelBanner       = '';
$channelSubscribers  = '';   // «30M» (без слова subscribers)
$channelVideoCountCh = '';   // «992»
$channelTotalViews   = '';   // «50,475,425»
$channelJoined       = '';   // «Nov 12, 2005»
$channelCountry      = '';   // «United States»
$channelVanityUrl    = '';   // «http://www.youtube.com/@RickAstleyYT»
$channelKeywords     = '';
$channelLinks        = [];   // [['title','text','url','domain'], …]
$channelVideos       = [];   // сетка видео вкладки Videos
$channelPlaylists    = [];   // [['id','title','thumbnail','count'], …]
$channelFeatured     = null; // первое видео — для «featured»
// Статус подписки текущего пользователя — НЕ кэшируется (user-specific)
$channelIsSubscribed = false;

// ─── Определяем идентификатор ─────────────────────────────────────────────────
if (empty($channel_id)) {
    $channel_id = $_GET['id'] ?? '';
}
$channel_id = trim($channel_id);

// ─── Алерт-ошибка канала («This channel does not exist.» и т.п.) ──────────────
// InnerTube кладёт её в alerts[].alertRenderer с type=ERROR.
function _ch_alert_error(array $raw): ?string {
    foreach ($raw['alerts'] ?? [] as $a) {
        $r = $a['alertRenderer'] ?? ($a['alertWithButtonRenderer'] ?? null);
        if (!is_array($r) || ($r['type'] ?? '') !== 'ERROR') continue;
        $t = $r['text']['simpleText'] ?? runs_to_text($r['text']['runs'] ?? []);
        $t = trim($t);
        if ($t !== '') return $t;
    }
    return null;
}

// Классифицируем текст алерта: забанен / удалён / просто недоступен
function _ch_classify_error(string $text): string {
    $t = mb_strtolower($text);
    if (str_contains($t, 'terminated') || str_contains($t, 'violation')
        || str_contains($t, 'community guidelines')) {
        return 'terminated';
    }
    if (str_contains($t, 'does not exist') || str_contains($t, 'not found')
        || str_contains($t, 'no longer available') || str_contains($t, 'removed')) {
        return 'not_found';
    }
    return 'unavailable';
}

// ─── username / @handle → UCxxx ───────────────────────────────────────────────
function _ch_resolve(string $raw): ?string {
    if ($raw === '') return null;
    if (preg_match('/^UC[A-Za-z0-9_-]{22}$/', $raw)) return $raw;

    $url = 'https://www.youtube.com/';
    if ($raw[0] === '@') $url .= $raw;
    elseif (str_starts_with($raw, 'user/') || str_starts_with($raw, 'channel/') || str_starts_with($raw, 'c/')) $url .= $raw;
    else $url .= 'user/' . $raw;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => INNERTUBE_BASE_URL . 'navigation/resolve_url?key=' . INNERTUBE_API_KEY . '&prettyPrint=false',
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode(['url' => $url, 'context' => innertube_context()], JSON_UNESCAPED_SLASHES),
    ]);
    _itube_ssl_opts($ch);
    _itube_proxy_opts($ch);
    $res = curl_exec($ch);
    curl_close($ch);

    $d  = json_decode((string)$res, true);
    $id = $d['endpoint']['browseEndpoint']['browseId'] ?? null;
    if (is_string($id) && preg_match('/^UC[A-Za-z0-9_-]{22}$/', $id)) return $id;

    // @handle мог быть отдан как /user/<handle> — пробуем ещё раз как @
    if ($raw[0] !== '@' && !str_contains($raw, '/')) {
        $ch2 = curl_init();
        curl_setopt_array($ch2, [
            CURLOPT_URL            => INNERTUBE_BASE_URL . 'navigation/resolve_url?key=' . INNERTUBE_API_KEY . '&prettyPrint=false',
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode(
                ['url' => 'https://www.youtube.com/@' . $raw, 'context' => innertube_context()],
                JSON_UNESCAPED_SLASHES),
        ]);
        _itube_ssl_opts($ch2);
        _itube_proxy_opts($ch2);
        $res2 = curl_exec($ch2);
        curl_close($ch2);
        $d2  = json_decode((string)$res2, true);
        $id2 = $d2['endpoint']['browseEndpoint']['browseId'] ?? null;
        if (is_string($id2) && preg_match('/^UC[A-Za-z0-9_-]{22}$/', $id2)) return $id2;
    }
    return null;
}

// ─── Вкладка About: просмотры, дата регистрации, страна, ссылки ───────────────
// aboutChannelViewModel приходит только по continuation-токену панели «…more».
// Панелей в ответе несколько (шортсы, плейлисты…), и нужная — та, что открывается
// из описания в шапке. Брать «первую попавшуюся» нельзя: у части каналов About
// оказывается не первой, и данные молча теряются.
function _ch_about(array $raw): ?array {
    $ph = _sm_find_renderer($raw, 'pageHeaderViewModel');
    $token = null;

    if ($ph !== null && isset($ph['description'])) {
        $ep = _sm_find_renderer($ph['description'], 'showEngagementPanelEndpoint');
        if ($ep !== null) {
            $t = _sm_find_renderer($ep, 'continuationCommand')['token'] ?? null;
            if (is_string($t) && $t !== '') $token = $t;
        }
    }
    if ($token === null) return null;

    $page = innertube_post('browse', ['continuation' => $token]);
    if ($page === null) return null;
    return _sm_find_renderer($page, 'aboutChannelViewModel');
}

// ─── Счётчики из шапки канала ─────────────────────────────────────────────────
// Только собственные метаданные канала: сканировать весь ответ нельзя — в нём
// лежат и рекомендованные каналы, и тогда в шапку попадают ЧУЖИЕ подписчики.
// Возвращает ['subscribers'=>'508M', 'videoCount'=>'992', 'handle'=>'@MrBeast']
function _ch_header_counts(array $raw): array {
    $out = ['subscribers' => '', 'videoCount' => '', 'handle' => ''];
    $ph  = _sm_find_renderer($raw, 'pageHeaderViewModel');
    if ($ph === null) return $out;

    foreach ($ph['metadata']['contentMetadataViewModel']['metadataRows'] ?? [] as $row) {
        foreach ($row['metadataParts'] ?? [] as $part) {
            $t = trim($part['text']['content'] ?? '');
            if ($t === '') continue;
            if ($out['handle'] === '' && $t[0] === '@') {
                $out['handle'] = $t;
            } elseif ($out['subscribers'] === '' && preg_match('/^([\d][\d.,]*\s*[KMB]?)\s+subscribers?$/i', $t, $m)) {
                $out['subscribers'] = trim($m[1]);
            } elseif ($out['videoCount'] === '' && preg_match('/^([\d][\d.,]*\s*[KMB]?)\s+videos?$/i', $t, $m)) {
                $out['videoCount'] = trim($m[1]);
            }
        }
    }
    return $out;
}

// Реальный URL из ссылки канала: YouTube заворачивает их в /redirect?…&q=<url>
function _ch_unwrap_link(string $url): string {
    if ($url === '') return '';
    if (str_contains($url, '/redirect?')) {
        $qs = parse_url($url, PHP_URL_QUERY) ?? '';
        parse_str($qs, $p);
        if (!empty($p['q'])) return $p['q'];
    }
    return $url;
}

// ─── Статус подписки текущего пользователя из ответа browse ───────────────────
// Берём из SubscribeButtonView / subscribeButtonRenderer.
// Работает только если innertube_post ушёл с куками/сессией пользователя.
// В общий кэш это поле НЕ пишем (user-specific).
function _ch_extract_subscribed(array $raw): bool {
    // Современный UI: pageHeaderViewModel → actions → SubscribeButtonView
    $sb = _sm_find_renderer($raw, 'subscribeButtonViewModel');
    if (is_array($sb)) {
        // subscribeButtonContent.subscribeState.subscribed
        if (isset($sb['subscribeButtonContent']['subscribeState']['subscribed'])) {
            return (bool)$sb['subscribeButtonContent']['subscribeState']['subscribed'];
        }
        // иногда лежит прямо в корне view model
        if (isset($sb['subscribeState']['subscribed'])) {
            return (bool)$sb['subscribeState']['subscribed'];
        }
    }

    // Старый UI: subscribeButtonRenderer
    $old = _sm_find_renderer($raw, 'subscribeButtonRenderer');
    if (is_array($old) && array_key_exists('subscribed', $old)) {
        return (bool)$old['subscribed'];
    }

    // Ещё один вариант — c4TabbedHeaderRenderer
    $hdr = _sm_find_renderer($raw, 'c4TabbedHeaderRenderer');
    if (is_array($hdr)) {
        $btn = $hdr['subscribeButton']['subscribeButtonRenderer'] ?? null;
        if (is_array($btn) && array_key_exists('subscribed', $btn)) {
            return (bool)$btn['subscribed'];
        }
    }

    return false;
}

// ─── Вкладка Playlists ────────────────────────────────────────────────────────
// Возвращает [['id','title','thumbnail','count'], …]. Канал без плейлистов —
// нормальная ситуация: модуль на странице просто не рисуется.
function _ch_collect_playlists(array $node, array &$out, int $depth = 0): void {
    if ($depth > 30 || count($out) >= 12) return;
    foreach ($node as $k => $v) {
        if (!is_array($v)) continue;
        if ($k === 'lockupViewModel' && str_contains($v['contentType'] ?? '', 'PLAYLIST')) {
            $id = $v['contentId'] ?? '';
            if ($id === '') continue;
            $md    = $v['metadata']['lockupMetadataViewModel'] ?? [];
            $title = trim($md['title']['content'] ?? '');
            if ($title === '') continue;

            $tvm   = $v['contentImage']['collectionThumbnailViewModel']['primaryThumbnail']['thumbnailViewModel'] ?? [];
            $thumb = best_thumb($tvm['image']['sources'] ?? []);

            // «5 episodes» / «26 videos» — бейдж поверх обложки
            $count = '';
            foreach ($tvm['overlays'] ?? [] as $ov) {
                $b = $ov['thumbnailOverlayBadgeViewModel']['thumbnailBadges'][0]['thumbnailBadgeViewModel']['text'] ?? '';
                if ($b !== '') { $count = $b; break; }
            }
            $out[] = ['id' => $id, 'title' => $title, 'thumbnail' => $thumb, 'count' => $count];
        } else {
            _ch_collect_playlists($v, $out, $depth + 1);
        }
    }
}

// ─── Сетка видео вкладки Videos ───────────────────────────────────────────────
function _ch_collect_videos(array $node, array &$out, string $chTitle, string $chId, int $depth = 0): void {
    if ($depth > 30 || count($out) >= 30) return;
    foreach ($node as $k => $v) {
        if (!is_array($v)) continue;

        if ($k === 'lockupViewModel' && ($v['contentType'] ?? '') === 'LOCKUP_CONTENT_TYPE_VIDEO') {
            $id = $v['contentId'] ?? '';
            if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) continue;
            $md    = $v['metadata']['lockupMetadataViewModel'] ?? [];
            $title = trim($md['title']['content'] ?? '');
            if ($title === '') continue;
            $views = ''; $ago = '';
            foreach (($md['metadata']['contentMetadataViewModel']['metadataRows'] ?? []) as $row) {
                foreach ($row['metadataParts'] ?? [] as $part) {
                    $txt = trim($part['text']['content'] ?? '');
                    if ($txt === '') continue;
                    if (stripos($txt, 'view') !== false && $views === '') $views = expand_count($txt);
                    elseif (preg_match('/\bago$/i', $txt) && $ago === '') $ago = $txt;
                }
            }
            $thumb = "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";
            $tvm   = $v['contentImage']['thumbnailViewModel'] ?? [];
            $best  = best_thumb($tvm['image']['sources'] ?? []);
            if ($best !== '') $thumb = $best;
            $dur = '';
            foreach ($tvm['overlays'] ?? [] as $ov) {
                foreach ($ov['thumbnailBottomOverlayViewModel']['badges'] ?? [] as $b) {
                    $t = $b['thumbnailBadgeViewModel']['text'] ?? '';
                    if (preg_match('/^\d+:\d{2}(:\d{2})?$/', $t)) { $dur = $t; break 2; }
                }
            }
            $out[] = [
                'id' => $id, 'title' => $title, 'views' => $views,
                'ago' => $ago, 'thumbnail' => $thumb, 'duration' => $dur,
                'author' => $chTitle, 'authorId' => $chId,
            ];
        } elseif ($k === 'videoRenderer' || $k === 'gridVideoRenderer') {
            $id = $v['videoId'] ?? '';
            if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) continue;
            $title = $v['title']['simpleText'] ?? runs_to_text($v['title']['runs'] ?? []);
            if (trim($title) === '') continue;
            $thumb = best_thumb($v['thumbnail']['thumbnails'] ?? []) ?: "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";
            $out[] = [
                'id' => $id, 'title' => $title,
                'views'     => expand_count($v['viewCountText']['simpleText'] ?? ''),
                'ago'       => $v['publishedTimeText']['simpleText'] ?? '',
                'thumbnail' => $thumb,
                'duration'  => $v['lengthText']['simpleText'] ?? '',
                'author'    => $chTitle, 'authorId' => $chId,
            ];
        } else {
            _ch_collect_videos($v, $out, $chTitle, $chId, $depth + 1);
        }
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
//  Сборка данных канала (с суточным кэшем — страница делает до 4 запросов)
// ═══════════════════════════════════════════════════════════════════════════════
function ch_fetch(string $rawId): array {
    $fail = fn(string $err, string $text) => [
        'exists' => false, 'error' => $err, 'errorText' => $text,
    ];

    $id = _ch_resolve($rawId);
    if ($id === null) return $fail('not_found', 'This channel does not exist.');

    $raw = innertube_post('browse', ['browseId' => $id]);
    if ($raw === null) return $fail('unavailable', 'This channel is currently unavailable.');

    // Точная причина от YouTube: несуществующий / забаненный / закрытый канал
    $alert = _ch_alert_error($raw);
    if ($alert !== null) return $fail(_ch_classify_error($alert), $alert);

    $meta = $raw['metadata']['channelMetadataRenderer'] ?? null;
    if ($meta === null || empty($meta['title'])) {
        return $fail('not_found', 'This channel does not exist.');
    }

    $out = [
        'exists'        => true,
        'error'         => '',
        'errorText'     => '',
        'id'            => $id,
        'title'         => $meta['title'],
        'description'   => $meta['description'] ?? '',
        'keywords'      => $meta['keywords'] ?? '',
        'vanityUrl'     => $meta['vanityChannelUrl'] ?? '',
        'avatar'        => default_avatar(best_thumb($meta['avatar']['thumbnails'] ?? [])),
        'banner'        => '',
        'subscribers'   => '',
        'videoCount'    => '',
        'totalViews'    => '',
        'joined'        => '',
        'country'       => '',
        'links'         => [],
        'videos'        => [],
        'playlists'     => [],
        // user-specific — в кэш НЕ попадёт (см. ch_fetch_cached)
        'is_subscribed' => _ch_extract_subscribed($raw),
    ];

    // Баннер: pageHeaderViewModel.banner.imageBannerViewModel.image.sources[]
    // (в URL баннера слова «banner» нет — искать по подстроке бесполезно).
    // Канал может быть без баннера: тогда шапка просто чёрная, как в 2012.
    $ph = _sm_find_renderer($raw, 'pageHeaderViewModel');
    if ($ph !== null) {
        $out['banner'] = best_thumb($ph['banner']['imageBannerViewModel']['image']['sources'] ?? []);
    }

    // ── About: точные просмотры / дата / страна / ссылки ──────────────────────
    $about = _ch_about($raw);
    if ($about !== null) {
        if (!empty($about['description'])) $out['description'] = $about['description'];
        $out['country'] = $about['country'] ?? '';

        // «4.52M subscribers» → «4.52M»
        if (preg_match('/^([\d][\d.,]*\s*[KMB]?)/u', trim($about['subscriberCountText'] ?? ''), $m)) {
            $out['subscribers'] = trim($m[1]);
        }
        // «431 videos» → «431»
        if (preg_match('/^([\d][\d.,]*\s*[KMB]?)/u', trim($about['videoCountText'] ?? ''), $m)) {
            $out['videoCount'] = trim($m[1]);
        }
        // «2,513,258,892 views» → «2,513,258,892»
        if (preg_match('/^([\d][\d.,]*\s*[KMB]?)/u', trim($about['viewCountText'] ?? ''), $m)) {
            $out['totalViews'] = trim($m[1]);
        }
        // «Joined Feb 1, 2015» → «Feb 1, 2015»
        $joined = $about['joinedDateText']['content'] ?? '';
        if (preg_match('/Joined\s+(.+)$/i', trim($joined), $m)) $out['joined'] = trim($m[1]);

        if (!empty($about['canonicalChannelUrl'])) $out['vanityUrl'] = $about['canonicalChannelUrl'];

        foreach ($about['links'] ?? [] as $lnk) {
            $lv = $lnk['channelExternalLinkViewModel'] ?? null;
            if ($lv === null) continue;
            $text = $lv['link']['content'] ?? '';
            $url  = _ch_unwrap_link(
                $lv['link']['commandRuns'][0]['onTap']['innertubeCommand']['commandMetadata']['webCommandMetadata']['url'] ?? ''
            );
            if ($url === '' && $text !== '') $url = 'http://' . ltrim($text, '/');
            if ($url === '') continue;
            $out['links'][] = [
                'title'  => $lv['title']['content'] ?? $text,
                'text'   => $text !== '' ? $text : $url,
                'url'    => $url,
                'domain' => parse_url($url, PHP_URL_HOST) ?: '',
            ];
        }
    }

    // Фолбэк на счётчики из шапки, если About недоступен
    $hdr = _ch_header_counts($raw);
    if ($out['subscribers'] === '') $out['subscribers'] = $hdr['subscribers'];
    if ($out['videoCount']  === '') $out['videoCount']  = $hdr['videoCount'];
    if ($out['vanityUrl']   === '' && $hdr['handle'] !== '') {
        $out['vanityUrl'] = 'http://www.youtube.com/' . $hdr['handle'];
    }

    // ── Вкладка Videos ────────────────────────────────────────────────────────
    $vt = innertube_post('browse', ['browseId' => $id, 'params' => 'EgZ2aWRlb3PyBgQKAjoA']);
    if ($vt !== null) {
        _ch_collect_videos($vt, $out['videos'], $out['title'], $id);
    }

    // ── Вкладка Playlists ─────────────────────────────────────────────────────
    $pt = innertube_post('browse', ['browseId' => $id, 'params' => 'EglwbGF5bGlzdHPyBgQKAkIA']);
    if ($pt !== null) {
        _ch_collect_playlists($pt, $out['playlists']);
    }

    return $out;
}

// ─── Кэш собранных данных канала ──────────────────────────────────────────────
// Версия схемы кэша: поднимать при изменении набора полей ch_fetch(),
// иначе старые записи переживут деплой и страница недосчитается данных.
define('CH_CACHE_VER', 2);

function ch_fetch_cached(string $rawId): array {
    $key = CACHE_DIR . '/channel_v' . CH_CACHE_VER . '_' . sha1(mb_strtolower($rawId)) . '.json';

    if (is_file($key) && (time() - filemtime($key)) < CACHE_TTL_CHANNEL) {
        $c = json_decode((string)file_get_contents($key), true);
        if (is_array($c) && isset($c['exists'])) {
            // из кэша is_subscribed не берём — он user-specific
            unset($c['is_subscribed']);
            return $c;
        }
    }

    $data = ch_fetch($rawId);

    // В файл кэша user-specific флаг не пишем
    $toCache = $data;
    unset($toCache['is_subscribed']);
    @file_put_contents($key, json_encode($toCache, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Ошибку кэшируем ненадолго: канал могут разбанить или создать заново.
    // Сдвигаем mtime в прошлое, чтобы запись протухла через CACHE_TTL_METADATA.
    if (empty($data['exists'])) {
        @touch($key, time() - CACHE_TTL_CHANNEL + CACHE_TTL_METADATA);
    }
    return $data;
}

// ═══════════════════════════════════════════════════════════════════════════════
//  Экспорт переменных для channel.php
// ═══════════════════════════════════════════════════════════════════════════════
if ($channel_id === '') {
    $channelError     = 'not_found';
    $channelErrorText = 'This channel does not exist.';
} else {
    $d = ch_fetch_cached($channel_id);

    if (empty($d['exists'])) {
        $channelError     = $d['error']     ?? 'unavailable';
        $channelErrorText = $d['errorText'] ?? '';
    } else {
        $channelExists       = true;
        $channelId           = $d['id'];
        $channelTitle        = $d['title'];
        $channelDescription  = $d['description'];
        $channelKeywords     = $d['keywords'];
        $channelVanityUrl    = $d['vanityUrl'];
        $channelAvatar       = $d['avatar'];
        $channelBanner       = $d['banner'];
        $channelSubscribers  = $d['subscribers'];
        $channelVideoCountCh = $d['videoCount'];
        $channelTotalViews   = $d['totalViews'];
        $channelJoined       = $d['joined'];
        $channelCountry      = $d['country'];
        $channelLinks        = $d['links'];
        $channelVideos       = $d['videos'];
        $channelPlaylists    = $d['playlists'] ?? [];
        $channelFeatured     = $channelVideos[0] ?? null;
        // Только если данные свежие (не из кэша) — берём статус из InnerTube
        if (array_key_exists('is_subscribed', $d)) {
            $channelIsSubscribed = (bool)$d['is_subscribed'];
        }
    }
}

// Заголовок страницы
$title = $channelExists ? $channelTitle : 'YouTube';
