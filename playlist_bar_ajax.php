<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');
$PlaylistsHTML = <<<HTML
<div class="yt-uix-slider yt-rounded" id="masthead-expanded-lists-content" data-slider-slide-selected="1"
    data-slider-slides="1">
    <button class="yt-uix-button yt-uix-button-default yt-uix-slider-prev" rel="prev"><img
            class="yt-uix-slider-prev-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt="previous"></button>
    <button class="yt-uix-button yt-uix-button-default yt-uix-slider-next" rel="next"><img
            class="yt-uix-slider-next-arrow" src="/yts/img/pixel-vfl3z5WfW.gif" alt="next"></button>
    <div class="yt-uix-slider-body">
        <div class="yt-uix-slider-slides">
            <ul class="yt-uix-slider-slide ">
                <li class="yt-uix-slider-slide-item ">
                    <div class="playlist-bar-item yt-tile-default yt-tile-static">
                        <span class="ux-thumb-wrap"><span class="video-thumb ux-thumb yt-thumb-default-106 ">
                                <span class="yt-thumb-clip"><span class="yt-thumb-clip-inner">
                                        <img alt="title" src="/yts/img/pbar_fav.jpg" width="106">
                                        <span class="vertical-align"></span></span></span></span>
                        </span>
                        </span>
                        <a href="#"><span dir="ltr" class="list-title">Favorites</span>
                            <br><br><br>
                            No videos
                            <br>
                            View list
                        </a>

                    </div>
                </li>
                <li class="yt-uix-slider-slide-item ">
                    <div class="playlist-bar-item yt-tile-default yt-tile-static">
                        <span class="ux-thumb-wrap"><span class="video-thumb ux-thumb yt-thumb-default-106 ">
                                <span class="yt-thumb-clip"><span class="yt-thumb-clip-inner">
                                        <img alt="title" src="/yts/img/pbar_wl.jpg" width="106">
                                        <span class="vertical-align"></span></span></span></span>
                        </span>
                        </span>
                        <a href="#"><span dir="ltr" class="list-title">Watch later</span>
                            <br><br><br>
                            No videos
                            <br>
                            View list
                        </a>

                    </div>
                </li>
                <li class="yt-uix-slider-slide-item ">
                    <div class="playlist-bar-item yt-tile-default yt-tile-static">
                        <span class="ux-thumb-wrap"><span class="video-thumb ux-thumb yt-thumb-default-106 ">
                                <span class="yt-thumb-clip"><span class="yt-thumb-clip-inner">
                                        <img alt="title" src="/yts/img/pbar_like.jpg" width="106">
                                        <span class="vertical-align"></span></span></span></span>
                        </span>
                        </span>
                        <a href="#"><span dir="ltr" class="list-title">Likes</span>
                            <br><br><br>
                            No videos
                            <br>
                            View list
                        </a>

                    </div>
                </li>
                <li>
                    <hr>
                </li>
            </ul>
        </div>
    </div>
</div>

HTML;
echo json_encode([
    'html' => $PlaylistsHTML,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;