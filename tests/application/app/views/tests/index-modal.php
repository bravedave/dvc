<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

$m = new dvc\html\modal;

$m->title()->setContent( 'Hello World');

/* add/remove classes */
$m->header()->addClass( 'py-2 bg-primary text-light');
$m->footer()->addClass( 'py-2');
$m->dialog()->removeClass( 'modal-dialog-centered');

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

<button class="btn btn-primary" data-toggle="modal" data-target="#<?= $m->id ?>">modal</button>

<script>
$(document).ready( function() {
    $('#<?= $form->id ?>').on( 'submit', function( e) {
        let _form = $(this);
        let _data = _form.serializeFormJSON();

        $('#<?= $m->id ?>').modal( 'hide');

        console.log( _data);

        return false;

    });

});
</script>
