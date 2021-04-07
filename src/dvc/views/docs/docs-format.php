<style>
div[data-role="content-primary"] > .markdown-body > h1 {
	display: none;
}
div[data-role="content-primary"] > .markdown-body > h1,
div[data-role="content-primary"] > .markdown-body > h2,
div[data-role="content-primary"] > .markdown-body > h3,
div[data-role="content-primary"] > .markdown-body > h4,
div[data-role="content-primary"] > .markdown-body > h5,
div[data-role="content-primary"] > .markdown-body > h6 {
    margin-top: 1rem;
    margin-bottom: .5rem;
}
</style>
<script>
$(document).ready( () => {
	let h = $('[data-role="content-primary"] > .markdown-body > h1');
	if ( h.length > 0) {
		let title = h.first().html();

		$('body > nav .navbar-brand')
		.html('')
		.append( $('<h4></h4>').html( title));

    document.title = title;

	}

})
</script>
