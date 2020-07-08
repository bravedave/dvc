# DTO - Data Transition Object

###### [Docs](/docs/) | [Databases](/docs/database) | DTO

### Data Transition Object
- DAO - Data Access Objects are Intelligent
- DTO - Data Transition Objects are Dumb

_Use a DAO to maniupulate and enrich a DTO_

The Result class - dvc\dbResult can return general or specific dto objects.

#### Specific DTO Objects
A general dto object is equivalent to a MySQL's fetch_object data structure,
this can be made more specific by including a template in the [application]/app/dao/dto folder

```php
namespace dao\dto;

class mailfolder extends _dto {
	public $name = '';
	public $fullname = '';
	public $subFolders = FALSE;
	public $type = 0;
	public $delimiter = '.';

}
```

#### See Also
- [dbResult Class](database-result)
