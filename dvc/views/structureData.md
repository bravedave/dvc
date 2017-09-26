### Structure - Data (Model) View Controller

# Structure - Data (Model)

### The Modelling

The Data model is the interface between the database (SQL Server) and the controller

This software uses a DAO / DTO model
- DAO - Data Access Object
- DTO - Data Transition Object

#### DAO
- NameSpace : dao
- Are located in [root]app/dao

#### DTO
- NameSpace : dao\dto
- Are located in [root]app/dao/dto

### Terms

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

### Examples
```
$dao = new dao\users;
if ( $res = $dao->getAll()) {
	while ( $dto = $res->dto()) {
		printf( '%s<br />', $dto->name);

	}

}
```