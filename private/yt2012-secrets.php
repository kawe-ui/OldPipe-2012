<?php
return [
    'YT_APP_URL'              => 'http://localhost/',
    'YT_APP_ENV'              => 'development',

    // OAuth client (type: ‘Web application’) from Google Cloud → APIs & Services →
    // Credentials. The client’s redirect URI: http://localhost/auth/google/callback
    'YT_GOOGLE_CLIENT_ID'     => 'YOUR_CLIENT_ID.apps.googleusercontent.com',
    'YT_GOOGLE_CLIENT_SECRET' => 'YOUR_CLIENT_SECRET',

    // Optional: API key for the fallback channel resolver via @handle
    // (Data API). The input works without it, but without this fallback.
    'YT_GOOGLE_API_KEY'       => '',

    // Proxy for external requests (InnerTube, RYD, googlevideo). Empty = none.
    'YT_PROXY_HOST'           => '',
    'YT_PROXY_PORT'           => '',
    'YT_PROXY_TYPE'           => 'http',   // http | socks5
    'YT_PROXY_USER'           => '',
    'YT_PROXY_PASS'           => '',
];
