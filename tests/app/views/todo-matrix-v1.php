<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/ ?>

<div class="container p-4" id="<?= $_container = strings::rand() ?>"></div>
<script type="module">
  // import { Toast } from '<?= strings::url('assets/module/toast') ?>';

  // console.log( 'ready to toast' );
  // Toast('Hello World!', 'success');

  // console.log( _brayworth_);
</script>

<script>
  (_ => {
    const container = $('#<?= $_container ?>');

    const getMatrix = () => new Promise(resolve => {

      const payload = {
        action: 'get-todo-data'
      };

      _.fetch.post(_.url('<?= $this->route ?>'), payload)
        .then(d => 'ack' == d.response ? resolve(d.data) : _.growl(d));
    });

    const matrix = data => {

      container.empty().append('<h4><?= config::label_todo ?></h4>');

      $.each(data, (i, dto) => {

        const row = $(
            `<div class="row g-2 js-todo" data-id="${dto.id}">
              <div class="col p-2 border border-light js-description">${dto.description}</div>
              <div class="col-auto">
                <button type="button" class="btn btn-light js-delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>`)
          .data('dto', dto)
          .on('delete', rowDelete)
          .appendTo(container);

        row.find('.js-description').one('click', function(e) {

          _.hideContexts(e);

          const save = function(e) {

            e.stopPropagation();

            const _fld = $(this);
            const _row = _fld.closest('div.js-todo');
            const _dto = _row.data('dto');
            const payload = {
              action: 'todo-update',
              id: _dto.id,
              description: _fld.val()
            };

            _.fetch.post(_.url('<?= $this->route ?>'), payload)
              .then(d => 'ack' == d.response ? container.trigger('refresh') : _.growl(d));
          };

          const fld = $('<input type="text" class="form-control">')
            .val(dto.description)
            .on('blur', function(e) {

              $(this).trigger('save');
            })
            .on('keypress', function(e) {

              if (13 == e.keyCode) {

                e.stopPropagation();
                $(this).trigger('save');
              }
            })
            .on('save', save);

          $(this)
            .removeClass('p-2 border border-light')
            .empty()
            .append(fld);

          fld.focus();
        });
      });

      container.append(
        `<div class="row g-2 mt-2">
          <div class="col">
            <input type="text" class="form-control js-new-todo" name="description" placeholder="new todo">
          </div>
        </div>`);

      container.find('.js-delete').on('click', function(e) {

        _.hideContexts(e);
        $(this).closest('div.js-todo').trigger('delete');
      });

      container.find('input.js-new-todo')
        .on('change', function(e) {

          if ('' != this.value) {

            $(this).parent()
              .html(`<div class="input-group">
                  <div class="bg-success text-white form-control">${this.value}</div>
                  <div class="input-group-text">
                    <div class="spinner-grow spinner-grow-sm"></div>
                  </div>
                </div>`);

            _.fetch
              .post(_.url('<?= $this->route ?>'), {
                action: 'todo-add',
                description: this.value
              }).then(d => 'ack' == d.response ?
                container.trigger('refresh') :
                _.growl(d));
          }
        })
        .focus();
    };

    const rowDelete = function(e) {

      _.ask.alert.confirm({
        title: 'Confirm Delete',
        text: 'Are you sure ?'
      }).then(e => {

        const payload = {
          action: 'todo-delete',
          id: this.dataset.id
        };

        _.fetch.post(_.url('<?= $this->route ?>'), payload).then(d => {

          if ('ack' == d.response) {

            this.remove();
          } else {

            _.growl(d);
          }

          container.find('input.js-new-todo').focus();
        });
      });
    };

    container.on('refresh', e => getMatrix().then(matrix));
    _.ready(() => container.trigger('refresh'));
  })(_brayworth_);
</script>