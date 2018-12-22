# Data Retrieval - Queries

Normally work is carried out in a controller or in a DAO - data access object.
From both points there are a number of methods of retrieving data.

### Connected
If configured, you are connected to the default database and the connection is
 available at ```$this->db``` - it can be assumed (unless the data source is
 not being available).

### Basic
As close to the datasource as exists, using $this->db->Q will return a
MySQL/SQLite result which can then be iterated.
```
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

### Result Class
The Result class offers more services, including advanced sets of data
which can be manipulated inline for reporting or data compilation.

This class can also return general or specific dto objects.
#### Specific DTO Objects
A general dto object is equivalent to a MySQL's fetch_object data structure,
this can be made more specific by including a template in the app\dao\dto folder

#### dtoSet
The result class can return all the rows of a given query in an array of dtos.
```
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

#### dtoSet advanced
The dtoSet is simply iterates the result and stores it in an array,
further manipulation is possible by passing a replacement function.
```
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
