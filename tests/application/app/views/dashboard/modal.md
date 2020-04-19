#### Simple Modal Style Popup
```
<script>
$(document).ready( () => {
    $('#<?= $uid ?>').on( 'click', function( e) {
        e.stopPropagation();e.preventDefault();

        _brayworth_.modal({
            title : 'fred',
            text : 'hey jude'

        });

    });

});
</script>

```