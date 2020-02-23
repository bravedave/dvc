/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	Apply drag drop capabilities to a container

	load:
		$.getScript( _brayworth_.urlwrite('js/_brayworth_.fileDragDropHandler.js'));

	test:
		let c = _brayworth_.fileDragDropContainer().appendTo('body');	// or where ever you want to append to;
		maybe:
			let c = _brayworth_.fileDragDropContainer().appendTo('body');
			_brayworth_.fileDragDropHandler.call( c, {
				url : url
			});

	*/
/*jshint esversion: 6 */
(function( _b_ ) {
	_b_.fileDragDropContainer = function( params) {
		let options = $.extend({
			fileControl : false,
			multiple : true,
			title : 'Choose file'

		}, params);

		//~ console.log( '_b_.fileDragDropContainer');
		let c = $('<div />');

		$('<div class="progress-bar progress-bar-striped box__fill" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" />')
			.appendTo( $('<div class="progress box__uploading" />').appendTo( c));

		$('<div class="progress-bar progress-bar-striped progress-queue text-center" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">queue</div>')
			.appendTo( $('<div class="progress d-none mt-2" />').appendTo( c));

		if ( options.fileControl) {
			let ig = $('<div class="input-group mb-1" />').appendTo( c);

			let rand = String( Math.round( Math.random() * 1000));
			let lbl = $('<span class="input-group-text">Upload</span>').attr( 'id', rand + 'FileAddon01');
			$('<div class="input-group-prepend" />').append( lbl).appendTo( ig);

			let div = $('<div class="custom-file" />').appendTo( ig);
			let fileControl = $('<input type="file" class="custom-file-input" />');

			if ( !!options.multiple) {
				fileControl.prop('multiple', true);

			}

			fileControl
			.attr( 'id', rand + 'File01')
			.attr('aria-describedby', rand + 'FileAddon01')
			.appendTo( div);

			$('<label class="custom-file-label" />')
				.html( options.title)
				.attr( 'for', rand + 'File01')
				.appendTo( div);

		}

		return ( c);

	}
	;
	let queue = [];
	let enqueue = function( params) {
		let options = $.extend({
			postData : {},
			droppedFiles : {},

		}, params);

		return new Promise( function( resolve, reject) {
			/*
			* create forms with 10 elements
			*/

			let data = new FormData();
			for(let o in options.postData) {
				data.append( o, options.postData[o]);

			}

			$.each( options.droppedFiles, function(i, file) {
				if ( i > 0 && i % 10 == 0) {
					queue.push( data);

					data = new FormData();
					for(let o in options.postData) {
						data.append( o, options.postData[o]);

					}

				}

				data.append('files-'+i, file);

			});

			queue.push( data);

			let progressQue = $('.progress-queue', options.host);
			if ( queue.length > 0) {
				progressQue
					.data('items', queue.length)
					.css('width', '0')
					.attr('aria-valuenow', '0');

				progressQue.parent().removeClass( 'd-none');

			}

			//~ console.log( queue.length)
			let queueHandler = function() {
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
			//~ droppedFiles : {},
			//~ postData : {},
		let options = $.extend({
			url : false,
			onUpload : function( response) {},
			host : $('body'),

		}, params);

		let formData = this;
		// Display the key/value pairs
		//~ for (var pair of formData.entries()) {
		    //~ console.log(pair[0]+ ', ' + pair[1]);
		//~ }

		return new Promise( function( resolve, reject) {

			// this is a form
			let progressBar = $('.box__fill', options.host);
			progressBar
				.css('width', '0')
				.attr('aria-valuenow', '0');

			options.host.addClass('is-uploading');

			$.ajax({
				url: options.url,
				type: 'POST',
				data: formData,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				xhr: function() {
					let xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function (e) {
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
			.done( function( d) {
				if ( 'ack' == d.response) {
					$.each( d.data, function( i, j) {
						$('body').growl( j);

					});

				}
				else {
					$('body').growl( d);

				}

				options.onUpload( d);

				resolve();

			})
			.always( function( r) {
				options.host.removeClass('is-uploading');

			})
			.fail( function( r) {
				console.warn(r);
				_b_.modal({
					title : 'Upload Error',
					text : 'there was an error uploading the attachments<br />we recommend you reload your browser'
				});

			});

		});

	};

	let uploader = function( params) {
			//~ url : false,
			//~ onUpload : function( response) {},
			//~ host : false,
		let options = $.extend({
			postData : {},
			droppedFiles : {},

		}, params);

		let data = new FormData();
		for(let o in options.postData) { data.append( o, options.postData[o]); }

		$.each( options.droppedFiles, function(i, file) { data.append('files-'+i, file); });

		sendData.call( data, options);

	}
	;
	_b_.fileDragDropHandler = function( params) {
		let _el = $(this);

		let options = $.extend( {
			url : false,
			queue : false,
			host : _el

		}, params);

		if ( !options.url)
			throw 'Invalid upload url';

		$('input[type="file"]', this).on( 'change', function( e) {
			let _me = $(this);

			options.droppedFiles = e.originalEvent.target.files;
			if ( options.droppedFiles) {
				_me.prop( 'disabled', true);
				if (options.queue) {
					enqueue( options).then( function() {
						_me.val('').prop( 'disabled', false);

					});

				}
				else {
					uploader( options);

				}

			}

		});

		let isAdvancedUpload = (function() {
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

	}
	;

})( _brayworth_ );
