<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div class="rowmy-4">
    <div class="col py-4 border" id="<?= $_uid = strings::rand() ?>">
        right click me for a context menu

    </div>

</div>
<script>
( ( el) => {
    el.on( 'contextmenu', function( e) {
        if ( e.shiftKey)
            return;

        e.stopPropagation();e.preventDefault();

        _brayworth_.hideContexts();

        let _context = _brayworth_.context();

        _context.append( $('<a href="#">hello</a>'));

        _context.open( e);

    });

})( $('#<?= $_uid ?>'))
</script>