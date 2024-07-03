/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {

  _.get = _.fetch.get;

  _.get.DataUri = url => new Promise(resolve => {

    //~ console.log( '_cms_.get.DataUri : converting : ', url);
    const img = new Image();
    img.onload = function () {

      const canvas = document.createElement('canvas');
      canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
      canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

      canvas.getContext('2d').drawImage(this, 0, 0);

      // Get raw image data
      resolve(canvas.toDataURL('image/jpeg').replace(/^data:image\/(png|jpg|jpeg);base64,/, ''));
    };

    img.src = url;
  });

  _.get.modal = url => new Promise(resolve => {
    if (!!url) {

      _.get(url).then(modal => {

        let _modal = $(_.bootstrap.version() < 5 ? _.bootstrap.v5.v4(modal) : _.bootstrap.v4.v5(modal));

        if (_modal.hasClass('modal')) {

          _modal.appendTo('body');
          _modal.on('hidden.bs.modal', e => _modal.remove());
        } else {

          let w = $('<div></div>');
          w.append(_modal).appendTo('body');
          _modal = $('.modal', w);
          _modal.on('hidden.bs.modal', e => w.remove());
        }

        _modal.modal('show');
        resolve(_modal);
      });
    } else {

      let _modal = _.modal.template();
      _modal.appendTo('body');
      _modal.on('hidden.bs.modal', e => _modal.remove());
      _modal.modal('show');
      resolve(_modal);
    }
  });

  _.get.script = url => new Promise(resolve => {
    const s = document.createElement('script');
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

      _.fetch.post(_.url('sms'), { action: 'sms-enabled' })
        .then(d => {

          if ('ack' == d.response) {

            _.get.sms._enabled = 1;
            resolve();
          } else {

            _.get.sms._enabled = 2;
            // console.log( d);
            console.log('sms not enabled...');
          }
        });
    }
  });
})(_brayworth_);
