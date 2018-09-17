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
_brayworth_.fileDragDropContainer = function() {
	let c = $('<div />');

	let _c = $('<div class="box__uploading" />').appendTo( c);

	let __c = $('<div class="box__fill text-center text-truncate">uploading</div>').appendTo( _c);

	$('<i class="fa fa-spinner fa-pulse fa-2x fa-fw" />').appendTo( __c);

	return ( c);

}
;

_brayworth_.fileDragDropHandler = function( params) {
	let _el = $(this);

	let options = {
		url : false,
		postData : {},
		onUpload : function( response) {},
	}

	$.extend( options, params);

	if ( !options.url)
		throw 'Invalid upload url';

	let isAdvancedUpload = (function() {
		let div = document.createElement('div');
		return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
	})();

	if ( isAdvancedUpload && !_el.hasClass('has-advanced-upload')) {
		//~ console.log( 'setup has-advanced-upload');
		_el
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
			//~ console.log( droppedFiles);

			if (droppedFiles) {
				//~ console.log( 'droppedFiles');

				let data = new FormData();
				for(let o in options.postData) {
					data.append( o, options.postData[o]);

				}

				$.each( droppedFiles, function(i, file) {
					data.append('files-'+i, file);

				});

				$('.box__fill', _el).css('width','0');
				_el.addClass('is-uploading');

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
							if (e.lengthComputable)
								$('.box__fill', _el).css('width', ( e.loaded / e.total * 100) + '%');

						})

						return xhr;

					}

				})
				.done( function( response) {
					if ( 'ack' == response.response) {
						$.each( response.data, function( i, j) {
							$('body').growl( j);

						})

					}
					else {
						$('body').growl( response);

					}

					options.onUpload( response);

				})
				.always( function( r) {
					_el.removeClass('is-uploading');

				})
				.fail( function( r) {
					console.warn(r);
					_brayworth_.modal({
						title : 'Upload Error',
						text : 'there was an error uploading the attachments<br />we recommend you reload your browser'
					});

				});

			}

		});

	}	// if (isAdvancedUpload && !_el.hasClass('has-advanced-upload'))

}
;
