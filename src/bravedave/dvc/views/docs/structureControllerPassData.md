# Pass Data from Controller to view

###### <navbar>[Docs](/docs/) | [Structure](/docs/structure) | [Controller](/docs/structureController) | Pass Data from Controller to view</navbar>

Sooner or later you are going to want to pass data from the controller to the view - that is what Model-View-Controller wants to do.

Modelling will produce the data, the view will display it ...

The *view* will sees the *controller* as ```$this```, so to pass the data, assign it as an instance variable on the controller .. i.e. in this example, the *view* can see ```$this->data```

```php
<?php
    $this->data = (object)[
        'title' => 'Example title'
        'dataset' => [
            (object)[
                'id' => 1,
                'name' => 'Fred'
            ]
        ]

    ];

```

## Report Example

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
    } ?>
    </body>
</html>
```