/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/

_brayworth_.swipeOff = function() {
	$(this)
		.off('mousedown touchstart')
		.off('mouseup touchend');
};

_brayworth_.swipeOn = function( params) {
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

};