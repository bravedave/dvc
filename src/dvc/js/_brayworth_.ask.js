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
            yes : function() {
                $(this).modal('hide');
                console.log( 'ok', this);

            }

        }

    });

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
          $.each(options.buttons, (key, j) => {
            $('<button class="btn btn-light" type="button"></button>')
              .html(key)
              .appendTo(footer)
              .on('click', e => j.call(modal, e));

          });

        },
        buttons: {},
        headClass: 'text-white bg-dark',
        onClose: e => { },
        removeOnClose: true,
        text: 'Question',
        title: 'Topic',

      }, ...params
    };

    // console.log( options);

    if (options.removeOnClose) {
      dlg.on('hidden.bs.modal', function (e) { $(this).remove(); });

    }

    $('.modal-header', dlg).addClass(options.headClass);
    dlg.appendTo('body');
    options.beforeOpen.call(dlg);
    dlg.modal('show');

    return dlg;	// a jQuery element

  }

  _.ask.alert = p => _.ask({ ...{ headClass: 'text-white bg-danger', title: 'Alert' }, ...p });
  _.ask.success = p => _.ask({ ...{ headClass: 'text-white bg-success', title: 'Success' }, ...p });
  _.ask.warning = p => _.ask({ ...{ headClass: 'text-white bg-warning', title: 'Warning' }, ...p });

})(_brayworth_);
