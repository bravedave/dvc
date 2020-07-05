###### [Docs](/docs/) | [Components](/docs/component) | Pages

```php
<?php
    class hello extends Controller {
        function index() {
            $p = new dvc\pages\bootstrap;
            $p
                ->header()
                ->title();

            $p->primary();
                print 'hello world';

            $p->secondary();
                print 'secondary';
```

* php classes have a ```__destruct``` method, the _page_ class __destruct method calls ```$p->close();``` method during destruction closing the html and appending a page footer

By default the page structure uses bootstrap 4 to create 4 main areas
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

#### Templates used
* app/views/navbar-default.php
* app/views/footer.php
