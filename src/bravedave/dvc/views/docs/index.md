# DVC - Dave's View Controller Framework

DVC is a lightweight PHP framework designed for rapid web development with sensible defaults and minimal configuration.

## Quick Start

1. **Create your ```composer.json```**

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

2. **Installation**:

   ```bash
   composer u
   ```

3. **Create Application Structure**:

   ```bash
   vendor/bin/dvc make::application
   ```
   This creates:
   - `src/app/application.php` (main application file)
   - Default folder structure structure

4. **Run Your Application**:

   ```bash
   vendor/bin/dvc serve
   ```

## Default Structure

```
src/
├── app/
│   └── application.php  # Main application file
└── controller/
    ├── home.php    # Default controller
    └── ...         # Additional controllers
```

## Key Concept

### Controller Routing
DVC uses a simple, convention-based routing system:

- URL Path: `/products`
- Maps to: `src/controller/products.php`

Example controller (`src/controller/products.php`):
```php
<?php
class products extends bravedave\dvc\controller {

    // _index is the default view
    protected function _index() {

        $this->data = (object)[
            'title' => $this->title = config::label,
        ];

        $this->renderBS5([
            'aside' => fn() => $this->load('blank'),
            'main' => fn() => printf('i am %s', __CLASSNAME__ )
        ]);
    }
}
```

## Next Steps
1. [Modules & Controllers](modules-and-controllers.md) - Deep dive into creating and organizing modules
2. [Database Strategy](database.md)
3. [View Templating](views.md) - Advanced rendering with `renderBS5()` and layouts
4. [Request Handling](requests.md) - POST/GET separation and API responses
5. [Tutorial](risorsa.md) - a simple tutorial