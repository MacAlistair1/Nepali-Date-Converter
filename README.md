# 🌗 Nepali Date Converter – Laravel Package

A lightweight and framework-ready Laravel package for converting and working with Nepali Bikram Sambat (BS) dates and Gregorian (AD) dates using pure PHP.

Features:
- BS ↔ AD conversion
- Date validation
- Weekday extraction
- Day-of-year calculation
- Total days in a BS year
- Difference from today
- Fully Laravel-compatible (also works in plain PHP)

---

## 📦 Installation

Install via Composer:
```bash
composer require jeeven/nepali-date-converter
```
Publish configuration (optional):
```bash
php artisan vendor:publish --tag=nepali-date-config
```

---

## 🚀 Usage

### 1. Convert AD → BS
```bash
use Jeeven\NepaliDateConverter\Facades\NepaliDate;

$bs = NepaliDate::adToBs("2026-07-29");
echo $bs; // "2083-04-14"

$bsDate = NepaliDate::adToBs("1999-07-29", true);
echo $bsDate->toNepaliFormat(); //२०५६/०४/१३
echo $bsDate->toNepaliHumanFormat(); //२०५६ श्रावण १३, बिहिवार
```

---

### 2. Convert BS → AD
```bash
use Jeeven\NepaliDateConverter\Facades\NepaliDate;

$ad = NepaliDate::bsToAd("2083-04-14");
echo $ad; // "2026-07-29"
```
---

### 3. Validate AD Date
```bash
$isValid = NepaliDate::isValidADDate("2024-02-29");
var_dump($isValid); // false
```
---

### 4. Validate BS Date
```bash
$isValid = NepaliDate::isValidBSDate("2080-13-05");
var_dump($isValid); // false
```
---

### 5. Get Full AD Date Info
```bash
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
```bash
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
```bash
NepaliDate::weekdayAD("2026-07-29"); // "Thursday"
NepaliDate::weekdayBS("2083-04-14"); // "Thursday"

NepaliDate::weekdayAD("2026-07-29", "np"); // "बिहिवार"
NepaliDate::weekdayBS("2083-04-14", "np"); // "बिहिवार"
```
---

### 8. Using the Facade
```bash
use Jeeven\NepaliDateConverter\Facades\NepaliDate;

NepaliDate::adToBs("2024-01-01");
```


---

### 9. Other Utils
```bash
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
```
---

## ⚙️ Configuration (Optional)
File: config/nepali-date.php
```bash
return [
    "start_year" => 1970,
    "end_year"   => 2090
];
```
---

## 🧪 Testing
```bash
php artisan test
```
---

## 📝 License
This package is open-source and available under the MIT License.
