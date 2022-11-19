<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<ul class="nav flex-column" id="<?= $_nav = strings::rand() ?>">
  <li class="nav-item"><a class="nav-link active" aria-current="page" href="#">Active</a></li>
  <li class="nav-item"><a class="nav-link" href="#">Link</a></li>
  <li class="nav-item"><a class="nav-link js-alert" href="#">Alert</a></li>
  <li class="nav-item"><a class="nav-link js-toast" href="#">Toast</a></li>
  <li class="nav-item"><a class="nav-link js-toast" href="<?= strings::url($this->route . '/accordion') ?>">Accordion</a></li>
  <li class="nav-item"><a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a></li>
</ul>

<h6 class="mt-2">Text Colors</h6>
<ul>
  <li class="text-primary">.text-primary</li>
  <li class="text-secondary">.text-secondary</li>
  <li class="text-success">.text-success</li>
  <li class="text-danger">.text-danger</li>
  <li class="text-warning">.text-warning</li>
  <li class="text-info">.text-info</li>
</ul>

<div class="row">
  <div class="col bg-primary">&nbsp;</div>
  <div class="col bg-secondary">&nbsp;</div>
  <div class="col bg-success">&nbsp;</div>
  <div class="col bg-lite">&nbsp;</div>
</div>

<div class="row">
  <div class="col bg-lite">&nbsp;</div>
  <div class="col bg-danger">&nbsp;</div>
  <div class="col bg-warning">&nbsp;</div>
  <div class="col bg-info">&nbsp;</div>
</div>
<script>
  (_ => {
    const nav = $('#<?= $_nav ?>');

    nav.find('.js-alert').on('click', function(e) {
      e.stopPropagation();
      e.preventDefault();

      _.ask.alert({
        text: 'how you doin ?',
        buttons: {
          'ok': function(e) {
            this.modal('hide')
          }
        }
      })
    });

    nav.find('.js-toast').on('click', function(e) {
      e.stopPropagation();
      e.preventDefault();

      _.growl('how you doin ?');
      _.growl({
        response: 'nak',
        description: 'how you doin ?'
      });
      _.growl({
        response: 'ack',
        description: 'great !!'
      });

    });

  })(_brayworth_);
</script>
