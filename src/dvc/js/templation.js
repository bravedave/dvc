/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * note:
 *	to use this library, by default it is going
 *	to call the home Controller
 *	to return the template:
 *	it won't work if you don't do that prep work
 *
 *
 * test - get:
 *	In this example d contains a template object - sort of vuejs ish (not very)
 *	templation.load({template:'sample'}).done( function( d) { console.log( d)});
 *
 * usage:
 *	$('body').html('');	// clear the page
 *	var t = templation.template('table').appendTo('body');
 *	for ( var i = 1; i < 10; i++) {
 *		var r = templation.template('tr').appendTo( t.get('tbody'));
 *		r.append( $('<td></td>').html( i));
 *
 *	}
 *
*/
/*jshint esversion: 6 */
window.templation = {
  urlwrite: (_url) => {
    if ('undefined' == typeof _url)
      _url = '';

    return ('/' + _url);

  }
};

(_ => {
  // const newLocal = '<div class="modal"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content" role="dialog" aria-labelledby="modal-title"><div class="modal-header py-1 bg-primary text-white"><h5 class="modal-title text-truncate" id="modal-title"></h5><i class="bi bi-x close text-white"></i></div><div class="modal-body"></div></div></div></div>';
  /*
    some predefined templates
    add more with templation.loadHTML('tr','<tr></tr>');
  */
  let cache = {
    container: '<div class="container"></div>',
    row: '<div class="row"></div>',
    form: '<form></form>',
    table: '<table><thead></thead><tbody></tbody><tfoot></tfoot></table>',
    tr: '<tr></tr>',
    modal: _.modal.template()[0].outerHTML,
  };

  function _t(src) {
    var _ = {
      src: src,
      _element: false,
      get: function (k) {
        if ('undefined' == typeof k)
          return (this._element);

        return $(k, this._element);
      },

      data: function (k, v) {
        if ('undefined' == typeof v)
          return (this._element.data(k));

        return (this._element.data(k, v));
      },

      html: function (k, v) {
        var e = this.get(k);
        if ('undefined' != typeof (v))
          return (e.html(v));

        return (e.html());
      },

      val: function (k, v) {
        var e = this.get(k);
        if ('undefined' != typeof (v))
          return (e.val(v));

        return (e.val());
      },

      append: function (p) {
        this._element.append(p);
        return (this);
      },

      appendTo: function (p) {
        this._element.appendTo(p);
        return (this);
      },

      prependTo: function (p) {
        this._element.prependTo(p);
        return (this);
      },

      remove: function (p) {
        this._element.remove();
        return (this);
      },

      reset: function () {
        this._element = $(this.src);
        return (this);
      }
    };

    return (_.reset());
  }

  templation.template = function (name) {
    if (name in cache)
      return (_t(cache[name]));

    throw 'template not in cache';
  };

  templation.loadHTML = (key, fragment) => {
    cache[key] = fragment;
    return (_t(fragment));
  };

  templation.load = function (params) {
    return (new Promise(function (resolve, reject) {
      let options = _brayworth_.extend({
        type: 'post',
        template: '',
        url: templation.urlwrite(),

      }, params);

      if (!options.template) {
        reject('no template'); // rejected

      }
      else if ('string' != typeof options.template) {
        reject('template must be a string'); // rejected

      }
      else {
        if (options.template in cache) {
          //~ console.log( 'resolve from cache');
          resolve(_t(cache[options.template])); // fulfilled

        }
        else {
          //~ console.log( 'not resolve from cache', cache);

          $.ajax({
            type: options.type,
            url: options.url,
            data: {
              action: 'get-template',
              template: options.template,
            }

          })
            .done(function (d) {
              cache[options.template] = d;

              if (d == '') {
                console.warn('templation:\n did you read the notes about the home controller\n > the home Controller must return the template upon\n   receiving the get-template request');

              }

              resolve(_t(d)); // fulfilled
            });

        }
      }
    }));
  };
})(_brayworth_);
