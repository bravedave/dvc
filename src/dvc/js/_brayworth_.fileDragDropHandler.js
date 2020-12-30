/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Apply drag drop capabilities to a container
 *
 *	load:
		$.getScript( _brayworth_.urlwrite('js/_brayworth_.fileDragDropHandler.js'));
 *
 *	test:
		let c = _brayworth_.fileDragDropContainer().appendTo('body');	// or where ever you want to append to;
		maybe:
			let c = _brayworth_.fileDragDropContainer().appendTo('body');
			_brayworth_.fileDragDropHandler.call( c, {
				url : url
			});
 */
/*jshint esversion: 6 */
(_ => {
	_.fileDragDropContainer = params => {
		let options = _.extend({
			fileControl : false,
			multiple : true,
			title : 'Choose file'

		}, params);

		//~ console.log( '_.fileDragDropContainer');
		let c = $('<div></div>');

		$('<div class="progress-bar progress-bar-striped box__fill" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>')
			.appendTo( $('<div class="progress box__uploading"></div>').appendTo( c));

		$('<div class="progress-bar progress-bar-striped progress-queue text-center" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">queue</div>')
			.appendTo( $('<div class="progress d-none mt-2"></div>').appendTo( c));

		if ( options.fileControl) {
			let ig = $('<div class="input-group mb-1"></div>').appendTo( c);

			let rand = String( Math.round( Math.random() * 1000));
			let lbl = $('<span class="input-group-text">Upload</span>').attr( 'id', rand + 'FileAddon01');
			$('<div class="input-group-prepend"></div>').append( lbl).appendTo( ig);

			let div = $('<div class="custom-file"></div>').appendTo( ig);
			let fileControl = $('<input type="file" class="custom-file-input">');

			if ( !!options.multiple) {
				fileControl.prop('multiple', true);

			}

			fileControl
			.attr( 'id', rand + 'File01')
			.attr('aria-describedby', rand + 'FileAddon01')
			.appendTo( div);

			$('<label class="custom-file-label"></label>')
				.html( options.title)
				.attr( 'for', rand + 'File01')
				.appendTo( div);

		}

		return ( c);

	};

	// new version
	_.fileDragDropContainer = params => {
		let options = _.extend({
			accept: '',
			fileControl: false,
			multiple: true,
			title: 'Choose file'

		}, params);

		//~ console.log( '_.fileDragDropContainer');
    let c = $('<div></div>');
    c.data( 'accept', options.accept);

		$('<div class="progress-bar progress-bar-striped box__fill" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>')
			.appendTo($('<div class="progress box__uploading"></div>').appendTo(c));

		$('<div class="progress-bar progress-bar-striped progress-queue text-center" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">queue</div>')
			.appendTo($('<div class="progress d-none mt-2"></div>').appendTo(c));

		if (options.fileControl) {

			let wrapper = $('<div class="pointer btn btn-outline-secondary d-block btn-sm upload-btn-wrapper">')
				.css({
					'position': 'relative',
					'overflow': 'hidden'

				});

			$('<i class="bi"></i>')
        .addClass(_.browser.isMobileDevice ? 'bi-camera-fill' : 'bi-upload')
				.appendTo(wrapper);

			let fileControl = $('<input type="file">')
				.css({
					'width': '100%',
					'position': 'absolute',
					'left': '0',
					'top': '0',
					'opacity': '0'

				})
				.appendTo(wrapper);

			if (!!options.multiple) {
				fileControl.prop('multiple', true);

      }

      if ( '' != options.accept) {
        fileControl.attr('accept', options.accept);

      }

			wrapper.appendTo(c);

		}

		return (c);

	};

  let acceptable = (file, accepting) => {
    if (accepting.length > 0) {
      return accepting.indexOf( file.type) > -1;

    }
    else {
      return true;

    }

  };

	let queue = [];
	let enqueue = params => {
		let options = _.extend({
			postData : {},
      droppedFiles : {},
      batchSize : 10,
      accept : ''

		}, params);

		return new Promise( resolve => {
			/*
			* create forms with {options.batchSize} elements
			*/

			let data = new FormData();
			for(let o in options.postData) data.append( o, options.postData[o]);

      // console.table(options);
      let accepting = '' != options.accept ? String( options.accept).split(',') : [];
      let fileCount = 0;
			$.each( options.droppedFiles, (i, file) => {
        if (acceptable(file, accepting)) {
          fileCount++;
          // console.log( file);

          if (fileCount > 0 && fileCount % options.batchSize == 0) {
            queue.push( data);

            data = new FormData();
            for(let o in options.postData) data.append( o, options.postData[o]);

          }

          data.append('files-'+i, file);

        }
        else {
          _.growl({
            response : 'nak',
            description : 'not accepting ' + file.type

          });

        }

			});

      if ( fileCount > 0) queue.push( data);

			let progressQue = $('.progress-queue', options.host);
			if ( queue.length > 0) {
				progressQue
					.data('items', queue.length)
					.css('width', '0')
					.attr('aria-valuenow', '0');

				progressQue.parent().removeClass( 'd-none');

			}

			//~ console.log( queue.length)
			let queueHandler = () => {
				if ( queue.length > 0) {
					let data = queue.shift();
					let p = ( progressQue.data('items') - queue.length) / progressQue.data('items') * 100;
					//~ console.log( 'queue', p)
					progressQue
						.css('width', p + '%')
						.attr('aria-valuenow', p);

					//~ console.log( data, queue.length)
					sendData.call( data, options).then( queueHandler);

				}
				else {
					progressQue.parent().addClass( 'd-none');
					resolve();

				}

			};

			queueHandler();

		});

	};

	let sendData = function( params) {
		let options = _.extend({
			url : false,
			onUpload : response => true,
			host : $('body'),

		}, params);

		let formData = this;

		return new Promise( function( resolve, reject) {

			// this is a form
			let progressBar = $('.box__fill', options.host);
			progressBar
				.css('width', '0')
				.attr('aria-valuenow', '0');

      console.log( options.host);
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
					let xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", e => {
						//~ if (e.lengthComputable)
							//~ $('.box__fill', options.host).css('width', ( e.loaded / e.total * 100) + '%');
						if (e.lengthComputable) {
							progressBar
								.css('width', ( e.loaded / e.total * 100) + '%')
								.attr('aria-valuenow', ( e.loaded / e.total * 100));

						}

					});

					return xhr;

				}

			})
			.done( d => {
				if ( 'ack' == d.response) {
					$.each( d.data, ( i, j) => _.growl( j));

				}
				else {
					_.growl( d);

				}

				options.onUpload( d);
				resolve();

			})
			.always( () => options.host.removeClass('is-uploading'))
			.fail( r => {
				console.warn(r);
				_.modal({
					title : 'Upload Error',
					text : 'there was an error uploading the attachments<br>we recommend you reload your browser'
				});

			});

		});

	};

	let uploader = params => {
    return new Promise( resolve => {
      let options = _.extend({
        postData : {},
        droppedFiles: {},
        accept: '',

      }, params);

      let data = new FormData();
      for(let o in options.postData) { data.append( o, options.postData[o]); }

      let accepting = '' != options.accept ? String(options.accept).split(',') : [];
      let fileCount = 0;
      $.each( options.droppedFiles, (i, file) => {
        if (acceptable(file,accepting)) {
          fileCount++;
          data.append('files-'+i, file);

        }
        else {
          _.growl({
            response: 'nak',
            description: 'not accepting ' + file.type

          });

        }

      });

      if (fileCount > 0) sendData.call( data, options);
      resolve();

    });

  };

	_.fileDragDropHandler = function( params) {
    let _el = $(this);
    let _data = _el.data();

		let options = _.extend( {
			url : false,
			queue : false,
      host : _el,
      accept : _data.accept

		}, params);

		if ( !options.url)
			throw 'Invalid upload url';

		$('input[type="file"]', this).on( 'change', function( e) {
			let _me = $(this);

			options.droppedFiles = e.originalEvent.target.files;
			if ( options.droppedFiles) {
				_me.prop( 'disabled', true);
				if (options.queue) {
          enqueue( options)
          .then( () => _me.val('').prop( 'disabled', false));

				}
				else {
					uploader( options).then( () => _me.val('').prop( 'disabled', false));

				}

			}

		});

		let isAdvancedUpload = (() => {
			let div = document.createElement('div');
			return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
		})();

		if ( isAdvancedUpload && !options.host.hasClass('has-advanced-upload')) {

			//~ console.log( 'setup has-advanced-upload');
			options.host
			.addClass('has-advanced-upload')
			.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
				e.preventDefault(); e.stopPropagation();
			})
			.on('dragover dragenter', function() { $(this).addClass('is-dragover'); })
			.on('dragleave dragend drop', function() { $(this).removeClass('is-dragover'); })
			.on('drop', function(e) {
				e.preventDefault();
        options.droppedFiles = e.originalEvent.dataTransfer.files;


				if ( options.droppedFiles) {
          if (options.queue) {
						enqueue( options);

					}
					else {
						uploader( options);

					}

				}

			});

		}	// if (isAdvancedUpload && !options.host.hasClass('has-advanced-upload'))

	};

})( _brayworth_ );
