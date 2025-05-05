<?php
// file: src/app/people/views/matrix.php
// MIT License

namespace people;

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
        <td class="text-center js-line-number"></td>
        <td>name</td>
        <td>phone</td>
        <td>email</td>
      </tr>
    </thead>

    <tbody></tbody>
  </table>
</div>
<script type="module">

  import { h, render } from 'preact';
  import { useState, useEffect } from 'hooks';
  import htm from 'htm';

  const search = $('#<?= $_search ?>');
  const table = $('#<?= $_table ?>');
  const _ = _brayworth_;

  const html = htm.bind(h);
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

  const edit = function(e) {

    e.stopPropagation();
    _.get.modal(_.url(`<?= $this->route ?>/edit/${this.dataset.id}`))
      .then(m => m.on('success', e => table.trigger('refresh')));
  };

  const Matrix = () => {
    const [data, setData] = useState([]);
    const [error, setError] = useState(null);
    const [refresh, setRefresh] = useState(0); // State to trigger re-fetch

    useEffect(() => {

      _.fetch.post(_.url('<?= $this->route ?>'), {
        action: 'get-matrix'
      }).then(d => {
        if ('ack' == d.response) {
          setData(d.data);
        } else {
          setError(d.description ? d.description : d);
        }
      });
    }, [refresh]);

    useEffect(() => table.trigger('update-line-numbers'), [data]); // Runs whenever `data` changes

    useEffect(() => {

      table.on('delete', 'tbody > tr', function(e) {

        e.stopPropagation();

        const payload = {
          action: 'people-delete',
          id: this.dataset.id
        };

        _.fetch.post(_.url('<?= $this->route ?>'), payload)
          .then(d => {
            if ('ack' == d.response) {
              this.remove();
              table.trigger('update-line-numbers');
            } else {
              _.growl(d);
            }
          });
      });

      table.on('edit', 'tbody > tr', edit);
      table.on('click', 'tbody > tr', function(e) {

        e.stopPropagation();
        $(this).trigger('edit');
      });
      
      table.on('contextmenu', 'tbody > tr', contextmenu);

      // Cleanup the event listener when the component unmounts
      return () => {
        table.off('delete', 'tbody > tr');
        table.off('edit', 'tbody > tr');
        table.off('click', 'tbody > tr');
        table.off('contextmenu', 'tbody > tr');
      };
    }, []);

    table.on('refresh', e => setRefresh(prev => prev + 1));

    return data.map(dto => html`
      <tr data-id="${dto.id}" class="pointer">
        <td class="text-center js-line-number"></td>
        <td>${dto.name}</td>
        <td>${dto.phone}</td>
        <td>${dto.email}</td>
      </tr>`);

    if (error) return html`<div>Error: ${error}</div>`;
    if (!data.length) return html`<div>Loading...</div>`;
  };

  // Attach render to the 'populate-matrix' event
  table.on('refresh', e => {
    e.stopPropagation();
    render(html`<${Matrix} />`, table.find('>tbody').get(0));
  });

  // return true from the prefilter to show the row
  _.table.search(search, table, /* prefilter tr => true */ );
  table.on('update-line-numbers', _.table._line_numbers_);

  $('#<?= $_uidAdd ?>').on('click', function(e) {

    _.hideContexts(e);
    _.get.modal(_.url('<?= $this->route ?>/edit'))
      .then(m => m.on('success', e => table.trigger('refresh')));
  });

  table.trigger('refresh');
</script>
