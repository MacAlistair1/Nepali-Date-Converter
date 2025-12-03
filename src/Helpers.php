<?php

namespace Jeeven\NepaliDateConverter;

class Helpers
{
    public static function converter(): NepaliDateConverter
    {
        return new NepaliDateConverter();
    }

    public static function adToBs(string $ad): mixed
    {
        return self::converter()->adToBs($ad);
    }

    public static function isValidADDate(string $ad): bool
    {
        return self::converter()->isValidADDate($ad);
    }

    public static function bsToAd(string $bs): string
    {
        return self::converter()->bsToAd($bs);
    }

    public static function isValidBSDate(string $bs): bool
    {
        return self::converter()->isValidBSDate($bs);
    }

    public static function infoAD(string $ad): array
    {
        return self::converter()->getADInfo($ad);
    }

    public static function infoBS(string $bs): array
    {
        return self::converter()->getBSInfo($bs);
    }

    public static function weekdayAD(string $ad): string
    {
        return self::converter()->weekdayAD($ad);
    }

    public static function weekdayBS(string $bs): string
    {
        return self::converter()->weekdayBS($bs);
    }

    public static function formattedNepaliDate(string $date, string $format = 'Y-m-d', string $locale = 'en'): string
    {
        return self::converter()->formattedNepaliDate($date, $format, $locale);
    }

    public static function formattedEnglishDate(string $date, string $format = 'Y-m-d', string $locale = 'en'): string
    {
        return self::converter()->formattedEnglishDate($date, $format, $locale);
    }
}
