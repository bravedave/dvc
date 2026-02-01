<?php
  // file: src/app/todo/views/matrix.php
  // MIT License

namespace todo;

use bravedave\dvc\strings; ?>

<div class="row gx-2 mb-2 d-print-none">
  <div class="col">
    <div class="input-group">
      <input type="search" accesskey="/" class="form-control" id="<?= $_search = strings::rand() ?>" autofocus>
    </div>
  </div>

  <div class="col-auto">
    <button class="btn btn-outline-primary" id="<?= $_uidAdd = strings::rand() ?>">
      <i class="bi bi-plus-circle"></i> new
    </button>
  </div>
</div>

<div class="table-responsive">
  <table class="table table-sm" id="<?= $_table = strings::rand() ?>">
    <thead class="small">
      <tr>
        <td>description</td>
        <td>complete</td>
      </tr>
    </thead>

    <tbody></tbody>
  </table>
</div>
<script>
  (_ => {
    const table = $('#<?= $_table ?>');
    const search = $('#<?= $_search ?>');

    const contextmenu = function(e) {

      if (e.shiftKey) return;
      const _ctx = _.context(e); // hides any open contexts and stops bubbling

      _ctx.append.a({
        html: '<i class="bi bi-pencil"></i>edit',
        click: e => $(this).trigger('edit')
      });

      _ctx.append.a({
        html: '<i class="bi bi-trash"></i>delete',
        click: e => $(this).trigger('delete')
      });

      _ctx.open(e);
    };

    const edit = function() {

      _.get.modal(_.url(`<?= $this->route ?>/edit/${this.dataset.id}`))
        .then(m => m.on('success', e => $(this).trigger('refresh')));
    };

    const getMatrix = () => new Promise((resolve, reject) => {

      _.fetch.post(_.url('<?= $this->route ?>'), {
        action: 'get-matrix'
      }).then(d => 'ack' == d.response ? resolve(d.data) : _.growl(d));
    });

    const matrix = data => {

      const tbody = table.find('> tbody').empty();
      $.each(data, (i, dto) => {
        $(`<tr class="pointer" data-id="${dto.id}">
            <td class="js-description">${dto.description}</td>
            <td class="js-complete">${dto.complete}</td>
          </tr>`)
          .on('click', function(e) {

            e.stopPropagation();
            $(this).trigger('edit');
          })
          .on('contextmenu', contextmenu)
          .on('delete', rowDelete)
          .on('edit', edit)
          .on('refresh', rowRefresh)
          .appendTo(tbody);
      });
    };

    const refresh = () => getMatrix().then(matrix).catch(_.growl);

    const rowDelete = function(e) {

      e.stopPropagation();

      _.fetch
        .post(_.url('<?= $this->route ?>'), {
          action: 'todo-delete',
          id: this.dataset.id
        })
        .then(d => {
          if ('ack' == d.response) {
            this.remove();
          } else {
            _.growl(d);
          }
        });
    };

    const rowRefresh = function(e) {
      e.stopPropagation();

      const row = $(this);

      _.fetch.post(_.url('<?= $this->route ?>'), {
        action: 'get-by-id',
        id: this.dataset.id
      }).then(d => {

        if ('ack' == d.response) {

          row.find('.js-description').html(d.data.description);
          row.find('.js-complete').html(d.data.complete);
        } else {

          _.growl(d);
        }
      });
    };

    // return true from the prefilter to show the row
    _.table.search(search, table, /* prefilter tr => true */ );

    $('#<?= $_uidAdd ?>').on('click', function(e) {

      _.hideContexts(e);

      _.get.modal(_.url('<?= $this->route ?>/edit'))
        .then(m => m.on('success', e => refresh()));
    });

    _.ready(() => refresh());
  })(_brayworth_);
</script>