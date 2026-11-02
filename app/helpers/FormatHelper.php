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
     * Format UTC date time to Asia/Ho_Chi_Minh display.
     */
    public static function formatDateTime(string $datetime, string $format = 'd/m/Y H:i'): string
    {
        try {
            $date = new DateTime($datetime, new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
            return $date->format($format);
        } catch (Exception) {
            return $datetime;
        }
    }
}
