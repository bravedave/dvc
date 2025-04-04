# DAO/DTO

[Docs](.) | [Databases](database) | DAO/DTO

DAO/DTO is a traditional abstract method which uses Data Access Objects to
create Data Transition Objects.

DVC's DAO allow flexibility to use SQLite and MySQL with very little change
to code, most file structuring is automatic and does not require GUI tools
such as MySQL Workbench - a useful tool for visualization, but not required
for administration;

## Namespace

* dao
  * [dto](database-dto)
  * db

### Dao Namespace

Each table in the database is represented by a dao object, it is important to use
 this object to access the data because, as the application scales you may want to
 explore memory caching to speed the application up.

This application natively supports the APC caching and would be easily extended
 to others. using the built in dao method facilitates the automatic flushing of
 objects.

## Common Methods

### delete

deletes a record from the table

```php
$id = 1;

$dao = new dao\contacts;
$dao->delete( $id);
```

### escape

calls the upstream mysqli->real_escape_string or SQLite->escapeString

### getAll

returns all the records in the table, optionally:
> returns : dbResult

* Parameter 1 : comma separated list of fields
* Parameter 2 : order statement

    $dao = new dao\contacts;
    if ( $res = $dao->getAll( 'id, name', 'ORDER BY name ASC')) {
      while ( $row = $res->fetch()) {
        // do something with associative array (ala mysql->mysqli_fetch_assoc)
      }
    };

### Insert

Inserts an associative array into a table
> returns : $id of inserted record

```php
$dao = new dao\contacts;
$dao->Insert([
  'name' => 'John Citizen',
  'email' => 'john@example.com',
  ]);
```

### Update

Update a table record using an associative array
> returns : $id of inserted record

* Parameter 1 : associative array of field => value
* Parameter 2 : condition for update

```php
$dao = new dao\contacts;
$dao->Update([
  'name' => 'John Citizen',
  'email' => 'john@example.com',
  ],
  'WHERE id = 1'
  );
```
