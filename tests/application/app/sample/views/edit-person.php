<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div id="<?= $_wrap = strings::rand() ?>">
  <form id="<?= $_form = strings::rand() ?>" autocomplete="off">
    <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header <?= theme::modalHeader() ?> py-2">
            <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?><?= \config::$BOOTSTRAP_VERSION ?></h5>
            <button type="button" class="close" data-<?= 5 == \config::$BOOTSTRAP_VERSION ? 'bs-dismiss' : 'dismiss' ?>="modal" aria-label="Close">
              <span aria-hidden="true"><i class="bi bi-x"></i></span>
            </button>

          </div>

          <div class="modal-body">
              <div class="form-group row">
                <div class="col">
                  <div class="input-group" id="<?= $_uid = strings::rand() ?>">
                    <input type="text" class="form-control" placeholder="name">
            <?php if ( 5 == \config::$BOOTSTRAP_VERSION) { ?>
                    <button type="button" class="btn btn-outline-secondary"><i class="bi bi-clipboard-plus"></i></button>

            <?php } else { ?>

                    <div class="input-group-append">
                      <button type="button" class="btn btn-light"><i class="bi bi-clipboard-plus"></i></button>

                    </div>

            <?php } ?>

                  </div>

                </div>
                <script>
                ( _ => {
                  $('button','#<?= $_uid ?>').on( 'click', function( e) {
                    let el = $('<div></div>').html( $('input', '#<?= $_uid ?>').val()).appendTo('body');
                    _.CopyToClipboard( el[0]).then( () => el.remove());

                  })

                }) (_brayworth_);
                </script>

              </div>

              <div class="form-group row">
                <div class="col">
                  <input type="text" class="form-control" placeholder="email">

                </div>

              </div>

              <div class="form-group row">
                <div class="col">
                  <input type="text" class="form-control" placeholder="mobile">

                </div>

              </div>

          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-<?= 5 == \config::$BOOTSTRAP_VERSION ? 'bs-dismiss' : 'dismiss' ?>="modal">close</button>
            <button type="submit" class="btn btn-primary">save</button>

          </div>

        </div>

      </div>

    </div>

  </form>

  <script>
  ( _ => $(document).ready( () => {
    $('#<?= $_form ?>')
    .on( 'submit', function( e) {
      let _form = $(this);
      let _data = _form.serializeFormJSON();
      let _modalBody = $('.modal-body', _form);

      // console.table( _data);

      return false;

    });

  }))( _brayworth_);
  </script>

</div>
