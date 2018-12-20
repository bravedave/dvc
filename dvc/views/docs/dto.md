# DTO

The Result class - dvc\dbResult can return general or specific dto objects.

#### Specific DTO Objects
A general dto object is equivalent to a MySQL's fetch_object data structure,
this can be made more specific by including a template in the [application]/app/dao/dto folder

```
namespace dao\dto;

class mailfolder extends _dto {
	public $name = '';
	public $fullname = '';
	public $subFolders = FALSE;
	public $type = 0;
	public $delimiter = '.';

}

```
