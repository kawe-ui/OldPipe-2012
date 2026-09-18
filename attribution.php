<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/attribution_api.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php';

// Если видео нет — можно редиректить или показать заглушку
if (!$attrExists) {
    $pageTitle = 'YouTube';
} else {
    $pageTitle = htmlspecialchars($attrTitle) . ' - Attribution';
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <title>YouTube - Broadcast Yourself.</title>
  <link rel="search" type="application/opensearchdescription+xml" href="/opensearch?locale=en_US" title="YouTube Video Search">
  <link rel="icon" href="/yts/img/favicon-vfldLzJxy.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/yts/img/favicon-vfldLzJxy.ico" type="image/x-icon">
  <link rel="icon" href="/yts/img/favicon_32-vflWoMFGx.png" sizes="32x32">
  <meta name="description" content="Share your videos with friends, family, and the world">
  <meta name="keywords" content="video, sharing, camera phone, video phone, free, upload">
  <link id="www-core-css" rel="stylesheet" href="/yts/cssbin/www-refresh-vflOJ_8Rx.css">
    <link id="css-617957165" rel="stylesheet" href="/yts/cssbin/www-core-vflJ0FjpG.css">
  <link rel="stylesheet" href="/yts/cssbin/www-the-rest-vflNb6rAI.css">
  <link rel="stylesheet" href="/yts/cssbin/www-attribution-vflDF7tHf.css">
</head>
<body id="" class="date-20120115 en_US ltr thumb-normal" dir="ltr">

<form name="logoutForm" method="POST" action="/logout">
  <input type="hidden" name="action_logout" value="1">
</form>

<!-- begin page -->
<div id="page" class="">

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?>

  <div id="content-container">
    <!-- begin content -->
    <div id="content">
      <div id="attribution-container" class="ytg-base">
        <h2>Video Attribution Information</h2>

        <?php if (!$attrExists): ?>
        <div class="video-details">
          <p style="padding:16px 0">
            <?php if (($attrError ?? '') === 'invalid_id'): ?>
              Invalid or missing video ID.
            <?php else: ?>
              This video is unavailable.
            <?php endif; ?>
          </p>
        </div>
        <?php else: ?>

        <div class="video-details">
          <div class="video-entry">
            <a href="/watch?v=<?php echo htmlspecialchars($attrVideoId); ?>" class="ux-thumb-wrap contains-addto">
              <span class="video-thumb ux-thumb ux-thumb-288">
                <span class="clip">
                  <span class="clip-inner">
                    <img alt="Thumbnail" src="<?php echo htmlspecialchars($attrThumbnail); ?>">
                    <span class="vertical-align"></span>
                  </span>
                </span>
              </span>
              <?php if ($attrDuration !== ''): ?>
              <span class="video-time"><?php echo htmlspecialchars($attrDuration); ?></span>
              <?php endif; ?>
            </a>
            <p>
              <span class="video-title"><?php echo htmlspecialchars($attrTitle); ?></span>
              <span class="video-username">
                <span class="username-prepend">by</span>
                <?php if ($attrAuthorUrl !== ''): ?>
                  <a href="<?php echo htmlspecialchars($attrAuthorUrl); ?>" class="yt-user-name" dir="ltr"><?php echo htmlspecialchars($attrAuthor); ?></a>
                <?php else: ?>
                  <span class="yt-user-name" dir="ltr"><?php echo htmlspecialchars($attrAuthor); ?></span>
                <?php endif; ?>
              </span>
            </p>
          </div>
        </div>

        <?php endif; ?>
      </div>
    </div>
    <!-- end content -->
  </div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>

</div>
<!-- end page -->

<script id="www-core-js" src="/yts/jsbin/www-core-vflaZ7PDD.js"></script>
</body>
</html>