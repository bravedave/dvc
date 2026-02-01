<?php
// file: src/app/contacts/views/matrix.php
// MIT License

namespace contacts;

use bravedave\dvc\strings; ?>

<div class="accordion" id="<?= $_uidAccordion = strings::rand() ?>">
  <div class="accordion-item border-0">
    <div id="<?= $_uidAccordion ?>-feed" class="accordion-collapse collapse show" data-bs-parent="#<?= $_uidAccordion ?>">

      <div class="row g-2 mb-2 d-print-none">
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
              <td>name</td>
              <td>email</td>
              <td>mobile</td>
            </tr>
          </thead>

          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="accordion-item border-0">
    <div id="<?= $_uidAccordion ?>-workbench" class="accordion-collapse collapse" data-bs-parent="#<?= $_uidAccordion ?>">
      <nav class="navbar navbar-expand d-print-none">
        <div class="navbar-brand">Workbench</div>
        <nav class="navbar-nav ms-auto">
          <button type="button" class="btn-close ms-2" data-bs-toggle="collapse" data-bs-target="#<?= $_uidAccordion ?>-feed"></button>
        </nav>
      </nav>
    </div>
  </div>

  <script>
    (_ => {
      const feed = $('#<?= $_uidAccordion ?>-feed');
      const workbench = $('#<?= $_uidAccordion ?>-workbench');
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
            <td class="js-name">${dto.name}</td>
            <td class="js-email">${dto.email}</td>
            <td class="js-mobile">${dto.mobile}</td>
          </tr>`)
            .on('click', function(e) {

              e.stopPropagation();
              $(this).trigger('view');
            })
            .on('contextmenu', contextmenu)
            .on('delete', rowDelete)
            .on('edit', edit)
            .on('refresh', rowRefresh)
            .on('view', viewer)
            .appendTo(tbody);
        });
      };

      const refresh = () => getMatrix().then(matrix).catch(_.growl);

      const rowDelete = function(e) {

        e.stopPropagation();

        _.fetch
          .post(_.url('<?= $this->route ?>'), {
            action: 'contacts-delete',
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

            row.find('.js-name').html(d.data.name);
            row.find('.js-email').html(d.data.email);
            row.find('.js-mobile').html(d.data.mobile);
          } else {

            _.growl(d);
          }
        });
      };

      const viewer = function(e) {

        const tabs = _.tabs(workbench);
        const view = tabs.newTab('view');

        view.pane.on('refresh', e => {

          e.stopPropagation();

          _.fetch.get(_.url(`<?= $this->route ?>/view/${this.dataset.id}`))
            .then(html => view.pane.html(html));
        });

        view.tab.on('show.bs.tab', e => {
          view.pane
            .html('<h1>Loading...</h1>')
            .trigger('refresh');
        });

        tabs.nav.prepend(`<h5 class="me-auto mt-2"><?= config::label_view ?></h5>`);

        const btnEdit = $(`<button type="button" class="btn btn-outline-primary ms-2">
          <i class="bi bi-pencil"></i> edit
        </button>`).appendTo(tabs.nav);
        btnEdit.on('click', e => {

          _.get.modal(_.url(`<?= $this->route ?>/edit/${this.dataset.id}`))
            .then(m => m.on('success', e => {

              view.tab.trigger('show.bs.tab');
              rowRefresh.call(this, e);
            }));
        });

        tabs.nav.append(`<button type="button" class="btn-close mt-2 ms-2" data-bs-toggle="collapse"
          data-bs-target="#<?= $_uidAccordion ?>-feed" aria-expanded="false" aria-controls="<?= $_uidAccordion ?>-feed"></button>`);

        workbench.collapse('show');
        view.tab.tab('show');
      };

      // return true from the prefilter to show the row
      _.table.search(search, table, /* prefilter tr => true */ );

      $('#<?= $_uidAdd ?>').on('click', function(e) {

        _.hideContexts(e);

        _.get.modal(_.url('<?= $this->route ?>/edit'))
          .then(m => m.on('success', e => refresh()));
      });

      [
        feed,
        workbench,
      ].forEach(el => {
        el
          .on('hide.bs.collapse', e => e.stopPropagation())
          .on('hidden.bs.collapse', e => e.stopPropagation())
          .on('show.bs.collapse', e => e.stopPropagation())
          .on('shown.bs.collapse', e => e.stopPropagation());
      });

      feed.on('show.bs.collapse', e => $('body').toggleClass('hide-nav-bar', false));
      workbench.on('show.bs.collapse', e => $('body').toggleClass('hide-nav-bar', true));

      _.ready(() => refresh());
    })(_brayworth_);
  </script>
</div>