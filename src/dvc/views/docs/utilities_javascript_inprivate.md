# Extend

###### [Docs](/docs/) | [Utilities](/docs/utilities) | [Javascript](/docs/utilities_javascript) | inPrivate

<table class="table">
    <tbody>
        <tr>
            <td>description</td>
            <td>detect if browser is running in private mode</td>

        </tr>

        <tr>
            <td>type</td>
            <td>javascript</td>

        </tr>

        <tr>
            <td>source</td>
            <td>src\dvc\js\_brayworth_.inPrivate.js</td>

        </tr>

    </tbody>

</table>

#### Example

```javascript
    _brayworth_.inPrivate().then( b => console.log( b ? 'in private' : 'NOT inprivate'));
```

#### Credits
*  https://gist.github.com/jherax/a81c8c132d09cc354a0e2cb911841ff1
*  https://stackoverflow.com/questions/2860879/detecting-if-a-browser-is-using-private-browsing-mode/37091058#37091058
