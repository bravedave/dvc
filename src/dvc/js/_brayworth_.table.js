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
      let table = $(this);
      let t = 0;
      table.find('> tbody > tr:not(.d-none) >td.js-line-number').each((i, e) => {
        $(e).data('line', i + 1).html(i + 1);
        t++;
      });
      table.find('> thead > tr > .js-line-number').data('count', t).html(t);
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
    }
  }
})(_esse_);
