# 🌗 Nepali Date Converter – Laravel Package

A lightweight and framework-ready Laravel package for converting and working with Nepali Bikram Sambat (BS) dates and Gregorian (AD) dates using pure PHP.

## ✨ Features

- 🔁 **BS ↔ AD Conversion**
  - Convert Nepali dates (BS) to Gregorian dates (AD)
  - Convert Gregorian dates (AD) to Nepali dates (BS)

- 🧪 **Date Validation**
  - Validate both BS and AD dates
  - Auto-normalization of date formats (YYYY/MM/DD, YYYY-MM-DD)

- 📅 **Date Formatting**
  - Format BS dates into Nepali or English
  - Supports PHP-like format patterns (`Y`, `m`, `d`, `F`, `l`)
  - Automatic Nepali digits, weekdays, and months

- 📆 **English Date Formatter**
  - Format AD dates in English or convert to Nepali digits & names (`formattedEnglishDate()`)

- 🔠 **Nepali Digit Conversion**
  - Convert any number/string into Nepali digits (`toNepaliDigits()`)

- 📆 **Today's Date Helper**
  - Get today’s date in AD or BS with custom format (`today()`)

- 🔍 **Difference Between Two Dates**
  - Return difference in:
    - years
    - months
    - days
    - hours
    - minutes
    - seconds
  - Supports AD and BS dates (`diff()`)

- 📜 **Human-Readable Difference**
  - Human-friendly output (like Laravel’s `diffForHumans`)
  - English or Nepali output (`humanDiff()`)

- 📅 **Weekday Extraction**
  - Get weekday in English or Nepali from BS or AD date

- 🔢 **Day-of-Year Calculation**
  - Compute day number within BS year

- 📘 **Total Days in BS Year**
  - Retrieve total number of days in any BS year

- 🛠 **Laravel Compatible**
  - Works as a Laravel package or standalone in pure PHP

---


## 📦 Installation

Install via Composer:
```php
composer require jeeven/nepali-date-converter
```
Publish configuration (optional):
```php
php artisan vendor:publish --tag=nepali-date-config
```

---

## 🚀 Usage

### 1. Convert AD → BS
```php
use Jeeven\NepaliDateConverter\Facades\NepaliDate;

$bs = NepaliDate::adToBs("2026-07-29");
echo $bs; // "2083-04-14"

$bsDate = NepaliDate::adToBs("1999-07-29", true);
echo $bsDate->toNepaliFormat(); //२०५६/०४/१३
echo $bsDate->toNepaliHumanFormat(); //२०५६ श्रावण १३, बिहिवार
```

---

### 2. Convert BS → AD
```php
use Jeeven\NepaliDateConverter\Facades\NepaliDate;

$ad = NepaliDate::bsToAd("2083-04-14");
echo $ad; // "2026-07-29"
```
---

### 3. Validate AD Date
```php
$isValid = NepaliDate::isValidADDate("2024-02-29");
var_dump($isValid); // false
```
---

### 4. Validate BS Date
```php
$isValid = NepaliDate::isValidBSDate("2080-13-05");
var_dump($isValid); // false
```
---

### 5. Get Full AD Date Info
```php
$info = NepaliDate::getADInfo("2026-07-29");
print_r($info);

Output example:
[
  "adDate"     => "2026-07-29",
  "bsDate"     => "2083-04-14",
  "weekday"    => "Thursday",
  "dayOfYear"  => 111,
  "totalDays"  => 365,
  "diffDays"   => -300
]
```
---

### 6. Get Full BS Date Info
```php
$info = NepaliDate::getBSInfo("2083-04-14");
print_r($info);

Output example:
[
  "adDate"     => "2026-07-29",
  "bsDate"     => "2083-04-14",
  "weekday"    => "Thursday",
  "dayOfYear"  => 111,
  "totalDays"  => 365,
  "diffDays"   => -300
]
```
---

### 7. Get Weekday
```php
NepaliDate::weekdayAD("2026-07-29"); // "Thursday"
NepaliDate::weekdayBS("2083-04-14"); // "Thursday"

NepaliDate::weekdayAD("2026-07-29", "np"); // "बिहिवार"
NepaliDate::weekdayBS("2083-04-14", "np"); // "बिहिवार"
```
---

### 8. Using the Facade
```php
use Jeeven\NepaliDateConverter\Facades\NepaliDate;

NepaliDate::adToBs("2024-01-01");
```

---

### 9. NepaliDate Utility Methods

## 1. `formattedNepaliDate($bsDate, $format = 'Y-m-d', $locale = 'en')`

This method is used to format a **BS (Nepali) date** into either **Nepali** or **English** formats. It supports custom date formats.

### Usage:
```php
echo NepaliDate::formattedEnglishDate("2025-12-03");
// 2025-12-03

echo NepaliDate::formattedEnglishDate("2025-12-03", "d/m/Y");
// 03/12/2025

echo NepaliDate::formattedEnglishDate("2025-12-03", "d F, l");
// 03 December, Wednesday

echo NepaliDate::formattedEnglishDate("2025-12-03", "d F, l", "np");
// १७ मंसिर, बुधवार

echo NepaliDate::formattedNepaliDate("2082/8/17");
// Default: 2082-08-17

echo NepaliDate::formattedNepaliDate("2082-08-17", "Y/m/d", "np");
// २०८२/०८/१७

echo NepaliDate::formattedNepaliDate("2082-08-17", "d F, l", "np");
// १७ मंसिर, बुधवार

echo NepaliDate::formattedNepaliDate("2082/08/17", "d/m/Y", "np");
// १७/०८/२०८२

// Get today's date in English (AD)
echo NepaliDate::today();
// Output: 2025-12-08 (example date)

// Custom format: English (AD)
echo NepaliDate::today("d/m/Y");
// Output: 08/12/2025

// Get today's date in Nepali (BS)
echo NepaliDate::today("d/m/Y", "np");
// Output: २२/०८/२०८२ (example BS date)


// Get full difference between two AD dates
print_r(NepaliDate::diff("2025-12-03", "2025-11-03"));
/*
Output:
[
    'years' => 0,
    'months' => 1,
    'days' => 30,
    'hours' => 720,
    'minutes' => 43200,
    'seconds' => 2592000
]
*/

// Get difference in specific units (days)
echo NepaliDate::diff("2025-12-03", "2025-11-03", 'en', 'days');
// Output: 30

// Get full difference between two BS dates
print_r(NepaliDate::diff("2082-08-17", "2082-08-10", "np"));
/*
Output:
[
    'years' => 0,
    'months' => 0,
    'days' => 7,
    'hours' => 168,
    'minutes' => 10080,
    'seconds' => 604800
]
*/

// Get difference in specific units (months)
echo NepaliDate::diff("2082-08-17", "2082-01-01", "np", "months");
// Output: 7


// Get human-readable difference in English (AD)
echo NepaliDate::humanDiff("2025-01-01", "2023-12-01");
// Output: "1 year, 1 month, 0 days"

// Get human-readable difference in Nepali (BS)
echo NepaliDate::humanDiff("2082-08-17", "2081-01-10", "np", "np");
// Output: "१ वर्ष, ७ महिना, ७ दिन"

// Get human-readable difference in English (AD) without years or months
echo NepaliDate::humanDiff("2025-01-01", "2025-01-10");
// Output: "9 days"

// Get human-readable difference in Nepali (BS) without years or months
echo NepaliDate::humanDiff("2082-08-17", "2082-08-10", "np", "np");
// Output: "७ दिन"


$number = 1234567;
echo NepaliDate::toNepaliDigits($number);
// Output: १२३४५६७

```
---

## ⚙️ Configuration (Optional)
File: config/nepali-date.php
```php
return [
    "start_year" => 1970,
    "end_year"   => 2090
];
```
---

## 🧪 Testing
```php
php artisan test
```
---

## 📝 License
This package is open-source and available under the MIT License.
