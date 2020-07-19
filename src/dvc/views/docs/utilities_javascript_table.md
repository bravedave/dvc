# TextPrompt

###### [Docs](/docs/) | [Utilities](/docs/utilities) | [Javascript](/docs/utilities_javascript) | Table

This utility consists of two parts
* a standlone table sorting utility
* a activating event which can be attached to the table column

```html
<table id="table">
    <thead>
        <td data-role="sort-header" data-key="category">Item</td>
        <td data-role="sort-header" data-key="value" data-sortype="numeric">Item</td>
    </thead>

    <tbody>
        <tr
            data-category="item"
            data-value="1000">
            <td>Amplifier<td>
            <td>$1,000<td>
        </tr>

        <tr
            data-category="item"
            data-value="500">
            <td>car<td>
            <td>$500<td>
        </tr>

    </tbody>

</table>
```

###### sort by clicking a column header using sort()

```javascript
$(document).ready(() => {
	$('thead>tr>td[data-role="sort-header"]').each( ( i, el) => {
		$(el)
		.addClass('pointer')
		.on('click', _brayworth_.table.sort);

	});

});
```

###### or sort the table manually using sortOn()

```javascript
$(document).ready(() => {
	_brayworth_.table.sortOn('#table', 'value');

});
```
