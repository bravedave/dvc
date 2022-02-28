/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
(_ => {
  _.table = {
    'sortOn': (table, key, sorttype, order) => {
      return new Promise((resolve, reject) => {
        //~ var debug = true;
        let debug = false;
        let tbody = $('tbody', table);
        if (!tbody) tbody = table;
        if (tbody.length > 1) tbody = tbody.first();

        if ('undefined' == typeof order) {
          if ( key == tbody.data('orderkey') {
            order = (tbody.data('order') == "desc" ? "asc" : "desc");
          }
          else {
            order = "desc";
          }
        }

        tbody.data('order', order);
        tbody.data('orderkey', key);

        if (!sorttype)
          sorttype = 'string';

        let warn = true;

        let items = tbody.children('tr');

        if (debug) console.log(key, sorttype, order, items.length);

        items.sort(function sortItem(a, b) {
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
            return (Number(ae) - Number(be));

          }
          else {
            if ('undefined' == typeof ae) ae = '';
            if ('undefined' == typeof be) be = '';
            return (String(ae).toUpperCase().localeCompare(String(be).toUpperCase()));

          }

        });

        $.each(items, function (i, e) {
          if (order == "desc") { tbody.prepend(e); }
          else { tbody.append(e); }

        });

        if (!(table instanceof jQuery)) table = $(table);
        table.trigger('update-line-numbers');

        resolve(table);

      });

    },

    'sort': function (e) {
      if ('undefined' != typeof e && !!e.target) e.stopPropagation();

      _.hideContexts();

      let _me = $(this);
      let table = _me.closest('table');
      if (!table) return;
      let _data = _me.data();

      if (!_data.key) return;

      return _.table.sortOn(table, _data.key, _data.sorttype);	//~ console.log( key );
    }
  };
})(_brayworth_);
