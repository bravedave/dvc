###### [Docs](/docs/) | [Utilities](/docs/utilities) | [Javascript](/docs/utilities_javascript)

### Ask
<table class="table">
    <tbody>
        <tr>
            <td>description</td>
            <td>Popup bootstrap modal - can be used as alert</td>

        </tr>

        <tr>
            <td>type</td>
            <td>javascript</td>

        </tr>

        <tr>
            <td>source</td>
            <td>src/dvc/js/_brayworth_.ask.js</td>

        </tr>

    </tbody>

</table>

#### Example

```javascript
    _brayworth_.ask({
        'text' : 'Is this OK ?',
        'headClass': 'text-white bg-danger',
        'buttons' : {
            'yes' : function() {
                $(this).modal('hide');
                console.log( 'ok', this);

            }

        }

    });

```