/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
 * test:
    _brayworth_.ask({
        buttons : {
            yes : function() {
                $(this).modal('hide');
                console.log( 'ok', this);

            }

        }

    });

 * */

_brayworth_.ask = ( params) => {
    let dlg = $('<div class="modal" tabindex="-1" role="dialog"><div class="modal-dialog modal-dialog-centered modal-sm" role="document" ><div class="modal-content"><div class="modal-header py-2"><h5 class="modal-title text-truncate" title="Modal">Modal</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body" /></div></div ></div >');

    let options = $.extend({
        text: 'Question',
        title: 'Topic',
        headClass: 'text-white bg-dark',
        beforeOpen: function () {
            let modal = this;
            $('.modal-title', modal).html(options.title);
            $('.modal-body', modal).append(options.text);

            let footer = $('<div class="modal-footer py-0" />').appendTo($('.modal-content', modal));

            $.each( options.buttons, function (key, j) {
                $('<button class="btn btn-light" />')
                .html(key)
                .appendTo(footer)
                .on('click', function (e) {
                    j.call(modal, e);

                });

            });

        },
        buttons: {},
        onClose : function(e) {

        }

    }, params);

    dlg.on('hidden.bs.modal', function(e) {
        console.log( 'hidden.bs.modal', this);

    });

    dlg.appendTo( 'body');
    $('.modal-header', dlg).addClass(options.headClass);
    options.beforeOpen.call( dlg);
    dlg.modal('show');

    return dlg;	// a promise

};
