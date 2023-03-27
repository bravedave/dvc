<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * to use this
 * 1. in www/js add squire.js and purify.min.js
*/
?>

<form id="<?= $_form = strings::rand() ?>" class="h-100">
  <h1>Toast Editor Demo</h1>

  <div id="<?= $_uidEditor = strings::rand() ?>">
</form>


<script>
  (_ => {

    const form = $('#<?= $_form ?>');

    const editor = new toastui.Editor({
      el: document.querySelector('#<?= $_uidEditor ?>'),
      initialEditType: 'wysiwyg',
      height: '500px',
      usageStatistics: false,
      hideModeSwitch: false
    });

    // editor.getMarkdown();

    // editor.focus();
  })(_brayworth_);
</script>