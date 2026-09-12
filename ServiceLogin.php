<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/session.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.inc.php';

if (yt_is_logged_in()) {
    header('Location: /');
} else {
    header('Location: /auth/google/login?return=%2F');
}
exit;
?>
