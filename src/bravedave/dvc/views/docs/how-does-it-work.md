# How does it work?

To progress past R & D a web server like Apache recommended, for development you can use PHP's built in web server.

Configure the web server so that it sends all appropriate requests to one *front-end-controller* PHP file.
* [apache](/docs/apache)

Your *application* executes via this PHP file.
* The most pervasive technique here is called [PSR-4 : Autoloading](https://www.php-fig.org/psr/psr-4/)

HTTP requests are re-routed within the app to controllers and controller functions - and there you create the logic of your application.

## To get started
### use
PHP's built in server:
1. create your project
2. execute the server
3. browse to localhost

## Next
### create
a default controller to overwrite the build in controller
* [first app](/docs/firstapp)
