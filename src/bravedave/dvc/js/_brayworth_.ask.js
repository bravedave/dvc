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
 * */
(_ => {
  _.ask = params => {
    let dlg = _.modal.template();

    let options = {
      ...{
        beforeOpen: function () {
          let modal = this;
          $('.modal-title', modal).html(options.title);
          $('.modal-body', modal).html(options.text);

          let footer = $('.modal-footer', modal);
          let bCount = 0;
          $.each(options.buttons, (key, j) => {
            bCount++;
            $(`<button class="btn bg-body-secondary" type="button">${key}</button>`)
              .appendTo(footer)
              .on('click', e => {
                e.stopPropagation();
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
        headClass: 'text-white bg-dark',
        onClose: e => { },
        removeOnClose: true,
        text: 'string' == typeof params ? params : '',
        title: 'Topic',
        size: ''
      }, ...params
    };

    // console.log( options);

    if (options.removeOnClose) dlg.on('hidden.bs.modal', function (e) { $(this).remove(); });

    if (/text\-/.test(options.headClass)) $('.modal-header', dlg).removeClass('text-white text-light text-dark text-success text-danger text-warning text-info');

    if (/bg\-/.test(options.headClass)) $('.modal-header', dlg).removeClass('bg-white bg-light bg-dark bg-success bg-danger bg-warning bg-info');

    if ('' != String(options.headClass)) $('.modal-header', dlg).addClass(options.headClass);

    dlg.appendTo('body');
    options.beforeOpen.call(dlg);
    dlg.modal('show');

    return dlg;	// a jQuery element
  };

  _.ask.confirm = p => new Promise(resolve => _.ask({
    ...{
      title: 'Confirm',
      text: 'string' == typeof p ? p : '',
      buttons: { confirm: e => resolve() }
    }, ...p
  }));

  _.ask.alert = p => _.ask({ ...{ headClass: 'text-white bg-danger', size: 'sm', title: 'Alert', text: 'string' == typeof p ? p : '' }, ...p });
  _.ask.alert.confirm = p => new Promise(resolve => _.ask.alert({
    ...{
      title: 'Confirm',
      text: 'string' == typeof p ? p : '',
      buttons: { confirm: e => resolve() }
    }, ...p
  }));

  _.ask.success = p => _.ask({ ...{ headClass: 'text-white bg-success', title: 'Success', text: 'string' == typeof p ? p : '' }, ...p });
  _.ask.success.confirm = p => new Promise(resolve => _.ask.success({
    ...{
      title: 'Confirm',
      text: 'string' == typeof p ? p : '',
      buttons: { confirm: e => resolve() }
    }, ...p
  }));

  _.ask.warning = p => _.ask({ ...{ headClass: 'text-white bg-warning', size: 'sm', title: 'Warning', text: 'string' == typeof p ? p : '' }, ...p });
  _.ask.warning.confirm = p => new Promise(resolve => _.ask.warning({
    ...{
      title: 'Confirm',
      text: 'string' == typeof p ? p : '',
      buttons: { confirm: e => resolve() }
    }, ...p
  }));

})(_brayworth_);
