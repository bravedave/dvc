/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * test:
    _brayworth_.ask({
      buttons : {
        yes : e => console.log( 'ok', this)
      }
    });

    _brayworth_.ask.alert.confirm('ok to do this').then(e => console.log('righto'));

    // dismissing (x, escape, backdrop) rejects with _.ask.dismissed, silently unless caught
    _brayworth_.ask.alert.confirm('ok to do this')
      .then(e => console.log('righto'))
      .catch(r => {
        if (_brayworth_.ask.dismissed === r) return console.log('cancelled');
        console.error(r);
      });

    // or trap the dismissal with a callback
    _brayworth_.ask.confirm({ text: 'ok to do this', onDismiss: e => console.log('cancelled') })
      .then(e => console.log('righto'));
 * */
(_ => {
  _.ask = params => {
    const dlg = _.modal.template();
    let acted = false;

    const options = {
      ...{
        beforeOpen: function () {
          let modal = this;
          $('.modal-title', modal).html(options.title);
          $('.modal-body', modal).html(options.text);

          let footer = $('.modal-footer', modal);
          let bCount = 0;
          $.each(options.buttons, (key, j) => {
            bCount++;
            $(`<button class="btn btn-light" type="button">${key}</button>`)
              .appendTo(footer)
              .on('click', e => {
                e.stopPropagation();
                acted = true;
                modal.modal('hide');

                j.call(modal, e);
              })
          });

          if (0 == bCount) footer.addClass('d-none');

          if ('sm' == String(options.size)) $('.modal-dialog', modal).addClass('modal-sm');
          if ('lg' == String(options.size)) $('.modal-dialog', modal).addClass('modal-lg');
          if ('xl' == String(options.size)) $('.modal-dialog', modal).addClass('modal-xl');

        },
        buttons: {},
        headClass: 'text-bg-dark',
        onClose: e => { },
        onDismiss: e => { },
        hidden: e => { },
        shown: e => { },
        removeOnClose: true,
        text: 'string' == typeof params ? params : '',
        title: 'Topic',
        size: '',
      }, ...params
    };

    // console.log( options);

    if (options.removeOnClose) dlg.on('hidden.bs.modal', function (e) { $(this).remove(); });

    if (/(^text| text)\-/.test(options.headClass)) {

      /**
       * remove the text classes including the bg- ones
       */
      const removeClasses = [
        'text-white',
        'text-light',
        'text-dark',
        'text-success',
        'text-danger',
        'text-warning',
        'text-info',
        'text-bg-white',
        'text-bg-light',
        'text-bg-dark',
        'text-bg-success',
        'text-bg-danger',
        'text-bg-warning',
        'text-bg-info',
        'text-bg-primary',
        'text-bg-secondary'
      ];
      $('.modal-header', dlg).removeClass(removeClasses.join(' '));
    }

    if (/bg\-/.test(options.headClass)) {

      const removeClasses = [
        'bg-primary',
        'bg-secondary',
        'bg-white',
        'bg-light',
        'bg-dark',
        'bg-success',
        'bg-danger',
        'bg-warning',
        'bg-info'
      ];
      $('.modal-header', dlg).removeClass(removeClasses.join(' '));
    }

    if ('' != String(options.headClass)) $('.modal-header', dlg).addClass(options.headClass);

    dlg.appendTo('body');
    options.beforeOpen.call(dlg);
    dlg.on('hidden.bs.modal', options.hidden);
    dlg.on('shown.bs.modal', options.shown);

    // closed (x, escape, backdrop) without a button action
    dlg.on('hidden.bs.modal', e => { if (!acted) options.onDismiss(e); });

    dlg.modal('show');

    return dlg;	// a jQuery element
  };

  /**
   * a dismissed confirm dialog rejects with _.ask.dismissed so the promise always settles;
   * the rejection is not reported to the console, catch it to react to a dismissal
   */
  _.ask.dismissed = Symbol('dialog dismissed');
  window.addEventListener('unhandledrejection', e => { if (_.ask.dismissed === e.reason) e.preventDefault(); });

  const confirmer = (ask, p) => new Promise((resolve, reject) => ask({
    ...{
      title: 'Confirm',
      text: 'string' == typeof p ? p : '',
      buttons: { confirm: e => resolve() }
    }, ...p,
    onDismiss: e => {
      reject(_.ask.dismissed);
      if (p && 'function' == typeof p.onDismiss) p.onDismiss(e);
    }
  }));

  _.ask.confirm = p => confirmer(_.ask, p);

  _.ask.alert = p => _.ask({ ...{ headClass: 'text-bg-danger', size: 'sm', title: 'Alert', text: 'string' == typeof p ? p : '' }, ...p });
  _.ask.alert.confirm = p => confirmer(_.ask.alert, p);

  _.ask.info = p => _.ask({ ...{ headClass: 'text-bg-info', title: 'Information', text: 'string' == typeof p ? p : '' }, ...p });
  _.ask.success = p => _.ask({ ...{ headClass: 'text-bg-success', title: 'Success', text: 'string' == typeof p ? p : '' }, ...p });
  _.ask.success.confirm = p => confirmer(_.ask.success, p);

  _.ask.warning = p => _.ask({ ...{ headClass: 'text-bg-warning', size: 'sm', title: 'Warning', text: 'string' == typeof p ? p : '' }, ...p });
  _.ask.warning.confirm = p => confirmer(_.ask.warning, p);
})(_brayworth_);
