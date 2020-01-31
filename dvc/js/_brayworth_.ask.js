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

_brayworth_.ask = function ( params) {
    let options = $.extend({
        text: 'Question',
        title: 'Topic',
        beforeOpen: function () {
            let modal = this;
            $('.modal-dialog', modal).removeClass('modal-lg').addClass('modal-sm');
            $('.modal-title', modal).html(options.title);
            $('.modal-body', modal).append(options.text);

            let footer = $('<div class="modal-footer" />').appendTo($('.modal-content', modal));

            $.each(options.buttons, function (key, j) {
                $('<button class="btn btn-light" />')
                    .html(key)
                    .appendTo(footer)
                    .on('click', function (e) {
                        j.call(modal, e);

                    });

            });

        },
        buttons: {}

    }, params);

    return _cms_.loadModal(options);	// a promise

};
