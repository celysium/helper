<?php

if (!function_exists('faToEn')) {
    /**
     * @param string|int $value
     * @return string
     */
    function faToEn(string|int $value): string
    {
        $numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str_replace($numbers, array_keys($numbers), (string)$value);
    }
}

if (!function_exists('isMobile')) {
    function isMobile(string|int $value): bool
    {
        $mobile = faToEn($value);
        if (preg_match('/^((9)[0-9]{9})+$/', $mobile) ||
            preg_match('/^((\+989)[0-9]{9})+$/', $mobile) ||
            preg_match('/^((0989)[0-9]{9})+$/', $mobile) ||
            preg_match('/^((989)[0-9]{9})+$/', $mobile)
        ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('regularMobile')) {
    function regularMobile(string|int $value): string|false
    {
        $mobile = faToEn($value);
        if (preg_match('/^((9)[0-9]{9})+$/', $mobile)) {
            return '0' . $mobile;
        }
        elseif (
            preg_match('/^((\+989)[0-9]{9})+$/', $mobile) ||
            preg_match('/^((0989)[0-9]{9})+$/', $mobile) ||
            preg_match('/^((989)[0-9]{9})+$/', $mobile)
        ) {
            return '0' . substr($mobile, -10);
        }
        return false;
    }
}