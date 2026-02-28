# Authentication

## Overview

Authentication in the DVC framework is session-based. The framework enforces authentication automatically on every controller where `$RequireValidation = true` (the default). When an unauthenticated user hits a protected route the framework either pops an inline modal login dialog or redirects to `/logon`, depending on `config::use_inline_logon`.

---

## How It Works — Step by Step

### 1. Every Request Is Checked in the Controller Constructor

`vendor/bravedave/dvc/src/bravedave/dvc/controller.php` — inside `__construct()`:

```php
if ($this->RequireValidation) {
    $this->authorised = currentUser::valid();
    if (!($this->authorised)) $this->authorize();
}
```

- `$RequireValidation = true` is the default — most controllers inherit this.
- Set `protected $RequireValidation = false;` in a controller to make it publicly accessible.

### 2. `currentUser::valid()` — Is the User Logged In?

The call chain is:

```
currentUser::valid()
  → currentUser::user()          // returns singleton \user instance
    → new \user                   // constructed on first call
      → session::get('userID')   // reads PHP session
      → users\dao\users::getByID($id)   // validates user still exists & is active
      → sets $this->id, $this->name, etc.
  → user::valid()                // returns $this->id > 0
```

`src/app/user.php`:
```php
class user extends dvcUser {
  public function __construct() {
    if ($this->id = (int)session::get('userID')) {
      $dao = new users\dao\users;
      if ($dto = $dao->getByID($this->id)) {
        if ($dto->active) {
          $this->id    = (int)$dto->id;
          $this->name  = $dto->name;
          // ...
        } else {
          $this->id = 0;
          session::set('userID', 0);   // deactivated user — log them out
        }
      }
    }
  }

  public function valid(): bool { return $this->id > 0; }
}
```

`src/app/currentUser.php` wraps this as a static singleton:
```php
class currentUser extends bravedave\dvc\currentUser {
  public static function valid(): bool { return self::user()->valid(); }
  public static function id(): int     { return self::user()->id; }
  public static function name(): string { return self::user()->name; }
  public static function reset(): void { self::$instance = null; }  // NOT in base class — must be added here
}
```

> **`reset()` is not provided by the base class.** `bravedave\dvc\currentUser` does not declare `reset()`. The app must add this method to clear the singleton after login (so the next `valid()` call re-reads the freshly written session).

### 3. `authorize()` — What Happens When Not Logged In

From `controller.php` (simplified — the full method also handles IMAP auth, Google OAuth, and `$Redirect_OnLogon`):

```php
protected function authorize() {
    if (config::use_inline_logon) {
        // Render a full page with a JS snippet that immediately pops the modal login form
        $p = new config::$PAGE_TEMPLATE_LOGON('Log On');
        $p->late['logon'] = '<script>(_ => _.ready(() => _.get.modal(_.url(\'logon/form\'))))( _brayworth_);</script>';
        $p->meta[] = '<meta name="viewport" content="initial-scale=1" />';
        $p->header()->content();
    } else {
        Response::redirect(strings::url('logon'));
    }
    die;
}
```

This application uses **inline logon** (`config::use_inline_logon = true`), so the framework renders a bare Bootstrap 5 page and immediately pops the login modal via `_.get.modal(_.url('logon/form'))`. The `logon/form` route must be provided by a `logon` module in the application. The user never leaves the page.

### 4. The Login Request (POST)

When the user submits credentials the JS posts to the current route with `action: '-system-logon-'`. The home controller intercepts this in its overridden `authorize()`:

`src/app/home/controller.php`:

```php
protected function authorize() {
    if ($this->isPost()) {
        $action = $this->getPost('action');
        if ($action == '-system-logon-') {
            $this->_authorize();
            die;
        }
    }
    parent::authorize();   // show login modal if not a POST
}

protected function _authorize(): void {
    $request = new ServerRequest;
    $action  = $request('action');

    if ($u = (string)$request('u')) {
        if ($p = (string)$request('p')) {
            \users\config::users_checkdatabase();
            $dao = new \users\dao\users;
            if ($dto = $dao->checkByPassword($u, $p)) {
                session::set('userID', $dto->id);   // write session
                \currentUser::reset();              // clear singleton cache
                json::ack($action);                // outputs JSON directly (no return needed in controller context)
                return;
            }
        }
    }
    json::nak($action);
}
```

> **Note:** In controller methods, `json::ack()` / `json::nak()` output directly rather than being returned. This differs from handler methods (which `return json::ack(...)`) but both patterns are valid within their respective contexts.

### 5. Password Verification

`src/app/users/dao/users.php`:

```php
public function checkByPassword(string $identity, string $password): ?dto\users {
    // accepts username OR email
    $sql = sprintf(
        'SELECT * FROM `users` WHERE (`username` = %s OR `email` = %s) AND `active` = 1 LIMIT 1',
        $this->quote($identity), $this->quote($identity)
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
```

- Passwords are stored as PHP `password_hash()` hashes.
- Only `active = 1` users can log in.
- Returns the user DTO on success, `null` on failure.
- On `ack` the JS closes the modal and performs a full page reload, which picks up the new session via `currentUser::valid()`.

### 6. Logout

`src/app/home/controller.php`:

```php
public function logout() {
    session::destroy();
    Response::redirect(null, 'user logged out');
}
```

Accessed via `GET /home/logout`. `session::destroy()` fully invalidates and destroys the PHP session (more secure than merely zeroing the `userID` value). `Response::redirect(null, 'user logged out')` renders a brief interstitial HTML page displaying the message, then performs a 1-second meta-refresh redirect to `/`. Note that `Response::redirect()` calls `exit` internally, so no further code runs after it.

> **`session::destroy()` vs `session::set('userID', 0)`:** Always prefer `session::destroy()` for logout. Setting `userID` to `0` leaves the session cookie alive and the session data intact, which is a weaker security posture. `session::destroy()` removes all session data. The framework's own base `logout()` method uses `session::destroy()`.

---

## Implementing This in Another Application

### Required Files

| File | Purpose |
|---|---|
| `src/app/user.php` | Session-aware user class; `valid()` returns `id > 0` |
| `src/app/currentUser.php` | Static singleton facade over `user`; provides `::valid()`, `::id()`, `::name()`, `::reset()` |
| `src/app/config.php` | Set `const use_inline_logon = true` and `const PAGE_TEMPLATE_LOGON` |
| `src/app/users/dao/users.php` | DAO with `checkByPassword(identity, password)` |
| `src/app/home/controller.php` | Overrides `authorize()` to handle `-system-logon-` POST |
| `src/app/logon/` (module) | Provides the `logon/form` route used by the inline modal; requires its own controller and view |

### Checklist

1. **Users table** — must have: `id`, `name`, `username`, `email`, `password` (bcrypt hash), `active` (tinyint).

2. **`user` class** — extend `bravedave\dvc\user`, read `session::get('userID')`, query the DB, set `$this->id`, implement `valid(): bool`.

3. **`currentUser` class** — extend `bravedave\dvc\currentUser`, return your `user` from `user()`. The base class does **not** provide `reset()` — add it yourself to clear `static::$instance` after login.

4. **`config`** — set `use_inline_logon = true` (modal) or leave `false` (redirect to `/logon`).

5. **Home controller** — override `authorize()` to catch `action == '-system-logon-'` POST, call your password-check DAO, write `session::set('userID', $dto->id)`, call `\currentUser::reset()`, then call `json::ack` or `json::nak` directly (no `return` needed; follow with `return` to exit the method).

6. **Protected controllers** — leave `protected $RequireValidation = true;` (default). Public controllers (e.g., assets, logon form) set it to `false`.

7. **Access checks in handlers/views** — use `\currentUser::valid()` and `\currentUser::isadmin()` for fine-grained checks.

---

## Summary Flow

```
GET /any-protected-route
  → controller::__construct()
    → currentUser::valid()
      → user::__construct() reads session userID
      → queries users table, checks active flag
      → valid() returns id > 0
    → if NOT valid → authorize()
      → renders page + JS that pops modal login form

User submits credentials
  → POST /home (action: '-system-logon-', u: ..., p: ...)
    → home\controller::authorize() intercepts
      → users\dao\users::checkByPassword()
        → SELECT WHERE username/email AND active=1
        → password_verify()
      → session::set('userID', $dto->id)
      → currentUser::reset()
      → json::ack  →  JS reloads page

GET /any-protected-route  (now authenticated)
  → currentUser::valid() returns true
  → page renders normally

GET /home/logout
  → session::destroy()       // fully invalidates the session
  → Response::redirect(null, 'user logged out')  // renders interstitial page, then meta-refresh to /
```