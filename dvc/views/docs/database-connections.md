# Databases

## Specify the Connections

#### SQLite
#### CONFIG File
*note : this is only required if you DO NOT have the db.json, or which to overide the settings*
- Location of file : [application]/app/dvc
- Name of File : config.php
```php
abstract class config extends _config {
	static $DB_TYPE = 'sqlite';

}
```

#### Automatic Generation
- The JSOn method maybe automated at the address [/install/db](/install/db), it creates a db.json in the [application]/data folder

#### JSON

- Location of file : [application]/data
- Name of File : db.json
- Sample File:
```json
{
	"db_type":"mysql",
	"db_host":"localhost",
	"db_name":"database name",
	"db_user":"user name",
	"db_pass":"password"
}
```

#### CONFIG File
*note : this is only required if you DO NOT have the db.json, or which to overide the settings*
- Location of file : [application]/app/dvc
- Name of File : config.php
- Sample File:
```php
	static $DB_TYPE = 'mysql';					// effectively activates the default sql system

	/**
	 * SQL Server database, username and passwords
	 **/
	static $DB_HOST = 'localhost';
	static $DB_NAME = 'database name';
	static $DB_USER = 'user name';
	static $DB_PASS = 'password';
```
