# DVC – Data View Controller

**DVC** is a lightweight, PSR-4-compliant PHP framework for building modern web applications and APIs. It comes pre-configured with Bootstrap but is flexible enough to work with any front-end you prefer.

📚 **Documentation:** [https://brayworth.com/docs](https://brayworth.com/docs)

---

## 🚀 Features

- **MVC Architecture** – Clean separation of concerns (Models, Views, Controllers)
- **Built-in CLI Tool** – Generate controllers, serve your app, and more
- **Bootstrap-Ready** – Ships with Bootstrap, easily replaceable
- **Simple & Modern** – No heavy abstractions, just solid structure

---

## AI Coding in This Repo

- AI onboarding and governance: [.github/AI-README.md](.github/AI-README.md)
- AI tooling index: [.github/AI-CODING-INDEX.md](.github/AI-CODING-INDEX.md)
- Primary coding standard: [.github/copilot-instructions.md](.github/copilot-instructions.md)
- API contract parity standard: [.github/prompts/api-contract-maintenance.standard.md](.github/prompts/api-contract-maintenance.standard.md)
- AI context rule: In any folder, `README.md` is authoritative for that folder and should be read before planning or making changes there.
- Exception: In `.github/`, use `.github/AI-README.md` as the authoritative governance document.

---

## Shared Redis Cache Health

Applications can opt into a shared cache-disable semaphore by setting
`config::$DB_CACHE_SEMAPHORE_PATH` to an existing private directory shared by
every web and CLI cache user. The directory must be readable and writable by
those users. Keep it outside public web roots and on a filesystem supporting
shared `flock()` locks; a container-local directory is not sufficient.

```php
config::$DB_CACHE = 'REDIS';
config::$DB_CACHE_REDIS_SERVER = 'redis'; // A Unix socket path is also supported.
config::$DB_CACHE_SEMAPHORE_PATH = '/private/shared/data/';
config::$DB_CACHE_MAX_TTL = 86400;
config::$DB_CACHE_PROBE_INTERVAL = 5;
```

One locked JSON marker is maintained per database host/name, cache prefix, and
Redis endpoint. Connection, read, write, invalidation, and scan failures disable
normal cache operations across processes. Unavailable or corrupt coordination
state fails closed. SQL remains the caller's fallback. Redis connection and read
timeouts are one second; failed operations log state transitions without data.

Recovery uses rate-limited health probes and waits a full `DB_CACHE_MAX_TTL`
after the first successful probe. Renewed failure restarts the cooldown. Shared
cache writes are capped to that positive TTL; the default is 86400 seconds.
Generation-qualified keys make old entries unreachable, and reads capture the
generation so a delayed pre-outage fill cannot populate the recovered namespace.
Call `get()` before querying SQL and filling a shared cache entry. Long-running
callers should perform a fresh read before filling after recovery.

Guarded wildcard deletion and `flush()` affect only the current identity's
namespace; recovery never calls `flushDB()`. With the semaphore disabled, legacy
Redis `flush()` still clears the selected Redis database. Explicit APCu support
is unchanged and does not use this semaphore. Setting `DB_CACHE` to an empty
string permanently bypasses caching, regardless of health recovery.

Run the isolated regression harness without loading a Redis extension:
`php -n tests/dvc/CacheTest.php`. It uses temporary files, child processes, and
in-memory adapters, not a live Redis server or application database.

## 🛠️ Getting Started

### Requirements

- PHP 8.0 or higher
- Composer

### ↓ Installation

#### 1. create a composer.json

The application relies on the composer autoload features, this (very) basic composer.json file tells the autloader where to look for this application and installs *bravedave/dvc*

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

#### 2. install the dependencies, create application and run the Dev server

```bash
composer u
vendor/bin/dvc make::application
vendor/bin/dvc serve
```

Then open [http://localhost:1265](http://localhost:1265) in your browser.

### 🎓 Tutorial

- there is a tutorial [here](src/bravedave/dvc/views/docs/risorsa.md)

---

## 🧰 Command-Line Interface (CLI)

DVC includes a CLI tool to help you scaffold components and run useful commands.

### Usage

```bash
php vendor/bin/dvc [command] [options]
```

### Available Commands

- `serve`
  Start a local PHP development server

- `make::application`
  Creates basic application structures including `public` folder and `src/app/application.php` file

- `make::module <Name>`
  Generate a module framework in `src/app/<Name>` and a controller stub in `controllers`

### Example

```bash
vendor/bin/dvc make::module blog
```

- Creates `src/app/blog/controller.php` and `src/controller/blog.php` with a boilerplate structures.
- Which is available to view at [http://localhost:1265/blog](http://localhost:1265/blog) in your browser.

---

## 📁 Folder Structure

```
src/
├── app/
│   ├── <module>/controller.php
│   └── <module>/views/..
├── controllers/
├── public/
│   └── _mvp.php
├── vendor/
│   └── bin/dvc
├── composer.json
└── README.md
```

---

## 📄 License

Licensed under the MIT License.
