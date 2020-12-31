/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *

	This is similar to bootstraps modal in construction,
	but also has similarity to jquery-ui functionality

	load:
		$('<script />').attr('src','/js/_brayworth_.modal.js').appendTo('head');

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
/*jshint esversion: 6 */
( _ => {
  _.modal = function( params) {
    if ( 'string' == typeof params) {
      /* This is a command - jquery-ui style */
      var _m = $(this).data( 'modal');
      if ( 'close' == params)
        _m.close();

      return;

    }

    let options = {
      title : '',
      width : false,
      height : false,
      mobile : _brayworth_.browser.isMobileDevice,
      fullScreen : _brayworth_.browser.isMobileDevice,
      className :  _brayworth_.templates.modalDefaultClass,
      autoOpen : true,
      buttons : {},
      headButtons : {},
      closeIcon: 'bi-x',
      onOpen : function() {},
      onEnter : function() {},

    };

    $.extend( options, params);

    let t = _brayworth_.templates.modal();
    t.get('.close').addClass(options.closeIcon);

    if ( options.className != '') {
      t.get('.modal-dialog').addClass( options.className);

    }

    if ( !!options.width){
      t.get('.modal-dialog').css({'width' : options.width, 'max-width' : options.width});

    }

    let content = ( !!options.text ? options.text : '');
    if ( 'undefined' != typeof this) {
      if ( !this._brayworth_ ) {
        content = ( this instanceof jQuery ? this : $(this));
        if ( options.title == '' && ( 'string' == typeof content.attr('title')))
          options.title = content.attr('title');

      }

    }

    t.html('.modal-title','').append( options.title);	// jquery-ui style
    // console.log( t.html('.modal-title'));

    t.append( content);		// this is the content

    if ( Object.keys( options.buttons).length > 0) {	// jquery-ui style
      let btnGrp = $('<div class="btn-group btn-group-sm"></div>').appendTo( t.footer());
      $.each( options.buttons, function( i, el) {
        let j = {
          text : i,
          title : '',
          click : function( e) {}
        };

        if ( 'function' == typeof el)
          j.click = el;
        else
          $.extend( j, el) ;

        let btn = $('<button />')
          .addClass( _brayworth_.templates.buttonCSS)
          .html( j.text)
          .on( 'click', function( e) {
            j.click.call( t.get(), e, this);

          })
          .appendTo( btnGrp);

        if ( 'object' == typeof el) el.button = btn;	// object now accessible to calling function

        if ( !!j.title) {
          btn.attr('title', j.title);

        }

      });

    }

    if ( Object.keys( options.headButtons).length > 0) {
      let margin = 'ml-auto';
      $.each( options.headButtons, function( i, el) {
        var j = {
          text : i,
          title : false,
          icon : false,
          click : function( e) {},
        };

        if ( 'function' == typeof el)
          j.click = el;
        else
          $.extend( j, el);

        let b;
        if ( !!j.icon) {
          b = $( '<div class="pointer pt-1 px-2"></div>').append(
            $('<i class="fa m-0" style="cursor: pointer;"></i>').addClass( j.icon));

        }
        else {
          b = $('<button></button>')
            .html( j.text)
            .addClass( _brayworth_.templates.buttonCSS);

        }

        if ('' != margin) b.addClass(margin);
        margin = '';

        if ( !!j.title) b.attr( 'title', j.title);

        b.on( 'click', function( e) { j.click.call( t.get(), e); });	// wrap the call and call it against the modal
        b.insertBefore( $('.close', t.header));

        if ( 'object' == typeof el) el.button = b;	// object now accessible to calling function

      });

      $('.close', t.header).addClass('ml-0')

    }

    let previousElement = document.activeElement;
    let hideClass = _brayworth_.bootstrap_version() < 4 ? 'hidden' : 'd-none';
    let bodyElements = [];
    if ( options.fullScreen) {
      /* hide all the body elements */
      $('body > *').each( function( i, el){
        var _el = $(el);
        if ( !_el.hasClass(hideClass)) {
          _el.addClass(hideClass);
          bodyElements.push( _el);

        }

      });

      t.get('.modal').addClass('modal-fullscreen');
      t.get('.modal-dialog').addClass('m-auto').removeClass('modal-dialog-centered');
      t.get('.modal-content').removeClass('w-25 w-50 w-75').addClass('w-100');

    }
    else {
      if ( !!options.height) {
        t.get('.modal-body')
          .height( options.height )
          .css({'overflow-y' : 'auto', 'overflow-x' : 'hidden'});

      }

    }

    t.appendTo( 'body');

    let _modal = _brayworth_.modalDialog.call( t.get(), {
      mobile : options.mobile,
      onOpen : options.onOpen,
      onEnter : options.onEnter,
      afterClose : function() {
        t.get().remove();
        if ( !!options.afterClose && 'function' == typeof options.afterClose)
          options.afterClose.call( t.modal);

        /* re-activate the body elements */
        $.each( bodyElements, function( i, el){
          $(el).removeClass(hideClass);

        });

        previousElement.focus();

      },

    });

    _modal.modal = _brayworth_.modal;	// to be sure, bootstrap has it's own modal

    _modal.load = function( url) {
      /*
       * this is a wrapper on the modal->body element
       * for jQuery.load
       */
      return new Promise( function( resolve, reject) {
        var d = $('<div></div>');
        t.append( d);
        d.load( url, function() {
          resolve( _modal);

        });

      });

    };

    _modal.checkHeight = function() {
      /*
      * check that the dialog fits on screen
      */
      let h = $('.modal-body', this).height();
      let mh = $(window).height() * 0.9;
      let ftr = $('.modal-footer', this);
      if ( ftr.length > 0) {
        mh -= ftr.height();

      }

      if ( h > mh) {
        $('.modal-body', this)
        .height( mh)
        .css({'overflow-y' : 'auto', 'overflow-x' : 'hidden'});

      }

      return ( this);

    };

    t.data( 'modal', _modal);
    if ( 'undefined' != typeof this && !this._brayworth_ ) {
      if ( this instanceof jQuery) {
        this.data('modal', _modal);

      }
      else {
        $(this).data('modal', _modal);

      }

    }

    return ( t.data( 'modal'));	// the modal

  };

  _.modal.template = () => $([
    '<div class="modal" tabindex="-1" role="dialog">',
      '<div class="modal-dialog modal-dialog-centered modal-sm" role="document">',
        '<div class="modal-content">',
          '<div class="modal-header py-2">',
            '<h5 class="modal-title text-truncate" title="Modal">Modal</h5>',
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
          '</div>',
          '<div class="modal-body"></div>',
          '<div class="modal-footer py-0"></div>',
        '</div>',
      '</div>',
    '</div>'
  ].join(''));

  _.templates.buttonCSS = 'btn btn-default';
  _.templates.modalDefaultClass = '';
  _.templates.modal = function() {
    let _t = templation.template('modal');
      _t.header = _t.get( '.modal-header');
      _t.body = _t.get( '.modal-body');
      _t.append = function( p) {
        this.body.append( p);
        return ( this);

      };

      _t.footer = function() {
        if ( !this._footer) {
          this._footer = $('<div class="modal-footer py-1 text-right"></div>');
          this.get('.modal-content').append( this._footer);

        }

        return ( this._footer);

      };

    return ( _t );

  };

}) (_brayworth_);
