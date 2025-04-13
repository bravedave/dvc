# Your PHP Application

## Overview
Your PHP Application is a modular and structured PHP-based application designed for ease of development and maintainability. It leverages Composer for dependency management and provides a command-line interface (CLI) for common development tasks.

## Directory Structure
```
composer.json         # Composer configuration file
composer.lock         # Composer lock file
public/               # Publicly accessible files
  _mvp.php            # Entry point for the application
src/                  # Application source code
  app/                # Core application logic
    application.php   # Main application class
  controller/         # Application controllers
  data/               # Application data
    defaults-sample.json # Sample default data
    readme.txt        # Data-related documentation
vendor/               # Composer dependencies
```

## Requirements
- PHP 8.0 or higher
- Composer

## Installation
1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd your-php-application
   ```
2. Install dependencies using Composer:
   ```bash
   composer install
   ```

## Usage
- The application entry point is `_mvp.php` in the `public/` directory.
- Core application logic resides in the `src/app/` directory.
- Use the `vendor/bin/dvc` CLI for development tasks.

## Development
### Using the `vendor/bin/dvc` CLI
The `vendor/bin/dvc` CLI provides a set of commands to streamline development. Some common commands include:
- `make:application`: Sets up the default application structure.
- `make:module <module-name>`: Creates a new module with the specified name.
- `clear:cache`: Clears the application cache.

Run the following command to see all available options:
```bash
vendor/bin/dvc --help
```

### Adding a New Module
1. Use the `make:module` command to generate the module structure:
   ```bash
   vendor/bin/dvc make:module <module-name>
   ```
2. Add necessary files such as controllers, DAOs, DTOs, and views.
3. Update the autoloader if required.

### Running Tests
- Place your test files in the `vendor/bravedave/dvc/tests/` directory.
- Run tests using the appropriate testing framework.

## Contributing
Contributions are welcome! Please follow the coding standards and submit a pull request.

## License
This project is licensed under the MIT License. See the `LICENSE` file for details.

## Acknowledgments
- [BraveDave DVC](https://github.com/bravedave/dvc) for the core framework.
- All contributors and open-source libraries used in this project.