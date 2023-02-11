# dbResult - the Database Result Class

###### [Docs](/docs/) | [Databases](/docs/database) | dbResult

The most common use of the dbResult class is from within the DAO objects where it is avaliable by direct reference to the ```$this``` object

```php
namespace dao;

class users extends _dao {
    function getAll() {
        return $this->Result( 'SELECT * FROM `users`);

    }

}
```

The result can then be iterated in a while loop

```php
class users extends \Controller {
    function index() {
        $dao = new dao\users;
        if ( $res = $dao->getAll()) {
            while ( $dto = $res->dto()) {
                /**
                 * the dto is efectively a MySQL fetch_object style object
                 * */
                printf( '%s<br />', $dto->name);

            }

        }

    }

}
```