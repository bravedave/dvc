<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/
?>
<style>
	.hide-when-no .hidden-on-no { display: none; }

</style>

<table id="<?= $_tbl = strings::rand() ?>" class="table table-sm hide-when-no">
	<thead class="small">
		<tr>
			<td>Path</td>
			<td class="text-center">Visits</td>
			<td class="text-center" data-role="toggle-cell">On Sitemap</td>

		</tr>

	</thead>

	<tbody>
	<?php
		$dao = new dao\sitemap;
		if ( $dtos = $dao->getAll()) {
			foreach( $dtos as $dto) {
				printf( '<tr class="%s">', $dto->exclude_from_sitemap ? 'hidden-on-no' : '');

				printf( '<td>%s</td>', $dto->path);
				printf( '<td class="text-center">%s</td>', $dto->visits);
				printf( '<td class="text-center" data-href="%s">%s</td>',
					strings::url( 'sitemap/toggle/' . $dto->id ),
					$dto->exclude_from_sitemap ? 'No' : 'Yes' );

				print '</tr>';

			}

		}

	?>
	</tbody>

</table>

<script>
$(document).ready( function() {
	$('#<?= $_tbl ?> > thead')
	.addClass('pointer')
	.on('click', function( e) {
		e.stopPropagation(); e.preventDefault();

		let t = $('#<?= $_tbl ?>');

		if ( t.hasClass('hide-when-no'))
			t.removeClass('hide-when-no');

		else
			t.addClass('hide-when-no');

	});

});
</script>

