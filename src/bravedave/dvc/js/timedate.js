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

  window.timeHandler = function (s) {

    let j = {
      Hours: 0,
      Minutes: 0,
      Suffix: 'am',
      zHours: function () {
        let i = this.Hours;
        if (12 == i && 'am' == this.Suffix) {
          i -= 12;  // 12am is 0 hours, 12pm is 12 hours

        }
        else if (12 != i && 'pm' == this.Suffix) {
          i += 12;

        }
        return i;

      },

      recon: function () {
        if (Number(this.Minutes) >= 60) {
          this.Hours += 1;
          this.Minutes -= 60;

        }

        return this;  // chain

      },

      reconHours: function () {
        if (this.Hours > 12) {
          this.Suffix = (this.Hours == 24 ? 'am' : 'pm');
          this.Hours -= 12;

        }
        else if (this.Hours == 12) {
          this.Suffix = 'pm';

        }

        return this;  // chain

      },

      toSeconds: function () {
        return ((this.zHours() * 60 * 60) + (this.Minutes * 60));

      },

      toString: function (bShort) {
        this
          .recon()
          .reconHours();

        let h = String(this.Hours);
        let m = String(this.Minutes);
        if (bShort) {
          let r = h;
          if (this.Minutes) r += m.padStart(2, '0');
          r += (/pm/i.test(this.Suffix) ? 'p' : 'a');

          return r;

        }
        else {
          return (h + ':' + m.padStart(2, '0') + ' ' + this.Suffix);

        }

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
        j.Hours = parseInt(a[0], 10);
        j.Minutes = parseInt(a[1], 10);

      }
      else if (s.indexOf('.') > 0) {
        // hours and minutes
        let a = s.split('.');
        j.Hours = parseInt(a[0], 10);
        j.Minutes = parseInt(a[1], 10);

      }
      else {
        j.Hours = parseInt(s, 10);

      }

      j.reconHours();

    }
    catch (e) {
      throw ('unable to parse time format : ' + e.description);

    }

    return j;
  }

})(_brayworth_);
