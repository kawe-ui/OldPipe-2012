<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  watch_404_api.php — реальная причина недоступности видео для watch_404.php
//  Экспортирует:
//    $unavailableMessage    — главное сообщение («This video is private.»)
//    $unavailableSubmessage — пояснение YouTube, если есть
//    $unavailableStatus     — статус playability (ERROR / LOGIN_REQUIRED / UNPLAYABLE)
//
//  Причина берётся из playabilityStatus ANDROID-клиента InnerTube: голый WEB
//  без аттестации отвечает UNPLAYABLE даже на живые видео, и его reason
//  («The page needs to be reloaded») — мусор. ANDROID же честно различает
//  private / removed / terminated account / geo-block и отдаёт тексты YouTube.
// ═══════════════════════════════════════════════════════════════════════════════

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/servermain.php');

$unavailableMessage    = 'This video is unavailable.';
$unavailableSubmessage = '';
$unavailableStatus     = 'ERROR';

if (empty($video_id)) {
    $video_id = isset($_GET['v']) ? trim($_GET['v']) : '';
}

if (preg_match('/^[A-Za-z0-9_-]{11}$/', (string)$video_id) === 1) {
    $cacheFile = CACHE_DIR . '/unavail_' . $video_id . '.json';
    $ps = null;
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL_METADATA) {
        $c = json_decode((string)@file_get_contents($cacheFile), true);
        if (is_array($c) && isset($c['status'])) $ps = $c;
    }

    if ($ps === null) {
        $d = innertube_post_as('player',
            ['videoId' => $video_id, 'racyCheckOk' => true, 'contentCheckOk' => true],
            ['clientName' => 'ANDROID', 'clientVersion' => '20.10.38',
             'androidSdkVersion' => 30, 'osName' => 'Android', 'osVersion' => '11'],
            ['User-Agent: com.google.android.youtube/20.10.38 (Linux; U; Android 11) gzip',
             'X-YouTube-Client-Name: 3', 'X-YouTube-Client-Version: 20.10.38']);
        if ($d !== null) {
            $raw = $d['playabilityStatus'] ?? [];
            $err = $raw['errorScreen']['playerErrorMessageRenderer'] ?? [];

            $reason = $raw['reason']
                ?? ($err['reason']['simpleText']
                ?? implode('', array_map(fn($r) => $r['text'] ?? '', $err['reason']['runs'] ?? [])));
            $sub = $err['subreason']['simpleText']
                ?? implode('', array_map(fn($r) => $r['text'] ?? '', $err['subreason']['runs'] ?? []));

            $ps = [
                'status'    => (string)($raw['status'] ?? 'ERROR'),
                'reason'    => trim((string)$reason),
                'subreason' => trim((string)$sub),
            ];
            @file_put_contents($cacheFile, json_encode($ps, JSON_UNESCAPED_UNICODE), LOCK_EX);
        }
    }

    if (is_array($ps)) {
        $unavailableStatus = $ps['status'] !== '' ? $ps['status'] : 'ERROR';
        if ($ps['reason'] !== '') {
            $unavailableMessage = $ps['reason'];
            // стиль 2012: сообщение оканчивается точкой
            if (!preg_match('/[.!?]$/u', $unavailableMessage)) $unavailableMessage .= '.';
        }
        $unavailableSubmessage = $ps['subreason'];
    }
}
