<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace beds_list;

use strings;
use theme;

$dto = $this->data->dto;    ?>

<div id="<?= $_wrap = strings::rand() ?>">
  <form id="<?= $_form = strings::rand() ?>" autocomplete="off">
    <input type="hidden" name="action" value="save-beds">
    <input type="hidden" name="id" value="<?= $dto->id ?>">

    <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>"
      aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header <?= theme::modalHeader() ?>">
            <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>

          </div>

          <div class="modal-body">
            <div class="form-group">
              <label for="<?= $uid = strings::rand() ?>">Beds</label>
              <input class="form-control" type="text" name="beds" required
                value="<?= $dto->beds ?>"
                id="<?= $uid ?>" />

            </div>

            <div class="form-group">
              <label for="<?= $uid = strings::rand() ?>">Description</label>
              <input class="form-control" type="text" name="description" required
                value="<?= $dto->description ?>"
                id="<?= $uid ?>" />

            </div>

          </div>

          <div class="modal-footer py-1">
              <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">close</button>
              <button type="button" class="btn btn-outline-danger d-none" id="<?= $deleteBtn = strings::rand() ?>">delete</button>
              <button type="submit" class="btn btn-primary">save</button>

          </div>

        </div>

      </div>

    </div>

  </form>

  <script>
  ( _ => {
    $('#<?= $_modal ?>').on( 'shown.bs.modal', e => $('#<?= $_wrap ?> input[name="beds"]').focus());

    $('#<?= $_form ?>').on( 'submit', function( e) {
      let _form = $(this);
      let _data = _form.serializeFormJSON();

      _.post({
        url : _.url('<?= $this->route ?>'),
        data : _data,

      }).then( (d) => {
        if ( 'ack' == d.response) {
            $('#<?= $_modal ?>').trigger('success');

        }
        else {
            _.growl( d);

        }

        $('#<?= $_modal ?>').modal('hide');

      });

      return false;

    });

  }) (_brayworth_);
  </script>

</div>
