```prompt
# Prompt: Implement Authentication

You are tasked with implementing **session-based authentication** in a DVC framework application.

## References

Before proceeding:
1. Review `.github/copilot-instructions.md` for DVC framework patterns
2. Review `Authentication.md` in the workspace root — this is the authoritative guide for how authentication works in this framework
3. Check root `README.md` for project-specific requirements (e.g., existing modules, naming conventions)

---

## Overview

Authentication in the DVC framework is session-based. The framework checks `currentUser::valid()` on every request to a protected controller (all controllers where `$RequireValidation = true`, which is the default). When not authenticated, `authorize()` either pops an inline modal login form or redirects to `/logon`.

The login POST uses `action: '-system-logon-'` — this is a framework convention. The home controller intercepts it in an overridden `authorize()` method.

---

## Files to Create

```
src/
├── controller/
│   ├── home.php              # Route file: class home extends home\controller {}
│   └── logon.php             # Route file: class logon extends logon\controller {}
└── app/
    ├── user.php              # Session-aware user class
    ├── currentUser.php       # Static singleton facade
    ├── config.php            # App config — use_inline_logon, PAGE_TEMPLATE_LOGON
    ├── home/
    │   └── controller.php    # Overrides authorize() for -system-logon- POST; provides logout()
    ├── logon/
    │   ├── config.php
    │   ├── controller.php    # Provides GET logon/form route
    │   └── views/
    │       └── form.php      # Modal login form HTML + JS
    └── users/                # Users management module (full CRUD — use Simple CRUD pattern)
        ├── config.php
        ├── controller.php
        ├── handler.php
        ├── dao/
        │   ├── dbinfo.php
        │   ├── users.php     # DAO — must include checkByPassword()
        │   ├── dto/
        │   │   └── users.php
        │   └── db/
        │       └── users.php
        └── views/
            ├── index.php
            ├── matrix.php
            └── edit.php
```

---

## Implementation Requirements

### 1. `src/app/user.php`

Extend `bravedave\dvc\user`. On construction, read the session and validate against the database:

```php
class user extends bravedave\dvc\user {
  public function __construct() {
    if ($this->id = (int)\bravedave\dvc\session::get('userID')) {
      $dao = new users\dao\users;
      if ($dto = $dao->getByID($this->id)) {
        if ($dto->active) {
          $this->id   = (int)$dto->id;
          $this->name = $dto->name;
          // map any other fields you need (email, etc.)
        } else {
          $this->id = 0;
          \bravedave\dvc\session::set('userID', 0);  // deactivated — force logout
        }
      } else {
        $this->id = 0;  // user deleted from DB
      }
    }
  }

  public function valid(): bool { return $this->id > 0; }
}
```

### 2. `src/app/currentUser.php`

Extend `bravedave\dvc\currentUser`. Add `reset()` — **this method is not in the base class** and must be provided here:

```php
class currentUser extends bravedave\dvc\currentUser {

  public static function valid(): bool { return self::user()->valid(); }
  public static function id(): int     { return (int)self::user()->id; }
  public static function name(): string { return (string)self::user()->name; }

  public static function reset(): void {
    self::$instance = null;  // clears singleton so next call re-reads session
  }

  public static function isadmin(): bool {
    return (bool)self::user()->isadmin();
  }
}
```

> `reset()` must be called after login (so the newly written session is picked up on the very next `valid()` call) and after logout.

### 3. `src/app/config.php`

Set the inline logon flags on the app config class. The `PAGE_TEMPLATE_LOGON` must point to a Bootstrap 5 page class:

```php
class config extends bravedave\dvc\config {
  const use_inline_logon = true;
  static $PAGE_TEMPLATE_LOGON = \dvc\pages\bootstrap5::class;
  // ... other app config
}
```

If `use_inline_logon = false`, the framework redirects to `/logon` instead and the logon module must provide a full page at that route.

### 4. `src/app/home/controller.php`

Override `authorize()` to intercept the `-system-logon-` POST. Also provide a `logout()` GET route:

```php
namespace home;

use bravedave\dvc\{controller as dvcController, ServerRequest, session, json};

class controller extends dvcController {

  protected function authorize() {
    if ($this->isPost()) {
      $action = $this->getPost('action');
      if ('-system-logon-' == $action) {
        $this->_authorize();
        die;
      }
    }
    parent::authorize();  // show inline modal or redirect to /logon
  }

  protected function _authorize(): void {
    $request = new ServerRequest;
    $action  = $request('action');

    if ($u = (string)$request('u')) {
      if ($p = (string)$request('p')) {
        \users\config::users_checkdatabase();
        $dao = new \users\dao\users;
        if ($dto = $dao->checkByPassword($u, $p)) {
          session::set('userID', $dto->id);
          \currentUser::reset();
          json::ack($action);
          return;
        }
      }
    }
    json::nak($action);
  }

  public function logout() {
    session::destroy();
    \bravedave\dvc\Response::redirect(null, 'logged out');
  }

  // ... rest of home controller (_index, before, postHandler, etc.)
}
```

> **`json::ack()` / `json::nak()` in controller context:** call without `return` (unlike handler methods), then `return` to exit the PHP method. `session::destroy()` is the correct logout mechanism — do not use `session::set('userID', 0)`, which leaves the session alive.

### 5. `src/controller/home.php` and `src/controller/logon.php`

Route files (standard pattern):

```php
// src/controller/home.php
class home extends home\controller {}

// src/controller/logon.php
class logon extends logon\controller {}
```

### 6. `src/app/logon/controller.php`

Provides the `logon/form` route that the inline modal loads via `_.get.modal(_.url('logon/form'))`. This controller must set `$RequireValidation = false` since unauthenticated users need to reach it:

```php
namespace logon;

use bravedave\dvc\controller as dvcController;

class controller extends dvcController {

  protected $RequireValidation = false;

  protected function before() {
    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';
  }

  public function form() {
    $this->load('form');
  }
}
```

### 7. `src/app/logon/views/form.php`

The login form rendered inside the modal. It POSTs to the **current originating route** (not `/logon`) using `_.url(_.route)` or the referrer, with `action: '-system-logon-'`. On `ack`, reload the page so the newly authenticated session takes effect:

```php
<?php namespace logon;
use bravedave\dvc\strings; ?>

<?php $_uid = strings::rand(); $_form = strings::rand(); ?>
<form id="<?= $_form ?>" autocomplete="off">
  <input type="hidden" name="action" value="-system-logon-">

  <div class="modal fade" id="<?= $_uid ?>" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Log On</h5>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Username or Email</label>
            <input type="text" name="u" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="p" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Log On</button>
        </div>

      </div>
    </div>
  </div>
</form>

<script>
(_ => {
  const form = $('#<?= $_form ?>');
  const modal = $('#<?= $_uid ?>');

  modal.on('shown.bs.modal', () => {
    form.find('[autofocus]').trigger('focus');

    form.on('submit', function(e) {
      e.preventDefault();
      const btn = form.find('[type="submit"]').prop('disabled', true);

      _.fetch.post.form(_.url(_.route || ''), this)
        .then(d => {
          if ('ack' == d.response) {
            location.reload();   // re-load page now authenticated
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

  modal.modal('show');

})(_brayworth_);
</script>
```

### 8. `src/app/users/` — Users Module

Implement as a **Simple CRUD** module (see `implement-simple-crud.prompt.md`). The critical addition is the `checkByPassword()` method on the DAO.

**`src/app/users/dao/db/users.php`** — required schema fields:

```php
$dbc = \sys::dbCheck('users');
$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');
$dbc->defineField('name', 'varchar');
$dbc->defineField('username', 'varchar');
$dbc->defineField('email', 'varchar');
$dbc->defineField('password', 'varchar');    // bcrypt hash — never plain text
$dbc->defineField('active', 'tinyint', null, null, 1);
$dbc->defineIndex('idx_username', 'username');
$dbc->defineIndex('idx_email', 'email');
$dbc->check();
```

**`src/app/users/dao/users.php`** — add `checkByPassword()`:

```php
namespace users\dao;

use bravedave\dvc\{dao, dtoSet};

class users extends dao {
  protected $_db_name = 'users';
  protected $template = dto\users::class;

  public function checkByPassword(string $identity, string $password): ?dto\users {
    $sql = sprintf(
      'SELECT * FROM `users` WHERE (`username` = %s OR `email` = %s) AND `active` = 1 LIMIT 1',
      $this->quote($identity),
      $this->quote($identity)
    );

    if ($dtos = (new dtoSet)($sql, null, $this->template)) {
      if (count($dtos) > 0) {
        $dto = $dtos[0];
        if (password_verify($password, $dto->password)) {
          return $dto;
        }
      }
    }
    return null;
  }

  public function Insert($a) {
    if (!empty($a['password'])) {
      $a['password'] = password_hash($a['password'], PASSWORD_DEFAULT);
    }
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {
    if (!empty($a['password'])) {
      $a['password'] = password_hash($a['password'], PASSWORD_DEFAULT);
    } else {
      unset($a['password']);  // don't overwrite with empty value
    }
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
```

> Passwords are **always** stored as `password_hash()` bcrypt hashes. Never store or log plain-text passwords. The `Insert()` and `UpdateByID()` overrides ensure hashing happens at the DAO layer — handlers pass the raw password value and the DAO handles it.

**`src/app/users/dao/dto/users.php`:**

```php
namespace users\dao\dto;

use bravedave\dvc\dto;

class users extends dto {
  public $id = 0;
  public $created = '';
  public $updated = '';
  public $name = '';
  public $username = '';
  public $email = '';
  public $password = '';   // never output this in views
  public $active = 1;
}
```

**`src/app/users/handler.php`** — do not echo the password back in responses:

```php
public static function usersSave(ServerRequest $request): json {
  $action = $request('action');
  $a = [
    'name'     => trim($request('name')),
    'username' => trim($request('username')),
    'email'    => trim($request('email')),
    'active'   => (int)$request('active'),
    'password' => $request('password'),   // DAO will hash; blank = no change on update
  ];

  $dao = new dao\users;
  if ($id = (int)$request('id')) {
    $dao->UpdateByID($a, $id);
  } else {
    $id = $dao->Insert($a);
  }

  return json::ack($action);
}
```

---

## Validation Checklist

Before completing:

- [ ] `user.php` reads `session::get('userID')`, queries DB, handles deactivated users
- [ ] `currentUser.php` provides `reset()` (not in base class)
- [ ] `config.php` sets `use_inline_logon` and `PAGE_TEMPLATE_LOGON`
- [ ] `home\controller` overrides `authorize()` to handle `-system-logon-` POST
- [ ] `home\controller::logout()` calls `session::destroy()` (not `session::set('userID', 0)`)
- [ ] `logon\controller` sets `$RequireValidation = false`
- [ ] `logon/form` view POSTs with `action: '-system-logon-'` and reloads on `ack`
- [ ] Route files exist: `src/controller/home.php`, `src/controller/logon.php`
- [ ] `users` module DAO has `checkByPassword()` using `password_verify()`
- [ ] `users` DAO `Insert()` hashes passwords with `password_hash()`
- [ ] `users` DAO `UpdateByID()` only hashes if password field is non-empty
- [ ] `password` field is **never** returned in JSON responses or rendered in views
- [ ] Protected controllers have `$RequireValidation = true` (default — no action needed unless it was changed)
- [ ] At least one initial admin user can be created (seed script or first-run setup)

---

## Security Checklist

- [ ] Passwords stored as `password_hash(PASSWORD_DEFAULT)` bcrypt — never plain text
- [ ] `checkByPassword()` uses `password_verify()` — never compare hashes directly
- [ ] Login queries filter `active = 1` — deactivated users cannot log in
- [ ] `session::destroy()` on logout — fully destroys session data
- [ ] Session cookie is `secure` and `samesite` (framework handles this via `session.php`)
- [ ] `logon/form` controller has `$RequireValidation = false` — no auth loop
- [ ] User input (identity, password) is not logged or included in error responses

---

## Common Mistakes to Avoid

❌ **Don't** use `session::set('userID', 0)` for logout — use `session::destroy()`
❌ **Don't** forget to call `\currentUser::reset()` after writing `userID` to session on login
❌ **Don't** assume `reset()` exists in the base class — add it to your `currentUser.php`
❌ **Don't** return the `password` field in `json::ack` responses or render it in views
❌ **Don't** compare password hashes directly — always use `password_verify()`
❌ **Don't** set `$RequireValidation = false` on controllers that should be protected
❌ **Don't** forget the `logon` route file (`src/controller/logon.php`)
✅ **Do** hash passwords at the DAO layer, not in the handler
✅ **Do** reload the page (`location.reload()`) after a successful login — don't try to re-render in JS
✅ **Do** call `parent::authorize()` as the fallback in the home controller's overridden `authorize()`

---

## Reference

- `Authentication.md` (workspace root) — complete explanation of the authentication flow, call chain, and session management
```
