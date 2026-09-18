<?php
/**
 * videos.php — /videos (archive 2012-08-02 layout)
 * Секции: Most Popular + категории + featured (как в archive)
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php';

$title = 'Videos';

function _videos_parse_vr(array $vr): ?array {
    $videoId = $vr['videoId'] ?? '';
    if ($videoId === '') return null;
    foreach ($vr['thumbnailOverlays'] ?? [] as $ov) {
        $style = $ov['thumbnailOverlayTimeStatusRenderer']['style'] ?? '';
        if (in_array($style, ['LIVE', 'SHORTS'], true)) return null;
    }
    $title = '';
    if (!empty($vr['title']['runs'])) {
        foreach ($vr['title']['runs'] as $r) $title .= $r['text'] ?? '';
    } elseif (!empty($vr['title']['simpleText'])) {
        $title = $vr['title']['simpleText'];
    }
    if (trim($title) === '') return null;

    $channelTitle = '';
    $channelId = '';
    foreach (['ownerText', 'shortBylineText', 'longBylineText'] as $f) {
        if (!empty($vr[$f]['runs'][0])) {
            $channelTitle = $vr[$f]['runs'][0]['text'] ?? '';
            $channelId = $vr[$f]['runs'][0]['navigationEndpoint']['browseEndpoint']['browseId'] ?? '';
            break;
        }
    }

    $thumbArr = $vr['thumbnail']['thumbnails'] ?? [];
    $thumb = "https://i.ytimg.com/vi/{$videoId}/mqdefault.jpg";
    if (!empty($thumbArr)) {
        usort($thumbArr, fn($a, $b) => (int)($b['width'] ?? 0) - (int)($a['width'] ?? 0));
        if (!empty($thumbArr[0]['url'])) $thumb = $thumbArr[0]['url'];
    }

    $durationText = $vr['lengthText']['simpleText'] ?? '';
    if ($durationText === '') {
        foreach ($vr['thumbnailOverlays'] ?? [] as $ov) {
            $durationText = $ov['thumbnailOverlayTimeStatusRenderer']['text']['simpleText'] ?? '';
            if ($durationText !== '') break;
        }
    }

    $viewsRaw = $vr['viewCountText']['simpleText']
        ?? ($vr['viewCountText']['runs'][0]['text'] ?? '');
    $views = '';
    if (function_exists('expand_count') && $viewsRaw !== '') {
        $views = expand_count($viewsRaw);
    }
    if ($views === '' && preg_match('/([\d][\d\s,\.]*[\d])/', $viewsRaw, $m)) {
        $views = trim($m[1]);
    }

    return [
        'id' => $videoId,
        'title' => $title,
        'author' => $channelTitle,
        'authorId' => $channelId,
        'thumbnail' => $thumb,
        'duration' => $durationText,
        'views' => $views,
        'ago' => $vr['publishedTimeText']['simpleText'] ?? '',
    ];
}

function _videos_collect(array $node, array &$out, int $depth = 0): void {
    if ($depth > 30 || count($out) >= 48) return;
    foreach ($node as $key => $value) {
        if (!is_array($value)) continue;
        switch ($key) {
            case 'videoRenderer':
            case 'gridVideoRenderer':
                $p = _videos_parse_vr($value);
                if ($p !== null) $out[] = $p;
                break;
            case 'richItemRenderer':
                $inner = $value['content'] ?? [];
                if (!empty($inner['videoRenderer'])) {
                    $p = _videos_parse_vr($inner['videoRenderer']);
                    if ($p !== null) $out[] = $p;
                } else {
                    _videos_collect($inner, $out, $depth + 1);
                }
                break;
            case 'lockupViewModel':
                $ctype = (string)($value['contentType'] ?? '');
                if ($ctype === 'LOCKUP_CONTENT_TYPE_VIDEO' || str_contains($ctype, 'VIDEO')) {
                    $id = $value['contentId'] ?? '';
                    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) break;
                    $md = $value['metadata']['lockupMetadataViewModel'] ?? [];
                    $title = trim($md['title']['content'] ?? '');
                    if ($title === '') break;
                    $views = ''; $ago = ''; $author = '';
                    foreach (($md['metadata']['contentMetadataViewModel']['metadataRows'] ?? []) as $row) {
                        foreach ($row['metadataParts'] ?? [] as $part) {
                            $txt = trim($part['text']['content'] ?? '');
                            if ($txt === '') continue;
                            if (stripos($txt, 'view') !== false && $views === '') {
                                $views = function_exists('expand_count') ? expand_count($txt) : $txt;
                            } elseif (preg_match('/\bago$/i', $txt) && $ago === '') {
                                $ago = $txt;
                            } elseif ($author === '' && !preg_match('/\d/', $txt) && stripos($txt, 'view') === false) {
                                $author = $txt;
                            }
                        }
                    }
                    $thumb = "https://i.ytimg.com/vi/{$id}/mqdefault.jpg";
                    $tvm = $value['contentImage']['thumbnailViewModel'] ?? [];
                    if (function_exists('best_thumb')) {
                        $best = best_thumb($tvm['image']['sources'] ?? []);
                        if ($best !== '') $thumb = $best;
                    }
                    $dur = '';
                    foreach ($tvm['overlays'] ?? [] as $ov) {
                        foreach ($ov['thumbnailBottomOverlayViewModel']['badges'] ?? [] as $b) {
                            $t = $b['thumbnailBadgeViewModel']['text'] ?? '';
                            if (preg_match('/^\d+:\d{2}(:\d{2})?$/', $t)) { $dur = $t; break 2; }
                        }
                    }
                    $out[] = [
                        'id' => $id, 'title' => $title, 'author' => $author, 'authorId' => '',
                        'thumbnail' => $thumb, 'duration' => $dur, 'views' => $views, 'ago' => $ago,
                    ];
                }
                break;
            default:
                _videos_collect($value, $out, $depth + 1);
        }
    }
}

function videos_views_label($raw): string {
    $raw = trim((string)$raw);
    if ($raw === '' || $raw === '0') return '';
    if (stripos($raw, 'view') !== false) return $raw;
    if (preg_match('/^\d+$/', $raw)) {
        return number_format((int)$raw, 0, '.', ',') . ' views';
    }
    return $raw . ' views';
}

function videos_fetch_list(string $mode, string $query = '', int $limit = 8): array {
    $out = [];
    if ($mode === 'trending') {
        foreach (['FEtrending', 'FEwhat_to_watch'] as $bid) {
            $res = innertube_post('browse', ['browseId' => $bid]);
            if ($res !== null && !empty($res['contents'])) {
                _videos_collect($res['contents'] ?? $res, $out);
                if ($out !== []) break;
            }
        }
    }
    if ($out === [] && $query !== '') {
        $sf = innertube_post('search', ['query' => $query]);
        if ($sf !== null) _videos_collect($sf, $out);
    }
    $seen = [];
    $out = array_values(array_filter($out, function ($v) use (&$seen) {
        if (empty($v['id']) || isset($seen[$v['id']])) return false;
        $seen[$v['id']] = true;
        return true;
    }));
    return array_slice($out, 0, $limit);
}

// Секции как в archive 2012-08-02
$sectionDefs = [
    ['title' => 'Most Popular',           'href' => '/videos',        'icon' => 'most-viewed', 'mode' => 'trending', 'query' => 'popular'],
    ['title' => 'Autos & Vehicles',       'href' => '/autos',         'icon' => '', 'mode' => 'search', 'query' => 'autos vehicles'],
    ['title' => 'Comedy',                 'href' => '/comedy',        'icon' => '', 'mode' => 'search', 'query' => 'comedy'],
    ['title' => 'Entertainment',          'href' => '/entertainment', 'icon' => '', 'mode' => 'search', 'query' => 'entertainment'],
    ['title' => 'Film & Animation',       'href' => '/film',          'icon' => '', 'mode' => 'search', 'query' => 'film animation'],
    ['title' => 'Gaming',                 'href' => '/gaming',        'icon' => '', 'mode' => 'search', 'query' => 'gaming'],
    ['title' => 'Howto & Style',          'href' => '/howto',         'icon' => '', 'mode' => 'search', 'query' => 'howto style'],
    ['title' => 'Nonprofits & Activism',  'href' => '/activism',      'icon' => '', 'mode' => 'search', 'query' => 'nonprofits activism'],
    ['title' => 'People & Blogs',         'href' => '/people',        'icon' => '', 'mode' => 'search', 'query' => 'vlog'],
    ['title' => 'Pets & Animals',         'href' => '/pets',          'icon' => '', 'mode' => 'search', 'query' => 'pets animals'],
    ['title' => 'Science & Technology',   'href' => '/science',       'icon' => '', 'mode' => 'search', 'query' => 'science technology'],
    ['title' => 'Travel & Events',        'href' => '/travel',        'icon' => '', 'mode' => 'search', 'query' => 'travel'],
    // Featured channels / topics from archive
    ['title' => 'CollegeHumor Originals', 'href' => '/user/collegehumor', 'icon' => '', 'mode' => 'search', 'query' => 'CollegeHumor'],
    ['title' => 'The Annoying Orange',    'href' => '/user/realannoyingorange', 'icon' => '', 'mode' => 'search', 'query' => 'Annoying Orange'],
    ['title' => 'YOGSCAST Lewis & Simon', 'href' => '/user/BlueXephos', 'icon' => '', 'mode' => 'search', 'query' => 'Yogscast'],
    ['title' => 'Jenna Marbles',          'href' => '/user/JennaMarbles', 'icon' => '', 'mode' => 'search', 'query' => 'Jenna Marbles'],
];

$cacheKey = (defined('CACHE_DIR') ? CACHE_DIR : sys_get_temp_dir()) . '/videos_sections_v1.json';
$ttl = defined('CACHE_TTL_METADATA') ? (int)CACHE_TTL_METADATA : 3600;
$sections = [];

if (is_file($cacheKey) && (time() - filemtime($cacheKey)) < $ttl) {
    $c = json_decode((string)file_get_contents($cacheKey), true);
    if (is_array($c) && !empty($c['sections'])) {
        $sections = $c['sections'];
    }
}

if ($sections === []) {
    foreach ($sectionDefs as $def) {
        $list = videos_fetch_list(
            $def['mode'] === 'trending' ? 'trending' : 'search',
            $def['query'],
            8
        );
        $sections[] = [
            'title'  => $def['title'],
            'href'   => $def['href'],
            'icon'   => $def['icon'] ?? '',
            'videos' => $list,
        ];
    }
    @file_put_contents(
        $cacheKey,
        json_encode(['sections' => $sections, 'ts' => time()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
}

if (isset($_GET['debug'])) {
    header('Content-Type: text/plain; charset=utf-8');
    foreach ($sections as $s) {
        echo $s['title'] . ': ' . count($s['videos']) . "\n";
    }
    exit;
}

$categories = [
    ['href' => '/videos',        'label' => 'All Categories',        'selected' => true],
    ['href' => '/recommended',   'label' => 'Recommended for You',   'selected' => false],
    ['href' => '/autos',         'label' => 'Autos & Vehicles',      'selected' => false],
    ['href' => '/comedy',        'label' => 'Comedy',                'selected' => false],
    ['href' => '/entertainment', 'label' => 'Entertainment',         'selected' => false],
    ['href' => '/film',          'label' => 'Film & Animation',      'selected' => false],
    ['href' => '/gaming',        'label' => 'Gaming',                'selected' => false],
    ['href' => '/howto',         'label' => 'Howto & Style',         'selected' => false],
    ['href' => '/activism',      'label' => 'Nonprofits & Activism', 'selected' => false],
    ['href' => '/people',        'label' => 'People & Blogs',        'selected' => false],
    ['href' => '/pets',          'label' => 'Pets & Animals',        'selected' => false],
    ['href' => '/science',       'label' => 'Science & Technology',  'selected' => false],
    ['href' => '/travel',        'label' => 'Travel & Events',       'selected' => false],
];

function render_browse_item(array $v): void {
    $id = htmlspecialchars($v['id'] ?? '', ENT_QUOTES, 'UTF-8');
    $title = htmlspecialchars($v['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $thumb = htmlspecialchars($v['thumbnail'] ?? ('https://i.ytimg.com/vi/' . ($v['id'] ?? '') . '/mqdefault.jpg'), ENT_QUOTES, 'UTF-8');
    $dur = htmlspecialchars($v['duration'] ?? '', ENT_QUOTES, 'UTF-8');
    $views = htmlspecialchars(videos_views_label($v['views'] ?? ''), ENT_QUOTES, 'UTF-8');
    $ago = htmlspecialchars($v['ago'] ?? '', ENT_QUOTES, 'UTF-8');
    $author = htmlspecialchars($v['author'] ?? '', ENT_QUOTES, 'UTF-8');
    $authorId = htmlspecialchars($v['authorId'] ?? '', ENT_QUOTES, 'UTF-8');
    $href = '/watch?v=' . $id . '&feature=b-mv';
    ?>
        <div class="browse-item yt-tile-default ">
    <a href="<?php echo $href ?>" class="ux-thumb-wrap yt-uix-sessionlink yt-uix-contextlink contains-addto " data-sessionlink="feature=b-mv"><span class="video-thumb ux-thumb yt-thumb-default-194 "><span class="yt-thumb-clip"><span class="yt-thumb-clip-inner"><img src="<?php echo $thumb ?>" alt="Thumbnail" width="194"><span class="vertical-align"></span></span></span></span><?php if ($dur !== ''): ?><span class="video-time"><?php echo $dur ?></span><?php endif; ?>
  <button onclick=";return false;" title="Watch Later" type="button" class="addto-button video-actions addto-watch-later-button-sign-in yt-uix-button yt-uix-button-default yt-uix-button-short yt-uix-tooltip" data-button-menu-id="shared-addto-watch-later-login" data-video-ids="<?php echo $id ?>" role="button"><span class="yt-uix-button-content">  <img src="/yts/img/pixel-vfl3z5WfW.gif" alt="Watch Later">
 </span><img class="yt-uix-button-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt=""></button>
</a>
    <div class="browse-item-content">
        <h3 dir="ltr">
<a href="<?php echo $href ?>" title="<?php echo $title ?>" class="yt-uix-sessionlink " data-sessionlink="feature=b-mv">
      <?php echo $title ?>
</a>  </h3>
      <div class="browse-item-info">
        <div class="metadata-line">
            <?php if ($views !== ''): ?><span class="viewcount"><?php echo $views ?></span><?php endif; ?>
          <?php if ($views !== '' && $ago !== ''): ?><span class="metadata-separator">|</span><?php endif; ?>
            <?php if ($ago !== ''): ?><span class="video-date-added"><?php echo $ago ?></span><?php endif; ?>
        </div>
        <?php if ($author !== ''): ?>
        <div class="yt-user-name-wrapper">
          <?php if ($authorId !== ''): ?>
          <a href="/channel/<?php echo $authorId ?>" class="yt-user-name" dir="ltr"><?php echo $author ?></a>
          <?php else: ?>
          <span class="yt-user-name" dir="ltr"><?php echo $author ?></span>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
    <?php
}

function render_collection(array $section): void {
    $vids = $section['videos'] ?? [];
    if ($vids === []) return;
    $title = htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8');
    $href  = htmlspecialchars($section['href'], ENT_QUOTES, 'UTF-8');
    $icon  = $section['icon'] ?? '';
    $ids = array_slice(array_column($vids, 'id'), 0, 8);
    $playAll = $ids
        ? '/watch_videos?video_ids=' . rawurlencode(implode(',', $ids)) . '&type=0&title=' . rawurlencode($section['title'])
        : $href;
    ?>
  <div class="browse-collection  has-box-ad">
    <div class="ytg-box collection-header with-icon">
      <a class="heading ytg-box" href="<?php echo $href ?>">
          <?php if ($icon !== ''): ?>
          <img class="header-icon <?php echo htmlspecialchars($icon) ?>" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
          <?php endif; ?>
        <div class="header-container">
          <h2><?php echo $title ?> »</h2>
        </div>
      </a>
        <a class="yt-playall-link yt-playall-link-default yt-uix-sessionlink " href="<?php echo htmlspecialchars($playAll) ?>">
    <img class="small-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt="">
Play all
  </a>
    </div>
<?php
    $row = [];
    foreach ($vids as $v) {
        $row[] = $v;
        if (count($row) === 4) {
            echo '          <div class="browse-item-row ytg-box">' . "\n";
            foreach ($row as $item) render_browse_item($item);
            echo "          </div>\n";
            $row = [];
        }
    }
    if ($row !== []) {
        echo '          <div class="browse-item-row ytg-box">' . "\n";
        foreach ($row as $item) render_browse_item($item);
        echo "          </div>\n";
    }
?>
  </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <title>Videos - YouTube</title>
  <link rel="search" type="application/opensearchdescription+xml" href="/opensearch?locale=en_US" title="YouTube Video Search">
  <link rel="icon" href="/yts/favicon-vfldLzJxy.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/yts/favicon-vfldLzJxy.ico" type="image/x-icon">
  <meta name="description" content="Share your videos with friends, family, and the world">
  <link id="www-core-css" rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">
  <link rel="stylesheet" href="/yts/cssbin/www-the-rest-vflNb6rAI.css">
  <link rel="stylesheet" href="/yts/cssbin/www-refresh-browse-vfl7Uzt8r.css">
  <link rel="stylesheet" href="/yts/cssbin/www-videos-nav-vflD5UeRf.css">
</head>
<body id="" class="date-20120802 en_US ltr ytg-old-clearfix " dir="ltr">

  <form name="logoutForm" method="POST" action="/logout">
    <input type="hidden" name="action_logout" value="1">
  </form>

  <div id="page" class="  browse-base browse-videos">
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>

  <div id="content-container">
    <div id="baseDiv" class="date-20120802 video-info ">
      <div id="alerts"></div>

      <div id="masthead-subnav" class="yt-nav yt-nav-dark">
    <ul>
      <li class=" selected"><span class="yt-nav-item">Videos</span></li>
      <li><a class="yt-nav-item" href="/music">Music</a></li>
      <li><a class="yt-nav-item" href="/movies">Movies</a></li>
      <li><a class="yt-nav-item" href="/shows">Shows</a></li>
      <li><a class="yt-nav-item" href="/live">Live</a></li>
      <li><a class="yt-nav-item" href="/sports">Sports</a></li>
      <li><a class="yt-nav-item" href="/education">Education</a></li>
      <li><a class="yt-nav-item" href="/news">News</a></li>
    </ul>
  </div>

  <div class="ytg-fl browse-header"></div>

  <div class="browse-container ytg-wide ytg-box no-stage browse-bg-gradient">
    <div class="ytg-fl browse-content">
      <div id="browse-side-column" class="ytg-2col ytg-last">
          <ol class="navigation-menu">
<?php foreach ($categories as $cat): ?>
      <li class="menu-item">
        <a class="<?php echo !empty($cat['selected']) ? 'selected' : '' ?>" href="<?php echo htmlspecialchars($cat['href']) ?>">
          <?php echo htmlspecialchars($cat['label']) ?>
        </a>
      </li>
<?php endforeach; ?>
  </ol>
      </div>

      <div id="browse-main-column" class="ytg-4col">
<?php
$any = false;
foreach ($sections as $section) {
    if (!empty($section['videos'])) {
        $any = true;
        render_collection($section);
    }
}
if (!$any) {
    echo '<p style="padding:20px;color:#333">No videos available right now.</p>';
}
?>
      </div>
    </div>
  </div>

    </div>
  </div>

<?php
if (is_file($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
}
?>
  </div>
</body>
</html>