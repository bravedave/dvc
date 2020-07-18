<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div id="<?= $_wrap = strings::rand() ?>">
    <form id="<?= $_form = strings::rand() ?>" autocomplete="off">
        <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white py-2">
                        <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="name" />

                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="email" />

                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="mobile" />

                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <hr />
    <div class="text-center">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#<?= $_modal ?>">Open Modal</button>
    </div>
    <script>
    $(document).ready( () => {

        $('#<?= $_modal ?>').on( 'hidden.bs.modal', e => { $('#<?= $_wrap ?>').remove(); });
        $('#<?= $_modal ?>').modal( 'show');

        $('#<?= $_form ?>')
        .on( 'submit', function( e) {
            let _form = $(this);
            let _data = _form.serializeFormJSON();
            let _modalBody = $('.modal-body', _form);

            // console.table( _data);

            return false;
        });
    });
    </script>
</div>
