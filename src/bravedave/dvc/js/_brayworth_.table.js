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
      let hcols = tbl.find('>thead > tr:first-child > *');
      let tbody = tbl.find('> tbody');

      if (hcols.length > 0 && tbody.length > 0) {

        if (!(Number(count) > 1)) count = 12;

        tbody
          .html('')
          .addClass('placeholder-glow');

        let stuffString = [];
        hcols.each((i, td) => {
          stuffString.push(`<td class="${td.classList.value}"><span class="placeholder w-100"></span></td>`);
        });

        for (let i = 0; i < count; i++) {
          tbody.append(`<tr>${stuffString.join('')}</tr>`);
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
      let debug = false;
      let tbody = $('tbody', table);
      if (!tbody) tbody = table;
      if (tbody.length > 1) tbody = tbody.first();

      // debug = true;

      if (!order) {

        if (key == tbody[0].dataset.orderkey) {

          order = ('desc' == tbody[0].dataset.order) ? 'asc' : 'desc';
        } else {

          order = 'desc';
        }
      }

      tbody[0].dataset.order = order;
      tbody[0].dataset.orderkey = key;

      if (!sorttype) sorttype = 'string';

      let items = tbody.find('> tr');

      if (debug) console.log(key, sorttype, order, items.length);

      items.sort((a, b) => {

        let ae = a.dataset[key];
        let be = b.dataset[key];

        if (debug) console.log(key, ae, be, sorttype, order);

        if (sorttype == 'numeric') {

          if (undefined == ae) ae = 0;
          if (undefined == be) be = 0;
          return Number(ae) - Number(be);
        }

        if (undefined == ae) ae = '';
        if (undefined == be) be = '';
        return String(ae).toUpperCase().localeCompare(String(be).toUpperCase());
      });

      $.each(items, (i, e) => order == 'desc' ? tbody.prepend(e) : tbody.append(e));

      if (!(table instanceof jQuery)) table = $(table);
      table.trigger('update-line-numbers');
      resolve(table);
    }),

    sort: function (e) {

      _.hideContexts(e);
      if (!this.dataset.key) return;

      let table = $(this).closest('table');
      if (!table) return;

      return _.table.sortOn(table, this.dataset.key, this.dataset.sorttype);	//~ console.log( key );
    },

    search: (ctrl, table, preScan) => {

      ctrl[0].dataset.srchIdx = 0;
      ctrl.data('table', table);

      const onblur = function (e) {

        if (_.browser.isMobileDevice) return;
        if (this.hasAttribute('accesskey')) {

          this.setAttribute('placeholder', `alt + ${this.getAttribute('accesskey')} to focus`);
        }
      };

      ctrl
        .on('blur', onblur)
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

          preScan = 'function' == typeof preScan ? preScan : () => true;

          table.find('> tbody > tr').each((i, tr) => {

            if (idx != this.dataset.srchIdx) return false;

            let _tr = $(tr);
            let str = _tr.text()
            if (!preScan(_tr)) {

              _tr.addClass('d-none');
            } else if (str.match(new RegExp(txt, 'gi'))) {

              _tr.removeClass('d-none');
            } else {

              _tr.addClass('d-none');
            }
          });

          table.trigger('update-line-numbers');
        });

      onblur.call(ctrl[0]);
      return ctrl;
    }
  }
})(_brayworth_);
