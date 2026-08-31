<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$key = getenv('JWT_SECRET_KEY');
$app_env = getenv('APP_ENV');
$is_placeholder = ($key === false || $key === '' || $key === 'CHANGE_THIS_TO_A_RANDOM_STRING_IN_PRODUCTION' || $key === 'MY_SECRET_KEY');

if ($app_env === 'production' && $is_placeholder) {
    log_message('error', 'JWT_SECRET_KEY is missing or a placeholder; refusing to sign tokens in production');
    $key = '';
} elseif ($is_placeholder) {
    // Development: allow a long derived key so local .env.example still boots,
    // but never the literal MY_SECRET_KEY as HMAC secret.
    $fallback = getenv('APP_BASE_URL');
    $key = hash('sha256', 'start-cms-dev-only|' . ($fallback ? $fallback : 'local'));
}

$config['jwt_key'] = $key;
$config['jwt_iss'] = 'start-cms';
$config['jwt_alg'] = 'HS256';
$config['token_timeout'] = 120; // minutes; keep in sync with sess_expiration (7200s)
