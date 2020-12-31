/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
(_ => _.textPrompt = params => {
  return new Promise(resolve => {
    let options = _.extend({
      title: 'Enter Text',
      text: '',
      verbatim: ''
    }, params);

    let modal = _.modal.template();

    $('.modal-dialog', modal).removeClass('modal-sm');
    $('.modal-footer', modal).append('<button type="submit" class="btn btn-outline-primary">OK</button>');
    $('.modal-title', modal).html(options.title);

    let input = $('<input type="text" name="text" class="form-control" required />');
    input.val(options.text).attr('placeholder', options.title);
    $('.modal-body', modal).append(input);

    if ('' != options.verbatim) {
      $('.modal-body', modal).append(
        $('<div class="form-text text-muted font-italic"></div>').html(options.verbatim)

      );

    }

    let form = $('<form></form>');
    form
    .append(modal)
    .appendTo('body')
    .on('submit', function (e) {
      let _form = $(this);
      let _data = _form.serializeFormJSON();

      options.text = _data.text;
      resolve(options.text);

      modal.modal('hide');
      return false;

    });

    if (!_brayworth_.browser.isMobileDevice) {
      modal.on('shown.bs.modal', e => { input.focus(); input.select(); });

    }

    modal.on('hidden.bs.modal', e => form.remove());
    modal.modal('show');

  });

})(_brayworth_);
