<?php

declare(strict_types=1);

namespace Ladina\CFONB\Support;

/**
 * Tiny date/time helper used when generating a default download filename.
 */
final class TimeZone
{
    /**
     * Set the default timezone, validating it first and falling back silently
     * when an unknown identifier is given.
     */
    public static function setTimeZone(string $timezone = 'UTC'): void
    {
        if ($timezone === 'UTC' || in_array($timezone, timezone_identifiers_list(), true)) {
            date_default_timezone_set($timezone);
        }
    }

    /**
     * Return the French name of a month (1-12), or an empty string otherwise.
     */
    public static function getFrMonthStr(int $month): string
    {
        return match ($month) {
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
            default => '',
        };
    }
}
