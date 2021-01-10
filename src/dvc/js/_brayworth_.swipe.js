/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * $('#el).swipeon({ left : function(e) { console.log( 'oh left we go ...')} })
 *
 * */
/*jshint esversion: 6 */
( _ => {
  _.swipeOff = function() {
    $(this)
      .off('mousedown touchstart')
      .off('mouseup touchend');
  };

  _.swipeOn = function( params) {
    let options = _.extend( {
      left : () => {},
      right : () => {},
      up : () => {},
      down : () => {},
    }, params);

    let down = false;

    let touchEvent = e => {
      let _touchEvent = (x,y) => {return {'x':x,'y':y}};
      let evt = e.originalEvent;
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
      catch( err) {
        console.warn( err);

      }
      return ( _touchEvent(0,0));

    };

    let swipeEvent = (down,up) => {
      let j = {
        'direction' : '',
        x : up.x - down.x,
        y : up.y - down.y };

      if ( j.x > 70) {
        j.direction = 'right';

      }
      else if ( j.x < -70) {
        j.direction = 'left';

      }

      return (j);

    };

    let _me = $(this)

    _me
    .on('mousedown touchstart', function (e) {
      if ( /^(input|textarea|img|a|select)$/i.test( e.target.nodeName ))
        return;

      down = touchEvent(e);
      if ( down) _me.addClass('swiping');

    })
    .on('mouseup touchend',function (e) {
      if ( down) {
        let sEvt = swipeEvent( down, touchEvent( e));
        down = false;	// reset
        if ( down) _me.removeClass('swiping');

        if ( sEvt.direction == 'left') {
          options.left.call( _me, sEvt);

        }
        else if ( sEvt.direction == 'right') {
          options.right.call( _me, sEvt);

        }

      }

    });

  };

  $.fn.swipeOn = _.swipeOn;
  $.fn.swipeOff = _.swipeOff;

}) (_brayworth_);
