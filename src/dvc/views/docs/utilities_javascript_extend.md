###### [Docs](/docs/) | [Utilities](/docs/utilities) | [Javascript](/docs/utilities_javascript)

### Extend
<table class="table">
    <tbody>
        <tr>
            <td>description</td>
            <td>jQuery Extend Replacement</td>

        </tr>

        <tr>
            <td>type</td>
            <td>javascript</td>

        </tr>

        <tr>
            <td>source</td>
            <td>src\dvc\js\_brayworth_.extend.js</td>

        </tr>

    </tbody>

</table>

#### Example

```javascript
    let a = {
        "name" : "John Citizen",
        "dob" : "1970-01-01"
    };

    let b = {
        "name" : "John Citizen",
        "address" : "1 Albatross Avenue, Suburbia"
    };

    let c = _brayworth_.extend( a, b);

    console.log( c);
    /* {
        "address" : "1 Albatross Avenue",
        Suburbia", "name" : "John Citizen",
        "dob" : "1970-01-01"

    } */
```
