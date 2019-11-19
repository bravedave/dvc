/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	this is intended to bring together
		templation - supercede, this does noting to the html, it just reads the file and caches it
		vue

	we want to load a vue specific html block

	test:
	_brayworth_.Vue.block({block:'modal'}).then( function( d) {
		var uid = 'x_' + Date.now();
		var modal = $(d).attr('id',uid).appendTo('body');

		_brayworth_.Vue({
			el : '#' + uid,
			data : {
				title : 'it\'s a cruel world',
				text : 'please step into my cruel world',

			},
			methods : {
				remove : function() {
					$(this.$el).remove();
					this.$destroy();

				}

			},
			destroyed : function() {
				console.log('it\'s over then');

			}

		})

	});

	_brayworth_.Vue.block({block:'panel'}).then( function( d) {
		console.log(d);

		$('body').html('');
		var uid = 'x_' + Date.now();
		$(d).attr('id',uid).appendTo('body');


		_brayworth_.Vue({
			el : '#' + uid,
			data : {
				title : 'it\'s a cruel world',
				body: 'please step into my cruel world',

			},

		})

	});

	*/
/*jshint esversion: 6 */
(function() {
	let cache = {};

	_brayworth_.Vue.block = function( params) {
		//~ console.log('_brayworth_.Vue.block');

		return ( new Promise( function( resolve, reject) {
			let options = {
				block : '',
				url : _brayworth_.urlwrite(),

			};

			$.extend( options, params);

			if ( !options.block) {
				reject( 'no template'); // rejected

			}
			else if ( 'string' != typeof options.block) {
				reject( 'template must be a string'); // rejected

			}
			else 	if ( options.block in cache) {
				//~ console.log( 'resolve from cache');
				resolve( cache[options.block]); // fulfilled

			}
			else {
				//~ console.log( 'not resolve from cache', cache);
				_brayworth_.post({
					url : options.url,
					data : {
						action : 'get-vue-block',
						block : options.block,
					}

				}).then( function( d) {
					cache[options.block] = d;
					resolve( d); // fulfilled

				});

			}

		}));

	};

})();
