# DVC Framework - Module Development Guide

## Project Overview

This project uses the **BraveDave DVC Framework**, a lightweight PHP MVC framework with modular architecture. All modules follow a consistent pattern for controllers, data access objects (DAO), data transfer objects (DTO), handlers, and views.

**Key Principles:**
- **Modular Architecture**: Each feature is a self-contained module with its own namespace
- **DAO/DTO Pattern**: All data handling must go through Data Access Objects and Data Transfer Objects
- **POST Handler Routing**: Updates are posted via dedicated handler classes with static methods
- **Auto-Migration**: Database schemas are declarative and auto-migrate
- **Type Safety**: DTOs provide typed data containers with IDE autocomplete support

## Module Structure Template

Every module follows this standardized structure:

```
src/app/{module-name}/
├── config.php                    # Module configuration & DB version
├── controller.php                # Routes GET requests, defines POST routing
├── handler.php                   # Processes POST requests (business logic)
├── dao/
│   ├── dbinfo.php               # Database version maintenance
│   ├── {entity}.php             # Data Access Object (one per entity)
│   ├── dto/
│   │   └── {entity}.php         # Data Transfer Object (matches DAO)
│   └── db/
│       └── {entity}.php         # Database schema definition
└── views/
    ├── index.php                # Sidebar/navigation view
    ├── matrix.php               # Main data grid/list view
    └── edit.php                 # Modal form for create/edit
```

## 1. Module Configuration (`config.php`)

**Purpose**: Extends root config, defines version constant, ensures database is current.

```php
<?php
namespace {module};

use config as rootConfig;

class config extends rootConfig {
  const {module}_db_version = 1;
  const label = '{Module Display Name}';

  static function {module}_checkdatabase() {
    $dao = new dao\dbinfo;
    $dao->checkVersion('{module}', self::{module}_db_version);
  }
}
```

**Key Points:**
- Always extend the application's root `config` class
- Version constant format: `{module}_db_version`
- Database check method called before each request via `before()` hook
- Increment version to trigger schema migrations

**Example from `src/app/todo/config.php`:**

```php
namespace todo;

use config as rootConfig;

class config extends rootConfig {
  const todo_db_version = 1;
  const label = 'Todo';

  static function todo_checkdatabase() {
    $dao = new dao\dbinfo;
    $dao->checkVersion('todo', self::todo_db_version);
  }
}
```

## 2. Controller (`controller.php`)

**Purpose**: Routes GET requests to views, POST requests to handlers via `postHandler()`.

### Controller Structure

```php
<?php
namespace {module};

use bravedave\dvc\{ controller as dvcController, ServerRequest};

class controller extends dvcController {

  // Default index page - protected method handles GET /module
  protected function _index() {
    $this->data = (object)[
      'title' => $this->title = config::label,
    ];

    $this->renderBS5([
      'aside' => fn() => $this->load('index'),
      'main' => fn() => $this->load('matrix')
    ]);
  }

  // Lifecycle hook - runs before every request
  protected function before() {
    config::{module}_checkdatabase();    // Ensure DB is current
    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';  // Register view directory
  }

  // Routes all POST requests based on 'action' parameter
  protected function postHandler() {
    $request = new ServerRequest;
    $action = $request('action');

    return match ($action) {
      '{entity}-save' => handler::{entity}Save($request),
      '{entity}-delete' => handler::{entity}Delete($request),
      'get-by-id' => handler::{entity}GetByID($request),
      'get-matrix' => handler::{entity}GetMatrix($request),
      default => parent::postHandler()
    };
  }

  // Public method for GET /module/edit/{id}
  public function edit($id = 0) {
    $this->data = (object)[
      'title' => $this->title = config::label,
      'dto' => new dao\dto\{entity}  // Empty template for new records
    ];

    if ($id = (int)$id) {
      $dao = new dao\{entity};
      $this->data->dto = $dao->getByID($id);  // Load existing record
      $this->data->title .= ' edit';
    }

    $this->load('edit');
  }
}
```

### Key Controller Patterns

**1. Protected `_index()` Method**
- Handles GET requests to base route (e.g., `/todo`)
- Prepares `$this->data` object with data for views
- Uses `renderBS5()` for Bootstrap 5 layout with aside/main sections

**2. `before()` Lifecycle Hook**
- Runs before every request (GET and POST)
- Check database version/schema
- Register module's view directory
- Always call `parent::before()` to maintain framework lifecycle

**3. `postHandler()` Routing**
- Routes ALL POST requests using PHP 8+ match expression
- Reads `action` parameter from POST data via `ServerRequest`
- Delegates to static handler methods
- Falls back to parent handler for framework actions

**4. Public Methods for GET Routes**
- Example: `public function edit($id = 0)` handles `/module/edit/5`
- Prepare data, load views
- Type cast parameters: `$id = (int)$id`

**5. View Rendering**
```php
$this->load('view-name');              // Loads from module's views/
$this->renderBS5([...]);               // Full page render with layout
```

**Example from `src/app/todo/controller.php`:**

```php
namespace todo;

use bravedave\dvc\{ controller as dvcController, ServerRequest};

class controller extends dvcController {

  protected function _index() {
    $this->data = (object)[
      'title' => $this->title = config::label,
    ];

    $this->renderBS5([
      'aside' => fn() => $this->load('index'),
      'main' => fn() => $this->load('matrix')
    ]);
  }

  protected function before() {
    config::todo_checkdatabase();
    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';
  }

  protected function postHandler() {
    $request = new ServerRequest;
    $action = $request('action');

    return match ($action) {
      'todo-delete' => handler::todoDelete($request),
      'get-by-id' => handler::todoGetByID($request),
      'get-matrix' => handler::todoGetMatrix($request),
      'todo-save' => handler::todoSave($request),
      default => parent::postHandler()
    };
  }

  public function edit($id = 0) {
    $this->data = (object)[
      'title' => $this->title = config::label,
      'dto' => new dao\dto\todo
    ];

    if ($id = (int)$id) {
      $dao = new dao\todo;
      $this->data->dto = $dao->getByID($id);
      $this->data->title .= ' edit';
    }

    $this->load('edit');
  }
}
```

## 3. Handler (`handler.php`)

**Purpose**: Processes POST requests, contains business logic, returns JSON responses. All data operations must go through DAOs.

### Handler Structure

```php
<?php
namespace {module};

use bravedave\dvc\{ServerRequest, json};

final class handler {

  public static function {entity}Save(ServerRequest $request): json {
    $action = $request('action');

    // Extract and validate data
    $a = [
      'field1' => $request('field1'),
      'field2' => $request('field2'),
    ];

    $dao = new dao\{entity};

    if ($id = (int)$request('id')) {
      // Update existing record
      $dao->UpdateByID($a, $id);
    } else {
      // Insert new record
      $dao->Insert($a);
    }

    return json::ack($action);
  }

  public static function {entity}Delete(ServerRequest $request): json {
    $action = $request('action');

    if ($id = (int)$request('id')) {
      (new dao\{entity})->delete($id);
      return json::ack($action);
    }

    return json::nak($action);
  }

  public static function {entity}GetByID(ServerRequest $request): json {
    $action = $request('action');

    if ($id = (int)$request('id')) {
      if ($dto = (new dao\{entity})->getByID($id)) {
        return json::ack($action, $dto);
      }
    }

    return json::nak($action);
  }

  public static function {entity}GetMatrix(ServerRequest $request): json {
    $action = $request('action');
    return json::ack($action, (new dao\{entity})->getMatrix());
  }
}
```

### Key Handler Patterns

**1. Final Class**
- `final class handler` - Cannot be extended (design decision)
- All handler classes should be final

**2. Static Methods**
- Handler methods are stateless utilities
- Type hint: `public static function methodName(ServerRequest $request): json`
- Each action gets its own method

**3. ServerRequest Access**
```php
$request = new ServerRequest;
$value = $request('field_name');              // Get POST data
$value = $request->getQueryParam('field');    // Get GET parameter
```

**4. Type Casting for Security**
```php
$id = (int)$request('id');           // Always cast IDs to int
$name = trim($request('name'));      // Sanitize strings
```

**5. JSON Response Pattern**
```php
return json::ack($action);              // Success without data
return json::ack($action, $data);       // Success with data payload
return json::nak($action);              // Failure/error
```

**6. DAO Instantiation**
```php
$dao = new dao\{entity};                // Create new instance
$dto = $dao->getByID($id);              // Returns DTO or null
$dao->Insert($array);                   // Returns new ID
$dao->UpdateByID($array, $id);          // Returns rows affected
$dao->delete($id);                      // Deletes record
```

**Example from `src/app/todo/handler.php`:**

```php
namespace todo;

use bravedave\dvc\{ServerRequest, json};

final class handler {

  public static function todoSave(ServerRequest $request): json {
    $action = $request('action');
    $a = [
      'name' => $request('name'),
      'description' => $request('description'),
    ];

    $dao = new dao\todo;
    if ($id = (int)$request('id')) {
      $dao->UpdateByID($a, $id);
    } else {
      $dao->Insert($a);
    }

    return json::ack($action);
  }

  public static function todoDelete(ServerRequest $request): json {
    $action = $request('action');
    if ($id = (int)$request('id')) {
      (new dao\todo)->delete($id);
      return json::ack($action);
    }
    return json::ack($action);
  }

  public static function todoGetByID(ServerRequest $request): json {
    $action = $request('action');
    if ($id = (int)$request('id')) {
      if ($dto = (new dao\todo)->getByID($id)) {
        return json::ack($action, $dto);
      }
    }
    return json::nak($action);
  }

  public static function todoGetMatrix(ServerRequest $request): json {
    $action = $request('action');
    return json::ack($action, (new dao\todo)->getMatrix());
  }
}
```

## 4. Data Access Object (`dao/{entity}.php`)

**Purpose**: Abstracts all database operations. ALL data handling must go through DAOs - never write raw SQL in controllers or handlers.

### DAO Structure

```php
<?php
namespace {module}\dao;

use bravedave\dvc\{dao, dtoSet};

class {entity} extends dao {
  protected $_db_name = '{table_name}';      // Database table name
  protected $template = dto\{entity}::class; // DTO class for results

  // Custom query returning multiple records
  public function getMatrix() : array {
    return (new dtoSet)('SELECT * FROM `{table_name}`');
  }

  // Override Insert to add timestamps
  public function Insert($a) {
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  // Override UpdateByID to update timestamp
  public function UpdateByID($a, $id) {
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }

  // Custom business logic methods
  public function getActiveItems() : array {
    $sql = 'SELECT * FROM `{table_name}` WHERE `active` = 1 ORDER BY `name`';
    return (new dtoSet)($sql);
  }
}
```

### Inherited DAO Base Methods

The framework's base `dao` class provides these methods automatically:

```php
// Retrieve single record
$dto = $dao->getByID($id);                    // Returns DTO or null

// Retrieve multiple records
$dtos = $dao->getAll($fields = '*', $order = '');  // Returns array of DTOs

// Insert new record
$newId = $dao->Insert($array);                // Returns new ID

// Update existing record
$rows = $dao->UpdateByID($array, $id);        // Returns rows affected

// Delete record
$dao->delete($id);                            // Returns boolean

// Count records
$count = $dao->count();                       // Returns int
```

### Key DAO Patterns

**1. Required Properties**
```php
protected $_db_name = 'table_name';           // Maps to database table
protected $template = dto\{entity}::class;    // Links to DTO for typed results
```

**2. dtoSet for Multiple Records**

`dtoSet` is invoked as a callable object that returns an array of DTOs:

```php
// Basic usage - query must be properly escaped
return (new dtoSet)('SELECT * FROM `table`');

// With sprintf for integers (safe)
return (new dtoSet)(sprintf('SELECT * FROM `table` WHERE `id` = %d', $id));

// With quote() for strings (use in DAOs)
$sql = sprintf('SELECT * FROM `table` WHERE `name` = %s', $this->quote($string));
return (new dtoSet)($sql);

// With filter function (second parameter)
return (new dtoSet)($sql, function($dto) {
  // Return $dto to include, null to exclude
  return $dto->active ? $dto : null;
});

// With custom DTO template (third parameter)
return (new dtoSet)($sql, null, dto\custom::class);
```

**dtoSet Parameters:**
1. `$sql` (string): SQL query - must be properly escaped
2. `$filter` (callable|null): Optional function to filter/transform each DTO
3. `$template` (string|null): Optional DTO class, overrides DAO's $template property

**String Quoting in DAOs:**
```php
// Use $this->quote() for string values in SQL
$name = $this->quote($userInput);
$sql = sprintf('SELECT * FROM `table` WHERE `name` = %s', $name);

// Integers can use %d directly
$sql = sprintf('SELECT * FROM `table` WHERE `id` = %d', $id);
```

**3. Timestamp Management**
```php
$a['created'] = self::dbTimeStamp();          // MySQL-compatible timestamp
$a['updated'] = self::dbTimeStamp();
```

**4. Parameterized Queries** (when using base dao methods)
- Framework automatically prepares statements
- Use `?` placeholders for parameters
- Pass parameters as array to methods like `db()->q()` or `db()->fetch()`

**5. DAO Invocation and getRichData()**

DAOs can be invoked as functions to retrieve and optionally enrich a single record:

```php
// Using DAO as callable - automatically calls getRichData if present
$dao = new dao\{entity};
$dto = $dao($id);  // Same as $dao->getByID($id) but with enrichment

// Manual call to getRichData
$dto = $dao->getByID($id);
if (method_exists($dao, 'getRichData')) {
  $dto = $dao->getRichData($dto);
}
```

**getRichData() Method:**

Optional method for DTO enrichment with additional lookups or calculated fields:

```php
public function getRichData(dto $dto): ?dto {
  // Perform additional lookups
  $userDao = new \user\dao\user;
  $dto->user_name = $userDao->getFieldByID($dto->user_id, 'name');

  // Add calculated fields
  $dto->days_old = (time() - strtotime($dto->created)) / 86400;

  // Add related data
  $dto->comments_count = (new \comment\dao\comment)->countByPost($dto->id);

  return $dto;
}
```

**When to use getRichData():**
- ✅ Single record views where additional context is needed
- ✅ API endpoints returning detailed single records
- ✅ Edit forms needing related data for dropdowns
- ❌ List/matrix views (performance impact on multiple records)
- ❌ High-frequency API calls (additional query overhead)
- ❌ When base DTO data is sufficient

**Performance Note:** getRichData adds database queries and processing time. Use judiciously and only when enrichment justifies the performance cost.

**Example from `src/app/todo/dao/todo.php`:**

```php
namespace todo\dao;

use bravedave\dvc\{dao, dtoSet};

class todo extends dao {
  protected $_db_name = 'todo';
  protected $template = dto\todo::class;

  public function getMatrix() : array {
    return (new dtoSet)('SELECT * FROM `todo`');
  }

  public function Insert($a) {
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
```

## 5. Data Transfer Object (`dao/dto/{entity}.php`)

**Purpose**: Typed container for database records. Provides type safety, IDE autocomplete, and consistent data structure.

### DTO Structure

```php
<?php
namespace {module}\dao\dto;

use bravedave\dvc\dto;

class {entity} extends dto {
  public $id = 0;
  public $created = '';
  public $updated = '';

  // Entity-specific fields with default values
  public $name = '';
  public $description = '';
  public $status = '';
  public $active = 0;
}
```

### Key DTO Patterns

**1. Plain Data Container**
- No business logic in DTOs
- Only public properties with default values
- Default values define type expectations

**2. Required Standard Fields**
```php
public $id = 0;           // Primary key (always added by framework)
public $created = '';     // Timestamp when record created
public $updated = '';     // Timestamp when record updated
```

**3. Type Hints via Defaults**
```php
public $count = 0;        // Integer expected
public $name = '';        // String expected
public $price = 0.0;      // Float expected
public $items = [];       // Array expected
```

**4. Extends Framework DTO**
- Inherits `JsonSerializable` interface
- Auto-converts to JSON in responses
- Provides utility methods for data manipulation

**5. Usage Patterns**
```php
// Empty template for new records
$dto = new dao\dto\{entity};

// From database via DAO
$dto = $dao->getByID($id);

// Manual creation
$dto = new dto\{entity};
$dto->name = 'Example';
$dto->status = 'active';

// Array to DTO
$dto = dao\dto\{entity}::from($array);
```

**Example from `src/app/todo/dao/dto/todo.php`:**

```php
namespace todo\dao\dto;

use bravedave\dvc\dto;

class todo extends dto {
  public $id = 0;
  public $created = '';
  public $updated = '';
  public $name = '';
  public $description = '';
}
```

## 6. Database Schema (`dao/db/{entity}.php`)

**Purpose**: Declarative schema definition for auto-migration. Schema changes are applied automatically when version increments.

### Schema Structure

```php
<?php
/**
 * Database Schema for {entity}
 *
 * Notes:
 * - Primary key 'id' (autoincrement) is added automatically - DO NOT define it
 * - Field types are MySQL format, converted to SQLite equivalents as needed
 * - Schema is checked/updated automatically via dbinfo::checkDIR()
 */

$dbc = \sys::dbCheck('{table_name}');

// Standard timestamp fields (always include)
$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');

// Entity-specific fields
$dbc->defineField('name', 'varchar');
$dbc->defineField('description', 'text');
$dbc->defineField('status', 'varchar', null, null, 'pending');
$dbc->defineField('active', 'tinyint', null, null, 1);
$dbc->defineField('sort_order', 'int', null, null, 0);

// Indexes for performance
$dbc->defineIndex('idx_name', ['name']);
$dbc->defineIndex('idx_status_active', ['status', 'active']);

// Execute schema check/migration
$dbc->check();

### Schema Field Types

**String Types:**
```php
$dbc->defineField('name', 'varchar');              // VARCHAR(255)
$dbc->defineField('email', 'varchar', ['length' => 100]);
$dbc->defineField('description', 'text');          // TEXT
$dbc->defineField('content', 'longtext');          // LONGTEXT
```

**Numeric Types:**
```php
$dbc->defineField('count', 'int');                 // INT
$dbc->defineField('price', 'decimal', ['length' => '10,2']);
$dbc->defineField('active', 'tinyint');            // TINYINT
$dbc->defineField('bignum', 'bigint');             // BIGINT
```

**Date/Time Types:**
```php
$dbc->defineField('created', 'datetime');          // DATETIME
$dbc->defineField('event_date', 'date');           // DATE
$dbc->defineField('event_time', 'time');           // TIME
```

**Field Parameters:**
```php
$dbc->defineField(string 'field', string 'type', int 'length', int 'decimals', string 'default value');
```

### Indexes

```php
// Single column index
$dbc->defineIndex('idx_name', 'column_name');

// Multi-column index
$dbc->defineIndex('idx_user_date', 'user_id, created');

// Unique index
$dbc->defineIndex('idx_email', 'email unique');
```

### Key Schema Patterns

**1. Auto-Generated ID**
- Primary key `id` (INT AUTO_INCREMENT) added automatically
- NEVER define `id` field manually

**2. Always Include Timestamps**
```php
$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');
```

**3. Cross-Database Compatibility**
- MySQL and SQLite supported
- Use MySQL types, framework converts to SQLite as needed

**4. Schema Migration**
- Schema file runs when module version increments
- Only missing fields/indexes are added
- Existing data is preserved

**Example from `src/app/todo/dao/db/todo.php`:**

```php
/**
 * note:
 *  id, autoincrement primary key is added to all tables - no need to specify
 *  field types are MySQL and are converted to SQLite equivalents as required
 */

$dbc = \sys::dbCheck('todo');

$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');
$dbc->defineField('name', 'varchar');
$dbc->defineField('description', 'varchar');

$dbc->check();
```

## 7. Database Info (`dao/dbinfo.php`)

**Purpose**: Manages database version tracking and executes schema files.

### dbinfo Structure

```php
<?php
namespace {module}\dao;

use bravedave\dvc\dbinfo as dvcDbInfo;

class dbinfo extends dvcDbInfo {
  protected function check() {
    parent::check();
    parent::checkDIR(__DIR__);  // Scans dao/db/ directory for schema files
  }
}
```

**Key Points:**
- Always extend framework's `dbinfo` class
- `checkDIR(__DIR__)` scans `dao/db/` directory
- Includes all PHP files found in `dao/db/`
- Version tracked in `src/data/db_version.json`
- Only runs when `config::{module}_db_version` increments

## 8. Views

### View Conventions

**1. Namespace Views**
```php
<?php
namespace {module}; ?>
<!-- HTML content -->
```

**2. Access Controller Data**
```php
$this->title           // Page title
$this->data->dto       // DTO object
$this->route           // Controller route
```

**3. Generate Unique IDs**
```php
<?php $_uid = strings::rand(); ?>
<div id="<?= $_uid ?>"></div>
```

### Index View (`views/index.php`)

Simple sidebar/navigation content:

```php
<?php
namespace {module}; ?>

<h1><?= config::label ?></h1>

<div class="list-group">
  <a href="<?= $this->route ?>" class="list-group-item">View All</a>
  <a href="<?= $this->route ?>/reports" class="list-group-item">Reports</a>
</div>
```

### Matrix View (`views/matrix.php`)

Main data grid with search, actions, and JavaScript:

```php
<?php
namespace {module};

use bravedave\dvc\strings; ?>

<div class="container-fluid">
  <!-- Search -->
  <div class="row mb-2">
    <div class="col">
      <input type="search" class="form-control" placeholder="search..."
             id="<?= $_search = strings::rand() ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary" id="<?= $_uidAdd = strings::rand() ?>">
        <i class="bi bi-plus-circle"></i> new
      </button>
    </div>
  </div>

  <!-- Data Table -->
  <table class="table table-sm table-hover" id="<?= $_table = strings::rand() ?>">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<script>
(_ => {
  const table = $('#<?= $_table ?>');
  const search = $('#<?= $_search ?>');
  const btnAdd = $('#<?= $_uidAdd ?>');

  // Fetch data from server
  const getMatrix = () => new Promise((resolve, reject) => {
    _.fetch.post(_.url('<?= $this->route ?>'), {
      action: 'get-matrix'
    }).then(d => {
      if ('ack' == d.response) {
        resolve(d.data);
      } else {
        _.growl(d);
        reject(d);
      }
    });
  });

  // Render table rows
  const matrix = data => {
    const tbody = table.find('> tbody').empty();

    $.each(data, (i, dto) => {
      $(`<tr data-id="${dto.id}">
        <td>${dto.id}</td>
        <td>${dto.name}</td>
        <td>${dto.description}</td>
        <td>${dto.status || ''}</td>
      </tr>`)
        .on('click', function(e) {
          _.hideContexts(e);  // hides any open contexts and stops propagation
          $(this).trigger('edit');
        })
        .on('contextmenu', contextmenu)
        .on('delete', rowDelete)
        .on('edit', edit)
        .appendTo(tbody);
    });
  };

  // Context menu handler
  const contextmenu = function(e) {

    if (e.shiftKey) return;
    const _ctx = _.context(e); // hides any open contexts and stops bubbling

    _ctx.append.a({
      html: '<i class="bi bi-pencil"></i>edit',
      click: e => $(this).trigger('edit')
    });

    _ctx.open(e);
  };

  // Delete row handler
  const rowDelete = function(e) {
    const tr = $(this);
    const id = tr.data('id');

    _.ask.alert('Are you sure?').then(() => {
      _.fetch.post(_.url('<?= $this->route ?>'), {
        action: '{entity}-delete',
        id: id
      }).then(d => {
        if ('ack' == d.response) {
          refresh();
        } else {
          _.growl(d);
        }
      });
    });
  };

  // Edit handler - load modal
  const edit = function(e) {
    const tr = $(this);
    const id = tr.data('id');

    _.get.modal(_.url('<?= $this->route ?>/edit/' + id))
      .then(m => {
        m.on('success', () => refresh());
      });
  };

  // Search filter
  let searchTimeout;
  search.on('keyup', function(e) {
    clearTimeout(searchTimeout);
    const term = $(this).val().toLowerCase();

    searchTimeout = setTimeout(() => {
      table.find('> tbody > tr').each(function() {
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.includes(term));
      });
    }, 300);
  });

  // Add button
  btnAdd.on('click', e => {
    _.hideContexts(e);  // hides any open contexts and stops propagation
    _.get.modal(_.url('<?= $this->route ?>/edit'))
      .then(m => m.on('success', () => refresh()));
  });

  // Refresh data
  const refresh = () => {
    getMatrix().then(matrix);
  };

  // Initialize on page ready
  _.ready(() => refresh());

})(_brayworth_);
</script>
```

### Edit View (`views/edit.php`)

Modal form for create/update:

**Note:** When a view is loaded via `$this->load()`, the controller's `protectedLoad()` method automatically extracts all properties from `$this->data` into the view's local scope. This means `$this->data->dto` becomes available as `$dto` directly in the view without explicit assignment.

```php
<?php
namespace {module};

use bravedave\dvc\strings;

// Note: $dto is automatically available from $this->data->dto via protectedLoad() ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <input type="hidden" name="action" value="{entity}-save">
  <input type="hidden" name="id" value="<?= $dto->id ?>">

  <div class="modal fade" id="<?= $_modal = strings::rand() ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title"><?= $this->title ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control"
                   value="<?= $dto->name ?>" required autofocus>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"
                      rows="3"><?= $dto->description ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="pending" <?= $dto->status == 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="active" <?= $dto->status == 'active' ? 'selected' : '' ?>>Active</option>
              <option value="completed" <?= $dto->status == 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>

      </div>
    </div>
  </div>

  <script>
  (_ => {
    const form = $('#<?= $_form ?>');
    const modal = $('#<?= $_modal ?>');

    modal.on('shown.bs.modal', () => {
      // Focus first input when modal shown
      form.find('[autofocus]').trigger('focus');

      // Form submit handler
      form.on('submit', function(e) {
        e.preventDefault();

        const btn = form.find('[type="submit"]');
        btn.prop('disabled', true);

        _.fetch.post.form(_.url('<?= $this->route ?>'), this)
          .then(d => {
            if ('ack' == d.response) {
              modal.trigger('success');  // Notify parent
              modal.modal('hide');
            } else {
              _.growl(d);
              btn.prop('disabled', false);
            }
          })
          .catch(e => {
            _.growl(e);
            btn.prop('disabled', false);
          });

        return false;
      });
    });

    // Show modal immediately
    modal.modal('show');

  })(_brayworth_);
  </script>
</form>
```

### Key View Patterns

**1. Unique ID Generation**
```php
<?= $_uid = strings::rand() ?>    // Generate and output in same line
<div id="<?= $_uid ?>"></div>     // Use the variable
```

**2. JavaScript IIFE Pattern**
```javascript
(_ => {
  // Module code here
  // _ is the _brayworth_ framework object
})(_brayworth_);
```

**3. Framework JavaScript Methods**
```javascript
_.url('path')                           // Generate URL
_.fetch.post(url, data)                 // POST request
_.fetch.post.form(url, formElement)     // POST form data
_.get.modal(url)                        // Load modal via GET
_.growl(message)                        // Show notification
_.ask.alert(message)                    // Confirmation dialog
_.ready(callback)                       // Document ready
_.hideContextMenu()                     // Hide context menu
_.contextMenu(items, {x, y})            // Show context menu
```

**4. Bootstrap 5 Modals**
```javascript
modal.modal('show');                    // Show modal
modal.modal('hide');                    // Hide modal
modal.on('shown.bs.modal', fn);         // Event when shown
modal.trigger('success');               // Custom event
```

**5. Form Data Binding**
```php
<input name="field" value="<?= $dto->field ?>">
```

## Data Flow Architecture

### GET Request Flow (View a Page)

```
1. Browser: GET /module
   ↓
2. DVC Framework: Route to module\controller
   ↓
3. controller::__construct()
   ↓
4. controller::before()
   - config::module_checkdatabase()
   - Register view paths
   - parent::before()
   ↓
5. controller::_index()
   - Prepare $this->data object
   - Call renderBS5()
   ↓
6. Framework renders Bootstrap 5 layout:
   - aside → views/index.php
   - main → views/matrix.php
   ↓
7. Browser receives HTML with JavaScript
   ↓
8. JavaScript executes on _.ready()
   - Fetches data via POST (action: 'get-matrix')
   - Renders table rows
```

### POST Request Flow (AJAX Data Operation)

```
1. Browser: POST /module
   Body: {action: 'entity-save', id: 5, name: 'Example', ...}
   ↓
2. DVC Framework: Route to module\controller
   ↓
3. controller::before()
   - Check database
   ↓
4. controller::postHandler()
   - new ServerRequest
   - Read 'action' parameter
   - Match 'entity-save'
   ↓
5. handler::entitySave($request)
   - Extract fields from request
   - Validate/sanitize data
   - new dao\entity
   - Check if update (id exists) or insert
   ↓
6. dao\entity::UpdateByID($array, $id)
   OR
   dao\entity::Insert($array)
   - Add/update timestamps
   - Execute SQL via framework
   - Return ID or rows affected
   ↓
7. handler returns json::ack('entity-save', $data)
   ↓
8. Framework destructs json object:
   - Sets Content-Type: application/json
   - Outputs JSON: {"response":"ack","description":"entity-save","data":{...}}
   ↓
9. Browser receives JSON
   - Checks d.response === 'ack'
   - Updates UI (refresh table, close modal, etc.)
   - Or shows error via _.growl(d)
```

### Edit Modal Flow (GET + POST Combined)

```
1. User clicks row or "Add" button
   ↓
2. JavaScript: _.get.modal(_.url('module/edit/5'))
   ↓
3. GET /module/edit/5
   ↓
4. controller::edit(5)
   - new dao\entity
   - $dto = $dao->getByID(5)
   - Prepare $this->data with DTO
   - load('edit')
   ↓
5. views/edit.php renders:
   - Form with hidden inputs (action, id)
   - Input fields bound to DTO values
   - JavaScript that shows modal
   ↓
6. Browser receives modal HTML
   - Framework injects into page
   - modal.modal('show') executes
   - Returns promise that resolves to modal element
   ↓
7. User edits fields and clicks "Save"
   ↓
8. Form submit event:
   - e.preventDefault()
   - _.fetch.post.form(url, formElement)
   ↓
9. POST /module (action: 'entity-save', id: 5, ...)
   [Follows POST flow above]
   ↓
10. On success response:
    - modal.trigger('success')  // Custom event
    - modal.modal('hide')
    ↓
11. Parent view catches 'success' event:
    - Refreshes table data
    - Shows success notification
```

## Module Creation Checklist

Use this checklist when creating a new module:

### 1. Module Structure
- [ ] Create directory: `src/app/{module}/`
- [ ] Create subdirectories: `dao/`, `dao/dto/`, `dao/db/`, `views/`

### 2. Configuration
- [ ] Create `config.php` extending root config
- [ ] Define `{module}_db_version` constant
- [ ] Define `label` constant
- [ ] Create `{module}_checkdatabase()` static method

### 3. Database
- [ ] Create `dao/dbinfo.php` extending framework dbinfo
- [ ] Create `dao/db/{entity}.php` with schema definition
- [ ] Define standard fields: `created`, `updated`
- [ ] Define entity-specific fields
- [ ] Add indexes for performance

### 4. Data Layer
- [ ] Create `dao/dto/{entity}.php` extending framework dto
- [ ] Define public properties with default values
- [ ] Include standard fields: `id`, `created`, `updated`
- [ ] Create `dao/{entity}.php` extending framework dao
- [ ] Set `$_db_name` and `$template` properties
- [ ] Override `Insert()` to add timestamps
- [ ] Override `UpdateByID()` to update timestamp
- [ ] Add custom query methods (e.g., `getMatrix()`)

### 5. Controller
- [ ] Create `controller.php` extending dvc controller
- [ ] Implement `before()` hook:
  - [ ] Call `config::{module}_checkdatabase()`
  - [ ] Call `parent::before()`
  - [ ] Register view path
- [ ] Implement `_index()` for default view
- [ ] Implement `postHandler()` with match expression
- [ ] Add public methods for other GET routes (e.g., `edit()`)

### 6. Handler
- [ ] Create `handler.php` as final class
- [ ] Implement static methods for each action:
  - [ ] `{entity}Save(ServerRequest $request): json`
  - [ ] `{entity}Delete(ServerRequest $request): json`
  - [ ] `{entity}GetByID(ServerRequest $request): json`
  - [ ] `{entity}GetMatrix(ServerRequest $request): json`
- [ ] Type cast all user input
- [ ] Use DAO for all data operations
- [ ] Return json::ack() or json::nak()

### 7. Views
- [ ] Create `views/index.php` (sidebar/navigation)
- [ ] Create `views/matrix.php` (main data grid):
  - [ ] Search input with unique ID
  - [ ] Action buttons (Add, etc.)
  - [ ] Data table with unique ID
  - [ ] JavaScript IIFE with _brayworth_
  - [ ] `getMatrix()` function
  - [ ] `matrix()` render function
  - [ ] Event handlers (click, contextmenu, delete, edit)
  - [ ] `refresh()` and `_.ready()` initialization
- [ ] Create `views/edit.php` (modal form):
  - [ ] Form with unique ID
  - [ ] Hidden inputs for action and id
  - [ ] Input fields bound to DTO
  - [ ] Modal with unique ID
  - [ ] JavaScript for form submit
  - [ ] `modal.trigger('success')` on save

### 8. Integration
- [ ] Test GET routes (index, edit)
- [ ] Test POST actions (save, delete, get data)
- [ ] Test modal create/edit flow
- [ ] Test search/filter functionality
- [ ] Test context menu and row actions
- [ ] Verify database migrations

### 9. Optional Enhancements
- [ ] Add sorting to matrix view
- [ ] Add pagination for large datasets
- [ ] Add export functionality (CSV, PDF)
- [ ] Add bulk actions (bulk delete, bulk update)
- [ ] Add advanced filtering
- [ ] Add validation to handler methods
- [ ] Add error logging
- [ ] Add unit tests

## CLI Module Generator

Use the DVC CLI to quickly scaffold a new module:

```bash
# Generate module structure automatically
vendor/bin/dvc make::module {module-name}
```

This creates the basic file structure. You still need to:
1. Define database schema in `dao/db/`
2. Add DTO properties
3. Implement handler methods
4. Create view templates
5. Add custom DAO methods

## Naming Conventions

### Files and Directories
- **Module names**: Lowercase, singular (e.g., `todo`, `user`, `product`)
- **Controller file**: `controller.php` (always)
- **Handler file**: `handler.php` (always)
- **DAO files**: Singular entity name (e.g., `todo.php`, `user.php`)
- **DTO files**: Match DAO name (e.g., `dto/todo.php`)
- **Schema files**: Match table name (e.g., `db/todo.php`)

### Classes and Namespaces
- **Namespace**: `namespace {module};` (e.g., `namespace todo;`)
- **Controller class**: `class controller extends dvcController`
- **Handler class**: `final class handler`
- **DAO class**: `class {entity} extends dao`
- **DTO class**: `class {entity} extends dto`

### Methods and Actions
- **Action names**: Kebab-case with module prefix (e.g., `todo-save`, `todo-delete`)
- **Handler methods**: camelCase with entity prefix (e.g., `todoSave`, `todoDelete`)
- **DAO methods**: camelCase (e.g., `getMatrix`, `getActiveItems`)
- **Controller methods**: camelCase (e.g., `_index`, `edit`)

### Database
- **Table names**: Lowercase, singular (e.g., `todo`, `user`, `product`)
- **Field names**: Lowercase with underscores (e.g., `created_at`, `user_id`)
- **Standard fields**: `id`, `created`, `updated` (always include)

## Security Best Practices

### Input Validation
```php
// Always type cast IDs
$id = (int)$request('id');

// Sanitize strings
$name = trim($request('name'));

// Validate required fields
if (empty($name)) {
  return json::nak($action, 'Name is required');
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  return json::nak($action, 'Invalid email');
}
```

### SQL Injection Prevention
```php
// GOOD - Use DAO methods (prepared statements)
$dao->getByID($id);
$dao->UpdateByID($array, $id);

// GOOD - Use sprintf for integers, quote() for strings in DAOs
$sql = sprintf('SELECT * FROM table WHERE id = %d', $id);
new dtoSet($sql);

// Or with quote() method for strings
$sql = sprintf('SELECT * FROM table WHERE name = %s', $this->quote($name));
new dtoSet($sql);

// BAD - Never concatenate user input into SQL
// new dtoSet("SELECT * FROM table WHERE id = $id");  // NEVER DO THIS
```

### XSS Prevention
```php
// Always escape output in views
<?= htmlspecialchars($dto->name) ?>

// Or use short form (framework provides)
<?= $dto->name ?>  // Framework auto-escapes in most contexts
```

### Authentication/Authorization
```php
// Check user permissions in controller before()
protected function before() {
  if (!$this->session->userID) {
    $this->redirect('/login');
  }
  parent::before();
}

// Check permissions in handler
public static function entityDelete(ServerRequest $request): json {
  if (!currentUser()->hasPermission('delete')) {
    return json::nak($request('action'), 'Permission denied');
  }
  // ... proceed with delete
}
```

## Performance Optimization

### Database Queries
```php
// GOOD - Single query with JOIN
$sql = 'SELECT u.*, p.name as product_name
        FROM users u
        LEFT JOIN products p ON p.user_id = u.id';
$users = new dtoSet($sql);

// BAD - N+1 query problem
// $users = $dao->getAll();
// foreach ($users as $user) {
//   $user->product = (new productDao)->getByUserId($user->id); // Multiple queries!
// }
```

### Caching
```php
// Cache expensive queries
public function getMatrix() : array {
  $cache = \cache::get('entity-matrix');
  if ($cache) return $cache;

  $data = (new dtoSet)('SELECT * FROM entity');
  \cache::set('entity-matrix', $data, 300); // 5 minutes
  return $data;
}
```

### Pagination
```php
// Add pagination to large datasets
public function getMatrix($page = 1, $perPage = 50) : array {
  $offset = ($page - 1) * $perPage;
  $sql = sprintf('SELECT * FROM entity LIMIT %d OFFSET %d', $perPage, $offset);
  return (new dtoSet)($sql);
}
```

## Testing Patterns

### Handler Unit Test Example
```php
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase {
  public function testTodoSave() {
    $request = new ServerRequest([
      'action' => 'todo-save',
      'name' => 'Test Task',
      'description' => 'Test Description'
    ]);

    $response = handler::todoSave($request);

    $this->assertEquals('ack', $response->response);
  }
}
```

### DAO Unit Test Example
```php
class TodoDaoTest extends TestCase {
  public function testInsertAndRetrieve() {
    $dao = new dao\todo;

    $id = $dao->Insert([
      'name' => 'Test',
      'description' => 'Test Description'
    ]);

    $this->assertGreaterThan(0, $id);

    $dto = $dao->getByID($id);
    $this->assertEquals('Test', $dto->name);
  }
}
```

## Common Patterns and Examples

### Master-Detail Relationship
```php
// In parent DAO
public function getWithChildren($id) {
  $dto = $this->getByID($id);
  if ($dto) {
    $dto->children = (new childDao)->getByParentId($id);
  }
  return $dto;
}

// In child DAO
public function getByParentId($parentId) : array {
  $sql = sprintf('SELECT * FROM child WHERE parent_id = %d', (int)$parentId);
  return (new dtoSet)($sql);
}
```

### Soft Delete
```php
// Add 'deleted' field to schema
$dbc->defineField('deleted', 'tinyint');  // default = 0

// Override delete in DAO
public function delete($id) {
  return $this->UpdateByID(['deleted' => 1], $id);
}

// Filter deleted in queries
public function getMatrix() : array {
  return (new dtoSet)('SELECT * FROM entity WHERE deleted = 0');
}
```

### File Upload Handler
```php
public static function entityUpload(ServerRequest $request): json {
  $action = $request('action');

  if ($file = $request->file('upload')) {
    $target = sprintf('%s/uploads/%s',
      \sys::config()->paths->upload,
      $file->getClientFilename()
    );

    $file->moveTo($target);

    return json::ack($action, ['filename' => $file->getClientFilename()]);
  }

  return json::nak($action, 'No file uploaded');
}
```

### Export to CSV
```php
public function exportCsv() {
  $dao = new dao\todo;
  $data = $dao->getMatrix();

  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="export.csv"');

  $fp = fopen('php://output', 'w');
  fputcsv($fp, ['ID', 'Name', 'Description', 'Created']);

  foreach ($data as $dto) {
    fputcsv($fp, [$dto->id, $dto->name, $dto->description, $dto->created]);
  }

  fclose($fp);
  exit;
}
```

## Troubleshooting

### Database Not Updating
- Check `config::{module}_db_version` is incremented
- Verify `dao/dbinfo.php` calls `checkDIR(__DIR__)`
- Check `src/data/db_version.json` current version
- Verify schema file has `$dbc->check()` at end

### POST Handler Not Found
- Verify action name matches in JavaScript and `postHandler()`
- Check handler method is static and public
- Ensure handler method returns `json` type
- Verify `ServerRequest` spelling and usage

### DTO Not Populating
- Check DAO `$template` property points to correct DTO class
- Verify DTO properties match database field names
- Ensure DTO extends framework `dto` class

### Views Not Loading
- Verify view path registered in `controller::before()`
- Check view file has namespace declaration
- Ensure view file exists in `views/` directory

### JavaScript Errors
- Check unique IDs generated with `strings::rand()`
- Verify `_brayworth_` framework loaded
- Check console for JavaScript errors
- Ensure IIFE pattern wrapped correctly

## Additional Resources

- **DVC Framework Documentation**: `vendor/bravedave/dvc/Readme.md`
- **Standards Document**: `vendor/bravedave/dvc/STANDARDS.md`
- **Todo Module Example**: `src/app/todo/`
- **CLI Help**: `vendor/bin/dvc --help`

## Quick Reference Card

### Essential Commands
```bash
# Create new module
vendor/bin/dvc make::module {name}

# Start dev server
vendor/bin/dvc serve

# Setup application
vendor/bin/dvc make::application
```

### Common Code Snippets

**Create DAO instance and fetch:**
```php
$dao = new dao\{entity};
$dto = $dao->getByID($id);
$all = $dao->getMatrix();
```

**Insert/Update:**
```php
$data = ['field' => 'value'];
$id = $dao->Insert($data);
$dao->UpdateByID($data, $id);
```

**JSON Response:**
```php
return json::ack($action, $data);
return json::nak($action, $message);
```

**AJAX POST:**
```javascript
_.fetch.post(_.url('route'), {
  action: 'action-name',
  data: value
}).then(d => {
  if ('ack' == d.response) {
    // Success
  }
});
```

**Load Modal:**
```javascript
_.get.modal(_.url('route/edit/' + id))
  .then(m => {
    m.on('success', () => refresh());
  });
```

---

**This guide represents the canonical pattern for creating modules in the DVC framework. Always refer to `src/app/todo/` as the reference implementation.**
