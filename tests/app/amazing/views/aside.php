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
  <h5>Bootstrap 5 Modal</h1>
    <p><em><small>Bootstrap 5 modal with form</small></em></p>
    <button type="button" class="btn btn-primary js-demo">
      Launch demo modal
    </button>
</div>

<script type="module">
  import {
    h,
    render
  } from 'preact';
  import htm from 'htm';

  import {
    modal
  } from '<?= strings::url($this->route . '/js/modal') ?>';

  const html = htm.bind(h);
  const container = $('#<?= $_container ?>');
  const _ = _brayworth_;

  container.find('.js-demo').on('click', async e => {

    _.hideContexts(e);

    const modalInstance = modal({
      title: "Send Email"
    });

    modalInstance._element.addEventListener('shown.bs.modal', function(e) {

      const uid = _.randomString();
      const handleSubmit = function(e) {

        e.preventDefault();
        modalInstance.hide();
        // Add any additional logic here
        console.log('Form submitted');
      };

      $(`<form id="${uid}">
          <input type="text" name="name" class="form-control" placeholder="Name">
        </form>`)
        .on('submit', handleSubmit)
        .appendTo($(this).find('.modal-body'));

      $(this).find('.modal-footer').append(
        `<button type="submit" class="btn btn-primary"
          form="${uid}">Submit</button>`);

      $(this).find('input[name="name"]').focus();
    });
  });
</script>