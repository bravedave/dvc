## Pass data from controller to view

Sooner or later you are going to want to pass data from the controller to the view - that is what Model-View-Controller wants to do.

Modelling will produce the data - but to simplify for this example ..

The view will see the controller as ```$this```, so to pass data conventionalize a variable to pass - **data**  is the recommended variable (but only because you want to pass data - no other particular reason, and here, objects are passed, but it can be arrays)

```php
<?php
class example extends Controller {
	function hello( $p1, $p2) {
        $this->data = (object)[
            'title' => 'My First Report',
            'res' => [
                (object)['name' => 'John'],
                (object)['name' => 'Frank'],
                (object)['name' => 'Barney'],

            ]

        ];

        $this->load('report');


	}

}
```

then the report.php can retrieve the data ...
```php
<html>
    <body>
    <?php
    foreach ( $this->data->res as $rec ) {
        printf( '<div>%s</div>', $rec->name);
    }
    ?>
    </body>
</html>
```