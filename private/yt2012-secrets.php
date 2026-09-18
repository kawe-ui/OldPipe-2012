<?php
// ═══════════════════════════════════════════════════════════════════════════════
//  yt2012-secrets.php — секреты YT2012-реставрации.
//
//  ЛЕЖИТ ВНЕ web root (C:\xampp\private, НЕ внутри htdocs) и НЕ в git.
//  Подключается на лету из includes/config.inc.php → yt_secret_config().
//
//  ВАЖНО: ключи массива обязаны начинаться с префикса YT_ — код читает их
//  через yt_env('YT_GOOGLE_CLIENT_ID') и т.д. Имена без префикса
//  (GOOGLE_CLIENT_ID) НЕ подхватятся.
//
//  Замени значения YOUR_... на реальные из Google Cloud Console. Пока они
//  начинаются с «YOUR_», приложение считает OAuth ненастроенным и показывает
//  страницу-заглушку (это защита от попытки войти с фейковыми данными).
// ═══════════════════════════════════════════════════════════════════════════════

return [
    'YT_APP_URL'              => 'http://localhost:5000/',
    'YT_APP_ENV'              => 'development',

    // OAuth-клиент (тип «Web application») из Google Cloud → APIs & Services →
    // Credentials. Redirect URI у клиента: http://localhost/auth/google/callback
    'YT_GOOGLE_CLIENT_ID'     => '556983179587-1dsrsbvto710od5libs4a2nkaivj3930.apps.googleusercontent.com',
    'YT_GOOGLE_CLIENT_SECRET' => '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',

    // Необязательно: API-ключ для запасного резолва канала по @handle
    // (Data API). Без него вход работает, просто без этого фолбэка.
    'YT_GOOGLE_API_KEY'       => '',
];
