<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div class="left-sidebar">
    <ul class="nav flex-column sidebar-nav">
        <li class="nav-item">
            <a class="nav-link" href="<?= strings::url('hello') ?>#">
                <i class="fa fa-angle-right"></i>
                Hello World

            </a>

        </li>

        <li class="nav-item">
            <a class="nav-link" href="#" id="<?= $uid = strings::rand() ?>">
                <i class="fa fa-angle-right"></i>
                Modal Dialog

            </a>

        </li>
        <script>
        $(document).ready( () => {
            $('#<?= $uid ?>').on( 'click', function( e) {
                e.stopPropagation();e.preventDefault();

                _brayworth_.modal({
                    title : 'fred',
                    text : 'hey jude'

                });

                $('main').load( _brayworth_.url('<?= $this->route ?>/doc/modal'));

            });

        });
        </script>

        <li class="nav-item">
            <a class="nav-link" href="#" id="<?= $uid = strings::rand() ?>">
                <i class="fa fa-angle-right"></i>
                Ask a Question

            </a>

        </li>
        <script>
        $(document).ready( () => {
            $('#<?= $uid ?>').on( 'click', function( e) {
                e.stopPropagation();e.preventDefault();

                _brayworth_.ask({
                    headClass: 'text-white bg-danger',
                    title : 'This is Red',
                    text : 'Do you agree ?',
                    buttons : {
                        yes : function() {
                            $(this).modal('hide');
                            console.log( 'ok', this);

                        }

                    }

                });

                $('main').load( _brayworth_.url('<?= $this->route ?>/doc/modal-ask'));

            });

        });
        </script>

        <li class="nav-item">
            <a class="nav-link" href="<?= strings::url('docs') ?>">
                <i class="fa fa-angle-right"></i>
                Docs

            </a>

        </li>

        <li class="nav-item">
            <a class="nav-link" href="#" id="<?= $uid = strings::rand() ?>">
                <i class="fa fa-angle-right"></i>
                Changes

            </a>

        </li>
        <script>
        $(document).ready( () => {
            $('#<?= $uid ?>').on( 'click', function( e) {
                e.stopPropagation();e.preventDefault();
                $('main').load( _brayworth_.url('<?= $this->route ?>/doc/changes'));

            });

        });
        </script>

    </ul>

</div>
