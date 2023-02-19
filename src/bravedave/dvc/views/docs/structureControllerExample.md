# Controller Example

###### <navbar>[Docs](/docs/) | [Structure](/docs/structure) | [Controller](/docs/structureController) | Example</navbar>

## Template - Controller

### Standard Functionality

By default the controller can render a bootstrap 5 page template with 4 main areas
<table border="1">
  <tbody>
    <tr><td colspan="2">navbar</td></tr>
    <tr>
      <td style="width: 75%">
        Main<br>
        9 columns<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </td>
      <td style="width: 75%">
        Aside<br>
        3 columns<br>
        &nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;
      </td>
    </tr>
    <tr><td colspan="2">footer</td></tr>
  </tbody>
</table>
<p>&nbsp;</p>

### Example

The controller encapsulates a mini application.
For Example, a table that requires maintenance, perhaps a stock item

```php
<?php

class template extends Controller {

  protected function posthandler() {
    $action = $this->getPost('action');

    if ( 'update' == $action) {

      header('content-type: application/json');
      print json_encode([
        'response' => 'ack',
        'description' => $action
      ]);
    } else {

      header('content-type: application/json');
      print json_encode([
        'response' => 'nak',
        'description' => $action
      ]);
    }
  }

  protected function _index() {

    $this->load('edit-page');
  }

  function index() {

    $this->isPost() ?
      $this->postHandler() :
      $this->_index();
  }
}
```
