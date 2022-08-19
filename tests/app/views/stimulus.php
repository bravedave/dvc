<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <script type="module">
    import {
      Application,
      Controller
    } from '/assets/brayworth/stimulus';

    let Stimulus = Application.start();

    Stimulus.register("<?= $_form ?>", class extends Controller {
      static targets = ["name"]

      greet() {
        const element = this.nameTarget;
        const name = element.value;
        console.log(`Hello, ${name}!`);
      }

      connect() {
        console.log("Hello, Stimulus!", this.element)
      }

      disconnect() {
        console.log("Ciao, Stimulus!", this.element)
      }
    });
  </script>

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" data-controller="<?= $_form ?>">
        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input data-<?= $_form ?>-target="name" type="text">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">close</button>
          <button type="button" class="btn btn-primary" data-action="click-><?= $_form ?>#greet">Save</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    (_ => $('#<?= $_modal ?>').on('shown.bs.modal', () => {
      $('#<?= $_form ?>')
        .on('submit', function(e) {
          let _form = $(this);
          let _data = _form.serializeFormJSON();
          let _modalBody = $('.modal-body', _form);

          // console.table( _data);

          return false;
        });
    }))(_brayworth_);
  </script>
</form>
