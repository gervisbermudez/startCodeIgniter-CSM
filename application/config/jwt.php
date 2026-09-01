<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$key = getenv('JWT_SECRET_KEY');
$is_placeholder = ($key === false || $key === '' || $key === 'CHANGE_THIS_TO_A_RANDOM_STRING_IN_PRODUCTION' || $key === 'MY_SECRET_KEY');

if ($is_placeholder) {
    $app_env = getenv('APP_ENV');
    $is_local = ($app_env === 'development');
    if (!$is_local) {
        $base = getenv('APP_BASE_URL');
        if (is_string($base) && $base !== '') {
            $host = parse_url($base, PHP_URL_HOST);
            if (is_string($host)) {
                $host = strtolower($host);
                if ($host === 'localhost' || $host === '127.0.0.1') {
                    $is_local = true;
                }
            }
        }
    }
    if (!$is_local) {
        log_message('error', 'JWT_SECRET_KEY is missing or a placeholder; refusing to sign tokens outside a clearly local environment');
        $key = '';
    } else {
        // Local only: derived key so .env.example still boots. Never HMAC with MY_SECRET_KEY.
        $fallback = getenv('APP_BASE_URL');
        $key = hash('sha256', 'start-cms-dev-only|' . ($fallback ? $fallback : 'local'));
    }
}

$config['jwt_key'] = $key;
$config['jwt_iss'] = 'start-cms';
$config['jwt_alg'] = 'HS256';
$config['token_timeout'] = 120; // minutes; keep in sync with sess_expiration (7200s)
