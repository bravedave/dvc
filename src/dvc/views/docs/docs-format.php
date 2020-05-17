<script>
$(document).ready( function() {
	let h = $('[data-role="content-primary"] > .markdown-body > h1');
	if ( h.length > 0) {
		$('body > nav .navbar-header').html('').append($('<h4></h4>').html( h.first().html()));
		h.first().addClass('d-none d-print-block');

	}

	// console.log( 'formatted');

})
</script>
