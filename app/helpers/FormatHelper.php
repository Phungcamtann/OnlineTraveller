<?php

declare(strict_types=1);

/**
 * Reusable formatting helper: currency, dates, VAT calculations and status badges.
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

    /**
     * Calculate VAT breakdown from a gross total (inclusive of VAT).
     * Default VAT rate in Vietnam tourism/hospitality is 8% (decreed VAT reduction).
     *
     * @param int|float $grossTotal Total price inclusive of VAT
     * @param float $vatRate VAT percentage rate (default 0.08 = 8%)
     * @return array ['net' => float, 'vat' => float, 'gross' => float, 'rate_percent' => int]
     */
    public static function calculateVat(int|float $grossTotal, float $vatRate = 0.08): array
    {
        $gross = (float)$grossTotal;
        $net = round($gross / (1 + $vatRate));
        $vat = $gross - $net;

        return [
            'net' => $net,
            'vat' => $vat,
            'gross' => $gross,
            'rate_percent' => (int)round($vatRate * 100),
        ];
    }
}

if (!class_exists('App\Helpers\FormatHelper', false)) {
    class_alias(FormatHelper::class, 'App\Helpers\FormatHelper');
}
