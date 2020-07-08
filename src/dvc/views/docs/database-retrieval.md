# Data Retrieval - Queries

###### [Docs](/docs/) | [Databases](/docs/database) | Data Retrieval - Queries

Normally work is carried out in a controller or in a DAO - data access object.
From both points there are a number of methods of retrieving data.

##### Connected
If configured, you are connected to the default database and the connection is
 available at ```$this->db```

##### Basic
If you are connected to a datasource, using $this->db->Q will return a
MySQL/SQLite result which can then be iterated.
```php
class contacts extends Controller {
	function listall() {
		if ( $res = $this->db->Q('SELECT * FROM contacts')) {
			while ( $row = $res->fetch()) {	// wrapper "fetch" works with both MySQL and SQLite
				// do something

			}

		}

	}

}
```

##### Result Class
The Result class offers more services, including advanced sets of data
which can be manipulated inline for reporting or data compilation.

This class can also return general or specific DTO objects.

##### Specific DTO Objects
In general - a DTO object is equivalent to a MySQL's fetch_object data structure,
this can be made more specific by including a template in the _app\dao\dto_ folder

##### dtoSet
The result class can return all the rows of a given query in an array of dtos.
```php
class contacts extends Controller {
	function listall() {
		if ( $res = $this->dbResult('SELECT * FROM contacts')) {
			while ( $dto = $res->dto()) {
				// do something

			}

			// or
			$dtos = $res->dtoSet();
			foreach ( $dtos as $dto) {
				// do something

			}

		}

	}

}
```

##### dtoSet advanced
The dtoSet is simply iterates the result and stores it in an array,
further manipulation is possible by passing a replacement function.
```php
class contacts extends Controller {
	function listall() {
		if ( $res = $this->Result('SELECT * FROM contacts')) {
			$dtos = $res->dtoSet( function( $dto) {

				$dto->timeProcessed = time();	// add to the data

				return $dto;	// to be sure, otherwise you will have no data !

			});

			foreach ( $dtos as $dto) {
				// do something

			}

		}

	}

}
```
