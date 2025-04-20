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

I'm amazed !

<script type="module">
  const _ = _brayworth_;
  import {
    modal
  } from '<?= strings::url($this->route . '/js/modal') ?>';

  const id = _.randomString();
  const m = $(modal('Amazing modal !'));

  m.on('shown.bs.modal', () => console.log('shown'));

  const form = $(`<form id="${id}">
    <input type="text" name="name" class="form-control" placeholder="Name">
    </form>`);

  form.on('submit', e => {

    e.preventDefault();
    m.modal('hide');
  });

  m.find('.modal-body').empty().append(form);
  m.find('.modal-footer')
    .append(`<button type="submit" class="btn btn-primary" form="${id}">Submit</button>`);

  console.log('finished')
</script>