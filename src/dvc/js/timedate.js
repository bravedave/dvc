/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

( _ => {
  window.CheckDateFormat = function( obj) {

    /* Date/Time Validation */
    if ( ( ! obj ) || ( ! obj.tagName )) {
      // obj is probably an Event ..
      if ( this.tagName ) {
        if ( /input/i.test( this.tagName))
          obj = this;
        // otherwise fall through and error ..

      }

    }

    let defaultInPast = false;
    let onlyFuture = false;
    if ( 'past' == obj.getAttribute('default')) {
      defaultInPast = true;

    }
    else if ( 'future' == obj.getAttribute('default')) {
      onlyFuture = true;

    }

    let strVal = obj.value.trim();
    if (strVal == '')
      return true;

    let d = new Date();

    let day = d.getDate();
    let month = d.getMonth()+1;
    let year = d.getFullYear();

    // console.log('1','CheckDateFormat', year);

    /* work some additions */
    if ( strVal.search(/tod|tom/i) > -1 ) {
      if ( strVal.search(/tom/i) > -1 )
        d = _.dayjs( d).add( 1, 'days').toDate();
      day = d.getDate();
      month = d.getMonth()+1;
      year = d.getFullYear();

    }
    else if ( strVal.search(/sun|mon|tue|wed|thu|fri|sat/i) > -1 ) {
      let target = 0;
      if ( strVal.search(/sun/i) > -1 ) target = 0;
      else if ( strVal.search(/mon/i) > -1 ) target = 1;
      else if ( strVal.search(/tue/i) > -1 ) target = 2;
      else if ( strVal.search(/wed/i) > -1 ) target = 3;
      else if ( strVal.search(/thu/i) > -1 ) target = 4;
      else if ( strVal.search(/fri/i) > -1 ) target = 5;
      else if ( strVal.search(/sat/i) > -1 ) target = 6;

      day = d.getDay();
      let dayOff = target - day + ( day > target ? 7 : 0  );
      d = _.dayjs( d).add( dayOff, 'days').toDate();

      day = d.getDate();
      month = d.getMonth()+1;
      year = d.getFullYear();

    }
    else if ( strVal.search(/d|w|m|y/i) > -1 ) {
      year = d.getFullYear();
      if ( strVal.search(/d/i) > -1 ) {
        let iPos = strVal.search(/d/i);
        let dayOff = parseInt( strVal.substring(0,iPos).trim(),10);
        year = d.getFullYear();
        d = _.dayjs( d).add( dayOff, 'days').toDate();

      }
      else if ( strVal.search(/w/i) > -1 ) {
        let iPos = strVal.search(/w/i);
        let dayOff = parseInt( strVal.substring(0,iPos).trim(),10)*7;
        d = _.dayjs( d).add( dayOff, 'days').toDate();
        year = d.getFullYear();

      }
      else if ( strVal.search(/m/i) > -1 ) {
        let iPos = strVal.search(/m/i);
        let monthOff = parseInt( strVal.substring(0,iPos).trim(),10);
        d = _.dayjs( d).add( monthOff, 'month').toDate();
        year = d.getFullYear();

      }
      else if ( strVal.search(/y/i) > -1 ) {
        let iPos = strVal.search(/y/i);
        let yearOff = parseInt( strVal.substring(0,iPos).trim(),10);
        year += yearOff;

      }

      month = d.getMonth()+1;
      day = d.getDate();

    }
    else if ( strVal.indexOf('+') > -1 ) {

      // must be at beginning or end
      if ( strVal.substr(0,1) == "+" ) {
        let dayOff = parseInt( strVal.substring(1),10);
        d = _.dayjs( d).add( dayOff, 'days').toDate();
        day = d.getDate();
        month = d.getMonth()+1;
        year = d.getFullYear();
        //~ console.log( day,month,year);

      }
      else if ( strVal.substr(--strVal.length,1) == "+" ) {
        let dayOff = parseInt( strVal.substring(0,--strVal.length),10);
        d = _.dayjs( d).add( dayOff, 'days').toDate();
        day = d.getDate();
        month = d.getMonth()+1;
        year = d.getFullYear();

      }

    }
    else if ( /(\/|\.)/.test( strVal)) {
      let a = [];
      if ( strVal.indexOf('/') > 0 )
        a = strVal.split('/');

      else if ( strVal.indexOf('.') > 0 )
        a = strVal.split('.');

      day = parseInt( Number( a[0]),10);
      month = parseInt( Number( a[1]),10);
      if (a.length > 2) {
        year = parseInt(a[2],10);
        if ( String(year).length < 4) {
          year = String(d.getFullYear()).substring(0, 4 - String(year).length) + year;

        }

      }
      else {
        // test if the date would fall in the past
        if (_.dayjs().date( day).month(month - 1).format('YYYY-MM-DD') < _.dayjs().format('YYYY-MM-DD')) {
          //~ console.log( 'adding year');
          year = d.getFullYear() +1;
          // console.log('2', 'CheckDateFormat', year);
          // console.log('2a', 'CheckDateFormat', day, month, _.dayjs().day(day).month(month - 1).format('YYYY-MM-DD'));

        }

      }

    }
    else {
      day = parseInt( Number(strVal),10);
      if ( day < d.getDate() && !defaultInPast )
        month ++;

    }


    if ( isNaN( day ))
      day = d.getDate();

    dMax = 31;
    if ( /9|4|6|11/.test( month )) dMax = 30;
    if ( /^2/.test( month ))
      dMax = ( year % 4 == 0 ? 29 : 28);


    if ( day < 1 || day > dMax ) {
      obj.value = _.dayjs().format('L');
      return false;

    }

    if ( month > 12 ) {
      month -= 12;
      year ++;

    }

    let a = {
      day		: day,
      month	: (parseInt(d.getMonth(),10)+1),
      year		: d.getFullYear()

    }
    // console.log('CheckDateFormat', a.year, year);

    if ( month > 0 || month < 13 ) a.month = month;
    if ( year > 0 && year.toString().length == 4 )	// only process a full year format
      a.year = year;

    let m = _.dayjs( new Date( a.year,a.month-1,a.day));
    // console.log('CheckDateFormat', a.year, m.format('L'));

    if (onlyFuture) {
      if (m.isBefore(_.dayjs())) m = m.add('1','year');
      if (m.isBefore(_.dayjs())) m = m.add('1','year');

    }

    obj.value = m.format('L');

    return true;

  }

  window.CheckTimeFormat = function( obj) {

    if ( ( ! obj ) || ( ! obj.tagName )) {
      // obj is probably an Event ..
      if ( this.tagName )
        if ( /input/i.test(this.tagName)) obj = this;

      // otherwise fall through and error ..

    }

    let strVal = obj.value.trim();
    if (strVal == '')
      return true;

    let eTag = strVal.substring( strVal.length -1, strVal.length );
    let sSuffix = 'am';
    let iHours = 0, iMinutes = 0, ampmThreshHold = 0;

    try {

      if ( eTag == 'p' || eTag == 'P' ) {
        sSuffix = 'pm';
        strVal = strVal.substring(0, strVal.length -1 ).trim();

      }
      else if ( eTag == 'a' || eTag == 'A' ) {
        strVal = strVal.substring(0, strVal.length -1 ).trim();

      }
      else if ( eTag == 'm' || eTag == 'M' ) {
        eTag = strVal.substring( strVal.length -2, strVal.length );
        if ( eTag == 'pm' || eTag == 'pM' || eTag == 'Pm' || eTag == 'PM' ) {
          sSuffix = 'pm';
          strVal = strVal.substring(0, strVal.length -2 ).trim();

        }
        else if ( eTag == 'am' || eTag == 'aM' || eTag == 'Am' || eTag == 'AM' ) {
          strVal = strVal.substring(0, strVal.length -2 ).trim();

        }

      }
      else {
        if ( /number/.test( typeof( obj.ampmThreshHold )))
          ampmThreshHold = obj.ampmThreshHold;

        else
          ampmThreshHold = 7;

      }

      if ( strVal.indexOf(':') > 0 ) {
        // hours and minutes
        let a = strVal.split(':');
        iHours = parseInt(a[0],10);
        iMinutes = parseInt(a[1],10);

      }
      else if ( strVal.indexOf('.') > 0 ) {
        // hours and minutes
        let a = strVal.split('.');
        iHours = parseInt(a[0],10);
        iMinutes = parseInt(a[1],10);

      }
      else {
        iHours = parseInt(strVal,10);
        if ( iHours > 24) {
          if ( /^1(0|1|2)/.test( '' + iHours)) {
            iHours = strVal.charAt(0) + strVal.charAt(1);
            iMinutes = strVal.substring( 2);

          }
          else {
            iHours = strVal.charAt(0);
            iMinutes = strVal.substring( 1);

          }

        }
        //~ console.log( iHours, ',', iMinutes);

      }


      if ( iHours >= 12 ) {
        sSuffix = 'pm';
        if ( iHours > 12 )
          iHours -= 12;

      }
      else if ( ampmThreshHold > iHours ) {
        sSuffix = 'pm';

      }

      let sHours = String(iHours)
      let sMinutes = String(iMinutes)
      obj.value = sHours + ':' + sMinutes.padStart(2,'0') + ' ' + sSuffix;

      return true;

    }
    catch(e) {
      alert( 'unable to parse time format : ' + e.description  );
      return false;

    }

  }

  window.timeHandler = function( s) {

    let j = {
      Hours: 0,
      Minutes: 0,
      Suffix: 'am',
      zHours : function() {
        let i = this.Hours;
        if (this.Suffix == 'pm')
          i += 12;
        return i;

      },

      recon : function() {
        if ( Number( this.Minutes) >= 60) {
          this.Hours += 1;
          this.Minutes -= 60;

        }

        return this;  // chain

      },

      reconHours : function() {
        if (this.Hours > 12) {
          this.Suffix = (this.Hours == 24 ? 'am' : 'pm');
          this.Hours -= 12;

        }
        else if (this.Hours == 12) {
          this.Suffix = 'pm';

        }

        return this;  // chain

      },

      toSeconds : function() {
        return ( ( this.zHours() * 60 * 60 ) + ( this.Minutes * 60 ));

      },

      toString : function( bShort) {
        this
        .recon()
        .reconHours();

        let h = String(this.Hours);
        let m = String(this.Minutes);
        if ( bShort ) {
          let r = h;
          if ( this.Minutes ) r += m.padStart(2,'0');
          r += ( /pm/i.test( this.Suffix ) ? 'p' : 'a' );

          return r;

        }
        else {
          return ( h + ':' + m.padStart(2,'0') + ' ' + this.Suffix );

        }

      }

    }

    if (!s) return j;
    if (s == '') return j;
    let eTag = s.substring( s.length -1, s.length );

    try {

      if ( eTag == 'p' || eTag == 'P' ) {
        j.Suffix = 'pm';
        s = s.substring(0, s.length -1 ).trim();

      }
      else if ( eTag == 'a' || eTag == 'A' ) {
        s = s.substring(0, s.length -1 ).trim();

      }
      else if ( eTag == 'm' || eTag == 'M' ) {
        eTag = s.substring( s.length -2, s.length );
        // if ( eTag == 'pm' || eTag == 'pM' || eTag == 'Pm' || eTag == 'PM' ) {
        if ( /^pm$/i.test( eTag)) {
          j.Suffix = 'pm';
          s = s.substring(0, s.length -2 ).trim();

        }
        // else if ( eTag == 'am' || eTag == 'aM' || eTag == 'Am' || eTag == 'AM' ) {
        else if ( /^am$/i.test( eTag)) {
          s = s.substring(0, s.length -2 ).trim();

        }

      }

      if ( s.indexOf(':') > 0 ) {
        // hours and minutes
        let a = s.split(':');
        j.Hours = parseInt(a[0],10);
        j.Minutes = parseInt(a[1],10);

      }
      else if ( s.indexOf('.') > 0 ) {
        // hours and minutes
        let a = s.split('.');
        j.Hours = parseInt(a[0],10);
        j.Minutes = parseInt(a[1],10);

      }
      else {
        j.Hours = parseInt(s,10);

      }

      j.reconHours();

    }
    catch(e) {
      throw( 'unable to parse time format : ' + e.description  );

    }

    return j;
  }

}) (_brayworth_);
