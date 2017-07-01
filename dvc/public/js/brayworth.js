/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
( function() {
	return;
	// Add feature check for Service Workers here
	if('serviceWorker' in navigator) {
		if ( /^https:/.test( window.location.href)) {
			navigator.serviceWorker
			.register('/js/service-worker.js')
			.then(function() {
				//~ console.log('Service Worker Registered');
			});

		}
		//~ else {
			//~ console.log( 'insecure location : not loading service worker');

		//~ }

	}

})();

$(document).ready( function() {
	_brayworth_.InitHRefs();
	_brayworth_.initDatePickers();

	$('[data-role="back-button"]').each( function( i, el ) {
		//~ console.log('processing : window.history.back()');
		$(el)
		.css('cursor','pointer')
		.on('click', function( evt ) {
			evt.stopPropagation(); evt.preventDefault();
			//~ console.log('window.history.back()');
			window.history.back();

		})

	})

	$('[data-role="visibility-toggle"]').each( function( i, el ) {
		//~ console.log('processing : window.history.back()');
		var o = $(el);
		var target = o.data('target');
		var oT = $('#' + target);
		if (oT) {

			o
			.css('cursor','pointer')
			.on('click', function( evt ) {
				evt.stopPropagation(); evt.preventDefault();
				if ( oT.hasClass( 'hidden'))
					oT.removeClass( 'hidden' );
				else
					oT.addClass( 'hidden' );

			})

		}

	})

	/** Scrolls the content into view **/
	$('a[href*="#"]:not([href="#"] , .carousel-control, .ui-tabs-anchor)').click(function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			var target = $(this.hash);
			target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			if (target.length) {
				if ( /nav/i.test( target.prop('tagName')))
					return;

				var tTop = target.offset().top;
				var nav = $('body>nav');

				if ( nav.length )
					tTop -= ( nav.height()+20);

				tTop = Math.max( 20, tTop);
				$('html, body').animate({
					scrollTop: tTop
				}, 1000);
				return false;

			}

		}

	});

	$('[role="print-page"]').each( function( i, el ) {
		$(el).on('click', function(e) {
			e.preventDefault();

			window.print();

		});

	});

});


(function ($) {
	$.fn.serializeFormJSON = function () {

		var o = {};
		var a = this.serializeArray();
		$.each(a, function () {
			if (o[this.name]) {
				if (!o[this.name].push)
					o[this.name] = [o[this.name]];

				o[this.name].push(this.value || '');
			}
			else
				o[this.name] = this.value || '';

		});

		return o;

	};

	$.fn.growlSuccess = function(params) {
		var options = { growlClass : 'success' }

		if ( /object/.test( typeof params))
			$.extend( options, params);
		else if ( /string/i.test( typeof params ))
			options.text = params;

		$(this).growl( options);

	}

	$.fn.growlError = function(params) {
		var options = { growlClass : 'error' }

		if ( /object/.test( typeof params))
			$.extend( options, params);
		else if ( /string/i.test( typeof params ))
			options.text = params;

		$(this).growl( options);

	}

	$.fn.growlAjax = function(j) {
		/*
		 * my standard ajax response is { response : 'ack or nak', description : 'blah blah ..' }
		 */

		var options = { growlClass : 'error', text : 'no description' }
		if ( !!j.response) {
			if ( j.response == 'ack')
				options.growlClass = 'success';

		}
		if ( !!j.description)
			options.text = j.description;
		if ( !!j.timeout)
			options.timeout = j.timeout;

		$(this).growl( options);

	}

	var growlers = []

	$.fn.growl = function(params) {
		var me = $(this);
		var options = {
			top : 60,
			right : 20,
			text : '',
			title : '',
			timeout : 3000,
			growlClass : 'information',

		}

		if ( /object/.test( typeof params))
			$.extend( options, params);
		else if ( /string/i.test( typeof params ))
			options.text = params;

		if ( options.title == '' && options.text == '')
			return;	// abandon ship

		var growler = $('<div class="growler"></div>');

		/*
		 * you have to find a place in the growlers for this one
		 */
		var growlerIndex = -1
		$.each( growlers, function( i, e) {
			if ( !e) {
				growlerIndex = i;
				growlers[growlerIndex] = growler;
				//~ console.log( 'growlerIndex - recycle', growlerIndex);
				return ( false);

			}

		});

		if ( growlerIndex < 0) {
			// grow the index
			growlerIndex = growlers.length;
			growlers[growlerIndex] = growler;
			//~ console.log( 'growlerIndex - new', growlerIndex);

		}

		options.top *= growlerIndex;	// this growler is offset down screen to avoid stacking

		var title = $('<h3></h3>');
		var content = $('<div></div>');

		if ( options.title != '')
			title.html(options.title).appendTo( growler);
		else
			content.css('padding-top','5px');

		if ( options.text != '')
			content.html(options.text).appendTo( growler);

		growler
			.css({ 'position' : 'absolute', 'top' : options.top, 'right' : options.right })
			.addClass(options.growlClass)
			.appendTo(this);

		setTimeout( function() {
			growlers[growlerIndex] = false;
			growler.remove();
		}, options.timeout)

	}

	$.fn.swipeOn = function( params) {
		var options = {
			left : function() {},
			right : function() {},
			up : function() {},
			down : function() {},
		}

		$.extend( options, params);

		var down = false;

		var touchEvent = function( e) {
			var _touchEvent = function( x, y) { return ({'x':x,'y':y}) }
			var evt = e.originalEvent;
			try {
				if ('undefined' !== typeof evt.pageX) {
					return ( _touchEvent( evt.pageX, evt.pageY));

				}
				else if ('undefined' !== typeof evt.touches) {
					if ( evt.touches.length > 0)
						return ( _touchEvent( evt.touches[0].pageX, evt.touches[0].pageY));
					else
						return ( _touchEvent( evt.changedTouches[0].pageX, evt.changedTouches[0].pageY));

				}

			}
			catch(e) {
				console.warn( e);

			}
			return ( _touchEvent(0,0));

		}

		var swipeEvent = function( down, up) {
			var j = {
				'direction' : '',
				x : up.x - down.x,
				y : up.y - down.y }

			if ( j.x > 70)
				j.direction = 'right'
			else if ( j.x < -70)
				j.direction = 'left'

			return (j);

		}

		$(this)
		.on('mousedown touchstart', function (e) {
			if ( /^(input|textarea|img|a|select)$/i.test( e.target.nodeName ))
				return;

			down = touchEvent(e);

		})
		.on('mouseup touchend',function (e) {
			if ( down) {
				var sEvt = swipeEvent( down, touchEvent( e));
				down = false;	// reset

				if ( sEvt.direction == 'left')
					options.left();
				else if ( sEvt.direction == 'right')
					options.right();

			}

		});

	}

	$.fn.swipeOff = function() {
		$(this)
		.off('mousedown touchstart')
		.off('mouseup touchend');
	}

	String.prototype.isEmail = function() {
		if ( this.length < 3)
			return ( false);

		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		return emailReg.test(this);

	}

})(jQuery);
