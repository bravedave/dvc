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
  <h1>Squire Editor Demo</h1>

  <p>Squire is a rich text editor primarily built for email apps. It’s designed to be
    integrated with your own UI framework, and so does not provide its own UI toolbar,
    widgets or overlays. This is a really simple demo, with the most trivial of UI
    integrations, to show the raw component in action.
  </p>
  <p>
    <a href="https://github.com/fastmail/squire">Learn more and see the source on
      GitHub</a>.
  </p>
  <div class="row g-2">

    <div class="col mb-1 js-toolbar">

      <div class="btn-group btn-group-sm" role="group" aria-label="text style">

        <button type="button" class="btn btn-outline-primary fw-bold"
          data-bs-toggle="button" data-editor-action="bold" title="bold">B</button>

        <button type="button" class="btn btn-outline-primary fst-italic"
          data-bs-toggle="button" data-editor-action="italic" title="italic">I</button>

        <button type="button" class="btn btn-outline-primary text-decoration-underline"
          data-bs-toggle="button" data-editor-action="underline"
          title="underline">U</button>
      </div>

      <div class="btn-group btn-group-sm" role="group" aria-label="undo/redo">
        <button type="button" class="btn btn-outline-primary" data-editor-action="undo"
          title="undo">
          <i class="bi bi-arrow-counterclockwise"></i>
        </button>

        <button type="button" class="btn btn-outline-primary" data-editor-action="redo"
          title="redo">
          <i class="bi bi-arrow-clockwise"></i>
        </button>
      </div>

      <div class="btn-group btn-group-sm" role="group" aria-label="font">
        <button type="button" class="btn btn-outline-primary user-select-none"
          data-editor-action="fontSize-plus" title="undo">
          <i class="bi bi-plus"></i>
        </button>
      </div>

      <?php if (false) {  ?>
        <p>
          <span id="" class="prompt">Font size</span>
          <span id="setFontFace" class="prompt">Font face</span>
        </p>

        <p>
          <span id="setTextColor" class="prompt">Text color</span>
          <span id="setHighlightColor" class="prompt">Text highlight</span>
          <span id="makeLink" class="prompt">Link</span>
        </p>
        <p>
          <span id="removeAllFormatting">Remove formatting</span>
        </p>
        <p>
          <span id="increaseQuoteLevel">Quote</span>
          <span id="decreaseQuoteLevel">Dequote</span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span id="makeUnorderedList">List</span>
          <span id="removeList">Unlist</span>
          <span id="increaseListLevel">Increase list level</span>
          <span id="decreaseListLevel">Decrease list level</span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span id="code">Code</span>
          <span id="removeCode">Uncode</span>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <span id="insertImage" class="prompt">Insert image</span>
          <span id="setHTML" class="prompt">Set HTML</span>
        </p>
      <?php }  ?>
    </div>
  </div>

  <div class="row g-2">

    <div class="col">

      <div id="<?= $_uidEditor = strings::rand()  ?>" class="border p-2" style="height: 300px;">
      </div>
    </div>
  </div>

</form>


<script type="module" charset="utf-8">
  (_ => {

    const form = $('#<?= $_form ?>');

    let want = [
      'color',
      'backgroundColor',
      'fontFamily',
      'fontSize',
      'lineHeight'
    ];

    let div = _('#<?= $_uidEditor ?>');
    let s = getComputedStyle(_('#<?= $_uidEditor ?>'));
    for (let key in s) {

      let prop = key.replace(/\-([a-z])/g, v => v[1].toUpperCase());
      if (want.indexOf(prop) < 0) continue;

      div.style[prop] = s[key];
      // el.style[prop] = s[key];
    }

    let editor = new Squire(_('#<?= $_uidEditor ?>'), {
      blockTag: 'div',
      blockAttributes: {
        'class': 'paragraph'
      },
      tagAttributes: {
        ul: {
          'class': 'UL'
        },
        ol: {
          'class': 'OL'
        },
        li: {
          'class': 'listItem'
        },
        a: {
          'target': '_blank'
        },
        pre: {
          style: 'border-radius:3px;border:1px solid #ccc;padding:7px 10px;background:#f6f6f6;font-family:menlo,consolas,monospace;font-size:90%;white-space:pre-wrap;word-wrap:break-word;overflow-wrap:break-word;'
        },
        code: {
          style: 'border-radius:3px;border:1px solid #ccc;padding:1px 3px;background:#f6f6f6;font-family:menlo,consolas,monospace;font-size:90%;'
        },
      }
    });
    let btnBold = form.find('button[data-editor-action="bold"]');

    editor.addEventListener('cursor', function(e) {
      if (editor.hasFormat('B')) {
        btnBold.addClass('active');
      } else {
        btnBold.removeClass('active');
      }

    });

    editor.addEventListener('input', function(e) {
      let html = editor.getHTML();
      sessionStorage.setItem('squire', html);
    });

    editor.addEventListener('pasteImage', function(event) {
      const items = [...event.detail.clipboardData.items];
      const imageItems = items.filter((item) => /image/.test(item.type));

      if (!imageItems.length) return false;

      let reader = new FileReader();

      reader.onload = loadEvent => this.insertImage(loadEvent.target.result);
      reader.readAsDataURL(imageItems[0].getAsFile());
    });

    form.find('.js-toolbar button')
      .on('click', function(e) {

        e.stopPropagation();

        let action = this.dataset.editorAction;
        let value = '';

        if (!this.classList.contains('active')) {

          switch (action) {
            case 'bold':

              action = 'removeBold';
              break;

            case 'fontSize-plus':

              let fi = editor.getFontInfo();

              if (!!fi.fontSize) {

                let fs = Number(fi.fontSize.replace(/[^0-9]/g, '')) + 1;
                action = 'setFontSize';
                value = `${fs}px`;
              } else {
                action = 'focus';

              }
              break;

            case 'italic':

              action = 'removeItalic';
              break;

            case 'underline':

              action = 'removeUnderline';
              break;

            default:
              break;
          }
        }

        if (!!action && editor[action]) editor[action](value);
      })

    let html = sessionStorage.getItem('squire');
    if (!!html) {

      editor.setHTML(html);
    }

    editor.focus();
  })(_brayworth_);


  // document.addEventListener('click', function(e) {
  //   var id = e.target.id,
  //     value;
  //   if (id && editor && editor[id]) {
  //     if (e.target.className === 'prompt') {
  //       value = prompt('Value:');
  //     }
  //     editor[id](value);
  //   }
  // }, false);
</script>