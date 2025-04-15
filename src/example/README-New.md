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

## 🛠️ Getting Started

### Requirements

- PHP 8.0 or higher
- Composer

### Installation

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

### Tutorial

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
