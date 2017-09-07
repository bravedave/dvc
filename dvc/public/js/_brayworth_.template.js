/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/


	load:
		$('<script></script>').attr('src','/js/_brayworth_.template.js').appendTo('head');

	test - get:
		_brayworth_.template({template:'sample'});
	*/

(function() {
	function t( src) {
		var _ = {
			src : src,
			_element : false,
			reset : function() {
				this._element = $(this.src);

			},

			get : function( k) {
				return $(k, this._element);

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

			},

			appendTo : function( p) {
				this._element.appendTo( p);

			}

		}

		_.reset();

		return (_);

	}

	_brayworth_.template = function(params) {
		return ( new Promise(function( success, fail) {
			var options = {
				template : '',

			}

			$.extend( options, params);

			if ( !options.template)
				fail( 'no template');

			$.ajax({
				type : 'post',
				url : '/',
				data : {
					action : 'get-template',
					template : options.template,
				}
			})
			.done( function( d) {
				success( t( d));

			});

		}));

	}

})();
