# Pages

*The philosphy being pages is restricted to ..*
* php has a __destruct method classes, so during that process you can close any open tags

<pre><code>
    &lt;?php
        class hello extends Controller {
            function index() {
                $p = new dvc\pages\bootstrap;
                    $p
                        -&gt;header()
                        -&gt;title();
                        <span></span>
</code></pre>

* here there header has been closed (&lt;/head&gt;), body has been opened (&lt;body&gt;), and a navbar has been written (&lt;nav&gt; ... &lt;/nav&gt;)
  * the default navbar is app/views/navbar

<pre><code>
                    $p->primary();
                        print 'hello&lt;br /&gt;';
                        <span></span>
</code></pre>

* here a primary area has been open in a 2 column layout (&lt;<div data-role="content-primary"&gt;)

<pre><code>
                    $p->secondary();
                        print 'secondary';
               <span></span>
</code></pre>

* here a primary has been closed, secondary area has been opened in a 2 column layout (&lt;<div data-role="content-secondary"&gt;)

<pre><code>
        }
        <span></span>
    }
    <span></span>
</code></pre>

* the page closed automatically and a footer was added

#### Templates used
* app/views/navbar.php
* app/views/footer.php
