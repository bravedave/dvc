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
    _.tiny6().then(() => {

      // plugins: 'paste textcolor table colorpicker autolink lists link',
      // external_plugins : {}
      // selector: 'textarea#<?= $_uid ?>'
      if ('5' == tinyMCE.majorVersion) {
        tinymce.init({
          browser_spellcheck: true,
          mode: 'none',
          relative_urls: false,
          remove_script_host: false,
          toolbar_items_size: 'small',
          branding: false,
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
          statusbar: false,
          paste_data_images: true,
          plugins: 'table autolink lists advlist imagetools link',
          toolbar: 'undo redo | bold italic bullist numlist outdent indent blockquote table link | styles fontfamily fontsize | forecolor backcolor',
        });

        tinymce.execCommand('mceAddEditor', false, '<?= $_uid ?>');

      } else {
        tinymce.init({
          browser_spellcheck: true,
          mode: 'none',
          relative_urls: false,
          remove_script_host: false,
          toolbar_items_size: 'small',
        });

        tinymce.execCommand('mceAddEditor', false, {
          id: '<?= $_uid ?>',
          options: {
            branding: false,
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
            statusbar: false,
            paste_data_images: true,
            plugins: 'table autolink lists advlist link',
            toolbar: 'undo redo | bold italic bullist numlist outdent indent blockquote table link | styles fontfamily fontsize | forecolor backcolor',

          }
        });

      }
      console.log('inited');

    });
  })(_brayworth_);
</script>
