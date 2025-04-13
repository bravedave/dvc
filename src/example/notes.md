# Agent mode Chat

1. Install the dvc and create the application

```bash
composer req bravedave\dvc
php vendor/bin/dvc make::application
```

2. Create a module risorsa

```bash
php vendor/bin/dvc make::module risorsa
``` 
3. the run

```bash
php vendor/bin/dvc serve
```

4. The module is visble at http://localhost:[port]/risorsa

5. Create the structures using agent

```chat
read composer.json and look for psr-4 autoload references
this is a PHP framefork
create and populate class risorsa\dao\risorsa extending bravedave\dvc\dao
create an associated dto at risorsa\dao\dto\risorsa which extends bravedave\dvc\dto
and reference it in risorsa\dao\risorsa
```

6. Create the data structure

```chat

create a table risorsa using a definition file at risorsa\dao\db\risorsa

the fields are:
* created => datetime
* updated => datetime
* computer => varchar
* purchase_date => varchar
* computer_name => varchar
* cpu => varchar
* memory => varchar
* hdd => varchar
* os => varchar

see the tutorial at https://github.com/bravedave/dvc/blob/master/src/bravedave/dvc/views/docs/risorsa.md for more info
```

Concluding Agent mode is pretty awesome !