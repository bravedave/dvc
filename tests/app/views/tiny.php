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

<div class="form-row">
  <div class="col">
    <textarea class="form-control js-textarea" name="textarea" id="<?= $_uid = strings::rand() ?>"></textarea>
  </div>
</div>
<script>
  (_ => {
    _.tiny()
      .then(() => {

        tinymce.init({
          branding: false,
          browser_spellcheck: true,
          deprecation_warnings: false,
          font_family_formats: "Andale Mono=andale mono,times;" +
            "Arial=arial,helvetica,sans-serif;" +
            "Arial Black=arial black,avant garde;" +
            "Century Gothic=century gothic,arial,helvetica,sans-serif;" +
            "Comic Sans MS=comic sans ms,sans-serif;" +
            "Courier New=courier new,courier;" +
            "Helvetica=helvetica;" +
            "Impact=impact,chicago;" +
            "Symbol=symbol;" +
            "Tahoma=tahoma,arial,helvetica,sans-serif;" +
            "Terminal=terminal,monaco;" +
            "Times New Roman=times new roman,times;" +
            "Trebuchet MS=trebuchet ms,geneva;" +
            "Verdana=verdana,geneva;" +
            "Webdings=webdings;" +
            "Wingdings=wingdings,zapf dingbats",
          menubar: false,
          mode: 'none',
          paste_data_images: true,
          plugins: 'table autolink lists advlist image imagetools link',
          relative_urls: false,
          remove_script_host: false,
          statusbar: false,
          toolbar: 'undo redo | bold italic bullist numlist outdent indent blockquote table link | styles fontfamily fontsize | forecolor backcolor',
          toolbar_items_size: 'small',
        });

        tinymce.execCommand('mceAddEditor', false, '<?= $_uid ?>');
        console.log('inited');
      });
  })(_brayworth_);
</script>