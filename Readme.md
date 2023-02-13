# DVC - Data View Controller

> PHP framework for web applications and APIs.
Configured with Bootstrap, but could just as easily support others.

* <https://brayworth.com/docs/>

# Getting Started

## Install

```sh
[ -d tdvc ] || mkdir tdvc
composer req --working-dir=tdvc bravedave/dvc "*"

cd tdvc
```

*you might like to start working in your ide now, open the folder in VSCode*

## Create an basic application

### Create a application folder
```sh
mkdir -p src/app
```

### Add an application file
*file src/app/application.php*
```php
<?php

class application extends bravedave\dvc\application {

  static public function startDir() {

    return dirname(__DIR__);
  }
}
```

### add an autoload section to composer

The application relies on the composer autoload features,
 this entry in composer.json tells the autloader where to look
 for this application

*composer now looks like*
```json
{
  "require": {
    "bravedave/dvc": "*"
  },
  "autoload": {
    "psr-4": {
      "": "src/app/"
    }
  }
}
```

### update runtime autoloader

```sh
composer u
```

## Create a documentroot

*this creates a documentroot and copies in a fallback file*

```sh
cp -R vendor/bravedave/dvc/tests/www application/www
```

## System will run

```sh
php -S localhost:8080 application/www/_mvp.php
```

available at http://localhost:8080

