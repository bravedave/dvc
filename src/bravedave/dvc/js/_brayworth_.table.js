/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {
  _.table = {

    _line_numbers_: function (e) {
      e.stopPropagation();

      let table = $(this);
      let t = 0;
      table.find('> tbody > tr:not(.d-none) >td.js-line-number').each((i, e) => {
        $(e).data('line', i + 1).html(i + 1);
        t++;
      });
      table.find('> thead > tr > .js-line-number').data('count', t).html(t);
    },

    _placeholders_: (table, count) => {

      let tbl = table instanceof $ ? table : $(table);
      let cc = table.find('>thead > tr:first-child > *').length;
      let tbody = table.find('> tbody');

      if (cc > 0 && tbody.length > 0) {

        if (!(Number(count) > 1)) count = 12;

        tbody
          .html('')
          .addClass('placeholder-glow');

        for (let i = 0; i < count; i++) {
          tbody
            .append(
              `<tr>${'<td><span class="placeholder w-100"></span></td>'.repeat(cc)}</tr>`
            );
        }
      }
    },

    installSorting: table => {
      $(table).find('thead > tr > td[data-role="sort-header"]')
        .each((i, el) => $(el)
          .addClass('pointer')
          .off('click.tablesort')
          .on('click.tablesort', _.table.sort));
    },

    sortOn: (table, key, sorttype, order) => new Promise((resolve, reject) => {
      // var debug = true;
      let debug = false;
      let tbody = $('tbody', table);
      if (!tbody) tbody = table;
      if (tbody.length > 1) tbody = tbody.first();

      if ('undefined' == typeof order) {
        if (key == tbody.data('orderkey')) {
          order = tbody.data('order') == "desc" ? "asc" : "desc";
        }
        else {
          order = "desc";
        }
      }

      tbody.data('order', order);
      tbody.data('orderkey', key);

      if (!sorttype) sorttype = 'string';

      let warn = true;
      let items = tbody.children('tr');

      if (debug) console.log(key, sorttype, order, items.length);

      items.sort((a, b) => {
        let ae = $(a).data(key);
        let be = $(b).data(key);
        if (/undefined/.test(typeof ae) || /undefined/.test(typeof ae)) {
          ae = $(a).data('key-' + key);
          be = $(b).data('key-' + key);

          if (warn) console.warn('table sorting is not jQuery3 compatible');
          warn = false;
        }

        if (debug) console.log(key, ae, be, sorttype, order);

        if (sorttype == "numeric") {

          if ('undefined' == typeof ae) ae = 0;
          if ('undefined' == typeof be) be = 0;
          return Number(ae) - Number(be);
        }

        if ('undefined' == typeof ae) ae = '';
        if ('undefined' == typeof be) be = '';
        return String(ae).toUpperCase().localeCompare(String(be).toUpperCase());
      });

      $.each(items, (i, e) => {
        if (order == "desc") {
          tbody.prepend(e);
        }
        else {
          tbody.append(e);
        }
      });

      if (!(table instanceof jQuery)) table = $(table);
      table.trigger('update-line-numbers');
      resolve(table);
    }),

    sort: function (e) {
      if ('undefined' != typeof e && !!e.target) e.stopPropagation();

      _.hideContexts();

      let _me = $(this);

      let _data = _me.data();
      if (!_data.key) return;

      let table = _me.closest('table');
      if (!table) return;
      return _.table.sortOn(table, _data.key, _data.sorttype);	//~ console.log( key );
    },

    search: (ctrl, table) => {

      ctrl[0].dataset.srchIdx = 0;
      ctrl.data('table', table);

      ctrl
        .on('blur', function (e) {

          if (_.browser.isMobileDevice) return;
          if (this.hasAttribute('accesskey')) {

            this.setAttribute('placeholder', `alt + ${this.getAttribute('accesskey')} to focus`);
          }
        })
        .on('focus', function (e) {

          this.setAttribute('placeholder', _.browser.isMobileDevice ? 'search ..' : 'type to search ..');
        })
        .on('input', function (e) {
        // .on('keyup', function (e) {

          // if (13 == e.keyCode) return;
          ++this.dataset.srchIdx;
          setTimeout(() => $(this).trigger('search'), 400);
        })
        .on('search', function (e) {
          let idx = ++this.dataset.srchIdx;
          let txt = this.value;

          let _me = $(this);
          let table = _me.data('table');

          table.find('> tbody > tr').each((i, tr) => {
            if (idx != this.dataset.srchIdx) return false;

            let _tr = $(tr);
            let str = _tr.text()
            if (str.match(new RegExp(txt, 'gi'))) {

              _tr.removeClass('d-none');
            } else {

              _tr.addClass('d-none');
            }
          });

          table.trigger('update-line-numbers');
        });

      return ctrl;
    }
  }
})(_brayworth_);
