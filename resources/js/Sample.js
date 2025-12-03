// Color helpers
const red = (txt) => `\x1b[31m${txt}\x1b[0m`;
const green = (txt) => `\x1b[32m${txt}\x1b[0m`;
const yellow = (txt) => `\x1b[33m${txt}\x1b[0m`;
const blue = (txt) => `\x1b[34m${txt}\x1b[0m`;

import {
  bsToAd,
  adToBs,
  isValidBSDate,
  isValidADDate,
} from "./nepaliDateConverter.js";

console.log(green(bsToAd("2062-02-02")), `//bsToAd("2062-02-02")`);
console.log(blue(adToBs("2005-05-16")), `//adToBs("2005-05-16")`);

console.log(green(bsToAd("2056-04-13")), `//bsToAd("2056-04-13")`);
console.log(blue(adToBs("1999-07-29")), `//adToBs("1999-07-29")`);

console.log(yellow(adToBs("2011-06-12")), `//adToBs("2011-06-12")`);

console.log(red(bsToAd("2044-03-32")), `//bsToAd("2044-03-32")`);

console.log(green(isValidBSDate("2056-04-31")), `//isValidBSDate("2056-04-31")`);
console.log(green(isValidADDate("2005-05-16")), `//isValidADDate("2005-05-16")`);


import { NepaliDate } from "./NepaliDateHelper.js";

console.log(NepaliDate.getADInfo("2026-07-29"));
console.log(NepaliDate.getBSInfo("2083-04-15"));
