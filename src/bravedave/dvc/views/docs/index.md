# DVC - *Data View controller*

Model View Controller (MVC) is a design pattern. It separate the parts of a software application into clear and distinct sections.

* The ***Model*** holds the data and information of the application.
* The ***View*** is what the user sees and interacts with - the user interface.
* The ***Controller*** is the part that coordinates between the Model and View making decisions on what should be shown and how it should be displayed.

DVC is an ***Application*** which uses the ***Model-View-Controller***[^note1] pattern to develop, maintain, and update a software application.

*it would look like:*

```php
<?php

class people extends Controller {

  function edit( $id) {

    if ( $id = (int)$id) {  // simple check

      // data
      $dao = new dao\people;  // dao - data access object
      if ( $dto = $dao->getByID( $id)) {  // data transition object

        $this->data = (object)[
          'person' => $dto
        ];

        $this->load('edit');  // view
      }
      else {

        print 'error .. not found';
      }
    }
    else {

      print 'error .. invalid';
    }
  }
}
```

[^note1]: Model-View-Controller *read more at <https://en.wikipedia.org/wiki/Model-view-controller>*
