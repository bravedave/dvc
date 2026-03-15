/**
 *
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * Context menu builder for the Brayworth UI helper.
 *
 * What this module does:
 * - creates lightweight context menus (`_.context`)
 * - supports custom menu items and click handlers
 * - keeps menus inside the visible viewport
 * - can render into the current window or a supplied iframe window
 *
 * Signature:
 *   _.context(event, confinedWindow = window)
 *
 * Example: current window
 *   const contextmenu = function (e) {
 *     if (e.shiftKey) return;
 *
 *     const ctx = _.context(e); // hides any open contexts and stops bubbling
 *     ctx.append.a({
 *       html: 'dump',
 *       click: () => console.log(this.dataset)
 *     });
 *
 *     ctx.open(e);
 *   };
 *
 *   $(el).on('contextmenu', contextmenu);
 *
 * Example: iframe object
 *   const iframeEl = document.getElementById('my-iframe');
 *   const iframeWindow = iframeEl?.contentWindow;
 *
 *   $(iframeEl?.contentDocument).on('contextmenu', '.row', function (e) {
 *     if (!iframeWindow || e.shiftKey) return;
 *
 *     const ctx = _.context(e, iframeWindow); // hides any open contexts and stops bubbling
 *     ctx.append.a({
 *       html: 'inspect',
 *       click: () => console.log('iframe row', this.dataset)
 *     });
 *
 *     ctx.open(e);
 *   });
 */

(_ => {

  // delta implemented: _.context accepts optional window and cx.open is confined to that window
  _.context = (e, confinedWindow = window) => {

    const contextWindow = confinedWindow || window;
    const contextDocument = contextWindow.document;

    _.hideContexts(e, contextWindow);

    if (e?.type?.toLowerCase() === 'contextmenu') e.preventDefault();

    const cx = {
      root: $('<ul class="menu menu-contextmenu" data-role="contextmenu"></ul>'),
      items: [],
      length: 0,
      hideClass: (_.bootstrap_version() < 4 ? 'hidden' : 'd-none'),

      addClose: function () {

        this
          .append()
          .append.a({ html: 'close' });

        return this;
      },

      create: function (item, after) {

        let el = $('<li></li>').append(!!item ? item : '<hr>');
        if (!!after) {

          if ('prepend' == after) {

            el.prependTo(this.root);
          } else {

            el.insertAfter(after);
          }
        } else {

          el.appendTo(this.root);
        }

        this.items.push(el);
        this.length = this.items.length;
        return el;
      },

      append: function (item) {

        this.create(item);
        return this;
      },

      prepend: function (item) {

        this.create(item, 'prepend');
        return this;
      },

      open: function (e) {

        const css = {
          position: 'absolute',
          top: 10,
          left: $(contextDocument).width() - 140,
        };

        if (!!e.pageY) { css.top = Math.max(e.pageY + 2, 0); }
        if (!!e.pageX) { css.left = Math.max(e.pageX + 2, 0); }
        if (e?.type?.toLowerCase() === 'contextmenu') e.preventDefault(); // don't show the browser context menu

        //~ console.log( this.root.width());

        const root = this.root;
        (e => {

          // 1040 is defined in the css
          const t = $(e.target);
          if (t.length > 0) css['z-index'] = Math.max(t.zIndex() + 10, 1040);
        })(e);

        root
          .css(css)
          .appendTo(contextDocument.body)
          .data('hide', 'detach');

        let offset = root.offset();

        const wH = $(contextWindow).height();
        const wW = $(contextWindow).width();
        const sT = $(contextWindow).scrollTop();

        /* try to keep menu on screen horizontally */
        if (offset.left + root.width() > wW) {

          const l = wW - root.width() - 5;
          root.css('left', Math.max(l, 2));
          offset = root.offset();
        }

        /* try to keep menu on screen vertically */
        if (offset.top + root.height() > (wH + sT)) {

          const t = (wH + sT) - root.height() - 5;
          root.css('top', Math.max(t, sT + 2));
          offset = root.offset();
        }

        /**
         * add helper class to display the submenu on left
         * if the window width is restrictive on the right
         */
        const tfr = () => offset.left > (wW - (root.width() * 2));
        root.toggleClass('menu-contextmenu-right', tfr());

        /**
         * add helper class to display the submenu high
         * if the window height is restrictive at bottom
         */
        const tfd = () => offset.top + (root.height() * 1.2) > (wH + sT);
        root.toggleClass('menu-contextmenu-low', tfd());

        return this;
      },

      close: function () {

        this.root.remove();
        return this;
      },

      remove: function () {

        return this.close();
      },

      reviewTop: function () {

        const root = this.root;
        const offset = root.offset();
        const wH = $(contextWindow).height();
        const sT = $(contextWindow).scrollTop();

        /* try to keep menu on screen vertically */
        if (offset.top + root.height() > (wH + sT)) {

          const t = (wH + sT) - root.height() - 5;
          root.css('top', Math.max(t, sT + 2));
        }
      },

      attachTo: function (parent) {

        const _me = this;

        $(parent)
          .off('click.removeContexts')
          .on('click.removeContexts', e => {
            if ($(e.target).closest('[data-role="contextmenu"]').length > 0) {
              if (/^(a)$/i.test(e.target.nodeName)) {
                return;

              }

            }

            hideContexts(e, contextWindow);

          })
          .on('contextmenu', e => {

            /*--[ check for abandonment ]--*/
            if ($(e.target).closest('[data-role="contextmenu"]').length) {

              return;
            }

            hideContexts(e, contextWindow);

            if (e.shiftKey) return;

            if (/^(input|textarea|img|a|select)$/i.test(e.target.nodeName) || $(e.target).closest('a').length > 0) {

              return;
            }

            if ($(e.target).closest('table').data('nocontextmenu') == 'yes') {

              return;
            }

            if ($(e.target).hasClass('modal') || $(e.target).closest('.modal').length > 0) {

              return;
            }

            /** stops the menu on jquery-ui dialogs */
            if ($(e.target).hasClass('ui-widget-overlay') || $(e.target).closest('.ui-dialog').length > 0) {

              return;
            }

            if (typeof contextWindow.getSelection != "undefined") {

              let sel = contextWindow.getSelection();
              if (sel.rangeCount) {

                if (sel.anchorNode.parentNode == e.target) {

                  let frag = sel.getRangeAt(0).cloneContents();
                  let text = frag.textContent;
                  if (text.length > 0) return;
                }
              }
            }
            /*--[ end: check for abandonment ]--*/

            e.preventDefault();
            _me.open(e);
          });

        return _me;
      }
    };

    const _new_element_ = p => {

      const o = {
        ...{
          click: e => { },
          html: 'string' == typeof p ? p : '',
          href: '#',
        },
        ...p
      };

      const el = $(`<a href="${o.href}">${o.html}</a>`)
        .on('click', e => {

          if ('#' == $(e.target).attr('href')) {
            e.stopPropagation();
            e.preventDefault();
          }

          cx.close();
          o.click(e);
        });

      if (!!o.target) el.attr('target', o.target);
      if (!!o.class) el.addClass(o.class);
      return el;
    };

    cx.append.a = p => { let el = _new_element_(p); cx.append(el); return el; };
    cx.prepend.a = p => { let el = _new_element_(p); cx.prepend(el); return el; };

    return cx;
  };

  const hideContexts = function (e, confinedWindow = null) {

    const contextDocument =
      confinedWindow?.document ||
      (this && this.nodeType === 9 ? this : null) ||
      e?.target?.ownerDocument ||
      document;

    if (!!e) {

      e.stopPropagation();
      // e.preventDefault();
    }

    $(contextDocument).find('[data-role="contextmenu"]').each((i, el) => {

      let _el = $(el);
      if (!!_el.data('hide')) {

        if (_el.data('hide') == 'hide') {

          _el.addClass(_.bootstrap_version() >= 4 ? 'd-none' : 'hidden');
        } else {

          // dispatch removal event on actual element
          el.dispatchEvent(new CustomEvent('removal'));
          _el.remove();
        }
      } else {

        // dispatch removal event on actual element
        el.dispatchEvent(new CustomEvent('removal'));
        _el.remove();
      }
    });
  };

  _.hideContexts = hideContexts;

  // delta implemented: internal hide behavior now calls hideContexts directly with confined window; legacy hide-contexts event retained
  $(document)
    .on('hide-contexts', hideContexts)
    .on('keyup.removeContexts', e => {

      if (27 == e.keyCode) hideContexts(e);
    })
    .on('click.removeContexts', e => {

      if ($(e.target).closest('[data-role="contextmenu"]').length > 0) {

        if (/^(a)$/i.test(e.target.nodeName)) { return; }
      }

      hideContexts(e);
    })
    .on('contextmenu.removeContexts', e => {

      if ($(e.target).closest('[data-role="contextmenu"]').length > 0) {

        if (/^(a)$/i.test(e.target.nodeName)) { return; }
      }

      hideContexts(e);
    });
})(_brayworth_);
