<?php

namespace Jeeven\NepaliDateConverter;

class BsDate
{
    public string $year;
    public string $month;
    public string $day;

    public function __construct(string $bsDate)
    {
        [$this->year, $this->month, $this->day] = explode('-', $bsDate);
    }

    public function toString(): string
    {
        return sprintf("%04d-%02d-%02d", $this->year, $this->month, $this->day);
    }

    public function toNepaliFormat(): string
    {
        $digits = BsCalendar::nepaliDigits();
        $date = sprintf("%04d/%02d/%02d", $this->year, $this->month, $this->day);
        return str_replace(range(0, 9), $digits, $date);
    }

    public function toNepaliHumanFormat(): string
    {
        $months = BsCalendar::nepaliMonthsInNep();
        $weekdays = BsCalendar::nepaliWeekDays();

        $year = (int)$this->year;
        $month = (int)$this->month;
        $day = (int)$this->day;

        $ad = NepaliDateConverter::bsToAd($this->toString());
        $weekdayIndex = (int)date('w', strtotime($ad)); // 0=Sunday
        $weekday = $weekdays[$weekdayIndex];

        return BsCalendar::nepaliDigits()[floor($year / 1000)] .
            BsCalendar::nepaliDigits()[$year % 1000 / 100] .
            BsCalendar::nepaliDigits()[$year % 100 / 10] .
            BsCalendar::nepaliDigits()[$year % 10] . " " .
            $months[$month - 1] . " " .
            str_replace(range(0, 9), BsCalendar::nepaliDigits(), sprintf("%02d", $day)) . ", " .
            $weekday;
    }
}
