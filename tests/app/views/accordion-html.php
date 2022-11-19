<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div class="accordion" id="<?= $_uidAccordion = strings::rand() ?>">

  <div id="<?= $_uidAccordion ?>-feed" class="collapse fade show" data-parent="#<?= $_uidAccordion ?>">

    <div class="alert alert-info">bootstrap version : <?= config::$BOOTSTRAP_VERSION ?></div>

    <button type="button" class="btn btn-primary js-btn-workbench">workbench</button>
  </div>

  <div id="<?= $_uidAccordion ?>-workbench" class="collapse fade" data-parent="#<?= $_uidAccordion ?>">
  </div>
</div>
<script>
  (_ => {
    const feed = $('#<?= $_uidAccordion ?>-feed');
    const workbench = $('#<?= $_uidAccordion ?>-workbench');

    const accordionNav = () => {
      let ul = $('<ul class="nav border-bottom"></ul>');
      let close = $('<a class="nav-link" data-toggle="collapse" href="#">x</a>')
        .on('click', function(e) {
          e.stopPropagation();
          e.preventDefault();

          feed.collapse('show');

        });

      $('<li class="nav-item ml-auto" data-role="close"></li>')
        .append(close)
        .appendTo(ul);

      ul.appendTo(workbench);

      return ul;
    };

    accordionNav();
    workbench.append('<div class="alert alert-success">welcome !</div>');

    feed
      .on('show.bs.collapse', e => {})
      .on('shown.bs.collapse', e => {});

    feed.find('.js-btn-workbench')
      .on('click', e => {
        e.stopPropagation();

        workbench.collapse('show');
      })
  })(_brayworth_);
</script>
