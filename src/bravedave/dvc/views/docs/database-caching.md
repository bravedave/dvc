# Caching

###### [Docs](/docs/) | [Databases](/docs/database) | Caching

Considerable speed advantages are offered by enabling caching, but to enable this means all updates need to be done through the DAO methods - particularly Update

Caching is facilitated using **APCu**, Interfaced through https://www.scrapbook.cash/

###### Description
* getByID - caches the object against a key created with the database-table-id
  * see **getByID** in _src/dao/_dao.php_
* UpdateByID - flushes the specific cache object - efficient
  * see **UpdateByID** in _src/dao/_dao.php_
* Update - flushes the entire cache - not efficient
  * see
    * **Update** in _src/dao/_dao.php_
    * **Update** in _src/dvc/db.php_


###### Installation
1. Install APCu
```bash
dnf install php-pecl-apcu
```

2. matthiasmullie/scrapbook
```bash
composer require matthiasmullie/scrapbook
```
