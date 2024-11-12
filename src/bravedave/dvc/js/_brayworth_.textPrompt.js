/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 */
(_ => _.textPrompt = params => new Promise(resolve => {

  const options = {
    ...{
      title: 'Enter Text',
      text: '',
      verbatim: '',
      tag: 'input'
    }, ...params
  };

  _.get.modal().then(m => {

    const uid = _.randomString();

    m.find('.modal-dialog').removeClass('modal-sm');
    m.find('.modal-footer')
      .removeClass('d-none')
      .append(`<button type="submit" class="btn btn-outline-primary" form="${uid}">OK</button>`);
    m.find('.modal-title').text(options.title);

    const form = $(`<form id="${uid}"></form>`);
    if ('textarea' == options.tag) {

      form.append('<textarea name="text" class="form-control" rows="4" required></textarea>');
    } else {

      form.append('<input type="text" name="text" class="form-control" required />');
    }

    form.find('[name="text"]').val(options.text).attr('placeholder', options.title);

    if ('' != options.verbatim) {

      const em = _.bootstrap.version() < 5 ? 'font-italic' : 'fst-italic';
      $(`<div class="form-text text-muted ${em}"></div>`)
        .text(options.verbatim)
        .appendTo(form);
    }

    form.on('submit', function (e) {

      options.text = form.find('[name="text"]').val();
      resolve(options.text);
      m.modal('hide');
      return false;
    });

    m.find('.modal-body').append(form);

    if (!_.browser.isMobileDevice) {

      m.on('shown.bs.modal', e => {

        const input = form.find('[name="text"]');
        input.focus();
        input.select();
      });
    }
  });
}))(_brayworth_);
