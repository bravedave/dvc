/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {
  _.get = url => new Promise(resolve => {
    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network Error');
        }
        return response.text();
      })
      .then(html => resolve(html));

  });

  _.get.modal = url => new Promise(resolve => {
    if (!!url) {
      _.get(url).then(modal => {

        if (_.bootstrap_version() >= 5) {

          modal = modal
            .replace(/data-dismiss/, 'data-bs-dismiss');
        }

        let _modal = $(modal);

        if (_.bootstrap_version() >= 5) {

          _modal.find('.close')
            .addClass('btn-close')
            .removeClass('close')
            .html('');

          _modal.find('.input-group-text').each( (i, el) => {

            let _el = $(el);

            if (_el.parent().hasClass('input-group-append') || _el.parent().hasClass('input-group-prepend')) {

              _el.parent().removeClass('input-group-append input-group-prepend').addClass('input-group-text');
              _el.removeClass('input-group-text');
            }
          });

          _modal.find('.text-left')
            .removeClass('text-left')
            .addClass('text-start');

          _modal.find('.text-right')
            .removeClass('text-right')
            .addClass('text-end');
        }

        if (_modal.hasClass('modal')) {
          _modal.appendTo('body');
          _modal.on('hidden.bs.modal', e => _modal.remove());

        }
        else {
          let w = $('<div></div>');

          w.append(_modal).appendTo('body');
          _modal = $('.modal', w);
          _modal.on('hidden.bs.modal', e => w.remove());
        }

        _modal.modal('show');
        resolve(_modal);
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

  _.get.script = url => new Promise(resolve => {
    let s = document.createElement('script');
    s.type = 'text/javascript';
    s.src = url;
    s.addEventListener('load', () => resolve());
    document.body.appendChild(s);

  });

  _.get.sms = () => _.get.modal(_.url('sms/dialog'));
  _.get.sms._enabled = 0;
  _.get.sms.enabled = () => new Promise(resolve => {
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

})(_brayworth_);
