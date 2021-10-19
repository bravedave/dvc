/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
( _ => {
	_.get = url => {
		return new Promise( (resolve, reject) => {
      fetch(url)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network Error');
          }
          return response.text();
        })
        .then(html => resolve(html));

		});

  };

  _.get.modal = url => {
    return new Promise( resolve => {
      if ( !!url) {
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

      }
      else {
        let _modal = _.modal.template();

        _modal.appendTo('body');
        _modal.on('hidden.bs.modal', e => _modal.remove());
        _modal.modal('show');

        resolve(_modal);

      }

    });

  };

  _.get.script = url => {
    return new Promise(resolve => {
      let s = document.createElement('script');
      s.type = 'text/javascript';
      s.src = url;
      s.addEventListener('load', () => resolve());
      document.body.appendChild(s);

    });

  };

  _.get.sms = () => {
    return _.get.modal(_.url('sms/dialog'))

  };
  _.get.sms._enabled = 0;
  _.get.sms.enabled = () => {
    return new Promise(resolve => {
      if (1 == _.get.sms._enabled) resolve();

      if (0 == _.get.sms._enabled) {
        _.post({
          url: _.url('sms'),
          data: { action: 'sms-enabled' },

        }).then(d => {
          if ('ack' == d.response) {
            _.get.sms._enabled = 1;
            resolve();

          }
          else {
            _.get.sms._enabled = 2;
            // console.log( d);
            console.log('sms not enabled...');

          }

        });

      }

    });

  };

}) (_brayworth_);
