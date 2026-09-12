# Security setup

## Secrets

Google credentials are no longer stored in the web root. Put fresh credentials in
C:/xampp/private/yt2012-secrets.php, outside htdocs:

    <?php
    return [
        'YT_APP_URL' => 'https://example.com/',
        'YT_APP_ENV' => 'production',
        'YT_GOOGLE_API_KEY' => 'replace-with-a-new-restricted-key',
        'YT_GOOGLE_CLIENT_ID' => '...apps.googleusercontent.com',
        'YT_GOOGLE_CLIENT_SECRET' => 'replace-with-a-new-client-secret',
    ];

Alternatively set the same names with Apache SetEnv or the PHP-FPM process
environment. Do not create this file inside htdocs, and do not commit it.

The credentials that were previously in includes/config.inc.php have already
been exposed. Revoke and regenerate the OAuth client secret and Google API key
in Google Cloud, then restrict the replacement API key to the required APIs and
the production server/IP as appropriate.

## Deployment

Use HTTPS in production and set YT_APP_URL to the exact public origin. The
application marks session and preference cookies Secure automatically when
HTTPS is detected. Ensure that a reverse proxy passes X-Forwarded-Proto: https
only from a trusted proxy.

The document root must be htdocs; C:/xampp/private must not be served by Apache.