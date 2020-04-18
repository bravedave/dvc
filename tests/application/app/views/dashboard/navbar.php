<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>
<nav class="navbar navbar-dark fixed-top bg-primary flex-md-nowrap p-0 shadow">
    <?php
    if ( isset( $this->data->pageUrl)) {
        printf( '<a class="navbar-brand col-sm-3 col-md-2 mr-0" href="%s">%s</a>',
            $this->data->pageUrl,
            $this->title

        );

    }
    else {
        printf( '<div class="navbar-brand col-sm-3 col-md-2 mr-0">%s</div>',
            $this->title

        );

    }   ?>
    <input type="text" class="form-control form-control-primary w-100" placeholder="Search...">
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="#">Logout</a>
        </li>
    </ul>

</nav>

