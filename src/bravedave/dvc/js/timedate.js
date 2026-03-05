/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

(_ => {
  window.CheckTimeFormat = function (obj) {

    if ((!obj) || (!obj.tagName)) {
      // obj is probably an Event ..
      if (this.tagName)
        if (/input/i.test(this.tagName)) obj = this;

      // otherwise fall through and error ..

    }

    let strVal = obj.value.trim();
    if (strVal == '')
      return true;

    let eTag = strVal.substring(strVal.length - 1, strVal.length);
    let sSuffix = 'am';
    let iHours = 0, iMinutes = 0, ampmThreshHold = 0;

    try {

      if (eTag == 'p' || eTag == 'P') {
        sSuffix = 'pm';
        strVal = strVal.substring(0, strVal.length - 1).trim();

      }
      else if (eTag == 'a' || eTag == 'A') {
        strVal = strVal.substring(0, strVal.length - 1).trim();

      }
      else if (eTag == 'm' || eTag == 'M') {
        eTag = strVal.substring(strVal.length - 2, strVal.length);
        if (eTag == 'pm' || eTag == 'pM' || eTag == 'Pm' || eTag == 'PM') {
          sSuffix = 'pm';
          strVal = strVal.substring(0, strVal.length - 2).trim();

        }
        else if (eTag == 'am' || eTag == 'aM' || eTag == 'Am' || eTag == 'AM') {
          strVal = strVal.substring(0, strVal.length - 2).trim();

        }

      }
      else {
        if (/number/.test(typeof (obj.ampmThreshHold)))
          ampmThreshHold = obj.ampmThreshHold;

        else
          ampmThreshHold = 7;

      }

      if (strVal.indexOf(':') > 0) {
        // hours and minutes
        let a = strVal.split(':');
        iHours = parseInt(a[0], 10);
        iMinutes = parseInt(a[1], 10);

      }
      else if (strVal.indexOf('.') > 0) {
        // hours and minutes
        let a = strVal.split('.');
        iHours = parseInt(a[0], 10);
        iMinutes = parseInt(a[1], 10);

      }
      else {
        iHours = parseInt(strVal, 10);
        if (iHours > 24) {
          if (/^1(0|1|2)/.test('' + iHours)) {
            iHours = strVal.charAt(0) + strVal.charAt(1);
            iMinutes = strVal.substring(2);

          }
          else {
            iHours = strVal.charAt(0);
            iMinutes = strVal.substring(1);

          }

        }
        //~ console.log( iHours, ',', iMinutes);

      }


      if (iHours >= 12) {
        sSuffix = 'pm';
        if (iHours > 12)
          iHours -= 12;

      }
      else if (ampmThreshHold > iHours) {
        sSuffix = 'pm';

      }

      let sHours = String(iHours)
      let sMinutes = String(iMinutes)
      obj.value = sHours + ':' + sMinutes.padStart(2, '0') + ' ' + sSuffix;

      return true;

    }
    catch (e) {
      alert('unable to parse time format : ' + e.description);
      return false;

    }

  }

  /**
   * Parse a time string or return a time object template.
   *
   * Usage:
   *  - Call `timeHandler()` or `timeHandler('')` to get an empty time object template:
   *      { Hours, Minutes, Suffix, zHours(), recon(), reconHours(), toSeconds(), toString(bShort) }
   *  - Call `timeHandler('9:30 am')`, `timeHandler('9.30pm')`, `timeHandler('930p')`, or `timeHandler('21:00')`
   *    to parse various formats. Suffixes may be 'a', 'am', 'p', 'pm' (case-insensitive).
   *
   * Parameters:
   *  - s: (string|undefined) time string to parse. If omitted or empty, returns the template object.
   *
  * Returns:
  *  - An object with numeric `Days`, `Hours`, `Minutes`, `Suffix` ('am'|'pm') and helper methods:
   *      - `zHours()` -> 24-hour hour value
   *      - `recon()` -> normalize minutes >= 60
   *      - `reconHours()` -> normalize hours > 12 and set Suffix
   *      - `toSeconds()` -> seconds since midnight
   *      - `toString(bShort)` -> formatted string (short when truthy)
   *
   * Throws:
   *  - If parsing fails, an exception is thrown with a descriptive message.
   *
   * Examples:
   *  - `timeHandler('8:05p').toString()` -> "8:05 pm"
   *  - `timeHandler('20').toSeconds()` -> seconds for 20:00
   */
  /**
   * delta
   *  test
   *    x = timeHandler('2pm')
   *    x.Minutes += 120
   *    x.toString()  // expected "4:00 pm"
   */
  window.timeHandler = function (s) {

    const j = {
      Days: 0,
      Hours: 0,
      Minutes: 0,
      Suffix: 'am',
      zHours: function () {
        // return total hours in 24-hour scale including Days
        let i = Number(this.Hours) || 0;

        if (this.Suffix === 'am' && i === 12) {
          i = 0; // 12am -> 0
        } else if (this.Suffix === 'pm' && i !== 12) {
          i += 12; // pm -> +12 except 12pm
        }

        const days = Number(this.Days) || 0;
        return i + (days * 24);
      },

      recon: function () {
        this.Minutes = Number(this.Minutes) || 0;
        this.Hours = Number(this.Hours) || 0;
        this.Days = Number(this.Days) || 0;

        // convert current Hours + Suffix into a 24-hour total
        let totalHours = this.Hours % 24;
        if (this.Suffix === 'pm') {
          if (this.Hours !== 12) totalHours = (this.Hours % 12) + 12;
        } else {
          // am
          if (this.Hours === 12) totalHours = 0;
        }

        // fold minutes into hours
        if (this.Minutes >= 60) {
          const addHours = Math.floor(this.Minutes / 60);
          totalHours += addHours;
          this.Minutes = this.Minutes % 60;
        }

        // fold hours into days
        if (totalHours >= 24) {
          const addDays = Math.floor(totalHours / 24);
          this.Days += addDays;
          totalHours = totalHours % 24;
        }

        this.Hours = totalHours;

        return this;  // chain
      },

      reconHours: function () {
        // compute display hour (1..12) without mutating internal 24-hour Hours
        const hrs = Number(this.Hours) || 0;
        if (hrs === 0) {
          this.Suffix = 'am';
          this.DisplayHour = 12;
        } else if (hrs === 12) {
          this.Suffix = 'pm';
          this.DisplayHour = 12;
        } else if (hrs > 12) {
          this.Suffix = 'pm';
          this.DisplayHour = hrs - 12;
        } else {
          this.Suffix = 'am';
          this.DisplayHour = hrs;
        }

        return this;  // chain
      },

      toSeconds: function () {
        return ((this.zHours() * 60 * 60) + ((Number(this.Minutes) || 0) * 60));
      },

      toString: function (bShort) {

        this
          .recon()
          .reconHours();

        let h = String(this.DisplayHour !== undefined ? this.DisplayHour : this.Hours);
        let m = String(this.Minutes);
        if (!!bShort) {

          let r = h;
          if (this.Minutes) r += m.padStart(2, '0');
          r += (/pm/i.test(this.Suffix) ? 'p' : 'a');
          return r;
        }
        let base = (h + ':' + m.padStart(2, '0') + ' ' + this.Suffix);
        if (this.Days) base = ('+' + this.Days + 'd ' + base);
        return base;
      }
    }

    if (!s) return j;
    if (s == '') return j;
    let eTag = s.substring(s.length - 1, s.length);

    try {

      if (eTag == 'p' || eTag == 'P') {
        j.Suffix = 'pm';
        s = s.substring(0, s.length - 1).trim();

      }
      else if (eTag == 'a' || eTag == 'A') {
        s = s.substring(0, s.length - 1).trim();

      }
      else if (eTag == 'm' || eTag == 'M') {
        eTag = s.substring(s.length - 2, s.length);
        // if ( eTag == 'pm' || eTag == 'pM' || eTag == 'Pm' || eTag == 'PM' ) {
        if (/^pm$/i.test(eTag)) {
          j.Suffix = 'pm';
          s = s.substring(0, s.length - 2).trim();

        }
        // else if ( eTag == 'am' || eTag == 'aM' || eTag == 'Am' || eTag == 'AM' ) {
        else if (/^am$/i.test(eTag)) {
          s = s.substring(0, s.length - 2).trim();

        }

      }

      if (s.indexOf(':') > 0) {
        // hours and minutes
        let a = s.split(':');
        j.Hours = Number.parseInt(a[0], 10) || 0;
        j.Minutes = Number.parseInt(a[1], 10) || 0;

      }
      else if (s.indexOf('.') > 0) {
        // hours and minutes
        let a = s.split('.');
        j.Hours = Number.parseInt(a[0], 10) || 0;
        j.Minutes = Number.parseInt(a[1], 10) || 0;

      }
      else {
        j.Hours = Number.parseInt(s, 10) || 0;

      }

      // normalize minutes->hours->days then compute display hour
      j.recon().reconHours();

    }
    catch (e) {

      throw ('unable to parse time format : ' + e.description);
    }

    return j;
  }
})(_brayworth_);
