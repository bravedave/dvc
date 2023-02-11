# Getting Started

## Install

[ -d tdvc ] || mkdir tdvc
composer req --working-dir=tdvc bravedave/dvc "*"

cd tdvc

## Create an basic application

mkdir -p application/app

*file application/app/application*
```php
<?php

class application extends bravedave\dvc\application {

  static public function startDir() {

    return dirname(__DIR__);
  }
}
```

### add an autoload section to composer

*composer now looks like*
```json
{
  "require": {
    "bravedave/dvc": "*"
  },
  "autoload": {
    "psr-4": {
      "": "application/app/"
    }
  }
}
```

### update runtime autoloader
composer config autoload "" "application/app"

## Create a documentroot

cp -R vendor/bravedave/dvc/tests/www/ www

php -S localhost:8080 application/www/_mvp.php

## System will run

available at http://localhost:8080

