# Controllers in DVC

Controllers handle your application logic and connect routes to views. DVC provides a structured way to organize controllers using modules.

## Creating a Module

1. **Generate module structure**:

   ```bash
   vendor/bin/dvc make::module example
   ```

   This creates:

   ```code
   src/
   ├── app/
   │   └── example/          # Module directory
   │       └── views/        # Module-specific views
   └── controller/
       └── example.php       # Module controller
   ```

2. **Resulting Controller Structure** (`src/controller/example.php`):

   ```php
   <?php
   namespace example;
   
   use bravedave\dvc\{
       controller as dvcController,
       ServerRequest
   };
   
   class controller extends dvcController {
       
       protected function _index() {
           $this->data = (object)[
               'title' => $this->title = config::label,
           ];
           
           $this->renderBS5([
               'aside' => fn() => $this->load('blank'),
               'main' => fn() => $this->load('index')
           ]);
       }
       
       protected function before() {
           parent::before();
           $this->viewPath[] = __DIR__ . '/views/';
       }
       
       protected function postHandler() {
           $request = (new ServerRequest);
           $action = $request('action');
           return match ($action) {
               default => parent::postHandler()
           };
       }
   }
   ```

## Key Features

### 1. Request Handling

- **GET Requests**: Automatically routed to `_index()` method
- **POST Requests**: Handled by `postHandler()`

### 2. View Rendering

- Loads view templates from `src/app/example/views/`

```php
$this->renderBS5([
    'aside' => fn() => $this->load('blank'),
    'main' => fn() => $this->load('index')
]);
```

### 3. View Structure

Create this file:

```php
<?php
// src/app/example/views/index.php

namespace app\example;
?>
<div class="container">
  <h1><?= $title ?></h1>
  <p>Welcome to the example module!</p>
</div>
```

## Accessing Your Module

1. **Start the development server**:

   ```bash
   vendor/bin/dvc serve
   ```

2. **Visit in browser**:

   ```code
   http://localhost:8000/example
   ```

## Advanced Usage

### Handling POST Requests

```php
protected function postHandler() {
    $request = (new ServerRequest);
    return match ($request('action')) {
        'save' => $this->saveData($request),
        'delete' => $this->deleteRecord($request),
        default => parent::postHandler()
    };
}
```

### Adding Custom Routes

Extend the `_index()` method with additional actions:

```php
public function detail($id) : void {
    $this->data = (object)[
        'item' => (object)[
            'id' => 10,
            'description' => 'toy train'
        ]
    ];

    $this->load('detail');
}
```

Access via: `/example/detail/123`
