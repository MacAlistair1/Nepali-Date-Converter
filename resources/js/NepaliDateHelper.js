/*!
 * NepaliDateHelper.js
 * -------------------
 * Wrapper class for Nepali date conversion & validation
 * Based on nepaliDateConverter.js logic
 * Developed by Jeeven Lamichhane
 */

import { bsData } from "./bsCalendarData.js";
import {
  bsToAd,
  adToBs,
  isValidBSDate,
  isValidADDate,
  totalDaysSince1970,
} from "./nepaliDateConverter.js";

export class NepaliDateHelper {
  constructor() {
    const today = new Date();
    this.todayAD = today.toISOString().slice(0, 10);
    this.todayBS = adToBs(this.todayAD);
  }

  // --------------------------
  // Format input YYYYMMDD → YYYY-MM-DD
  // --------------------------
  formatInput(value) {
    value = value.replace(/\D/g, "");
    if (value.length >= 4)
      value =
        value.slice(0, 4) + (value.length >= 5 ? "-" : "") + value.slice(4);
    if (value.length >= 7)
      value =
        value.slice(0, 7) + (value.length >= 8 ? "-" : "") + value.slice(7, 9);
    return value;
  }

  // --------------------------
  // AD ↔ BS conversion
  // --------------------------
  adToBs(dateStr) {
    if (!isValidADDate(dateStr)) throw new Error("Invalid AD date!");
    return adToBs(dateStr);
  }

  bsToAd(dateStr) {
    if (!isValidBSDate(dateStr)) throw new Error("Invalid BS date!");
    return bsToAd(dateStr, "YYYY-MM-DD");
  }

  // --------------------------
  // Validation
  // --------------------------
  isValidAD(dateStr) {
    return isValidADDate(dateStr);
  }

  isValidBS(dateStr) {
    return isValidBSDate(dateStr);
  }

  // --------------------------
  // Info for AD date
  // --------------------------
  getADInfo(adDate) {
    if (!this.isValidAD(adDate)) return null;

    const bsDate = adToBs(adDate);
    const [bsY, bsM, bsD] = bsDate.split("-").map(Number);
    const totalDays = bsData[bsY][12];
    const dayOfYear =
      totalDaysSince1970(bsY, bsM, bsD) - totalDaysSince1970(bsY, 1, 1) + 1;
    const diffDays =
      totalDaysSince1970(bsY, bsM, bsD) -
      totalDaysSince1970(...this.todayBS.split("-").map(Number));

    return {
      adDate,
      bsDate,
      totalDays,
      dayOfYear,
      diffDays,
    };
  }

  // --------------------------
  // Info for BS date
  // --------------------------
  getBSInfo(bsDate) {
    if (!this.isValidBS(bsDate)) return null;

    const adDate = bsToAd(bsDate);
    const [y, m, d] = bsDate.split("-").map(Number);
    const totalDays = bsData[y][12];
    const dayOfYear =
      totalDaysSince1970(y, m, d) - totalDaysSince1970(y, 1, 1) + 1;
    const diffDays =
      totalDaysSince1970(y, m, d) -
      totalDaysSince1970(...this.todayBS.split("-").map(Number));

    return {
      bsDate,
      adDate,
      totalDays,
      dayOfYear,
      diffDays,
    };
  }
}

// --------------------------
// Export default object
// --------------------------
export const NepaliDate = new NepaliDateHelper();
