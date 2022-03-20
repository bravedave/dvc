# Risorsa - Asset Management

## How to create a PHP module

<em>
Assuming Installed and running - suggest WSL

1. PHP
1. Composer

and using *bravedave/dvc*
</em>

### Recipe

1. Create a new project `composer create-project bravedave/mvp risorsa`
1. Open that folder with VS Code and start your terminal, the working directory will be *risorsa*
1. Add dvc `composer req bravedave/dvc`
1. clean up (we don't need these)
   * `rm -fr src/app/home src/app/template`
   * `rm -f src/app/slim.php src/app/launcher.php`
1. modify www/_mvp.php to point to ***application::run***, not *launcher::run* ..
1. Create a folder for the src `mkdir src/risorsa`
1. tell composer where that src is

```json
"autoload": {
  "psr-4": {
    "risorsa\\": "src/risorsa/"
  }
},
```

* update autoload `composer u`

by now the app should run `./run.sh`

### Creating an application

We are creating most of the code in *namespace risorsa*, there are other ways to reference the program, but we are going to create a *DVC* controller to reference it directly. In *DVC* controllers are located in src/app/controller, and this controller is risorsa

1. Create a directory `mkdir src/controller`
1. Create the referencing controller
   1. Create a file *src/controller/risorsa.php*

```php
<?php
class risorsa extends risorsa\controller {}
```

that about wraps up the *getting ready* phase, on to coding the application..

#### Create the controller

* create a file src/risorsa/controller

```php
<?php

namespace risorsa;

use strings;

class controller extends \Controller {
  protected $viewPath = __DIR__ . '/views/';

  protected function _index() {
    // these lines is temporary
    print 'hello from risorsa ..';
    return;
    // these lines is temporary

    $this->render([
      'primary' => ['blank'],
      'secondary' => ['blank'],
      'data' => (object)[
        'searchFocus' => true,
        'pageUrl' => strings::url($this->route)
      ]
    ]);
  }

  protected function before() {
    parent::before();
  }

  protected function postHandler() {
    $action = $this->getPost('action');
    parent::postHandler();
  }
}
```

the app now runs at /risorsa and says *hello from risorsa ..*

you can remove the lines between *"these lines is temporary"* inclusive of those lines, the app will still run, but you have a navbar, footer and blank views .. a clean start

* you can create a navbar and footer, it's not required as this is a module, so a navbar and footer is probably more global than this, to create one, create a file at *src/app/views/navbar-default.php* and *src/app/views/footer.php* -  and use the bootstrap examples

so ... to the app

#### Connect to a database

keeping it simple, use sqlite - mysql and mariadb are supported.

* rename src/data/defaults-sample.json to src/data/defaults.json

db_type is the important line - noting it is sqlite, refresh your page and the data file *db.sqlite* is created in the data folder

#### Create an Index page

1. Create a folder at *src/risorsa/views*
1. Add a file *src/risorsa/views/index.php*

```php
<?php

namespace risorsa;  ?>

<h6 class="mt-1">Risorsa</h6>

<ul class="nav flex-column">
  <li class="nav-item">
    <a class="nav-link" href="#"><i class="bi bi-plus-circle"></i> new</a>
  </li>
</ul>
<script>
( _ => $(document).ready( () => {

}))( _brayworth_);
</script>
```

1. Modify the controllers secondary view to load 'index'
    * at about line 22 of *src/risorsa/controller.php*

```php
    'secondary' => ['index'],
```
