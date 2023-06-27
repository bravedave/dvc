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
              <div class="modal-content bg-transparent shadow-none">
                <div class="modal-body">
                  <div class="d-flex text-center py-2">
                    ${!!msg ? `<h1 class="py-0 my-1 ${bv < 5 ? 'mr' : 'me'}-auto">${msg}</h1>` : ''}
                    <i class="m-auto spinner-border my-2" style="width: 3rem; height: 3rem;"></i>
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
