/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

_brayworth_.table.sortOn = function( table, key, sorttype, order) {
	//~ var debug = true;
	var debug = false;
	var tbody = $('tbody', table);
	if ( !tbody) tbody = table;

	if ( 'undefined' == typeof order)
		order = ( tbody.data('order') == "desc" ? "asc" : "desc" );
	tbody.data('order', order);

	if ( !sorttype)
		sorttype = 'string';

	var warn = true;

	var items = tbody.children('tr');

	if ( debug) console.log( key, sorttype, order, items.length );

	items.sort(function sortItem(a, b) {
		var ae = $(a).data(key);
		var be = $(b).data(key);
		if ( /undefined/.test( typeof ae ) || /undefined/.test( typeof ae )) {
			ae = $(a).data('key-' + key);
			be = $(b).data('key-' + key);

			if ( warn)
				console.warn( 'table sorting is not jQuery3 compatible');

			warn = false;

		}

		if ( debug) console.log( key, ae, be, sorttype, order );

		if (sorttype == "numeric") {
			if ( 'undefined' == typeof ae)
				ae = 0;
			if ( 'undefined' == typeof be)
				be = 0;
			return ( Number(ae) - Number(be));

		}
		else {
			if ( 'undefined' == typeof ae)
				ae = '';
			if ( 'undefined' == typeof be)
				be = '';
			return (ae.toUpperCase().localeCompare(be.toUpperCase()));

		}


	});

	$.each(items, function (i, e) { (order == "desc" ? tbody.prepend(e) : tbody.append(e)); });
	var lines = $('tr>[role="line-number"]', tbody);
	if ( lines.length > 0) {
		lines.each( function( i, e ) { $(e).html(i+1); });

	}
	else {
		var lines = $('tr>td>[role="line-number"]', tbody);
		if ( lines.length > 0) {
			lines.each( function( i, e ) { $(e).html(i+1); });

		}

	}

}

_brayworth_.table.sort = function( e) {
	if ( 'undefined' != typeof e && !!e.target) e.stopPropagation();

	_brayworth_.hideContexts();

	var table = $(this).closest( 'table' );
	if ( !table) return;

	var key = $(this).data("key");
	if ( !key) return;

	var sorttype = $(this).data("sorttype");

	_brayworth_.table.sortOn( table, key, sorttype);	//~ console.log( key );

}
;
