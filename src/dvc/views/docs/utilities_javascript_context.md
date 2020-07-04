###### [Utilities](/docs/utilities) | [Javascript](/docs/utilities_javascript)

### Context
<table class="table">
    <tbody>
        <tr>
            <td>description</td>
            <td>Context Menu</td>

        </tr>

        <tr>
            <td>type</td>
            <td>javascript</td>

        </tr>

        <tr>
            <td>source</td>
            <td>src\dvc\js\_brayworth_.context.js</td>

        </tr>

    </tbody>

</table>

#### Example

```javascript
$('#elementID').on( 'contextmenu', function( e) {
    if ( e.shiftKey) return;                // use the native menu if you hold shiftKey down

    e.stopPropagation();e.preventDefault(); // prevent the native browser contextmenu

    _brayworth_.hideContexts();             // hide any open contextmenus

    let _context = _brayworth_.context();

    _context.append( $('<a href="#">hello</a>'));

    _context.open( e);

});
```
