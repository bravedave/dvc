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

    _clear_placeholders_: table => {

      let tbl = table instanceof $ ? table : $(table);
      tbl.find('>tbody').removeClass('placeholder-glow');
      tbl.find('>tbody > tr.placeholder-row').remove();
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

          let colspan = td.getAttribute('colspan');
          if (!colspan) {

            stuffString
              .push(`<td class="${td.classList.value}"><span class="placeholder w-100"></span></td>`);
          } else {

            stuffString
              .push(`<td class="${td.classList.value}" colspan="${colspan}"><span class="placeholder w-100"></span></td>`);
          }
        });

        for (let i = 0; i < count; i++) {
          tbody.append(`<tr class="placeholder-row">${stuffString.join('')}</tr>`);
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

    sortOn: (table, key, sorttype, order) => new Promise(resolve => {

      let debug = false;

      if (!(table instanceof jQuery)) table = $(table); // to be sure

      const tbody = (b => {
        if (!b) b = table[0];
        return b[0];
      })(table.find('> tbody').first());

      // debug = true;

      if (!order) {

        order = (key == tbody.dataset.orderkey) ?
          ('desc' == tbody.dataset.order) ? 'asc' : 'desc' :
          order = 'desc';
      }

      tbody.dataset.order = order;
      tbody.dataset.orderkey = key;

      if (!sorttype) sorttype = 'string';

      // https://stackoverflow.com/questions/282670/easiest-way-to-sort-dom-nodes
      // console.log('JQuery4 compatible sorting ..');
      const newOrder = [...tbody.children]
        .sort((a, b) => {

          let ae = a.dataset[key];
          let be = b.dataset[key];

          if (debug) console.log(key, ae, be, sorttype, order);
          if (sorttype == 'numeric') return Number(ae ?? 0) - Number(be ?? 0);
          return String(ae ?? '').toUpperCase().localeCompare(String(be ?? '').toUpperCase());
        });

      if (order == 'desc') newOrder.reverse();
      newOrder.forEach(node => tbody.appendChild(node));
      // console.log('JQuery4 compatible sorting complete ..');

      table[0].dispatchEvent(new Event('update-line-numbers', { bubbles: true })); // dispatch jQuery compatible event
      // table.trigger('update-line-numbers');
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

      // ensure we have a jquery object for table
      if (!(table instanceof $)) table = $(table);

      ctrl[0].dataset.srchIdx = 0;
      ctrl.data('table', table);
      ctrl.attr('autocomplete', 'off');

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

          setTimeout(() => this.dispatchEvent(new Event('search', { bubbles: true })), 400); // dispatch jQuery compatible event
          // setTimeout(() => $(this).trigger('search'), 400);
        })
        .on('search', function (e) {
          const idx = ++this.dataset.srchIdx;
          const txt = this.value;

          const _me = $(this);
          const table = _me.data('table');

          preScan = ('function' == typeof preScan) ? preScan : () => true;
          table[0].dispatchEvent(new Event('search-start', { bubbles: true })); // dispatch jQuery compatible event
          // table.trigger('search-start');
          table.find('> tbody > tr').each((i, tr) => {

            if (idx != this.dataset.srchIdx) return false;

            const _tr = $(tr);
            if (!preScan(_tr)) {

              _tr.addClass('d-none');
            } else {

              const str = _tr.text();
              /**
               * if the user types an & character,
               * and the row has an encoded string ensure the text is matched
               * may need expanasion for more than just &amp;
               */
              const decodedStr = str.replace(/&amp;/g, '&');
              _tr.toggleClass('d-none', !(decodedStr.match(new RegExp(txt, 'gi'))));
            }
          });

          // dispatch jQuery compatible events
          table[0].dispatchEvent(new Event('update-line-numbers', { bubbles: true }));
          table[0].dispatchEvent(new Event('search-complete', { bubbles: true }));

          // table
          //   .trigger('update-line-numbers')
          //   .trigger('search-complete');
        });

      onblur.call(ctrl[0]);
      return ctrl;
    }
  }
})(_brayworth_);
