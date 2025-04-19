# DVC - Data View Controller

> PHP framework for web applications and APIs.
Configured with Bootstrap, but could just as easily support others.

* <https://brayworth.com/docs/>

## Getting Started

### Install

```sh
mkdir newapp
cd newapp
```

#### create a composer.json

The application relies on the composer autoload features,
 this (very) basic composer.json file tells the autloader where to look
 for this application and installs *bravedave/dvc*

*composer.json*

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

#### install bravedave/dvc and update runtime autoloader

```sh
composer u
```

### Create an basic application

#### create a application folder

*note: this is the same location used in the composer.json file*

```sh
mkdir -p src/app
```

#### add an application file

*file src/app/application.php*

```php
<?php

class application extends bravedave\dvc\application {

  static public function startDir() {

    return dirname(__DIR__);
  }
}
```

#### create a documentroot

* *this creates a documentroot and copies in a fallback file*

```sh
cp -R vendor/bravedave/dvc/tests/www src
```

#### system will run

```sh
php -S localhost:8000 src/www/_mvp.php
```

available at <http://localhost:8000>

there is a tutorial [here](src/bravedave/dvc/views/docs/risorsa.md)
