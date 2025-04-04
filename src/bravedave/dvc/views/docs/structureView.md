# View

[Docs](.) | [Structure](structure) | **View**

A view is just html, ```$this``` is generally the controller.

So - just write html ...
```html
<html>
    <body>
        <h1>Hello World</h1>
    </body>
</html>
```

## Wrapped in HTML
The constroller has a render function.

Using this the html is more of a fragment:

```html
<div class="alert alert-success">
    You can just write bootstrap fragments
</div>
```

because - by default it is inserted in a ```.col``` element
1. opens a Bootstrap (4) page
2. inserts the html
3. closes the page

```html
<html>
    <head>
        <link type="text/css" rel="stylesheet" href="-[ bootstrap css ]-" />
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col">

--- your view inserted here ---

                </div>
            </div>
        </div>

        <script type="text/javascript" src="-[ bootstrap js ]"></script>
    </body>
</html>
```
