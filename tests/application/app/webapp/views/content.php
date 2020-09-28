<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
*/  ?>

<div class="p-2 p-md-3 m-md-3 text-center bg-light">
  <div class="col-md-5 p-lg-5 mx-auto my-3">
    <h1 class="display-4 font-weight-normal">Simple WebApp</h1>
    <p class="lead font-weight-normal">
      A simple Web-App Framework
    </p>

    <a class="btn btn-outline-secondary" href="#"
      wapp-role="navigation"
      data-target="main"
      data-url="<?= strings::url( $this->route . '/content2') ?>">Coming soon</a>

  </div>

</div>
