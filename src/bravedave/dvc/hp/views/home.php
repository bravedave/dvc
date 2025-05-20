<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\hp;

use bravedave\dvc\strings;

?>

<div class="container py-5">
  <div class="row">
    <div class="col-4 col-sm-3 col-xl-3 mb-4">
      <img src="<?= strings::url($this->route . '/images/thoughtful.png') ?>"
        alt="David at workbench" class="img-fluid rounded shadow">
    </div>
    <div class="col-sm">
      <div>
        <h2 class="mb-4">Built on My Own Terms</h2>
        <p>
          Most of my work runs on DVC - a framework I crafted
          over years to solve problems my way.</p>

        <p>It's not about reinventing the wheel.
          It's about understanding the wheel: how it turns, why it sticks,
          and how to make it roll smoother.
          DVC isn't packed with buzzwords or bloated abstractions.
          It's lean, predictable, and ruthlessly practical - the kind of
          tool that disappears into the work until you need it.
        </p>

        <p>When something has to work today and tomorrow, this is what I trust.</p>

        <p>Dive into the <a href="<?= strings::url('docs') ?>">DVC docs</a>
          (if you're the kind of person who reads the blueprints).</p>
      </div>
    </div>
  </div>
</div>