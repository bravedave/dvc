# DVC - Data View Controller
> PHP framework for simple powerful web applications and APIs.

* https://brayworth.com/docs/

* Can be configured to work with any client side framework
  * jquery-ui
  * bootstrap
  * pure
  * materialcss

## Running
* Install the template project from https://github.com/bravedave/dvc-template, it will pull in this project as part of the install


## Development Install
  _note these instructions for PHP Version 7.4.x_

For testing and development of core features on a Windows 10 computer
1. Install PreRequisits
   * Install PHP : http://windows.php.net/download/
     * Install the non threadsafe binary
       * Test by running php -v from the command prompt
         * If required install the VC++ runtime available from the php download page
       * by default there is no php.ini (required)
         * copy php.ini-production to php.ini
         * edit and modify/add (uncomment)
           * extension=fileinfo
           * extension=sqlite3
           * extension=mbstring
           * extension=openssl

   * Install Git : https://git-scm.com/
     * Install the *Git Bash Here* option
   * Install Composer : https://getcomposer.org/

2. Clone this Repo
   ```
   git clone https://github.com/bravedave/dvc.git dvc
   ```

2. Install dependencies
   ```
   cd dvc
   composer update
   ```

1. DVC is not intended to run this way, the vendor folder is in the wrong location - to overcome this, add the local path. note the .gitignore is set to exclude _autoload-local-path.php_ from upload to github:
   ```
   copy autoload-local-path.php-sample autoload-local-path.php
   ```

3. Run the test Environment from ./tests
   ```
   cd tests
   run.cmd
   ```

   ... the tests are visible on http://localhost/
