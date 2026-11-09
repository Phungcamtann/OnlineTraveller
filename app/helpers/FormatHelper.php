<?php

declare(strict_types=1);

/**
 * Reusable formatting helper: currency, dates and status badges.
 */
class FormatHelper
{
    /**
     * Format integer amount as VND currency.
     */
    public static function formatVnd(int|float $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Format date time to Asia/Ho_Chi_Minh display.
     */
    public static function formatDateTime(string $datetime, string $format = 'd/m/Y H:i'): string
    {
        try {
            $timestamp = strtotime($datetime);
            return $timestamp ? date($format, $timestamp) : $datetime;
        } catch (\Throwable) {
            return $datetime;
        }
    }
}

if (!class_exists('App\Helpers\FormatHelper', false)) {
    class_alias(FormatHelper::class, 'App\Helpers\FormatHelper');
}
