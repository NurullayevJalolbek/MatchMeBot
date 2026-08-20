<?php

use Carbon\Carbon;

if (!function_exists('format_number')) {
    /**
     * Raqamni probellar bilan formatlash (masalan: 100 000).
     */
    function format_number($number, int $decimals = 0): string
    {
        if ($number === null || $number === '') {
            return '0';
        }

        $num = (float) str_replace([' ', ','], ['', '.'], (string) $number);
        
        return number_format($num, $decimals, '.', ' ');
    }
}

if (!function_exists('format_price')) {
    /**
     * Narx / summani valyutasi bilan formatlash (masalan: 100 000 UZS).
     */
    function format_price($amount, string $currency = 'UZS', int $decimals = 0): string
    {
        if ($amount === null || $amount === '') {
            return '0 ' . $currency;
        }

        return format_number($amount, $decimals) . ($currency ? ' ' . $currency : '');
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Sanani birinchi soat keyin sana ko'rinishida formatlash (masalan: 15:30 15.08.2026).
     */
    function format_datetime($date, string $format = 'H:i d.m.Y'): string
    {
        if (!$date) {
            return '—';
        }

        if ($date instanceof Carbon || $date instanceof \DateTimeInterface) {
            return $date->format($format);
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}

if (!function_exists('format_date')) {
    /**
     * Faqat sanani formatlash (masalan: 15.08.2026).
     */
    function format_date($date, string $format = 'd.m.Y'): string
    {
        if (!$date) {
            return '—';
        }

        if ($date instanceof Carbon || $date instanceof \DateTimeInterface) {
            return $date->format($format);
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}

if (!function_exists('format_time')) {
    /**
     * Faqat soatni formatlash (masalan: 15:30).
     */
    function format_time($date, string $format = 'H:i'): string
    {
        if (!$date) {
            return '—';
        }

        if ($date instanceof Carbon || $date instanceof \DateTimeInterface) {
            return $date->format($format);
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}
