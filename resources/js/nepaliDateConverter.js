import { bsData } from "./bsCalendarData.js";

/*!
 * Nepali Date Converter
 * ---------------------
 * This JavaScript logic is developed and maintained by Jeeven Lamichhane.
 * Provides conversion between AD (Gregorian) and BS (Bikram Sambat) dates.
 * Features:
 *   - AD → BS conversion
 *   - BS → AD conversion
 *   - Validation for both AD and BS dates
 *   - Day of Year and Total Days calculation
 *   - Difference from today's date
 *   - Day of the week calculation
 *
 * Usage:
 *   Import the required functions and use as shown in the code snippets.
 *
 * Copyright © 2025 Jeeven Lamichhane
 */

// Reference BS → AD
const REF_BS = { year: 2062, month: 1, day: 1 };
const REF_AD = new Date(2005, 3, 14); // April = 3, JS months 0-based

function pad(n) {
  return n.toString().padStart(2, "0");
}

export function totalDaysSince1970(y, m, d) {
  let days = 0;
  for (let Y = 1970; Y < y; Y++) {
    if (!bsData[Y]) throw new Error(`Year ${Y} not in dataset`);
    days += bsData[Y][12]; // 13th element = total days in year
  }
  for (let M = 1; M < m; M++) {
    days += bsData[y][M - 1]; // month days
  }
  days += d - 1;
  return days;
}

// ------------------- BS → AD -------------------
export function bsToAd(bsDateStr, format = "YYYY-MM-DD") {
  const [by, bm, bd] = bsDateStr.split("-").map(Number);
  if (!bsData[by]) throw new Error("BS Year out of range");

  const refBsTotal = totalDaysSince1970(REF_BS.year, REF_BS.month, REF_BS.day);
  const targetBsTotal = totalDaysSince1970(by, bm, bd);
  const diff = targetBsTotal - refBsTotal;

  const ad = new Date(REF_AD.valueOf());
  ad.setDate(ad.getDate() + diff);

  const y = ad.getFullYear();
  const m = ad.getMonth() + 1;
  const d = ad.getDate();
  return format.replace("YYYY", y).replace("MM", pad(m)).replace("DD", pad(d));
}

// ------------------- AD → BS -------------------
export function adToBs(adDateStr, format = "YYYY-MM-DD") {
  const [ay, am, ad] = adDateStr.split("-").map(Number);
  const adDate = new Date(ay, am - 1, ad);

  const refBsTotal = totalDaysSince1970(REF_BS.year, REF_BS.month, REF_BS.day);
  const refAdTotal = REF_AD.getTime() / (1000 * 60 * 60 * 24);

  const targetAdTotal = adDate.getTime() / (1000 * 60 * 60 * 24);
  let diff = Math.round(targetAdTotal - refAdTotal);

  let bsYear = REF_BS.year;
  let bsMonth = REF_BS.month;
  let bsDay = REF_BS.day;

  while (diff !== 0) {
    const monthDays = bsData[bsYear][bsMonth - 1];
    if (diff > 0) {
      bsDay++;
      if (bsDay > monthDays) {
        bsDay = 1;
        bsMonth++;
        if (bsMonth > 12) {
          bsMonth = 1;
          bsYear++;
        }
      }
      diff--;
    } else {
      bsDay--;
      if (bsDay < 1) {
        bsMonth--;
        if (bsMonth < 1) {
          bsMonth = 12;
          bsYear--;
        }
        bsDay = bsData[bsYear][bsMonth - 1];
      }
      diff++;
    }
  }

  return format
    .replace("YYYY", bsYear)
    .replace("MM", pad(bsMonth))
    .replace("DD", pad(bsDay));
}

// ------------------- BS VALIDATION -------------------
export function isValidBSDate(bsDateStr) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(bsDateStr)) return false;
  const [y, m, d] = bsDateStr.split("-").map(Number);
  if (!bsData[y]) return false;
  if (m < 1 || m > 12) return false;
  if (d < 1 || d > bsData[y][m - 1]) return false;

  // roundtrip check
  try {
    return adToBs(bsToAd(bsDateStr)) === bsDateStr;
  } catch {
    return false;
  }
}

// ------------------- AD VALIDATION -------------------
export function isValidADDate(adDateStr) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(adDateStr)) return false;
  const [y, m, d] = adDateStr.split("-").map(Number);
  const date = new Date(adDateStr);
  if (isNaN(date)) return false;
  if (
    date.getFullYear() !== y ||
    date.getMonth() + 1 !== m ||
    date.getDate() !== d
  )
    return false;

  // roundtrip check
  try {
    return bsToAd(adToBs(adDateStr)) === adDateStr;
  } catch {
    return false;
  }
}

export function getDayInfoBS(bsDateStr) {
  const [y, m, d] = bsDateStr.split("-").map(Number);

  if (!isValidBSDate(bsDateStr)) return null;

  const totalDaysInYear = bsData[y][12]; // total days in BS year
  const dayOfYear =
    totalDaysSince1970(y, m, d) - totalDaysSince1970(y, 1, 1) + 1;

  const today = new Date();
  const todayBS = adToBs(today.toISOString().slice(0, 10));
  const diffDays =
    totalDaysSince1970(y, m, d) -
    totalDaysSince1970(...todayBS.split("-").map(Number));

  return {
    bsDate: bsDateStr,
    totalDaysInYear,
    dayOfYear,
    diffDays,
    adDate: bsToAd(bsDateStr),
  };
}
