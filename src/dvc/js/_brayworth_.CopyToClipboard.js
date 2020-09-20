/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

( _ => {
  _.CopyToClipboard = el => {
    return new Promise( ( resolve, reject) => {
      if (document.createRange && window.getSelection) {
        let range = document.createRange();
        let sel = window.getSelection();
        sel.removeAllRanges();

        try {
          range.selectNodeContents(el);
          sel.addRange(range);
          if (document.execCommand('copy')) {
            _.growlSuccess('Content Copied to Clipboard');
            window.getSelection().removeAllRanges();
            resolve( el);

          }
          else {
            _.growlError('Error: Copying to Clipboard');
            reject(el);

          }

        }
        catch (e) {
          range.selectNode(el);
          sel.addRange(range);
          _.growlError('Error: Copy to Clipboard');
          reject(el);

        }

      }
      else {
        let body = document.body;
        if (body.createTextRange) {
          let range = body.createTextRange();
          range.moveToElementText(el);
          range.select();

          resolve(el);

        }
        else {
          reject(el);

        }

      }

    });

  };

}) (_brayworth_);
