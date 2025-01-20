<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\views\docs;  ?>
<script>
  (_ => {

    const loadEditor = new Promise((resolve, reject) => {

      if (!!window.toastui) {

        resolve(toastui);
      } else {

        $('head').append(`<link rel="stylesheet" href="${_.url('assets/toastui/css')}">`);
        _.get.script(_.url("assets/toastui/js"))
          .then(() => resolve(toastui));
      }
    });

    _.ready(() => {

      const h = $('main > .markdown-body > h1');
      if (h.length > 0) {

        const btn = $(`<button type="button" class="btn btn-sm d-print-none float-end">
          <i class="bi bi-pencil"></i></button>`);

        btn.on('click', e => {

          _.hideContexts(e); // stopPropagation and hide all contexts

          loadEditor.then(async toastui => {

            const data = await _.fetch.post(_.url('<?= $this->route ?>'), {
              action: '-get-doc-',
              file: '<?= $new ? 'new-file' : $file ?>'
            });

            if ('ack' == data.response) {

              const uid = _.randomString();
              const form = $(`<form id="${uid}" class="h-100" method="post">
                <input type="hidden" name="action" value="-save-doc-">
                <input type="hidden" name="file" value="<?= $file ?>">
                <input type="hidden" name="contents">
                <div id="${uid}-editor"></div>
              </form>`);

              const btnSave = document.createElement('button');

              btnSave.className = 'toastui-editor-toolbar-icons last';
              btnSave.type = 'submit';
              btnSave.form = uid;
              btnSave.style.backgroundImage = 'none';
              btnSave.style.margin = '0';
              btnSave.innerHTML = `Save`;

              $('main > .markdown-body')
                .empty()
                .removeClass('markdown-body')
                .append(form);

              const editor = new toastui.Editor({
                el: document.querySelector(`#${uid}-editor`),
                initialEditType: 'wysiwyg',
                height: (window.innerHeight - 90) + 'px',
                usageStatistics: false,
                hideModeSwitch: false,
                toolbarItems: [
                  ['heading', 'bold', 'italic', 'strike'],
                  ['hr', 'quote'],
                  ['ul', 'ol', 'task', 'indent', 'outdent'],
                  ['table', 'image', 'link'],
                  [{
                    el: btnSave,
                    command: 'save',
                    tooltip: 'Save Document'
                  }]
                ]
              });

              editor.setMarkdown(data.data);
              editor.moveCursorToStart(true);

              form.on('submit', function(e) {

                e.stopPropagation();
                e.preventDefault();

                form.find('input[name="contents"]').val(editor.getMarkdown());

                _.fetch.post.form(_.url('<?= $this->route ?>'), this)
                  .then(d => ('ack' == d.response) ? location.reload() : _.growl(d));

                return false;
              });
            } else {

              _.growl(data);
            }
          });
        });

        btn.insertAfter(h);
      }
    });
  })(_brayworth_);
</script>