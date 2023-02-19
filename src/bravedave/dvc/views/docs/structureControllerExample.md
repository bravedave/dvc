# Controller Example

###### <navbar>[Docs](/docs/) | [Structure](/docs/structure) | [Controller](/docs/structureController) | Example</navbar>

## Template - Controller

### Standard Functionality

By default the controller can render a bootstrap 5 page template with 4 main areas
<table border="1">
  <tbody>
    <tr><td colspan="2">Navbar</td></tr>
    <tr>
      <td style="width: 75%">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
        Main<br>
        9 columns
      </td>
      <td style="width: 75%">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        Aside<br>
        3 columns
      </td>
    </tr>
    <tr><td colspan="2">footer</td></tr>
  </tbody>
</table>
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
