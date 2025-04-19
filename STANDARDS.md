# DVC Framework Standards

## PSR Compliance

### ✅ Required

- **PSR-4 (Autoloading)**  
  All classes must follow `vendor/package` namespace structure:

  ```php
  // src/controller/example.php
  namespace bravedave\dvc\controller;
  ```

- **PSR-7 (HTTP Interface)**  
  `ServerRequest` implements key methods:

  ```php
  $request->getQueryParam('param');      // $_GET
  $request('param');     // $_POST
  $request->getServerParams(); // $_SERVER
  ```

### 🔀 Modified

- **PSR-12 (Coding Style)** *with adjustments*:

  ```php
  // Braces on same line + 2-space indents
  class Example {
    public function demo() {
      if ($test) {
        // ...
      }
    }
  }
  ```

### ❌ Ignored

- PSR-1/2 (Superseded by PSR-12)
- PSR-5 (PHPDoc standard)

## Style Enforcement

### CLI Tools

1. **PHP-CS-Fixer Config** (`.php-cs-fixer.php`):

```php
return (new PhpCsFixer\Config())
    ->setIndent('  ') // 2 spaces
    ->setRules([
        'braces_position' => [
            'position_after_functions_and_oop_constructs' => 'same'
        ],
        'blank_line_after_opening_tag' => false,
    ]);
```

2. **Composer Script**:

```json
"scripts": {
    "lint": "php-cs-fixer fix --dry-run",
    "fix": "php-cs-fixer fix"
}
```

## Implementation Guide

### HTTP Requests

```php
// Lightweight PSR-7 implementation
$request = new \bravedave\dvc\ServerRequest();
$id = $request->getQueryParam('id'); // PSR-7 inspired
```

### Class Structure

```php
namespace bravedave\dvc\controller;

class example {
  protected function _index() {
    // 2-space indent
    if ($x) {
      return $this->render();
    }
  }
}
```
