# Databases

## Specify the Connections

#### SQLite
If you only require SQlite:
create [application]/data/defaults.json as follows:
```json
{
	"db_type" : "sqlite"
}
```

_[application]/data/db.json is also valid for these settings_


#### Automatic Generation
- The JSOn method maybe automated at the address [server]/install/db, it creates a db.json in the [application]/data folder
  - e.g. http://localhost/install/db

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
