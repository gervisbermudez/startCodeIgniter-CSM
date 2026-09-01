<?php

class AUTHORIZATION
{
    public static function validateTimestamp($token)
    {
        return self::validateToken($token);
    }

    public static function validateToken($token)
    {
        $CI =& get_instance();
        if (!is_string($token) || $token === '') {
            return false;
        }
        $token = self::stripBearer($token);
        $key = $CI->config->item('jwt_key');
        if (!is_string($key) || $key === '') {
            return false;
        }
        return JWT::decode($token, $key);
    }

    public static function generateToken($data)
    {
        $CI =& get_instance();
        $key = $CI->config->item('jwt_key');
        if (!is_string($key) || $key === '') {
            return false;
        }
        if (!isset($data['iat'])) {
            $data['iat'] = time();
        }
        if (!isset($data['exp'])) {
            $timeout = (int) $CI->config->item('token_timeout');
            if ($timeout < 1) {
                $timeout = 120;
            }
            $data['exp'] = time() + ($timeout * 60);
        }
        if (!isset($data['iss'])) {
            $data['iss'] = $CI->config->item('jwt_iss');
        }
        return JWT::encode($data, $key, 'HS256');
    }

    public static function stripBearer($token)
    {
        if (!is_string($token)) {
            return '';
        }
        if (stripos($token, 'Bearer ') === 0) {
            return trim(substr($token, 7));
        }
        return trim($token);
    }

}
