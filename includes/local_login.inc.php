<?php
if (!function_exists('yt_local_logout')) {
    function yt_local_logout(): void {
        if (isset($_SESSION['yt_local_user'])) {
            unset($_SESSION['yt_local_user']);
        }
    }
}