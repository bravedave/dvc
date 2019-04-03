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
(function( _b_ ) {
	_b_.fileDragDropContainer = function() {
		let c = $('<div />');

		//~ let _c = $('<div class="box__uploading" />').appendTo( c);

		//~ let __c = $('<div class="box__fill text-center text-truncate">uploading</div>').appendTo( _c);

		//~ $('<i class="fa fa-spinner fa-pulse fa-2x fa-fw" />').appendTo( __c);

		//~ return ( c);

		let _c = $('<div class="progress box__uploading" />').appendTo( c);

		$('<div class="progress-bar progress-bar-striped box__fill" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" />').appendTo( _c);

		return ( c);


	}
	;
	let uploader = function( params) {
		let options = $.extend({
			url : false,
			postData : {},
			onUpload : function( response) {},
			droppedFiles : {},
			host : false,

		}, params);

		let data = new FormData();
		for(let o in options.postData) {
			data.append( o, options.postData[o]);

		}

		$.each( droppedFiles, function(i, file) {
			data.append('files-'+i, file);

		});

		let progressBar = $('.box__fill', options.host);
		progressBar
			.css('width', '0')
			.attr('aria-valuenow', ( e.loaded / e.total * 100));

		options.host.addClass('is-uploading');

		$.ajax({
			url: options.url,
			type: 'POST',
			data: data,
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

				})

				return xhr;

			}

		})
		.done( function( d) {
			if ( 'ack' == d.response) {
				$.each( d.data, function( i, j) {
					$('body').growl( j);

				})

			}
			else {
				$('body').growl( d);

			}

			options.onUpload( d);

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

	}
	;
	_b_.fileDragDropHandler = function( params) {
		let _el = $(this);

		let options = $.extend( {
			url : false,
			host : _el

		}, params);

		if ( !options.url)
			throw 'Invalid upload url';

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
			.on('dragover dragenter', function() {
				$(this).addClass('is-dragover');
			})
			.on('dragleave dragend drop', function() {
				$(this).removeClass('is-dragover');
			})
			.on('drop', function(e) {
				e.preventDefault();
				let droppedFiles = e.originalEvent.dataTransfer.files;

				if (droppedFiles) {
					options.droppedFiles = droppedFiles;
					uploader( options);

				}

			});

		}	// if (isAdvancedUpload && !options.host.hasClass('has-advanced-upload'))

	}
	;

})( _brayworth_ );