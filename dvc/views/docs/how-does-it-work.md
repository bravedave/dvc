# How does it work?

You need a web server like Apache, but for development
you can use PHP's built in web server.

You should configure your web server so that it sends all
appropriate requests to one *front-end-controller* PHP file.
* [apache](/docs/apache)

Your *application* executes via this PHP file.
* The most pervasive technique here is called PSR-4 : Autoloading

HTTP requests are re-routed within the app to controllers
and controller functions - and here you create the logic of
your application.

## To get started
### use
PHP's built in server:
* create your project
* execute the server
* browse to localhost

## Next
### create
a default controller to overwrite the build in controller
* [first app](/docs/firstapp)
