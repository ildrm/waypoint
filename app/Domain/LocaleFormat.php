<?php

namespace App\Domain;

final class LocaleFormat
{
    public static function number(int|float|string $value, ?string $locale = null): string
    {
        $f = new \NumberFormatter($locale ?? app()->getLocale(), \NumberFormatter::DECIMAL);
        $value = (string) $value;
        if (! preg_match('/^(-?)([0-9]+)(?:\.([0-9]+))?$/D', $value, $m)) {
            return (string) $f->format((float) $value);
        }$integer = $m[2];
        $size = $f->getAttribute(\NumberFormatter::GROUPING_SIZE) ?: 3;
        $secondary = $f->getAttribute(\NumberFormatter::SECONDARY_GROUPING_SIZE) ?: $size;
        $parts = [];
        while (strlen($integer) > $size) {
            array_unshift($parts, substr($integer, -$size));
            $integer = substr($integer, 0, -$size);
            $size = $secondary;
        }array_unshift($parts, $integer);
        $out = $m[1].implode($f->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL), $parts).(isset($m[3]) ? $f->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL).$m[3] : '');
        $digits = [];
        for ($i = 0; $i < 10; $i++) {
            $digits[(string) $i] = $f->format($i);
        }

return strtr($out, $digits);
    }

    public static function date(\DateTimeInterface $value, ?string $locale = null): string
    {
        $f = new \IntlDateFormatter($locale ?? app()->getLocale(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT, config('app.timezone'));

        return (string) $f->format($value);
    }

    public static function money(string $decimal, string $currency, ?string $locale = null): string
    {
        return $currency.' '.self::number($decimal,$locale);
    }
}
