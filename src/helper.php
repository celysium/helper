<?php

if (!function_exists('slug')) {
    function slug($string, $separator = '-'): string
    {
        $string = mb_strtolower(trim($string), 'UTF-8');
        $string = preg_replace('/\s+/', ' ', $string);
        return str_replace(['‌', ' ', '؟', '?', '.', ',', '(', ')', '&', '=', '/', '\\', '%'], [$separator, $separator, '', '', '', '', '', '', '', '', '', '', ''], $string);
    }
}

if (!function_exists('faToEn')) {
    /**
     * @param string|int|float $value
     * @return string
     */
    function faToEn(string|int|float $value): string
    {
        $numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str_replace($numbers, array_keys($numbers), (string)$value);
    }
}

if (!function_exists('enToFa')) {
    /**
     * @param string|int|float $value
     * @return string
     */
    function enToFa(string|int|float $value): string
    {
        $numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str_replace(array_keys($numbers), $numbers, (string)$value);
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
