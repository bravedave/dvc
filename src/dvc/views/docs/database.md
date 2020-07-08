# Databases

###### [Docs](/docs/) | Databases

MySQL and SQLite data connections.

There are a range if support structures which including query, insert, update,
create and maintenance. All fully encapsulated including escaping of values.

- [Connect the Database](database-connections.md)
- [Retrieve Data](database-retrieval.md)
- [DAO/DTO](database-dao-dto.md)

_Data Access Objects manipulate Data Tranition Objects_

###### DAO - Data Access Object
_A Data Access Object is an Intelligent Interface to a Data Layer_

| Element    | Description      |
| :--        | :--              |
| NameSpace  | dao              |
| Root Class | _dao             |
| src        | src/dao/_dao.php |


###### DAO - example
```php
namespace dao;

class users extends _dao {
	protected $_db_name = 'users';

}
```

###### DTO - Data Transition Object
_A Data Transition Object is an unIntelligent Data Structure, it must be serializable and transportable. It is not desirable to subclasses, as that will threaten the portability of the element_

| Element    | Description          |
| :--        | :--                  |
| NameSpace  | dao                  |
| Root Class | _dao                 |
| src        | src/dao/dto/_dto.php |


###### DTO - Example of Use
```php
$dao = new dao\users;
if ( $res = $dao->getAll()) {
	while ( $dto = $res->dto()) {
		printf( '%s<br />', $dto->name);

	}

}
```
