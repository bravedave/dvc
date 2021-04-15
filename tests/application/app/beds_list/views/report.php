<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace beds_list;

use dvc\icon;
use strings;
?>

<h1 class="d-none d-print-block"><?= $this->title ?></h1>
<table class="table" id="<?= $_table = strings::rand() ?>">
	<thead class="small">
		<tr>
			<td class="text-center">#</td>
			<td class="text-center">Beds</td>
			<td>Description</td>

		</tr>

	</thead>

	<tbody>
	<?php
	while ( $dto = $this->data->dataset->dto()) {
		printf( '<tr data-id="%d">', $dto->id);

		print '<td class="small text-center" line-number>&nbsp;</td>';

		printf( '<td class="text-center">%s</td>', $dto->beds);
		printf( '<td>%s</td>', $dto->description);

		print '</tr>';
	}
	?></tbody>

	<tfoot class="d-print-none">
		<tr>
			<td colspan="3" class="text-right">
				<button type="button" class="btn btn-outline-secondary" id="<?= $addBtn = strings::rand() ?>"><i class="bi bi-plus"></i></a>

			</td>

		</tr>

	</tfoot>

</table>

<script>
( _ => {
  $(document).on( 'add-beds', e => {
    _.get.modal( _.url('<?= $this->route ?>/edit'))
    .then( m => m.on( 'success', e => window.location.reload()));

  });

  $('#<?= $_table ?>')
  .on('update-row-numbers', function(e) {
    $('> tbody > tr:not(.d-none) >td[line-number]', this)
    .each( ( i, e) => $(e).html( i+1));

  })
  .trigger('update-row-numbers');

  $('#<?= $addBtn ?>').on( 'click', e => $(document).trigger( 'add-beds'));

  $('#<?= $_table ?> > tbody > tr').each( ( i, tr) => {

    $(tr)
    .addClass( 'pointer' )
    .on( 'delete', function( e) {
      let _tr = $(this);

      _.ask({
        headClass: 'text-white bg-danger',
        text: 'Are you sure ?',
        title: 'Confirm Delete',
        buttons : {
          yes : function(e) {
            $(this).modal('hide');
            _tr.trigger( 'delete-confirmed');

          }

        }

      });

    })
    .on( 'delete-confirmed', function(e) {
      let _tr = $(this);
      let _data = _tr.data();

      _.post({
        url : _.url('<?= $this->route ?>'),
        data : {
          action : 'delete',
          id : _data.id

        },

      }).then( d => {
        if ( 'ack' == d.response) {
          _tr.remove();
          $('#<?= $_table ?>').trigger('update-line-numbers');

        }
        else {
          _.growl( d);

        }

      });

    })
    .on( 'edit', function(e) {
      let _tr = $(this);
      let _data = _tr.data();

      _.get.modal( _.url('<?= $this->route ?>/edit/' + _data.id))
      .then( m => m.on( 'success', e => window.location.reload()));

    })
    .on( 'contextmenu', function( e) {
      if ( e.shiftKey)
        return;

      e.stopPropagation();e.preventDefault();

      _.hideContexts();

      let _tr = $(this);
      let _context = _.context();

      _context.append( $('<a href="#"><b>edit</b></a>').on( 'click', e => {
        e.stopPropagation();e.preventDefault();

        _context.close();
        _tr.trigger( 'edit');

      }));

      _context.append( $('<a href="#"><i class="bi bi-trash"></i>delete</a>').on( 'click', e => {
        e.stopPropagation();e.preventDefault();

        _context.close();
        _tr.trigger( 'delete');

      }));

      _context.open( e);

    })
    .on( 'click', function(e) {
      e.stopPropagation(); e.preventDefault();
      $(this).trigger( 'edit');

    });

  });

}) (_brayworth_);
</script>
