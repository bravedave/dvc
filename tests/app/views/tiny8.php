<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/
?>

<div class="dcs mb-2">

  <textarea class="form-control js-textarea" style="height: calc(100vh - 6.5rem)" name="textarea"
    id="<?= $_editor = strings::rand() ?>"></textarea>
</div>
<script>
  (_ => {
    _.tiny8().then(tinymce => {

      tinymce.init({
        selector: '#<?= $_editor ?>',
        license_key: 'gpl', // gpl for open source, T8LK:... for commercial
        skin: 'oxide',
        plugins: 'image lists link anchor charmap',
        toolbar: 'blocks | bold italic bullist numlist | link image charmap',
        content_style: 'body { font-family:Century Gothic,Helvetica,Arial,sans-serif; font-size:16px }',
        promotion: false,
        menubar: false
      });
    });
  })(_brayworth_);
</script>