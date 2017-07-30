# First App

## Keeping it simple
* The default controller is called home
* The default controller action is index this relates directly to a public function
  in the controller:
* The simplest controller would be:
<pre><code>
    &lt;?php
        class hello extends Controller {
            function index() {
                print 'hello&lt;br /&gt;';
               <span></span>
        }
        <span></span>
    }
    <span></span>
</code></pre>

* <a href="/hello" _target="blank">hello - sample controller</a>
  * The sample controller constructs a page using bootstrap
    * More about the pages class [here](pages)
<pre><code>
    &lt;?php
        class hello extends Controller {
            function index() {
                $p = new dvc\pages\bootstrap;
                    $p
                        -&gt;header()
                        -&gt;title();
                        <span></span>
                    $p->primary();
                        print 'hello&lt;br /&gt;';
                        <span></span>
                    $p->secondary();
                        print 'secondary';
               <span></span>
        }
        <span></span>
    }
    <br />
</code></pre>

## Disabling Documentation
* If you create a controller called docs - it will disable this documentation, which on
  a production application may be what you want
