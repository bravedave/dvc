###### [Structure](/docs/structure) | [Controller](/docs/structureController) | Example

### Templates - Controller

# Standard Functionality

By default the controller can render a bootstrap 4 template with 4 main areas
<pre>
+---------------------------------------+
| Navbar                                |
+---------------------------+-----------+
| Primary                   | Secondary |
| 9 Columns                 | 3 Columns |
+---------------------------+-----------+
| Footer                                |
+---------------------------------------+
</pre>

# Example

The controller encapsulates a mini application.
For Example, a table that requires maintenance, perhaps a stock item

```php
<?php

class _template extends Controller {
  protected function posthandler() {
    $action = $this->getPost('action');

    if ( 'gibblegok' == $action) {
      \Json::ack( $action);

    }
    else { parent::postHandler(); }

  }

  protected function _index() {
    $this->render([
      'title' => $this->title = sprintf( '%s : Index', $this->label),
      'primary' => 'blank',
      'secondary' => 'blank'

    ]);

  }

  function index() {
    $this->isPost() ?
      $this->postHandler() :
      $this->_index();

  }

}
```
