# DVC – Data View Controller

Work in Progress - not accurate

**DVC** is a lightweight, PSR-4-compliant PHP framework for building modern web applications and APIs. It comes pre-configured with Bootstrap but is flexible enough to work with any front-end you prefer.

📚 **Documentation:** [https://brayworth.com/docs](https://brayworth.com/docs)

---

## 🚀 Features

- **MVC Architecture** – Clean separation of concerns (Models, Views, Controllers)
- **Built-in CLI Tool** – Generate controllers, serve your app, and more
- **Bootstrap-Ready** – Ships with Bootstrap, easily replaceable
- **Simple & Modern** – No heavy abstractions, just solid structure

---

## 🛠️ Getting Started

### Requirements

- PHP 8.0 or higher
- Composer

### Installation

```bash
mkdir myapp && cd myapp
git clone https://github.com/bravedave/dvc .
composer install
```

### Run the Dev Server

```bash
composer start
```

Then open [http://localhost:8080](http://localhost:8080) in your browser.

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
  Generate a controller stub in `app/Controllers`

- `make::module <Name>`
  Generate a module framework in `app/<Name>` and a controller stub in `controllers`

- `version`
  Display the current framework version

### Example

```bash
vendor/bin/dvc make::module blog
```

Creates `app\blog\controller.php` and `controllers/blog.php` with a boilerplate structures.

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
