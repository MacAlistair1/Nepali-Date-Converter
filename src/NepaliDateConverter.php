<?php

namespace Jeeven\NepaliDateConverter;

use DateTime;

/**
 * NepaliDateConverter.php
 * ------------------------
 * Jeeven Lamichhane Official Logic
 * Conversion between AD (Gregorian) and BS (Bikram Sambat)
 */
class NepaliDateConverter
{
    private static $bsData;
    private static $refBS;
    private static $refAD;

    private static $startYear;

    public function __construct()
    {
        self::$bsData = BsCalendar::data();

        self::$startYear = config('nepali-date')['start_year'];

        if (self::$startYear < 1970) {
            self::$startYear = 1970;
        }

        // Reference date mapping
        self::$refBS = ['year' => 2062, 'month' => 1, 'day' => 1];
        self::$refAD = new DateTime('2005-04-14'); // 14 April 2005
    }

    private static function normalize(string $date, string $dateType = 'en'): string
    {
        // Keep only digits
        $digits = preg_replace('/\D/', '', $date);

        // If 8 digits: already YYYYMMDD
        if (strlen($digits) === 8) {
            $year = substr($digits, 0, 4);
            $month = substr($digits, 4, 2);
            $day = substr($digits, 6, 2);
        }
        // If 7 digits: month or day is a single digit
        elseif (strlen($digits) === 7) {
            $year = substr($digits, 0, 4);
            $rest = substr($digits, 4);

            // Try interpreting as YYYYMDD or YYYYMM D
            if ((int) substr($rest, 0, 1) > 1) {
                // Month is 1 digit, day is 2
                $month = '0' . substr($rest, 0, 1);
                $day = substr($rest, 1, 2);
            } else {
                // Month is 2 digits, day is 1
                $month = substr($rest, 0, 2);
                $day = '0' . substr($rest, 2, 1);
            }
        } else {
            throw new \Exception("Invalid date format: $date");
        }

        // Final validation
        if ($dateType == 'en' && !checkdate((int) $month, (int) $day, (int) $year)) {
            throw new \Exception("Invalid date: $year-$month-$day");
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }


    private static function pad(int $n): string
    {
        return str_pad($n, 2, '0', STR_PAD_LEFT);
    }

    // -------------------
    // Total days since self::$startYear for BS
    // -------------------
    public static function totalDaysSince1970(int $y, int $m, int $d): int
    {
        $days = 0;
        for ($Y = self::$startYear; $Y < $y; $Y++) {
            if (!isset(self::$bsData[$Y]))
                throw new \Exception("Year $Y not in dataset");
            $days += self::$bsData[$Y][12]; // total days in BS year
        }
        for ($M = 1; $M < $m; $M++) {
            $days += self::$bsData[$y][$M - 1];
        }
        $days += $d - 1;
        return $days;
    }

    // -------------------
    // BS → AD
    // -------------------
    public static function bsToAd(string $bsDate): string
    {
        [$by, $bm, $bd] = array_map('intval', explode('-', self::normalize($bsDate, 'np')));
        if (!isset(self::$bsData[$by]))
            throw new \Exception("BS Year out of range");

        $refTotal = self::totalDaysSince1970(self::$refBS['year'], self::$refBS['month'], self::$refBS['day']);
        $targetTotal = self::totalDaysSince1970($by, $bm, $bd);
        $diff = $targetTotal - $refTotal;

        $ad = clone self::$refAD;
        $ad->modify("$diff days");

        return $ad->format('Y-m-d');
    }

    // -------------------
    // AD → BS
    // -------------------
    public static function adToBs(string $adDate, bool $asObject = false): mixed
    {
        [$ay, $am, $ad] = array_map('intval', explode('-', self::normalize($adDate)));
        $adDateObj = new DateTime("$ay-$am-$ad");

        $refTotal = self::totalDaysSince1970(self::$refBS['year'], self::$refBS['month'], self::$refBS['day']);
        $refAdTotal = intval(self::$refAD->format('U') / 86400);
        $targetAdTotal = intval($adDateObj->format('U') / 86400);
        $diff = $targetAdTotal - $refAdTotal;

        $bsYear = self::$refBS['year'];
        $bsMonth = self::$refBS['month'];
        $bsDay = self::$refBS['day'];

        while ($diff !== 0) {
            $monthDays = self::$bsData[$bsYear][$bsMonth - 1];
            if ($diff > 0) {
                $bsDay++;
                if ($bsDay > $monthDays) {
                    $bsDay = 1;
                    $bsMonth++;
                    if ($bsMonth > 12) {
                        $bsMonth = 1;
                        $bsYear++;
                    }
                }
                $diff--;
            } else {
                $bsDay--;
                if ($bsDay < 1) {
                    $bsMonth--;
                    if ($bsMonth < 1) {
                        $bsMonth = 12;
                        $bsYear--;
                    }
                    $bsDay = self::$bsData[$bsYear][$bsMonth - 1];
                }
                $diff++;
            }
        }

        $bsDateStr = sprintf("%04d-%02d-%02d", $bsYear, $bsMonth, $bsDay);

        if ($asObject) {
            return new BsDate($bsDateStr);
        }

        return $bsDateStr; // legacy behavior
    }

    // -------------------
    // Validate BS
    // -------------------
    public static function isValidBSDate(string $bsDate): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $bsDate))
            return false;
        [$y, $m, $d] = array_map('intval', explode('-', $bsDate));
        if (!isset(self::$bsData[$y]))
            return false;
        if ($m < 1 || $m > 12)
            return false;
        if ($d < 1 || $d > self::$bsData[$y][$m - 1])
            return false;

        try {
            return self::adToBs(self::bsToAd($bsDate)) === $bsDate;
        } catch (\Exception $e) {
            return false;
        }
    }

    // -------------------
    // Validate AD
    // -------------------
    public static function isValidADDate(string $adDate): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $adDate))
            return false;

        [$y, $m, $d] = array_map('intval', explode('-', $adDate));

        // checkdate(month, day, year) returns false for invalid dates
        if (!checkdate($m, $d, $y))
            return false;

        // Round-trip validation (optional)
        try {
            return self::bsToAd(self::adToBs($adDate)) === $adDate;
        } catch (\Exception $e) {
            return false;
        }
    }


    // -------------------
    // Weekdays
    // -------------------
    public static function weekdayAD(string $adDate, string $locale = 'en'): string
    {
        $date = new DateTime(self::normalize($adDate));
        $dayIndex = (int) $date->format('w'); // 0 = Sunday, 6 = Saturday

        if ($locale === 'np') {
            $weekdays = BsCalendar::nepaliWeekDays(); // returns ['आइतवार', 'सोमवार', ...]
            return $weekdays[$dayIndex];
        }

        return $date->format('l');
    }

    public static function weekdayBS(string $bsDate, string $locale = 'en'): string
    {
        return self::weekdayAD(self::bsToAd($bsDate), $locale);
    }

    // -------------------
    // Full info (like JS getDayInfoBS / AD)
    // -------------------
    public static function getBSInfo(string $bsDate): array
    {
        if (!self::isValidBSDate($bsDate))
            return [];

        [$y, $m, $d] = array_map('intval', explode('-', $bsDate));
        $ad = self::bsToAd($bsDate);

        $totalDaysInYear = self::$bsData[$y][12];
        $dayOfYear = self::totalDaysSince1970($y, $m, $d) - self::totalDaysSince1970($y, 1, 1) + 1;
        $today = new DateTime();
        $todayBS = self::adToBs($today->format('Y-m-d'));
        $diffDays = self::totalDaysSince1970($y, $m, $d) - self::totalDaysSince1970(...explode('-', $todayBS));

        return [
            'bs' => $bsDate,
            'ad' => $ad,
            'weekday' => self::weekdayBS($bsDate),
            'total_days_in_year' => $totalDaysInYear,
            'day_of_year' => $dayOfYear,
            'diff_days_from_today' => $diffDays,
        ];
    }

    public static function getADInfo(string $adDate): array
    {
        if (!self::isValidADDate($adDate))
            return [];

        $bs = self::adToBs($adDate);
        [$y, $m, $d] = array_map('intval', explode('-', $bs));

        $totalDaysInYear = self::$bsData[$y][12];
        $dayOfYear = self::totalDaysSince1970($y, $m, $d) - self::totalDaysSince1970($y, 1, 1) + 1;

        $today = new DateTime();
        $todayBS = self::adToBs($today->format('Y-m-d'));
        $diffDays = self::totalDaysSince1970($y, $m, $d) - self::totalDaysSince1970(...explode('-', $todayBS));

        return [
            'ad' => $adDate,
            'bs' => $bs,
            'weekday' => self::weekdayAD($adDate),
            'total_days_in_year' => $totalDaysInYear,
            'day_of_year' => $dayOfYear,
            'diff_days_from_today' => $diffDays,
        ];
    }

    /**
     * Return a formatted BS date in either Nepali or English
     *
     * @param string $bsDate  BS date in YYYY-MM-DD
     * @param string $format  Format string, e.g., 'Y-m-d' or 'd M, Y, l'
     * @param string $locale  'en' or 'np'
     * @return string
     */
    public static function formattedNepaliDate(string $bsDate, string $format = 'Y-m-d', string $locale = 'en'): string
    {
        // Normalize input to YYYY-MM-DD
        $bsDate = self::normalize($bsDate, 'np');

        // Validate BS date
        if (!self::isValidBSDate($bsDate)) {
            throw new \InvalidArgumentException("Invalid BS date: $bsDate");
        }

        [$y, $m, $d] = explode('-', $bsDate);
        $y = (int) $y;
        $m = (int) $m;
        $d = (int) $d;

        // If English locale, return formatted string using PHP date placeholders
        if ($locale === 'en') {
            return str_replace(
                ['Y', 'm', 'd'],
                [sprintf("%04d", $y), sprintf("%02d", $m), sprintf("%02d", $d)],
                $format
            );
        }

        // Nepali locale
        $monthsNep = BsCalendar::nepaliMonthsInNep();
        $weekdaysNep = BsCalendar::nepaliWeekDays();
        $digitsNep = BsCalendar::nepaliDigits();

        // Day of week from AD equivalent
        $adDate = self::bsToAd($bsDate);
        $weekdayIndex = (int) (new DateTime($adDate))->format('w'); // 0=Sun ... 6=Sat

        // Convert digits to Nepali (with zero-padding)
        $yearNep = implode('', array_map(fn($dgt) => $digitsNep[$dgt], str_split(sprintf("%04d", $y))));
        $monthNep = implode('', array_map(fn($dgt) => $digitsNep[$dgt], str_split(sprintf("%02d", $m))));
        $dayNep = implode('', array_map(fn($dgt) => $digitsNep[$dgt], str_split(sprintf("%02d", $d))));
        $weekdayNep = $weekdaysNep[$weekdayIndex];

        // Replace placeholders in format
        $replacements = [
            'Y' => $yearNep,
            'm' => $monthNep,
            'd' => $dayNep,
            'F' => $monthsNep[$m - 1],
            'l' => $weekdayNep,
        ];

        $formatted = strtr($format, $replacements);

        return $formatted;
    }

    public static function formattedEnglishDate(string $adDate, string $format = 'Y-m-d', string $locale = 'en'): string
    {
        // Normalize input to YYYY-MM-DD
        $adDate = self::normalize($adDate);

        // Validate AD date
        if (!self::isValidADDate($adDate)) {
            throw new \InvalidArgumentException("Invalid AD date: $adDate");
        }

        if ($locale === 'en') {
            // English formatting from AD directly
            [$y, $m, $d] = explode('-', $adDate);
            $y = (int) $y;
            $m = (int) $m;
            $d = (int) $d;

            $replacements = [
                'Y' => sprintf("%04d", $y),
                'm' => sprintf("%02d", $m),
                'd' => sprintf("%02d", $d),
                'F' => date('F', strtotime($adDate)),
                'l' => date('l', strtotime($adDate)),
            ];

            return strtr($format, $replacements);
        }

        // Nepali locale → convert AD → BS first
        $bsDate = self::adToBs($adDate); // get BS date
        [$y, $m, $d] = explode('-', $bsDate); // BS values

        $digitsNep = BsCalendar::nepaliDigits();
        $weekdaysNep = BsCalendar::nepaliWeekDays();
        $monthsNep = BsCalendar::nepaliMonthsInNep();

        $weekdayIndex = (new DateTime($adDate))->format('w'); // weekday from AD

        $dayNep = implode('', array_map(fn($dgt) => $digitsNep[$dgt], str_split($d)));
        $monthNep = $monthsNep[$m - 1];
        $yearNep = implode('', array_map(fn($dgt) => $digitsNep[$dgt], str_split($y)));
        $weekdayNep = $weekdaysNep[$weekdayIndex];

        $replacementsNep = [
            'Y' => $yearNep,
            'm' => implode('', array_map(fn($dgt) => $digitsNep[$dgt], str_split($m))),
            'd' => $dayNep,
            'F' => $monthNep,
            'l' => $weekdayNep,
        ];

        return strtr($format, $replacementsNep);
    }
}
