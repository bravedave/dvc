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

  let element = `<input type="text" name="text" class="form-control"
    placeholder="${options.title}"
    value="${options.text}" required>`;
  if ('textarea' == options.tag) {

    element = `<textarea name="text" class="form-control" rows="4"
      placeholder="${options.title}"
      required>${options.text}</textarea>`;
  }

  let helper = '';
  if ('' != options.verbatim) {

    const em = _.bootstrap.version() < 5 ? 'font-italic' : 'fst-italic';
    helper = `<div class="form-text text-muted ${em}">${options.verbatim}</div>`;
  }

  const uid = _.randomString();
  const form = $(`<form id="${uid}">${element}${helper}</form>`);

  import('/js/modal').then(({ modal }) => modal({

    title: options.title,
    show: function (e) {

      const m = $(this);
      // console.log(this);

      m.find('.modal-body').append(form);
      m.find('.modal-footer')
        .empty()
        .append(`<button type="submit" class="btn btn-outline-primary"
            form="${uid}">OK</button>`);

      form.on('submit', function (e) {

        options.text = form.find('[name="text"]').val();
        resolve(options.text);
        m.modal('hide');
        return false;
      });
    },
    shown: function (e) {

      if (!_.browser.isMobileDevice) {

        const input = form.find('[name="text"]');
        input.focus();
        input.select();
      }
    }
  }))
}))(_brayworth_);
