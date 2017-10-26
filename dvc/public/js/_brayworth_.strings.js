/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	test:
		x = '0418745334';
		x.IsMobilePhone();
	*/
(function() {	// strings
	String.prototype.trim = function() {return this.replace(/^\s+|\s+$/g,"");}
	String.prototype.ltrim = function() {return this.replace(/^\s+/,"");}
	String.prototype.rtrim = function() {return this.replace(/\s+$/,"");}
	String.prototype.pad = function(len, padChar){
		if ( padChar == undefined ) {padChar = " ";}
		if ( isNaN(len) ) { len = this.length; }
		var res = this;
		while ( res.length < len ) {res = res.concat(padChar);}
		return ( res );
	};

	String.prototype.padLeft = function(len, padChar){
			if ( padChar == undefined ) {padChar = " ";}
			if ( isNaN(len) ) { len = this.length; }
			var res = this;
			if (res.length > len) {
				var iStart = ( res.length - len );
				res = res.substring( iStart );
			} else {
				while ( res.length < len ) {res = padChar.concat(res);};
			}
			return ( res );
	};

	String.prototype.format = function () {
		var args = arguments;
		return this.replace(/\{\{|\}\}|\{(\d+)\}/g, function (m, n) {
			if (m == "{{") { return "{"; }
			if (m == "}}") { return "}"; }
			return args[n];
		});
	};

	String.prototype.toCapitalCase = function() {
		var re = /\s/;
		var words = this.split(re);
		re = /(\S)(\S+)/;
		var reI = /^[a-zA-Z]'[a-zA-Z]+$/;
		for (var i = words.length - 1; i >= 0; i--) {
			if ( words[i] != "&" ) {
				if ( words[i].length > 3 && reI.test(words[i])) {
					//~ alert( 'It\'s Irish' );
					parts = words[i].split(/'/);
					words[i] = parts[0].toUpperCase() + "'" + parts[1].substring(0,1).toUpperCase() + parts[1].substring(1).toLowerCase();
				} else if ( re.test(words[i])) {
					words[i] = RegExp.$1.toUpperCase() + RegExp.$2.toLowerCase();
				}
			}
		}
		return words.join(' ');
	}

	String.prototype.AsLocalPhone = function() {
		var p = this;
		var ns = this.replace(/\s+|\(|\)|\-/g,"");
		if ( ns.length == 8 ) {

			// test if has area code
			if ( ! ns.substring(0,1) == '0' )
				ns = fallback_area_code + ns;
		}

		if ( ns.length == 10 ) {

			re = /(\S\S)(\S\S\S\S)(\S+)/;
			if ( re.test(ns)) {
				//~ ns = "(" + RegExp.$1 + ") " + RegExp.$2 + " " + RegExp.$3;
				if ( !!useCompactPhoneFormat)
					ns = RegExp.$1 + " " + RegExp.$2 + "" + RegExp.$3;
				else
					ns = RegExp.$1 + " " + RegExp.$2 + " " + RegExp.$3;

				return( ns );
			}
		} else if ( /^0011/.test( ns )) {

			re = /(\S\S\S\S)(\S\S)(\S+)/;
			if ( re.test(ns)) {
				//~ ns = "(" + RegExp.$1 + ") " + RegExp.$2 + " " + RegExp.$3;
				ns = RegExp.$1 + " " + RegExp.$2 + " " + RegExp.$3;
				return( ns );
			}
		}

		return( p );

	};

	String.prototype.IsMobilePhone = function() {
		var p = this;
		var ns = this.replace(/\s+|\(|\)|\-/g,"");
		if ( ns.length == 10 )
			return ( true);

		else if ( /^(\+|0011)/.test( ns)) {
			re = /(\S\S\S\S)(\S+)/;
			if ( re.test(ns))
				return( true);

		}

		return( false);

	};

	String.prototype.AsMobilePhone = function() {
		var p = this;
		var ns = this.replace(/\s+|\(|\)|\-/g,"");
		if ( ns.length == 10 ) {
			re = /(\S\S\S\S)(\S\S\S)(\S+)/;
			if ( re.test(ns)) {
				//~ ns = "(" + RegExp.$1 + ") " + RegExp.$2 + " " + RegExp.$3;
				if ( !!useCompactPhoneFormat)
					ns = RegExp.$1 + " " + RegExp.$2 + '' + RegExp.$3;
				else
					ns = RegExp.$1 + " " + RegExp.$2 + ' ' + RegExp.$3;
				return( ns );

			}

		}
		else if ( /^(\+|0011)/.test( ns )) {
			re = /(\S\S\S\S)(\S+)/;
			if ( re.test(ns)) {
				//~ ns = "(" + RegExp.$1 + ") " + RegExp.$2;
				ns = RegExp.$1 + " " + RegExp.$2;
				return( ns );

			}

		}
		return( p );

	};

	String.prototype.isEmail = function() {
		if ( this.length < 3)
			return ( false);

		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		return emailReg.test(this);

	}

	Number.prototype.formatComma = function(x) {
		return this.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

	}

	Number.prototype.formatCurrency = function() {
		var parts = this.toString().split(".");
		if ( parts.length < 2)
			parts.push( '00');
		else if ( parts[1].length < 1)
			parts[1] += '00';
		else if ( parts[1].length < 2)
			parts[1] += '0';

		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		return parts.join(".");

	}

})();

