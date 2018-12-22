# Structure - Data (Model) View Controller

### Structure - Data (Model)

#### The Modelling

The Data model is the layer between the database (SQL Server) and the controller

This software uses a DAO / DTO model

<img class="pull-right img-thumbnail" src="/images/dao-dto-path.png" />

##### DAO - Data Access Object
- NameSpace : dao
- Are located in [root]app/dao

##### DTO - Data Transition Object
- NameSpace : dao\dto
- Are located in [root]app/dao/dto

##### Terms

<table class="table table-striped">
	<thead>
		<tr>
			<td>DVC Term</td>
			<td>Equivalent Object</td>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>dto</td>
			<td>SQL Row Object (fetch_object)</td>
		</tr>
		<tr>
			<td>dtoSet</td>
			<td>Array of dto's</td>
		</tr>
		<tr>
			<td>result</td>
			<td>SQL Result</td>
		</tr>
	</tbody>

</table>

#### How to Use
* DAO - Required
* DTO - Optional
   * If the dto requires no customization, there is no requirement to create a dto file

1. Create a folder for DAO files under the [root]app folder i.e. [root]app/dao
2. Create a DAO file

#### Example DAO
```
<?php
NameSpace dao;

class users extends _dao {
	protected $_db_name = 'users';

}
```

#### Example DAO Use
```
$dao = new dao\users;
if ( $res = $dao->getAll()) {
	while ( $dto = $res->dto()) {
		printf( '%s<br />', $dto->name);

	}

}
```
