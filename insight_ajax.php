<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  insight_ajax.php — панель «Video statistics» watch (протокол 2012, XML)
//  GET /insight_ajax?action_get_statistics_and_data=1&v=ID
//  www-core: yt.www.watch.actions.stats → Em(html_content) в watch-actions-panel.
//
//  Разметка и конверт скопированы с настоящего ответа www.youtube.com декабря
//  2012 (web.archive.org/web/20121215154448/…insight_ajax…): watch-actions-stats
//  → stats-header → views → stats-box → stats-big-chart + stats-views. Все
//  классы стилизует www-core-vflJ0FjpG.css, который watch.php уже грузит.
//  Конверт оригинала: html_content ПЕРВЫМ, return_code в CDATA — именно так.
//
//  График оригинал брал у chart.apis.google.com (сервис мёртв с 2019-го) —
//  теперь его рисует /insight_chart по реальной истории просмотров из Wayback
//  Machine. Счётчик — точный из InnerTube через yt_video_metadata() (единый
//  источник с More info). Секции Key discovery events / Audience не воспроиз-
//  водятся: реферальные события и демография есть только у YouTube, публичных
//  источников нет — выдумывать их значило бы врать.
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

header('Content-Type: text/xml; charset=utf-8');
header('Cache-Control: no-cache');

$video_id = $_GET['v'] ?? '';
$meta = preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id) ? yt_video_metadata($video_id) : null;
$ok   = $meta !== null && ($meta['status'] ?? '') === 'OK' && (int)$meta['viewCount'] > 0;

$chartUrl = '/insight_chart?v=' . rawurlencode($video_id);

ob_start();
// после <![CDATA[ у оригинала ровно 7 пустых строк; PHP съедает первый
// перевод строки после закрывающего тега, поэтому в шаблоне их 8
?>








    <div id="watch-actions-stats" class="watch-actions-stats">
        <div class="stats-header">
    <h1>
Video statistics    </h1>
<?php if (!$ok): ?>
    <p>Statistics are currently unavailable.</p>
<?php endif; ?>
  </div>

<?php if ($ok): ?>
          <div class="views">
      <h2>Views and discovery</h2>
      <div class="stats-box yt-uix-expander yt-uix-expander-collapsed">
          <div class="stats-big-chart">
            <img class="stats-big-chart-collapsed" src="<?php echo $chartUrl ?>" alt="">
              <img class="stats-big-chart-expanded" src="<?php echo $chartUrl ?>" alt="">
          </div>

        <div class="stats-views">
<h3><?php echo number_format((int)$meta['viewCount'], 0, '.', ',') ?></h3>Views
        </div>
  </div>
     </div>
    <div class="clearL"></div>
<?php endif; ?>
  </div>







<?php
// …и 7 пустых строк перед ]]> — как в оригинале
$html = ob_get_clean();

echo '<?xml version="1.0" encoding="utf-8"?>'
   . '<root><html_content><![CDATA[' . $html . ']]></html_content>'
   . '<return_code><![CDATA[0]]></return_code></root>';
