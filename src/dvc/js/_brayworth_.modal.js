/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *

  This is similar to bootstraps modal in construction,
  but also has similarity to jquery-ui functionality

  Test:
    _brayworth_.modal.call( $('<div title="fred">hey jude</div>'))
    _brayworth_.modal.call( $('<div title="fred">hey jude</div>'), {
      buttons : {
        Close : function(e) {
          this.modal( 'close');

        }

      }

    })

    _brayworth_.modal({ title : 'fred', text : 'hey jude'});
    _brayworth_.modal({
      width : 300,
      title : 'fred',
      text : 'hey jude',
      buttons : {
        Close : function(e) {
          this.modal( 'close');
        }
      }
    });
*/
(_ => {
  _.modal = function (params) {
    if ('string' == typeof params) {
      /* This is a command - jquery-ui style */
      let _m = $(this).data('modal');
      if ('close' == params)
        _m.close();

      return;

    }

    let options = {
      ...{
        title: '',
        width: false,
        height: false,
        mobile: _.browser.isMobileDevice,
        fullScreen: _.browser.isMobileDevice,
        className: _.templates.modalDefaultClass,
        autoOpen: true,
        buttons: {},
        headButtons: {},
        closeIcon: 'bi-x',
        onOpen: () => { },
        onEnter: () => { },
      }, ...params
    };

    let t = _.templates.modal();
    let close = t.get('.close');
    if ($('.bi', close).length > 0) {
      $('.bi', close).addClass(options.closeIcon);
    } else if (close.length > 0) {
      // console.log('add to close')
      close.addClass(options.closeIcon);
    }

    if (options.className != '') {
      t.get('.modal-dialog').addClass(options.className);

    }

    if (!!options.width) {
      t.get('.modal-dialog').css({ 'width': options.width, 'max-width': options.width });

    }

    let content = (!!options.text ? options.text : '');
    if ('undefined' != typeof this) {
      if (!this._brayworth_) {
        content = (this instanceof jQuery ? this : $(this));
        if (options.title == '' && ('string' == typeof content.attr('title')))
          options.title = content.attr('title');

      }

    }

    t
      .html('.modal-title', '')
      .append(options.title);	// jquery-ui style
    // console.log( t.html('.modal-title'));

    t.append(content);		// this is the content

    if (Object.keys(options.buttons).length > 0) {	// jquery-ui style
      $.each(options.buttons, (i, el) => {
        let j = {
          text: i,
          title: '',
          click: e => { }
        };

        if ('function' == typeof el)
          j.click = el;
        else
          j = { ...j, ...el };

        let btn = $(`<button class="${_.templates.buttonCSS}" type="button">${j.text}</button>`)
          .on('click', function (e) {
            j.click.call(t.get(), e, this);

          })
          .appendTo(t.footer());

        if ('object' == typeof el) el.button = btn;	// object now accessible to calling function

        if (!!j.title) btn.attr('title', j.title);

      });
    }

    if (Object.keys(options.headButtons).length > 0) {
      let margin = 'ml-auto';
      $.each(options.headButtons, (i, el) => {
        let j = {
          text: i,
          title: false,
          icon: false,
          click: e => { },
        };

        if ('function' == typeof el)
          j.click = el;
        else
          j = _.extend(j, el);

        let b;
        if (!!j.icon) {
          b = $('<div class="pointer pt-1 px-2"></div>').append(
            $('<i class="m-0"></i>')
              .addClass(j.icon)
              .addClass(/^fa/.test(j.icon) ? 'fa' : 'bi')

          );

        }
        else {
          b = $('<button></button>')
            .html(j.text)
            .addClass(_.templates.buttonCSS);

        }

        if ('' != margin) b.addClass(margin);
        margin = '';

        if (!!j.title) b.attr('title', j.title);

        b.on('click', function (e) { j.click.call(t.get(), e); });	// wrap the call and call it against the modal
        b.insertBefore($('.close', t.header));

        if ('object' == typeof el) el.button = b;	// object now accessible to calling function

      });

      $('.close', t.header).addClass('ml-0')

    }

    let previousElement = document.activeElement;
    let hideClass = _.bootstrap_version() < 4 ? 'hidden' : 'd-none';
    let bodyElements = [];
    if (options.fullScreen) {
      /* hide all the body elements */
      $('body > *').each((i, el) => {
        let _el = $(el);
        if (!_el.hasClass(hideClass)) {
          _el.addClass(hideClass);
          bodyElements.push(_el);

        }

      });

      t.get('.modal').addClass('modal-fullscreen');
      t.get('.modal-dialog').addClass('m-auto').removeClass('modal-dialog-centered');
      t.get('.modal-content').removeClass('w-25 w-50 w-75').addClass('w-100');

    }
    else {
      if (!!options.height) {
        t.get('.modal-body')
          .height(options.height)
          .css({ 'overflow-y': 'auto', 'overflow-x': 'hidden' });

      }

    }

    t.appendTo('body');

    let _modal = _.modalDialog.call(t.get(), {
      mobile: options.mobile,
      onOpen: options.onOpen,
      onEnter: options.onEnter,
      afterClose: () => {
        t.get().remove();
        if (!!options.afterClose && 'function' == typeof options.afterClose) {
          options.afterClose.call(t.modal);

        }

        /* re-activate the body elements */
        $.each(bodyElements, (i, el) => $(el).removeClass(hideClass));

        previousElement.focus();
      },
    });

    _modal.modal = _.modal;	// to be sure, bootstrap has it's own modal

    // wrapper on the modal->body element for jQuery.load
    _modal.load = url => new Promise(resolve => {
      let d = $('<div></div>');
      t.append(d);
      d.load(url, () => resolve(_modal));
    });

    _modal.checkHeight = function () {
      /*
      * check that the dialog fits on screen
      */
      let h = $('.modal-body', this).height();
      let mh = $(window).height() * 0.9;
      let ftr = $('.modal-footer', this);
      if (ftr.length > 0) mh -= ftr.height();

      if (h > mh) {
        $('.modal-body', this)
          .height(mh)
          .css({ 'overflow-y': 'auto', 'overflow-x': 'hidden' });
      }

      return (this);
    };

    t.data('modal', _modal);
    if ('undefined' != typeof this && !this._brayworth_) {
      if (this instanceof jQuery) {
        this.data('modal', _modal);

      }
      else {
        $(this).data('modal', _modal);

      }

    }

    return (t.data('modal'));	// the modal

  };

  _.modal.template = () => $(
    `<div class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-truncate" title="Modal">Modal</h5>
            <button type="button" class="close" data-${_.bootstrap_version() >= 5 ? 'bs-' : ''}dismiss="modal" aria-label="Close">
              <i aria-hidden="true" class="bi bi-x"></i>
            </button>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>`);

  _.modal.pdf = p => {
    let options = {
      ...{
        size: 'xl',
        title: 'Viewer',
        headClass: '', /* allows for theming */
        url: 'string' == typeof p ? p : ''
      },
      ...p
    };

    let ask = _.ask(options);

    let id = _.randomString();

    ask.find('.modal-dialog').addClass('modal-fullscreen-sm');
    ask.find('.modal-body')
      .addClass('p-2')
      .append(`<style>
        @media (min-width: 768px) {
          #${id} { min-height: calc(100vh - 100px) !important; }
        }
        </style>
        <iframe title="${options.title}" id="${id}" src="${options.url}"
          width="100%" height="100%" frameborder="0"></iframe>`);

    return ask; // ask is a promise

  };

  _.templates.buttonCSS = 'btn btn-default';
  _.templates.modalDefaultClass = '';
  _.templates.modal = () => {
    let _t = templation.template('modal');
    _t.header = _t.get('.modal-header');
    _t.body = _t.get('.modal-body');
    _t.append = function (p) {
      this.body.append(p);
      return (this);
    };

    _t.footer = function () {
      if (!this._footer) {
        this._footer = this.get('.modal-footer');
        if (this._footer.length == 0) {
          this._footer = $('<div class="modal-footer py-1 text-right"></div>');
          this.get('.modal-content').append(this._footer);
        }
      }

      return (this._footer);
    };

    return (_t);
  };
})(_brayworth_);
