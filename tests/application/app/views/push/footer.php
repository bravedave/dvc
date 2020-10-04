<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use dvc\icon;
use dvc\push; ?>

<footer class="footer-fixed">
	<div class="container-fluid">
		<div class="row mb-0">
			<div class="col-1 position-relative">
				<button class="btn btn-sm btn-light" type="button"
          id="<?= $_chat = strings::rand() ?>">
					<?= icon::get( icon::chat ) ?>

				</button>

			</div>

			<div class="col text-right" id="brayworthLOGO">
				<a title="software by BrayWorth using php" href="https://brayworth.com" target="_blank">BrayWorth</a>

			</div>

		</div>

	</div>

</footer>
<script>
$(document).ready( () => {
  (_ => {
    <?php if ( push::enabled()) { ?>

      /**
      * Check the current Notification permission.
      * If its denied, skip this until the user
      * changes the permission manually
      */

      if (Notification.permission === 'denied') {
        console.warn('Notifications are denied by the user');

      }
      else {

        _.push.url = _.url( 'chat');
        _.push.applicationServerKey = '<?= trim( config::notification_keys()->pubKey) ?>';
        _.push.serviceWorker = _.url( 'serviceWorker');
        _.push.load();

        $('#<?= $_chat ?>')
        .on( 'contextmenu', function( e) {
          if ( e.shiftKey)
            return;

          e.stopPropagation();e.preventDefault();

          _brayworth_.hideContexts();

          let _me = $(this);
          let _context = _brayworth_.context();

          if ( _.push.active) {
            _context.append( $('<a href="#">Unsubscribe from Notifications</a>').on( 'click', e => {
              e.stopPropagation();e.preventDefault();

              _context.close();
              _me.trigger( 'unsubscribe');

            }));

            _context.append( $('<a href="#">Send test Message</a>').on( 'click', function( e) {
              e.stopPropagation();e.preventDefault();

              _context.close();
              _me.trigger( 'send-test-message');

            }));

          }
          else {
            _context.append( $('<a href="#">Subscribe for Notifications</a>').on( 'click', e => {
              e.stopPropagation();e.preventDefault();

              _context.close();
              _me.trigger( 'subscribe');

            }));

          }

          _context.open( e);

        })
        .on( 'send-test-message', e => {
          _.post({
            url : _.url('chat'),
            data : {
              action : 'send-test-message'

            },

          }).then( d => {
            if ( 'ack' == d.response) {
            }
            else {
              _.growl( d);

            }

          });

        })
        .on( 'subscribe', e => {
          // _.push.unsubscribe() :
          _.push.subscribe();

        })
        .on( 'unsubscribe', e => {
          // _.push.unsubscribe() :
          _.push.unsubscribe();

        });

      }

    <?php } // if ( push::enabled()) ?>

  })(_brayworth_);

});
</script>
