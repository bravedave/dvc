# Databases

## Specify the Connections
### JSON

- Location of file : [application]/data
- Name of File : db.json
- Sample File:
<pre><code>
{
	"db_type":"mysql",
	"db_host":"localhost",
	"db_name":"database name",
	"db_user":"user name",
	"db_pass":"password"
}
</code></pre>

### CONFIG File

- Location of file : [application]/app/dvc
- Name of File : config.php
- Sample File:
<pre><code>
	static $DB_TYPE = 'mysql';					// effectively activates the default sql system

	/**
	 * SQL Server database, username and passwords
	 **/
	static $DB_HOST = 'localhost';
	static $DB_NAME = 'database name';
	static $DB_USER = 'user name';
	static $DB_PASS = 'password';
</code></pre>
