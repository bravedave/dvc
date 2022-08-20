###### [Docs](/docs/) | [Utilities](/docs/utilities) | [PHP](/docs/utilities_php) | Request

<table class="table">
    <tbody>
        <tr>
            <td>description</td>
            <td>Request Class to intepret a request to the server</td>

        </tr>

        <tr>
            <td>type</td>
            <td>php</td>

        </tr>

        <tr>
            <td>source</td>
            <td>src\dvc\core\Request.php</td>

        </tr>

    </tbody>

</table>

#### Usage
```php
    if ( dvc\core\Request::get()->isPost()) {
        // everything must be post ...

    };
```

#### Comments
This class is largely wrapped into the Controller class, and utilized heavily by the Application class, as such normally you use this within the controller as:
```php
class property extends \Controller {
    function view() {
        if ( $id = (int)$this->getParam( 'id')) {
            printf( 'cool, so you want to view property #%d', $id);

        }
        else {
            printf 'say, what are you doing here ?';

        }

    }

}
```

#### Functions (public)
* DNT()
* ReWriteBase()
* setReWriteBase( $htaccess)
* setControllerName($controllerName)
* getControllerName()
* setActionName( string $actionName)
* getActionName()
* getSegment( $index)
* getSegments()
* getPost( $name = '', $default = false )
* getQuery( $name = '')
* getUri()
* getUrl()
* getReferer()
* getRemoteIP()
* getServer( $name)
* getServerName()
* getServerIP()
* getSubNet( $ip)
* ServerIsLocal()
* ClientIsLocal()
* DocumentRoot()
* isPost()
* isGet()
* toArray()
* getParam( $name = '', $default = false)
* fileUpload( $path, $accept = null)
