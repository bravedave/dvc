/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * https://learn.jquery.com/plugins/basic-plugin-creation/
 *
 *	source:
 *		the source is inspired by jquery-ui and should behave as such
 *
 *		the request is passed in the format [<jsonObject>.term]
 *		the response will be parsed in the fashion [<jsonObject>.label]
 * */
(($, _) => {

  $.fn.autofill = function (params) {

    if ('string' == typeof params) {

      if ('destroy' == params) {

        this.trigger('destroy-autofill');
        return this;
      }
    }

    const options = {
      ...{
        timeout: 400,
        wrapper: $('<div class="autofill-wrapper"></div>'),
        autoFocus: false,
        activateOnEnter: true,
        minLength: 3,
        minWidth: 150,
        select: false,
        source: (request, response) => { },
      },
      ...params
    };

    // console.log(options);
    const list = $('<ul class="list-group" style="position: absolute; left: 0; z-index: 5; width: 100%;"></ul>');

    options.wrapper.append(list);
    if (!!options.appendTo) {

      const parent = options.appendTo instanceof jQuery ? options.appendTo : (options.appendTo);
      parent.append(options.wrapper);
    } else {

      options.wrapper.insertAfter(this);
    }

    const _me = this;
    const keyMove = {
      active: -1,
      items: () => $('>li', list),
      current: false,
      _initialized: false,

      activate: function (item) {

        if (this.current) {

          this.current.removeClass('active');
          this.current = false;
        }

        this.current = $(item);
        this.current.addClass('active');
      },

      deactivate: function (item) {

        let _item = $(item);
        if (_item.hasClass('active')) _item.removeClass('active');

        if (this.current && !this.current.hasClass('active')) {

          this.current = false; // there is no item active
        }
      },

      up: function () {

        let items = this.items();
        if (items.length > 0) {

          let item = -1;
          $.each(items, (i, el) => {

            if ($(el).hasClass('active')) {

              item = i;
              return true; // break;
            }
          });

          if (item < 0) {

            this.activate(items[items.length - 1]);
          } else {

            item--;
            this.activate(items[item < 0 ? items.length - 1 : item]);
          }
        }
      },

      down: function () {

        let items = this.items();
        if (items.length > 0) {

          let item = -1;
          $.each(items, (i, el) => {

            if ($(el).hasClass('active')) {

              item = i;
              return true; // break
            }
          });

          if (item < 0) {

            this.activate(items[0]);
          } else {

            item++;
            this.activate(items[item > items.length - 1 ? 0 : item]);
          }
        }
      },

      selectitem: function (e) {

        keyMove.activate(this);

        let item = $(this).data('item');
        _me.val(!!item.value ? item.value : (!!item.label ? item.label : item));
        if (!!e.stopPropagation) {

          // this stops the click going through to an underlying element
          e.stopPropagation();
          e.preventDefault();
        }

        keyMove.clear();
        if ('function' == typeof options.select) {

          options.select.call(_me, e, {
            item: item
          });
        }
      },

      select: function (e) {

        let items = this.items();
        if (items.length > 0) {

          let item = -1;
          $.each(items, (i, el) => {

            if ($(el).hasClass('active')) {

              item = i;
              return (false);
            }
          });

          if (item > -1) {

            this.selectitem.call(items[item], e);
          } else if (!!e && (13 == e.keyCode || 9 == e.keyCode)) {

            if (options.activateOnEnter || options.autoFocus) {

              this.selectitem.call(items[0], e);
            }
          }
        }
      },

      clear: function () {

        list.html('');
        this.current = false;
        return this;
      },

      init: function () {

        if (this._initialized) return;

        /**
         * initialise placement of the list item
         *
         * this is sized and placed the first time it is used the element
         * should be positioned and sized correctly by now.
         *
         */

        this._initialized = true;

        /*-- --[ position exactly where ? ]-- --*/
        if ('static' == options.appendTo.css('position')) {

          options.appendTo.css('position', 'relative');
        }

        let _mePos = _me.offset();
        let parentPos = options.appendTo.offset();
        let childOffset = {
          position: 'absolute',
          top: _mePos.top - parentPos.top + _me.outerHeight(),
          left: _mePos.left - parentPos.left
        };
        childOffset.width = Math.max(_me.outerWidth(), options.minWidth);
        childOffset.width = `calc(100% - ${childOffset.left}px)`;
        options.wrapper.css(childOffset);
        /*-- --[ position exactly where ? ]-- --*/
      }
    };

    let lastVal = '';
    let iterant = 0;
    let blurTimeOut = false;

    this
      .one('destroy-autofill', () => {

        $(this)
          .off('search.autofill')
          .off('keydown.autofill')
          .off('keypress.autofill')
          .off('keyup.autofill')
          .off('focus.autofill')
          .off('blur.autofill');

        $(this).siblings('.autofill-wrapper').remove();
      })
      .on('focus.autofill', () => {

        if (blurTimeOut) {

          clearTimeout(blurTimeOut);
          blurTimeOut = false;
        }
      })
      .on('blur.autofill', () => {

        if (blurTimeOut) {

          clearTimeout(blurTimeOut);
          blurTimeOut = false;
        }

        blurTimeOut = setTimeout(() => {

          //~ console.log( 'setTimeout :: clear');
          keyMove.clear();
          blurTimeOut = false;
        }, 900);
      })
      .on('keydown.autofill', e => {

        //~ console.log( 'keydown.autofill', e.keyCode);
        if (e.keyCode == 9 && options.autoFocus) {

          keyMove.select(e);
          return;
        }
      })
      .on('keypress.autofill', e => {

        // allowing enter to pass thru will probably submit the form, just settle here
        return (e.keyCode || e.which || e.charCode || 0) !== 13;
      })
      .on('keyup.autofill', function (e) {

        if (e.shiftKey) return;

        if (!_.browser.isMobileDevice) {

          if (e.keyCode == 9) {

            return; // tab key does not trigger search
          } else if (e.keyCode == 13) {

            keyMove.select(e);
            return;
          } else if (e.keyCode == 38) {

            keyMove.up();
            return;
          } else if (e.keyCode == 40) {

            keyMove.down();
            return;
          }
        }

        if (_me.val().length < options.minLength || _me.val() == lastVal) return;

        lastVal = _me.val();

        //~ console.log( typeof options.source);
        if (/^(array|object)$/.test(typeof options.source)) {

          keyMove
            .clear()
            .init();

          let rex = new RegExp(lastVal);
          $.each(options.source, (i, _el) => {

            let el = {
              ...{
                label: 'string' == typeof _el ? _el : '',
                value: 'string' == typeof _el ? _el : '',
              }, ..._el
            };

            if (!!el.label) {

              if (rex.test(el.label)) {

                $(`<li class="list-group-item p-1" tabindex="-1">
                    <div class="text-truncate">${_.encodeHTMLEntities(el.label)}</div>
                  </li>`)
                  .data('item', el)
                  .on('click', function (e) {
                    keyMove.selectitem.call(this, e);
                  })
                  .on('mouseover', function () {
                    keyMove.activate(this);
                  })
                  .appendTo(list);
              }
            }
          });
        } else {

          $(this).trigger('search.autofill');
        }
      })
      .on('search.autofill', function (e, d) {

        lastVal = _me.val();

        let _data = {
          term: lastVal,
          iterant: ++iterant,
        };

        setTimeout(() => {

          if (_data.iterant != iterant) return;

          let render = _el => {

            let el = {
              ...{
                label: 'string' == typeof _el ? _el : '',
                value: 'string' == typeof _el ? _el : '',
              }, ..._el
            };

            let label = !!el.label ? el.label : (!!el.value ? el.value : el);

            let touchStartTimeout = false;
            let _li = $(`<li class="list-group-item p-1" tabindex="-1">
                <div class="text-truncate" tabindex="-1">${_.encodeHTMLEntities(label)}</div>
              </li>`)
              .data('item', el)
              .css('border', '1px solid dashed')
              .on('mousedown', e => e.preventDefault() /* Prevent focus leaving field */)
              .on('click', function (e) {

                keyMove.selectitem.call(this, e);
              })
              .on('touchstart', function (e) {

                keyMove.activate(this);
                touchStartTimeout = setTimeout(() => keyMove.deactivate(this), 300);
              })
              .on('touchend', function (e) {

                if ($(this).hasClass('active')) {

                  if (touchStartTimeout) clearTimeout(touchStartTimeout);
                  keyMove.selectitem.call(this, e);
                }
              })
              .on('mouseover', function (e) {

                keyMove.activate(this);
              });

            return _li;
          };

          options.source(_data, d => {

            keyMove.clear();
            keyMove.init();
            $.each(d, (i, el) => list.append(render(el)));
          });
        }, options.timeout);
      });

    return this; // chain
  };
})(jQuery, _brayworth_);




