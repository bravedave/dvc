<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace home;

use strings;

printf('<div class="container" id="%s"></div>', $_container = strings::rand());  ?>
<script>
  (_ => {
    const container = $('#<?= $_container ?>');

    const getMatrix = () => new Promise(resolve => {
      _.post({
        url: _.url('<?= $this->route ?>'),
        data: {
          action: 'get-todo-data'
        },
      }).then(d => 'ack' == d.response ? resolve(d.data) : _.growl(d));
    });

    const matrix = data => {

      container.html('');
      $.each(data, (i, dto) => {

        $(`<div class="row g-2 js-todo">
          <div class="col p-2">${dto.description}</div>
          <div class="col-auto">
            <button type="button" class="btn bg-body-secondary js-delete">
              <i class="bi bi-trash"></i>
            </button>
          </div>
          </div>`)
          .data('dto', dto)
          .on('delete', function(e) {
            e.stopPropagation();

            let _row = $(this);
            let dto = _row.data('dto');

            _.post({
              url: _.url('<?= $this->route ?>'),
              data: {
                action: 'todo-delete',
                id: dto.id
              },
            }).then(d => 'ack' == d.response ? _row.remove() : _.growl(d));
          })
          .appendTo(container);
      });

      $(`<div class="row g-2 mt-2">
        <div class="col">
          <input type="text" class="form-control js-new-todo" name="description" placeholder="new todo">
        </div>
      </div>`).appendTo(container);

      container.find('.js-delete')
        .on('click', function(e) {
          e.stopPropagation();

          $(this).closest('div.js-todo').trigger('delete');
          this.innerHTML = '<div class="spinner-grow spinner-grow-sm"></div>';
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

            _.post({
              url: _.url('<?= $this->route ?>'),
              data: {
                action: 'todo-add',
                description: this.value
              },
            }).then(d => 'ack' == d.response ? container.trigger('refresh') : _
              .growl(d));
          }
        })
        .focus();
    };

    container.on('refresh', e => getMatrix().then(matrix));

    $(document).ready(() => container.trigger('refresh'));
  })(_brayworth_);
</script>