# Namespaces

This is a PSR style application;
* see <a href="http://www.php-fig.org/" target="_blank">http://www.php-fig.org/</a>

Basically, use the correct namespace rule and the file will be loaded on demand

for example: to load the boostrap page in the [hello controller example](hello.md)
reference the namespace dvc\pages\bootstrap - the autoloader will do the rest

Mostly a file in the root namespace will extend a file in the dvc namespace.
This makes it possible to overwrite the behaviour of any file.

