<?php
  // file: src/app/contacts/views/edit.php
  // MIT License

namespace contacts;

use bravedave\dvc\{strings, theme};

// note: $dto and $title into the environment ?>
<form id="<?= $_form = strings::rand() ?>" autocomplete="off">

  <input type="hidden" name="action" value="contacts-save">
  <input type="hidden" name="id" value="<?= $dto->id ?>">

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-modal="true" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-lg-down modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          <!-- --[name]-- -->
          <div class="row g-2">

            <div class="col-md-3 col-form-label text-truncate">name</div>
            <div class="col mb-2">

              <input type="text" class="form-control" name="name" value="<?= $dto->name ?>">
            </div>
          </div>
          <!-- --[email]-- -->
          <div class="row g-2">

            <div class="col-md-3 col-form-label text-truncate">email</div>
            <div class="col mb-2">

              <input type="text" class="form-control" name="email" value="<?= $dto->email ?>">
            </div>
          </div>
          <!-- --[mobile]-- -->
          <div class="row g-2">

            <div class="col-md-3 col-form-label text-truncate">mobile</div>
            <div class="col mb-2">

              <input type="text" class="form-control" name="mobile" value="<?= $dto->mobile ?>">
            </div>
          </div>

        </div>

        <div class="modal-footer">

          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    (_ => {
      const form = $('#<?= $_form ?>');
      const modal = $('#<?= $_modal ?>');

      modal.on('shown.bs.modal', () => {

        form.on('submit', function(e) {

          _.fetch.post.form(_.url('<?= $this->route ?>'),this).then(d => {

            if ('ack' == d.response) {

              modal.trigger('success');
              modal.modal('hide');
            } else {

              _.growl(d);
            }
          });

          // console.table( _data);

          return false;
        });

        form.find('input:not([type="hidden"]), select, textarea').first().focus();
      });
    })(_brayworth_);
  </script>
</form>