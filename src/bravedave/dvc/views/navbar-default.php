<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * compatibility : bootstrap 5
*/

use dvc\theme;

$title = $title ?? $this->title;
$aside = ($aside ?? true);  ?>

<nav class="<?= theme::navbar() ?> navbar-md-expand" id="<?= $_nav = strings::rand() ?>" role="navigation">

  <div class="container-fluid">

    <?php if ($aside) { ?>
      <button type="button" class="navbar-toggler js-show-aside">
        <i class="bi bi-three-dots-vertical"></i>
      </button>
    <?php } ?>

    <div class="navbar-brand"><?= $title ?></div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#<?= $_uid = strings::rand() ?>" aria-controls="<?= $_uid ?>"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="<?= $_uid ?>">

      <ul class="ms-auto navbar-nav">

        <li class="nav-item">

          <a class="nav-link" href="<?= strings::url() ?>">

            <i class="bi bi-house"></i>
            Home
          </a>
        </li>

        <li class="nav-item">

          <a class="nav-link" href="<?= strings::url('docs/') ?>">

            <i class="bi bi-file-text"></i>
            docs
          </a>
        </li>

        <li class="nav-item">

          <a class="nav-link" href="https://github.com/bravedave/">
            <i class="bi bi-github"></i>
            GitHub
          </a>
        </li>
      </ul>
    </div>
  </div>
  <?php if ($aside) { ?>

    <style>
      @media (max-width: 767px) {

        body:not(.show-aside) aside {
          display: none !important
        }

        body.show-aside main {
          display: none !important
        }
      }
    </style>
    <script>
      (_ => {
        const nav = $('#<?= $_nav ?>');

        nav.find('.js-show-aside')
          .on('click', function(e) {

            _.hideContexts(e);
            $(this).trigger('show');
          })
          .on('hide', function(e) {

            e.stopPropagation();
            document.body.classList.remove('show-aside');

            $(this).find('.bi')
              .removeClass('bi-three-dots')
              .addClass('bi-three-dots-vertical');
          })
          .on('show', function(e) {
            e.stopPropagation();

            if (document.body.classList.toggle('show-aside')) {

              $(this).find('.bi')
                .removeClass('bi-three-dots-vertical')
                .addClass('bi-three-dots');
            } else {

              $(this).find('.bi')
                .removeClass('bi-three-dots')
                .addClass('bi-three-dots-vertical');
            }
          });
      })(_brayworth_);
    </script>
  <?php } ?>
</nav>