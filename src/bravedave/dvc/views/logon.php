<?php
/*
  David Bray
  BrayWorth Pty Ltd
  e. david@brayworth.com.au

  MIT License
*/  ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <input type="hidden" name="action" value="-system-logon-">
  <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label">logon</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <input type="text" name="u" class="form-control"
              placeholder="username or email" autocomplete="username" required>
          </div>
          <div class="form-group">
            <input type="password" name="p" class="form-control" placeholder="password"
              autocomplete="current-password" required>
          </div>

        </div>

        <div class="modal-footer">
          <?php if (\config::allow_password_recovery) { ?>
            <button type="button" class="btn btn-light" id="<?= $_btnReset = strings::rand() ?>">reset
              password</button>
          <?php   } ?>

          <button type="submit" class="btn btn-primary">logon</button>
        </div>

      </div>

    </div>

  </div>
  <script>
    (_ => $('#<?= $_modal ?>')
      .on('shown.bs.modal', () => {
        <?php if (config::allow_password_recovery) { ?>
          $('#<?= $_btnReset ?>').on('click', e => $('#<?= $_form ?>').trigger('reset-password'));
          $('#<?= $_form ?>')
            .on('reset-password', function(e) {
              let _form = $(this);
              let _data = _form.serializeFormJSON();
              _.post({
                  url: _.url(),
                  data: {
                    action: '-send-password-',
                    u: _data.u,

                  }

                })
                .then(d => {
                  _.growl(d);
                  if ('ack' == d.response) {
                    $('.modal-body', _form).append(
                      $('<div class="alert alert-info"></div>').html(d.message)
                    )

                  }

                });

            });
        <?php   } ?>

        $('#<?= $_form ?>')
          .on('submit', function(e) {
            let _form = $(this);
            let _data = _form.serializeFormJSON();

            _.post({
                url: _.url(),
                data: _data

              })
              .then(d => {
                $('body').growl(d);
                if ('ack' == d.response) {
                  $('<?= $_modal ?>').modal('hide');
                  window.location.reload();

                } else {
                  $('.modal-body', _form)
                    .append($('<div class="alert alert-danger">failed</div>'));

                }

              });

            return false;

          });
        $('input[name="u"]', '#<?= $_form ?>').focus();

      }))(_brayworth_);
  </script>
</form>