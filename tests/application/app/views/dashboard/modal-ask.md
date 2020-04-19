#### Simple Modal Style Question for Alerts
```
<script>
$(document).ready( () => {
    $('#<?= $uid ?>').on( 'click', function( e) {
        e.stopPropagation();e.preventDefault();

        _brayworth_.ask({
            headClass: 'text-white bg-danger',
            title : 'This is Red',
            text : 'Do you agree ?',
            buttons : {
                yes : function() {
                    $(this).modal('hide');
                    console.log( 'ok', this);

                }

            }

        });

    });

});
</script>
```