<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<nav class="nav flex-column" id="<?= $_nav = strings::rand() ?>">

  <h6 class="mt-2">Tutorial</h6>
  <a class="nav-link" href="<?= strings::url('docs/risorsa') ?>">Risorsa</a>

  <h6 class="mt-2">Nav</h6>
  <a class="nav-link active" aria-current="page" href="#">Active</a>
  <a class="nav-link" href="#">Link</a>
  <a class="nav-link js-alert" href="#">Alert</a>
  <a class="nav-link js-prompt" href="#">Prompt</a>
  <a class="nav-link js-toast" href="#">Toast</a>
  <a class="nav-link" href="<?= strings::url($this->route . '/accordion') ?>">Accordion</a>
  <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
</nav>

<h6 class="mt-2">Text Colors</h6>
<ul>
  <li class="text-primary">.text-primary</li>
  <li class="text-secondary">.text-secondary</li>
  <li class="text-success">.text-success</li>
  <li class="text-danger">.text-danger</li>
  <li class="text-warning">.text-warning</li>
  <li class="text-info">.text-info</li>
</ul>

<h6 class="mt-2">Background colors</h6>
<div class="container border">
  <div class="row">
    <div class="col bg-primary">&nbsp;</div>
    <div class="col bg-secondary">&nbsp;</div>
    <div class="col bg-success">&nbsp;</div>
    <div class="col bg-lite">&nbsp;</div>
    <div class="col bg-danger">&nbsp;</div>
    <div class="col bg-warning">&nbsp;</div>
    <div class="col bg-info">&nbsp;</div>
  </div>
</div>

<p class="mt-4 fst-italic">
  constructed using bootstrap, jquery and many other open source technologies
</p>

<script>
  (_ => {
    const nav = $('#<?= $_nav ?>');

    nav.find('.js-alert').on('click', e => {

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

    nav.find('.js-prompt').on('click', e => {

      e.preventDefault();
      _.textPrompt({
        text: 'how you doin ?',
      }).then(console.log);
    });

    nav.find('.js-toast').on('click', e => {

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