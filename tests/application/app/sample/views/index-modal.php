<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use dvc\theme;

$m = new dvc\html\modal;

$m->title()->setContent( 'Hello World');

/* add/remove classes */
$m->header()->addClass( theme::modalHeader());  // 'py-2 bg-primary text-light');
// $m->footer()->addClass( 'py-2');
// $m->dialog()->removeClass( 'modal-dialog-centered');

/* add elements */
$m->body()->append( 'input', null, [
    'class' => 'form-control',
    'placeholder' => 'hello world' ]);

$action = $m->footer()->append( 'button', 'close', [
    'class' => 'btn btn-secondary',
    'type' => 'button',
    'data-toggle' => 'modal',
    'data-target' => '#' . $m->id ]);

$action = $m->footer()->append( 'button', 'save', [
    'class' => 'btn btn-primary',
    'type' => 'submit' ]);

/* wrap in a form */
$form = new dvc\html\form;
$form->append( 'input', null, [
    'type' => 'hidden',
    'name' => 'action',
    'value' => 'lets-do-this'
]);

$form->appendChild( $m);

$form->render();   ?>

<ul class="nav flex-column">
	<li class="nav-item text-center border-top mt-4 pt-2">
    <button class="btn btn-outline-primary"
      data-toggle="modal"
      data-target="#<?= $m->id ?>">modal</button>

  </li>

</ul>

<script>
$(document).ready( () => {
  $('#<?= $form->id ?>').on( 'submit', function( e) {
    let _form = $(this);
    let _data = _form.serializeFormJSON();

    $('#<?= $m->id ?>').modal( 'hide');

    console.log( _data);

    return false;

  });

});
</script>
