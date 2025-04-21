<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace amazing;

use bravedave\dvc\strings;

?>
<div class="container" id="<?= $_container = strings::rand() ?>">
  <h1>Bootstrap 5 Modal</h1>
  <p>Bootstrap 5 modal with form</p>
  <button type="button" class="btn btn-primary js-demo">
    Launch demo modal
  </button>
</div>

<script type="module">
  const _ = _brayworth_;
  const container = $('#<?= $_container ?>');

  container.find('.js-demo').on('click', async e => {

    _.hideContexts(e);

    const {
      modal
    } = await import('<?= strings::url($this->route . '/js/modal') ?>');

    const id = _.randomString();
    const m = $(modal('Amazing modal !'));

    m.on('shown.bs.modal', () => form.find('input[name="name"]').focus());

    const form = $(`<form id="${id}">
        <input type="text" name="name" class="form-control" placeholder="Name">
      </form>`);

    form.on('submit', e => {

      e.preventDefault();
      m.modal('hide');
    });

    m.find('.modal-body').empty()
      .append(form);
    m.find('.modal-footer')
      .append(`<button type="submit" class="btn btn-primary" form="${id}">Submit</button>`);
  });

  // console.log('finished')
</script>