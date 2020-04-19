<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-primary flex-md-nowrap p-0 shadow">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>

    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php
            if ( isset( $this->data->pageUrl)) {
                printf( '<li class="nav-item active">
                        <a class="nav-link" href="%s">%s <span class="sr-only">(reload)</span></a>
                    </li>',
                    $this->data->pageUrl,
                    $this->title

                );

            }
            else {
                printf( '<li class="nav-item active">
                        <a class="nav-link" href="#">%s <span class="sr-only">(current)</span></a>
                    </li>',
                    $this->title

                );


            }   ?>

            <li class="nav-item text-nowrap">
                <a class="nav-link" href="#">Logout</a>

            </li>

        </ul>

    </div>

    <form class="form-inline">
        <input type="text" class="form-control form-control-primary" placeholder="Search..." />

    </form>

</nav>

