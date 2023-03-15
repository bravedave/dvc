/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {
  _.loadModal = params => {
    let options = {
      ...{
        url: _.url('modal'),
        headerClass: '',
        beforeOpen: () => { },
        onClose: () => { },
        onSuccess: () => { },
      }, ...params
    };

    //~ console.log( options);

    return (new Promise(resolve => {
      _.get(options.url).then(data => {
        let _modal = $(_.bootstrap.version() < 5 ? data : _.bootstrap.v4.v5(data));
        let modal = $(_modal).appendTo('body');

        modal
          .on('brayworth.success', options.onSuccess)
          .on('brayworth.modal', options.onClose)
          .on('hidden.bs.modal', function (e) {
            modal.remove();
            modal.trigger('brayworth.modal');
          });

        if ('' != options.headerClass) {
          modal.find('.modal-header')
            .removeClass()
            .addClass('modal-header')
            .addClass(options.headerClass);
        }

        if (!_.browser.isMobileDevice) {
          let autofocus = modal.find('[autofocus]');
          if (autofocus.length > 0) {
            modal.on('shown.bs.modal', e => autofocus.first().focus());

          }
          else {
            modal.find('textarea:not([type="hidden"]):not([readonly]), input:not([type="hidden"]):not([readonly]), button:not([disabled]), a:not([tabindex="0"])');
            if (autofocus.length > 0) {
              modal.on('shown.bs.modal', e => autofocus.first().focus());

            }
          }
        }

        modal.on('show.bs.modal', options.beforeOpen);

        modal.modal('show');
        resolve(modal);
      });
    }));
  };
})(_brayworth_);
