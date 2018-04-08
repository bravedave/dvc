/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	load library (if not loaded):
		$.getScript('/ews/js');

	note: this requires fullcalendar.js is loaded

	test:
		ews.calendar.fullcalendar({ host : 'body' });

	*/
ews.calendar.fullcalendar = function( params) {
	var options = {
		host : 'body',
		scrollTime : '07:00:00',
		timezone : 'local',
		displayEventTime : true,
		contentHeight : $(window).height() - 110,
		defaultView: 'listWeek',
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		firstDay : 1,
		businessHours: {
		    dow: [ 1,2,3,4,5,6 ], // Monday - Saturday
			start: '07:00',
			end: '18:00',
		},
		views: {
			listWeek : {
				displayEventTime : true,
				displayEventEnd : false,

			},
			basic : {
				displayEventTime : true,

			},
			agenda : {
				displayEventTime : true,

			},
			agendaWeek : {
				displayEventTime : false,

			},
			agendaDay : {
				displayEventTime : false,

			},

		},
		loading: function(bool) {
			bool ? hourglass.on() : hourglass.off();

		},

	};

	if (_brayworth_.bootstrap_version() >= 4) {
		options.themeSystem = 'bootstrap4';

	}

	$.extend( options, params);

	if ( 'string' == typeof options.host)
		options.host = $(options.host);

	$(options.host).fullCalendar( options);

}
