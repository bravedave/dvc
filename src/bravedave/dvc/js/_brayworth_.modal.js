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
      const _m = $(this).data('modal');
      if ('close' == params) _m.close();
      return;
    }

    const options = {
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
      },
      ...params
    };

    let content = (!!options.text ? options.text : '');
    if ('undefined' != typeof this) {

      if (!this._brayworth_) {

        content = (this instanceof jQuery ? this : $(this));
        if (options.title == '' && ('string' == typeof content.attr('title')))
          options.title = content.attr('title');
      }
    }

    let m = _.modal.template();
    m
      .on('show.bs.modal', e => { })
      .on('shown.bs.modal', e => options.onOpen.call(m));

    m.checkHeight = function () {

      // check that the dialog fits on screen
      const h = $('.modal-body', this).height();
      const ftr = $('.modal-footer', this);
      
      let mh = $(window).height() * 0.9;
      if (ftr.length > 0) mh -= ftr.height();

      if (h > mh) {
        $('.modal-body', this)
          .height(mh)
          .css({
            'overflow-y': 'auto',
            'overflow-x': 'hidden'
          });
      }

      return this;
    };

    m.close = function () {

      $(this).data('modal').modal('hide');
      return this;
    };

    m.find('.modal-title').html(options.title);
    m.find('.modal-body').append(content);

    if (Object.keys(options.buttons).length > 0) { // jquery-ui style

      $.each(options.buttons, (i, el) => {

        let j = {
          text: i,
          title: '',
          click: e => { }
        };

        if ('function' == typeof el) {

          j.click = el;

        } else {

          j = {
            ...j,
            ...el
          };
        }

        const btn = $(`<button class="${_.templates.buttonCSS}" type="button">${j.text}</button>`)
          .on('click', function (e) { j.click.call(m, e, this); });

        m.find('.modal-footer').append(btn);

        // object now accessible to calling function
        if ('object' == typeof el) el.button = btn;
        if (!!j.title) btn.attr('title', j.title);
      });
    } else {

      m.find('.modal-footer').addClass('p-0');
    }

    if (Object.keys(options.headButtons).length > 0) {

      let closeBtn = m.find('.modal-header .close, .modal-header .btn-close')
      let margin = 'ml-auto';
      $.each(options.headButtons, (i, el) => {

        let j = {
          text: i,
          title: false,
          icon: false,
          click: e => { },
        };

        if ('function' == typeof el) {

          j.click = el;
        } else {

          j = {
            ...j,
            ...el
          };
        }

        let b;
        if (!!j.icon) {

          b = $('<div class="pointer pt-1 px-2"></div>')
            .append(
              $('<i class="m-0"></i>')
                .addClass(j.icon)
                .addClass(/^fa/.test(j.icon) ? 'fa' : 'bi')
            );
        } else {

          b = $(`<button>${j.text}</button>`).addClass(_.templates.buttonCSS);
        }

        if ('' != margin) b.addClass(margin);
        margin = '';

        if (!!j.title) b.attr('title', j.title);

        b.on('click', e => j.click.call(m, e)); // wrap the call and call it against the modal
        b.insertBefore(closeBtn);

        if ('object' == typeof el) el.button = b; // object now accessible to calling function
      });

      closeBtn.addClass('ml-0')
    }

    if (!!options.width) {

      m.find('.modal-dialog').css({
        'width': options.width,
        'max-width': options.width
      });
    }

    if (options.className != '') {

      m.find('.modal-dialog').addClass(options.className);
    }

    // console.log(options, this);
    m.data('modal', m);
    m.modal('show');
    return m;
  };

  _.modal.template = () => $(
    `<div class="modal fade" tabindex="-1" role="dialog" aria-modal="true" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header text-bg-primary">
            <h5 class="modal-title text-truncate" title="Modal">Modal</h5>
            <button type="button" class="${_.bootstrap_version() >= 5 ? 'btn-close btn-close-white' : 'close'}" data-${_.bootstrap_version() >= 5 ? 'bs-' : ''}dismiss="modal" aria-label="Close" tabindex="-1">
              ${_.bootstrap_version() >= 5 ? '' : '<i aria-hidden="true" class="bi bi-x"></i>'}
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
        openInNewWindow: true,
        url: 'string' == typeof p ? p : ''
      },
      ...p
    };

    let ask = _.ask(options);

    let id = _.randomString();

    if (options.openInNewWindow) {

      const btnClose = ask.find('.modal-header > .close, .modal-header > .btn-close');
      if (btnClose.length > 0) {

        btnClose.addClass(_.bootstrap.version() < 5 ? 'ml-4' : 'ms-2')

        $('<button type="button" class="btn btn-primary js-open-in-new-window"><i class="bi bi-box-arrow-up-right"></i></button>').on('click', e => {
          e.stopPropagation();
          window.open(options.url);
        })
          .addClass(_.bootstrap.version() < 5 ? 'ml-auto' : 'ms-auto')
          .insertBefore(btnClose);
      }
    }

    ask.find('.modal-dialog').addClass('modal-fullscreen-md-down');
    ask.find('.modal-body')
      .addClass('p-2')
      .append(`<style>
        @media (min-width: 768px) and (min-height: 450px) {
          #${id} { min-height: calc(100vh - 11rem) !important; }
        }
        </style>
        <embed title="${options.title}" id="${id}" src="${options.url}" width="100%" height="100%"></embed>`);

    return ask; // ask is a modal

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
