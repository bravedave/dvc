/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {

  _.context = () => {
    let cx = {
      root: $('<ul class="menu menu-contextmenu" data-role="contextmenu"></ul>'),
      items: [],
      length: 0,
      detachOnHide: true,
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

        }
        else {

          el.appendTo(this.root);
        }

        this.items.push(el);
        this.length = this.items.length;
        return (el);
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
        let css = {
          position: 'absolute',
          top: 10,
          left: $(document).width() - 140,

        };

        if (!!e.pageY) { css.top = Math.max(e.pageY + 2, 0); }
        if (!!e.pageX) { css.left = Math.max(e.pageX + 2, 0); }

        //~ console.log( this.root.width());

        let root = this.root;
        (e => {
          let t = $(e.target);
          if (t.length > 0) {
            css['z-index'] = t.zIndex() + 10;

          }

        })(e);


        if (this.detachOnHide) {
          root
            .css(css)
            .appendTo('body')
            .data('hide', 'detach');

        }
        else {
          //~ console.log( this.root.parent());
          if (root.parent().length < 1) {
            root
              .appendTo('body')
              .data('hide', 'hide');

          }

          root
            .css(css)
            .removeClass('hidden d-none');

        }

        let offset = root.offset();
        let wH = $(window).height();
        let wW = $(window).width();
        let sT = $(window).scrollTop();

        /* try to keep menu on screen horizontally */
        if (offset.left + root.width() > wW) {
          let l = wW - root.width() - 5;
          root.css('left', Math.max(l, 2));
          offset = root.offset();

        }

        /* try to keep menu on screen vertically */
        if (offset.top + this.root.height() > (wH + sT)) {
          let t = (wH + sT) - root.height() - 5;
          root.css('top', Math.max(t, sT + 2));
          offset = root.offset();

        }


        /**
         * add helper class to display the submenu on left
         * if the window width is restrictive on the right
         */
        if (offset.left > (wW - (root.width() * 2))) {
          root
            .addClass('menu-contextmenu-right');

        }
        else {
          root
            .removeClass('menu-contextmenu-right');

        }

        /**
         * add helper class to display the submenu high
         * if the window height is restrictive at bottom
         */
        if (offset.top + (root.height() * 1.2) > (wH + sT)) {
          root
            .addClass('menu-contextmenu-low');

        }
        else {
          root
            .removeClass('menu-contextmenu-low');

        }

        return (this);

      },

      close: function () {

        if (this.detachOnHide) {

          this.root.remove();
        } else {

          this.root.addClass(this.hideClass);
        }

        return (this);
      },

      remove: function () {

        return (this.close());
      },

      attachTo: function (parent) {

        let _me = this;

        $(parent)
          .off('click.removeContexts')
          .on('click.removeContexts', e => {
            if ($(e.target).closest('[data-role="contextmenu"]').length > 0) {
              if (/^(a)$/i.test(e.target.nodeName)) {
                return;

              }

            }

            $(document).trigger('hide-contexts');

          })
          .on('contextmenu', e => {
            /*--[ check for abandonment ]--*/
            if ($(e.target).closest('[data-role="contextmenu"]').length) {
              return;

            }

            $(document).trigger('hide-contexts');

            if (e.shiftKey) {
              return;

            }

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

            if (typeof window.getSelection != "undefined") {
              let sel = window.getSelection();
              if (sel.rangeCount) {
                if (sel.anchorNode.parentNode == e.target) {
                  let frag = sel.getRangeAt(0).cloneContents();
                  let text = frag.textContent;
                  if (text.length > 0)
                    return;

                }

              }

            }
            /*--[ end: check for abandonment ]--*/

            e.preventDefault();
            _me.open(e);

          });

        return (_me);

      }

    };

    /*
      ( _ => {
        $(document)
          .on( 'contextmenu', function( e) {
            if ( e.shiftKey)
              return;

            e.stopPropagation();e.preventDefault();

            _.hideContexts();

            let ctx = _.context();
            ctx.append.a({
              html : 'hello',
              click: e =>  _.ask({text:'hello'})
            })

            ctx
              .addClose()
              .open( e);
          });

      }) (_brayworth_);
    */

    const _new_element_ = p => {

      let o = {
        ...{
          click: e => { },
          html: '',
          href: ''
        }, ...p
      };


      return $(`<a href="#">${o.html}</a>`)
        .on('click', e => {
          if ('' == o.href) {
            e.stopPropagation(); e.preventDefault();
          }

          cx.close();
          o.click(e);
        });
    }

    cx.append.a = p => _new_element_(p).appendTo(cx);
    cx.prepend.a = p => _new_element_(p).prepend(cx);

    return cx;
  };

  // _.contextX = () => {
  //   let _ctx = $('<div class="dropdown-menu" data-role="contextmenu"></div>');

  //   _ctx.extend({
  //     addClose: function () {
  //       let _context = this;
  //       this.append('<div class="dropdown-divider"></div>');

  //       $('<a class="dropdown-item">close</a>')
  //         .appendTo(this)
  //         .on('click', e => _context.close());

  //       return this;

  //     },

  //     close: function (e) {
  //       this
  //         .closest('[data-role="contextmenu"]')
  //         .remove();
  //     },

  //     open: function (e) {
  //       $('[data-role="contextmenu"]').remove();

  //       let offsets = $(e.currentTarget).offset()
  //       let dropdown = $('<div class="dropdown position-absolute"></div>')
  //         .css({
  //           top: (e.pageY - offsets.top) + 'px',
  //           left: (e.pageX - offsets.left) + 'px'
  //         });

  //       $('<div class="position-relative"></div>')
  //         .append(dropdown)
  //         .insertBefore(e.currentTarget);

  //       dropdown
  //         .append(this)
  //         .dropdown('show');

  //     }

  //   });

  //   return _ctx;

  // };

  $(document)
    .on('hide-contexts', e => {

      $('[data-role="contextmenu"]').each((i, el) => {

        let _el = $(el);
        if (!!_el.data('hide')) {
          if (_el.data('hide') == 'hide') {
            _el.addClass(_.bootstrap_version() >= 4 ? 'd-none' : 'hidden');

          } else { _el.remove(); }

        } else { _el.remove(); }
      });
    })
    .on('keyup.removeContexts', e => {

      if (27 == e.keyCode) $(document).trigger('hide-contexts');
    })
    .on('click.removeContexts', e => {

      if ($(e.target).closest('[data-role="contextmenu"]').length > 0) {

        if (/^(a)$/i.test(e.target.nodeName)) { return; }
      }

      $(document).trigger('hide-contexts');
    })
    .on('contextmenu.removeContexts', e => {

      if ($(e.target).closest('[data-role="contextmenu"]').length > 0) {

        if (/^(a)$/i.test(e.target.nodeName)) { return; }
      }

      $(document).trigger('hide-contexts');
    });
})(_brayworth_);
