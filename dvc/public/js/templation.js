/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/


	load:
		$('<script></script>').attr('src','/js/templation.js').appendTo('head');

	test - get:
		In this example d contains a template object - sort of vuejs ish (not very)
		templation({template:'sample'}).done( function( d) { console.log( d)});

	usage:
		$('body').html('');	// clear the page
		var t = templation.template('table').appendTo('body');
		for ( var i = 1; i < 10; i++) {
			var r = templation.template('tr').appendTo( t.get('tbody'));
			r.append( $('<td></td>').html( i));

		}

	*/

var templation = {
	urlwrite : function( _url) {
		if ( 'undefined' == typeof _url)
			_url = '';

		return ( '/' + _url);

	}

};

(function() {
	/*
		some predefined templates
		add more with templation.loadHTML('tr','<tr></tr>');
	*/
	var cache = {
		container : '<div class="container"></div>',
		row : '<div class="row"></div>',
		form : '<form></form>',
		table : '<table><tbody></tbody></table>',
		tr : '<tr></tr>',
		modal : '<div class="modal"><div class="modal-content" role="dialog" aria-labelledby="modal-header-title"><div class="modal-header"><i class="fa close"></i><h1 id="modal-header-title"></h1></div><div class="modal-body"></div></div></div>',

	};

	function _t( src) {
		var _ = {
			src : src,
			_element : false,
			get : function( k) {
				if ( 'undefined' == typeof k)
					return ( this._element);
				else
					return $( k, this._element);

			},

			data : function( k, v) {
				if ( 'undefined' == typeof v)
					return ( this._element.data( k));
				else
					return ( this._element.data( k, v));

			},

			html : function( k, v) {
				var e = this.get(k);
				if ( 'undefined' != typeof( v))
					return ( e.html( v));

				else
					return ( e.html());

			},

			val : function( k, v) {
				var e = this.get(k);
				if ( 'undefined' != typeof( v))
					return ( e.val( v));

				else
					return ( e.val());

			},

			append : function( p) {
				this._element.append( p);
				return (this);

			},

			appendTo : function( p) {
				this._element.appendTo( p);
				return (this);

			},

			prependTo : function( p) {
				this._element.prependTo( p);
				return (this);

			},

			remove : function( p) {
				this._element.remove();
				return (this);

			},

			reset : function() {
				this._element = $(this.src);
				return (this);

			}

		}

		return ( _.reset());

	}

	templation.template = function( name) {
		if ( name in cache)
			return( _t( cache[name]));
		else
			throw 'template not in cache';

	}

	templation.loadHTML = function( key, fragment) {
		cache[key] = fragment;
		return( _t( fragment));

	}

	templation.load = function( params) {
		return ( new Promise( function( resolve, reject) {
			var options = {
				type : 'post',
				template : '',
				url : templation.urlwrite(),

			}

			$.extend( options, params);

			if ( !options.template) {
				reject( 'no template'); // rejected

			}
			else if ( 'string' != typeof options.template) {
				reject( 'template must be a string'); // rejected

			}
			else {
				if ( options.template in cache) {
					//~ console.log( 'resolve from cache');
					resolve( _t( cache[options.template])); // fulfilled

				}
				else {
					//~ console.log( 'not resolve from cache', cache);

					$.ajax({
						type : options.type,
						url : options.url,
						data : {
							action : 'get-template',
							template : options.template,
						}

					})
					.done( function( d) {
						cache[options.template] = d;
						resolve( _t( d)); // fulfilled

					});

				}

			}

		}));

	}

})();
