/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	load library (if not loaded):
		$.getScript('/ews/js');

	test:
		ews.calendar.day({ host : 'body' });

	*/
ews.calendar.day = function( params) {

	var options = {
		host : 'body',

	}

	$.extend( options, params);

	var viewStart = '7am';
	var viewStartElement = false;

	if ( ! ( options.host instanceof jQuery))
		options.host = $(options.host);

	templation.load({template:'dayplanner'}).then( function( template) {

		//~ console.log( d);
		//~ console.log( d.get());
		var plannerbox = template.get('div[plannerbox]');
		var interval = 30;
		var seed = options.seed.clone();
		//~ console.log( 'plannerbox', plannerbox.length);

		options.host.html('');	// clean slate
		options.host.data('template', template);

		template.appendTo( options.host);

		var t = templation.template('table').appendTo( plannerbox);
			t.get().addClass('table table-striped table-header-fixed dayplanner').css('margin','auto');

		var r = templation.template('tr').appendTo( t.get('thead'));
			r.append( $('<td class="text-center" colspan="2" />').html( seed.format('dddd MMM D')))

		seed.second( 0);
		for ( var h = 0; h < 24; h++) {
			seed.hour( h);
			for ( var m = 0; m < 60; m += interval) {
				seed.minute( m);
				var sEnd = seed.clone();
					sEnd.add( interval, 'minutes');
				var r = templation.template('tr').appendTo( t.get('tbody'));
					if ( h < 7)
						r.get().addClass( 'early');
					else if ( h > 19)
						r.get().addClass( 'late');
					r.data( 'time_start', seed.format())
					r.data( 'time_end', sEnd.format())
					//~ console.log( seed.format());
				if ( m == 0)
					r.append( $('<td class="text-center small" />').html( seed.format('h:mma').replace( /m$/, '')));
				else
					r.append( '<td>&nbsp;</td>');

				r.append( '<td today />')

				if ( h == 7 && m == 0)
					viewStartElement = r.get();

			}

		}

		if ( !!viewStartElement) {
			var offset = viewStartElement.offset();
			offset.left -= 20;
			offset.top -= 20;
			//~ console.log( offset);
			t.get('tbody').animate({
				scrollTop: offset.top,
				scrollLeft: offset.left

			});

		}

		options.template = t;
		ews.calendar.day.populate( options);

	});

}

ews.calendar.day.populate = function( params) {
	var options = {
		template : false,
		seed : _brayworth_.moment(),
		url : _brayworth_.urlwrite( 'ews/'),
		user : 0,
		left : 0,
		width : '100%',
		color : 'black',
		backgroundColor : '#90caf9',

	}

	$.extend( options, params);

	_brayworth_.post({
		url: options.url,
		data : {
			start : options.seed.format( 'YYYY-MM-DD 00:00'),
			end : options.seed.format( 'YYYY-MM-DD 23:59'),
			user : options.user,

		}

	})
	.then( function( d) {

		//~ console.log( d);
		$.each( d, function( i, evt) {
			//~ console.warn( evt.start);
			var evtStart = _brayworth_.moment( evt.start);
			var evtEnd = _brayworth_.moment( evt.end);
			var t = options.template;
			t.get( 'tbody > tr').each( function( iOuter, row) {
				var time_start = _brayworth_.moment( $(row).data('time_start'));
				var time_end = _brayworth_.moment( $(row).data('time_end'));
				//~ console.log( time, evt.start);
				if ( evtStart >= time_start && time_end > evtStart) {
					var c = $('<div style="position: relative; width: 100%;" />');
					$('td[today]', row).append(c)

					var content = $('<div class="planner-cell" planner-label />')
						.data('user', options.user)
						.css({
							'left' : options.left + '%',
							'width' : options.width,
							'color' : options.color,
							'background-color' : options.backgroundColor,
							})
						.appendTo( c);
					content.html( evt.title);
					//~ console.log( 'found');

					// how many rows will the box cover
					t.get( 'tbody > tr').each( function( iInner, row) {
						var _row = $(row);
						var time_start = _brayworth_.moment( _row.data('time_start'));
						var time_end = _brayworth_.moment( _row.data('time_end'));
						if ( iInner >= iOuter) {
							if ( evtEnd.isSameOrBefore( time_end)) {	// >= time_start && time_end >= evtEnd) {
								var contentoffSet = content.offset();
								var offSet = _row.offset();
								var height = offSet.top - contentoffSet.top + _row.height();
								content.height( height);

								return ( false);

							}

						}

					});

					return ( false);

				}

			});

			// console.log( 'out of here');

		});

	})

}


ews.calendar.day.test = function() {
	//~ $(document).off('contextmenu');
	ews.calendar.day({ host : 'body' });

}
