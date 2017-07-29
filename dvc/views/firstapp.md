# First App

## Keeping it simple
* The default controller is called home
* The default controller action is index this relates directly to a public function
  in the controller:
* The simplest controller would be:
<pre>
    &lt;?php
        class hello extends Controller {
            function index() {
                print 'hello&lt;br /&gt;';

            }

        }

</pre>

* <a href="/hello" _target="blank">hello - sample controller</a>
<pre>
    &lt;?php
      class hello extends Controller {
          function index() {
              $p = new Page();
                  $p
                      -&gt;header()
                      -&gt;title();

                  $p->primary();
                      print 'hello&lt;br /&gt;';
                      print dvc\html::icon( 'John Citizen');

                  $p->secondary();
                      print 'secondary';

          }

      }

</pre>

## Disabling Documentation
* If you create a controller called docs - it will disable this documentation, which on
  a production application may be what you want
