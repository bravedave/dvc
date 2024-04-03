/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

(_ => {

  _.tabs = e => {

    let t = {

      nav: $('<nav class="nav nav-tabs" id="myTab" role="tablist"></nav>'),
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
        if (item.active) tab.addClass('active');

        const pane = $(`<div class="tab-pane" id="${item.target}"
          role="tabpanel" aria-labelledby="${item.id}" tabindex="0">...</div>`);
        if (item.active) pane.addClass('active');

        this.items[item.id] = {
          tab: tab,
          pane: pane
        };

        this.nav.append(tab);
        this.panes.append(pane);

        return this;
      }
    }

    return t;
  };
})(_brayworth_);
// <!-- Nav tabs -->
// <ul class="nav nav-tabs" id="myTab" role="tablist">
// </ul>

// <!-- Tab panes -->
// <div class="tab-content">
//   <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">...</div>
//   <div class="tab-pane" id="messages" role="tabpanel" aria-labelledby="messages-tab" tabindex="0">...</div>
//   <div class="tab-pane" id="settings" role="tabpanel" aria-labelledby="settings-tab" tabindex="0">...</div>
// </div>