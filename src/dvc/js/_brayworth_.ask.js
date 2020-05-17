/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
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
    let dlg = $([
        '<div class="modal" tabindex="-1" role="dialog">',
            '<div class="modal-dialog modal-dialog-centered modal-sm" role="document">',
                '<div class="modal-content">',
                    '<div class="modal-header py-2"><h5 class="modal-title text-truncate" title="Modal">Modal</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>',
                    '<div class="modal-body"></div>',
                '</div>',
            '</div>',
        '</div>'
        ].join('')

    );

    let options = _brayworth_.extend({
        beforeOpen: function () {
            let modal = this;
            $('.modal-title', modal).html( options.title);
            $('.modal-body', modal).html( options.text);

            let footer = $('<div class="modal-footer py-0"></div>').appendTo($('.modal-content', modal));

            $.each( options.buttons, ( key, j) => {
                $('<button class="btn btn-light" type="button" />')
                .html(key)
                .appendTo(footer)
                .on('click', function (e) {
                    j.call(modal, e);

                });

            });

        },
        buttons: {},
        headClass: 'text-white bg-dark',
        onClose : (e) => {},
        removeOnClose: true,
        text: 'Question',
        title: 'Topic',

    }, params);

    // console.log( options);

    if ( options.removeOnClose) {
        dlg.on('hidden.bs.modal', function(e) {
            // console.log( 'hidden.bs.modal', this);
            $(this).remove();

        });

    }

    $('.modal-header', dlg).addClass(options.headClass);
    dlg.appendTo( 'body');
    options.beforeOpen.call( dlg);
    dlg.modal('show');

    return dlg;	// a promise

};
