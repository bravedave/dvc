/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *
  test:
    _brayworth_.modalDialog.call( $('<div class="modal"><div class="modal-content"><div class="modal-header"><h1>Header</h1><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body">Hello World</div></div></div>').appendTo('body'))
    _brayworth_.modalDialog.call( $('<div class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header py-1"><h5 class="modal-title">Hello World</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body">...</div></div></div></div>').appendTo('body'))
*/
/*jshint esversion: 6 */
(_ => {
  $.fn.modalDialog = _.modalDialog = function (_options) {

    if (/string/.test(typeof (_options))) {

      if (_options == 'close') {

        let modal = this.data('modal');
        modal.close();
        return modal;	// chain
      }
    }

    let modal = this;				// the modal
    let options = {
      ...{
        mobile: _.browser.isMobileDevice,
        beforeClose: () => { },
        afterClose: () => { },
        onEnter: () => { },
        onEscape: function () { this.close(); },
        onOpen: () => { },
      }, ..._options
    };

    let close = $('.modal-header .close', this);	// Get the <span> element that closes the modal

    modal.close = () => {

      options.beforeClose.call(modal);
      if (_.bootstrap.version() < 5) {

        modal.removeClass('modal-active');
        $('body').removeClass('modal-open');
      } else {

        modal.modal('hide');
        console.log('modal.modal(\'hide\')');
      }

      $('body').css('padding-right', '');	// credit bootstrap class
      //~ $(window).off('click');
      options.afterClose.call(modal);

      modal = false;
      $(document).off('keyup.modal');
      $(document).off('keypress.modal');
    };

    if (options.mobile) {

      modal.addClass('modal-mobile');
    } else {

      // let rect = document.body.getBoundingClientRect();
      if (document.body.scrollHeight > window.innerHeight) {

        $('body').css('padding-right', '17px');	// credit bootstrap
      }
    }

    if (_.bootstrap.version() < 5) {

      $('body').addClass('modal-open');	// bootstrap class
      modal.addClass('modal-active');
    } else {

      modal.modal('show');
    }

    modal.data('modal', modal);

    let _AF = $('[autofocus]', modal);
    if (_AF.length > 0) {

      _AF.first().focus();
    }
    else {

      _AF = $('textarea:not([readonly]), input:not([readonly]), button:not([disabled]), a:not([tabindex="0"])', modal);
      if (_AF.length > 0) _AF.first().focus();
    }

    $(document)
      .on('keyup.modal', e => {

        if (e.keyCode == 27) {

          // escape key maps to keycode `27`
          if (modal) options.onEscape.call(modal, e);
        }
      })
      .on('keypress.modal', e => {

        if (e.keyCode == 13) options.onEnter.call(modal, e);
      });

    // When the user clicks on <span> (x), close the modal
    close
      .off('click')
      .addClass('pointer')
      .on('click', e => modal.close());

    options.onOpen.call(modal);
    return modal;	// chain
  };
})(_brayworth_);
