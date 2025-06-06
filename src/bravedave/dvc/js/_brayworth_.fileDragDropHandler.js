/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Apply drag drop capabilities to a container
 *
 *	test:
    const c = _brayworth_.fileDragDropContainer().appendTo('body');	// or where ever you want to append to;
    maybe:
      const c = _brayworth_.fileDragDropContainer().appendTo('body');
      _brayworth_.fileDragDropHandler.call( c, {
        url : url
      });
 */
(_ => {
  _.fileDragDropContainer = params => {

    const options = {
      ...{
        accept: '',
        fileControl: false,
        multiple: true,
        title: 'Choose file'
      }, ...params
    };

    //~ console.log( '_.fileDragDropContainer');
    const c = $(`<div class="d-print-none" data-accept="${options.accept}">
        <div class="progress box__uploading">
          <div class="progress-bar progress-bar-striped box__fill" role="progressbar"
            style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="progress d-none mt-2">
          <div class="progress-bar progress-bar-striped progress-queue text-center"
            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
            aria-valuemax="100">queue</div>
        </div>
      </div>`);

    if (options.fileControl) {

      const wrapper = $(
        `<div class="pointer btn btn-outline-secondary d-block btn-sm upload-btn-wrapper"
          style="position: relative; overflow: hidden;">
          <i class="bi bi-${_.browser.isMobileDevice ? 'camera-fill' : 'upload'}"></i>
        </div>`);

      const fileControl = $(`<input type="file"
        style="width: 100%; position: absolute; left: 0; top: 0; opacity: 0">`)
        .appendTo(wrapper);

      if (!!options.multiple) fileControl.prop('multiple', true);
      if ('' != options.accept) fileControl.attr('accept', options.accept);

      wrapper.appendTo(c);
    }

    return c;
  };

  const acceptable = (file, accepting) => {

    if (accepting.length > 0) {

      let type = file.type;
      if ('' == type && /\.heic$/i.test(file.name)) {

        type = 'image/heic';
      } else if ('' == type && /\.csv$/i.test(file.name)) {

        type = 'text/csv';
      }
      return accepting.indexOf(type) > -1;
    } else {

      return true;
    }
  };

  let queue = [];
  const enqueue = params => {

    const options = {
      ...{
        postData: {},
        droppedFiles: {},
        batchSize: 10,
        accept: '',
        onReject: d => _.growl(d)
      }, ...params
    };

    // console.log( options.accept)

    return new Promise((resolve, reject) => {
      /*
      * create forms with {options.batchSize} elements
      */

      let data = new FormData();
      for (let o in options.postData) data.append(o, options.postData[o]);

      // console.table(options);
      const accepting = '' != options.accept ? String(options.accept).split(',') : [];
      let fileCount = 0;
      $.each(options.droppedFiles, (i, file) => {

        if (acceptable(file, accepting)) {

          fileCount++;
          // console.log( file);
          if (fileCount > 0 && fileCount % options.batchSize == 0) {

            queue.push(data);
            data = new FormData();
            for (let o in options.postData) data.append(o, options.postData[o]);
          }

          data.append('files-' + i, file);
        } else {

          options.onReject({
            response: 'nak',
            description: 'not accepting ' + file.type,
            file: file
          });
        }
      });

      if (fileCount > 0) queue.push(data);

      const progressQue = $('.progress-queue', options.host);
      if (queue.length > 0) {

        progressQue
          .data('items', queue.length)
          .css('width', '0')
          .attr('aria-valuenow', '0');

        progressQue.parent().removeClass('d-none');
      }

      //~ console.log( queue.length)
      const queueHandler = () => {

        if (queue.length > 0) {

          const data = queue.shift();
          const p = (progressQue.data('items') - queue.length) / progressQue.data('items') * 100;
          //~ console.log( 'queue', p)
          progressQue
            .css('width', p + '%')
            .attr('aria-valuenow', p);

          //~ console.log( data, queue.length)
          sendData(data, options).then(queueHandler);
        } else {

          progressQue.parent().addClass('d-none');
          resolve();
        }
      };

      queueHandler();
    }).catch(msg => console.warn(msg));
  };

  const sendData = (formData, params) => {

    const options = {
      ...{
        url: false,
        onError: _.growl,
        onUpload: response => true,
        host: $('body'),
      }, ...params
    };

    return new Promise((resolve, reject) => {

      // this is a form

      const progressBar = $('.box__fill', options.host);
      progressBar
        .css('width', '0')
        .attr('aria-valuenow', '0');

      // console.log( options.host);
      options.host.addClass('is-uploading');

      $.ajax({
        url: options.url,
        type: 'POST',
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        xhr: () => {

          const xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener("progress", e => {

            if (e.lengthComputable) {

              progressBar
                .css('width', (e.loaded / e.total * 100) + '%')
                .attr('aria-valuenow', (e.loaded / e.total * 100));
            }
          });

          return xhr;
        }
      })
        .done(d => {

          if ('ack' == d.response) {

            if (!!d.data && 'string' != typeof d.data) {

              $.each(d.data, (i, j) => _.growl(j));
            } else {

              _.growl(d);
            }
          } else {

            options.onError(d);
          }

          options.onUpload(d);
          resolve();
        })
        .always(() => options.host.removeClass('is-uploading'))
        .fail(r => {

          console.warn(r);
          _.ask.alert({
            title: 'Upload Error',
            text: 'there was an error<br>consider reloading your browser'
          });
        });
    }).catch(msg => console.warn(msg));
  };

  const uploader = params => new Promise((resolve) => {

    const options = {
      ...{
        postData: {},
        droppedFiles: {},
        accept: '',
        onReject: _.growl,
      }, ...params
    };

    const data = new FormData();
    for (let o in options.postData) { data.append(o, options.postData[o]); }

    const accepting = '' != options.accept ? String(options.accept).split(',') : [];
    let fileCount = 0;
    $.each(options.droppedFiles, (i, file) => {

      if (acceptable(file, accepting)) {

        fileCount++;
        data.append('files-' + i, file);
      } else {

        options.onReject({
          response: 'nak',
          description: 'not accepting ' + file.type,
          file: file
        });
      }
    });

    if (fileCount > 0) sendData(data, options);
    resolve();
  }).catch(msg => console.warn(msg));

  _.fileDragDropHandler = function (params) {

    const _el = $(this);
    const _data = _el.data();

    const options = {
      ...{
        url: false,
        queue: false,
        host: _el,
        accept: _data.accept
      }, ...params
    };

    if (!options.url)
      throw 'Invalid upload url';

    $('input[type="file"]', this).on('change', function (e) {

      const _me = $(this);
      options.droppedFiles = e.originalEvent.target.files;
      if (options.droppedFiles) {

        _me.prop('disabled', true);
        if (options.queue) {

          enqueue(options).then(() => _me.val('').prop('disabled', false));
        } else {

          uploader(options).then(() => _me.val('').prop('disabled', false));
        }
      }
    });

    const isAdvancedUpload = (() => {
      const div = document.createElement('div');
      return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    })();

    if (isAdvancedUpload && !options.host.hasClass('has-advanced-upload')) {

      //~ console.log( 'setup has-advanced-upload');
      options.host
        .addClass('has-advanced-upload')
        .on('drag dragstart dragend dragover dragenter dragleave drop', e => {
          e.preventDefault(); e.stopPropagation();
        })
        .on('dragover dragenter', function () { $(this).addClass('is-dragover'); })
        .on('dragleave dragend drop', function () { $(this).removeClass('is-dragover'); })
        .on('drop', e => {

          e.preventDefault();
          options.droppedFiles = e.originalEvent.dataTransfer.files;
          if (options.droppedFiles) {

            options.queue ? enqueue(options) : uploader(options);
          }
        });
    }
  };
})(_brayworth_);
