# First App

## Keeping it simple
* The default controller is called home
* The default controller action is index this relates directly to a public function
  in the controller:
* The simplest controller would be:

```php
  <?php
  class hello extends Controller {
    function index() {
      print 'hello<br />';
    }
  }

```

* <a href="/hello" _target="blank">hello - sample controller</a>
  * The sample controller constructs a page using bootstrap

```php
  <?php
    class hello extends Controller {
      function index() {
        $this->title = config::label;

        /**
         * renderBS5 wraps the page in <html><body> tags
         * and load the bootstrap5 css/js
         */
        $this->renderBS5([
          'aside' => fn () => print 'index',
          'main' => fn () => print 'main'
        ]);
      }
    }
```

#### TIP: Disabling Documentation
* If you create a controller called docs - it will disable this documentation, which on
  a production application may be what you want
* Alternatively : Create a folder in your application/views folder - docs
  * ie: application/views/docs
  and create content there, start with contents.md which will disable the docs folder
