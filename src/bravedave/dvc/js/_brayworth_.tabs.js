/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

(_ => {

  _.tabs = container => {

    const t = {

      nav: $('<nav class="nav nav-tabs"></nav>'),
      panes: $('<div class="tab-content"></div>'),
      items: {},
      append: function (item) {

        this.newTab(item);
        return this;
      },

      newTab: function (p) {

        const init = {
          ...{
            id: _.randomString()
          },
          ...p
        };

        const item = {
          ...{
            class: 'nav-link',
            id: `${init.id}-tab`,
            active: false,
            target: `${init.id}`,
            label: 'string' == typeof p ? p : 'Tab'
          },
          ...p
        };

        const tab = $(`<button class="${item.class}" id="${item.id}"
          data-bs-toggle="tab" data-bs-target="#${item.target}" type="button"
          role="tab" aria-controls="${item.target}" aria-selected="true">${item.label}</button>`);

        tab
          .on('hide.bs.tab', e => e.stopPropagation())
          .on('hidden.bs.tab', e => e.stopPropagation())
          .on('show.bs.tab', e => e.stopPropagation())
          .on('shown.bs.tab', e => e.stopPropagation());

        if (item.active) tab.addClass('active');

        const pane = $(`<div class="tab-pane" id="${item.target}"
          role="tabpanel" aria-labelledby="${item.id}">...</div>`);
        if (item.active) pane.addClass('active');

        let o = {
          tab: tab,
          pane: pane
        };
        this.items[item.id] = o;

        this.nav.append(tab);
        this.panes.append(pane);

        return o;
      }
    }

    if (!!container) {

      if (container instanceof jQuery) {

        container.empty()
          .append(t.nav)
          .append(t.panes);
      }
    }

    return t;
  };
})(_brayworth_);
