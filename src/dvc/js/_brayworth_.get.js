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
	_.get = url => {
		return new Promise( (resolve, reject) => {
			let req = new XMLHttpRequest();
			req.open('GET', url);

			req.onload = function() {
				// This is called even on 404 etc so check the status
				if (req.status == 200) {
					resolve( req.response);	// Resolve the promise with the response text
				}
				else {
					reject( Error( req.statusText)); // Otherwise reject with the status text which will hopefully be a meaningful error

				}

			};

			req.onerror = e => { reject( Error('Network Error')); };	// Handle network errors
			req.send();		// Make the request

		});

  };

  _.get.modal = url => {
    return new Promise((resolve, reject) => {
      _.get( url).then( modal => {
        let _modal = $(modal);

        if ( _modal.hasClass('modal')) {
          _modal.appendTo('body');
          _modal.on('hidden.bs.modal', e => _modal.remove());

        }
        else {
          let w = $('<div></div>');

          w.append( _modal).appendTo('body');
          _modal = $( '.modal', w);
          _modal.on('hidden.bs.modal', e => w.remove());

        }

        _modal.modal('show');
        resolve( _modal);

      });

    });

  };

}) (_brayworth_);
