/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
( _ => {
  _.toaster = () => false;

  $(document).ready( () => {
    let adjustTop = () => false;
    let wrap = $('<div style="position: absolute; top: 50px; right: 20px; width: 300px"></div>');
    let nav = $('[role="growler"]');
    let mode = 'append';

    if ( nav.length > 0) {
      mode = 'prepend';
      wrap = $('<div style="position: absolute; top: -4rem; left: 5px; width: 290px"></div>');

    }
    else {
      nav = $('body > nav.sticky-top');

    }

    if ( nav.length > 0) {
      wrap.appendTo( nav[0]);

    }
    else {
      wrap.appendTo( 'body');
      adjustTop = () => {
        //~ console.log({ 'top' : ($(window).scrollTop() + 50) + 'px'});
        wrap.css({ 'top' : ($(window).scrollTop() + 50) + 'px'});

      };

    }

    _.toaster = params => {
      let options = {
        title : 'Info',
        text : '...',
        delay : 2000,
        growlClass : 'success'

      };

      if ( 'string' == typeof params) {
        options.text = params;

      }
      else {
        options = _.extend( options, params);

        if ( options.title == 'Info' || options.text == '...') {
          /*
          * a little repetitive - it's an ajax response
          *
          * the basic ajax response is:
          * { response : 'ack', description : 'go you good thing' }
          *
          * with an optional timeout set to 0 it will become a bootstrap v4 alert:
          * { response : 'ack', description : 'go you good thing', timeout : 0 }
          */
          if (!!params.response) { options.growlClass = ( /(ack|ok)/i.test( params.response) ? 'success' : 'error' ); }
          if ( !!params.description) { options.text = params.description; }
          if ( options.growlClass == 'error' ) options.delay = 6000;

        }

      }

      return new Promise( resolve => {
        let timestamp = _.dayjs();
        let toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="true"></div>').attr( 'data-delay', options.delay);
        let header = $('<div class="toast-header"></div>').appendTo(toast);

        if ( options.growlClass == 'error' ) {
          header.append( '<i class="bi bi-square-fill mr-2 text-danger"></i>');

        }
        else {
          header.append( '<i class="bi bi-square-fill mr-2 text-success"></i>');

        }

        $('<strong class="mr-auto"></strong>').html( options.title).appendTo( header);
        let timer = $('<small class="text-muted ml-2">just now</small>').appendTo( header);
        $('<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button>').appendTo( header);

        $('<div class="toast-body"></div>').html( options.text).appendTo(toast);

        adjustTop();
        toast.on('hidden.bs.toast', function( e) {
          $(this).remove();
          resolve(e);

        })

        if ('prepend' == mode) {
          toast.prependTo( wrap).toast('show');

        }
        else {
          toast.appendTo( wrap).toast('show');

        }

        let utime = (toast, timer, timestamp, utime) => {
          let secs= _.dayjs().unix() - timestamp.unix();
          timer.html( secs + ' second(s) ago');

          if ( toast.hasClass('show')) setTimeout( utime, 1000, toast, timer, timestamp, utime);

        };

        setTimeout( utime, 1000, toast, timer, timestamp, utime);

      });

    };

  });

})( _brayworth_);
