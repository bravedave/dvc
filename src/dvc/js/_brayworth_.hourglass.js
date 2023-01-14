/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {
  window.hourglass = _.hourglass = {
    h: false,

    on: function (msg) {
      let _me = this;

      if (!!_me.h) _me.off();

      return new Promise(resolve => {
        let bv = _.bootstrap.version();

        _me.h = $(`<div class="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-body">
                  <div class="d-flex text-center">
                    ${!!msg ? `<h1 class="py-2 ${bv < 5 ? 'mr' : 'me'}-auto">${msg}</h1>` : ''}
                    <i class="spinner-border my-2"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>`)
          .appendTo('body');

        _me.h.on('hidden.bs.modal', function (e) { $(this).remove(); });
        _me.h.modal('show');
        resolve(_me);
      })
    },

    off: function () {
      let _me = this;

      return new Promise((resolve, reject) => {

        if (!!_me.h) _me.h.modal('hide');
        _me.h = false;
        resolve(_me);
      });
    }
  };

})(_brayworth_);
