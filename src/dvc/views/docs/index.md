# DVC

## DVC - Data View Controller

A Model-View-Controller _read more at <https://en.wikipedia.org/wiki/Model-view-controller>_

DVC is an **Application** which uses the Model-View-Controller model

### Application

The Application established some global parameters and a connection to data

### Controller

The Controller interprets the request from the Web, channeling the route to the program and interpretting GTE/POST parameters

### Data (Model)

The controller will request any data, respecting the parameters it is given

### View

#### For Example

```bash
[http://example.dom]/[people]/[edit]/[55]

[server]/[controller]/[sub-controller]/[parameter]

[server]/[class]/[class function]/[parameter]

```

* Server - we arrive here from the internet
* Controller (class) - **people** - the _Application_ understands the _controller_ is the **people** controller and instantiates it
* Sub-Controller (function) - **edit** - the _Application_ understands the sub controller to call is **edit** and calls it against the instantiated _controller_
  * the the _parameter_ **55**

**and this could look like**

```php
<?php

class people extends Controller {
  function edit( $id) {
    if ( $id = abs( (int)$id)) {  // simple check

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
