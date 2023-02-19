# Structure

###### <navbar>[Docs](/docs/) | [Structure](/docs/structure) | Controller</navbar>

Controllers accept control of a request (from the browser) from the application.
The Application operates at the root of the web structure and interprets the request and calls a controller to execute the request

## Browser Request

```http://localhost/example/hello```

## Controller

```php
<?php
class example extends Controller {

	function hello() {

		print 'hello';
	}
}
```

In *DVC* up to two parameters can be passed

## Browser Request

```http://localhost/example/hello/john/citizen```

## Controller
```php
<?php
class example extends Controller {

	function hello( $p1, $p2) {

		printf( 'hello : %s, %s', $p1, $p2;
	}
}
```

#### See Also
- [Pass data from controller to view](/docs/structureControllerPassData)
- [Example Controller](/docs/structureControllerExample)


## Limitations

* it's quite simple - function names support upper, lower case and underscore but not too much else
* parameter passing is parsed through php filters : ```$url = filter_var( $this->url, FILTER_SANITIZE_URL);```,
so again it has to be fairly simple - periods [.] for instance are stripped out. POST and GET via parameters (```http://req/?v=1```)
are the most flexible ways to pass information.