# DVC

## DVC - Data View Controller

> PHP framework for web applications and APIs.

* <https://brayworth.com/docs/>

* Configured with Bootstrap4, but could just as easily support others.

### Running

* Install the template project from https://github.com/bravedave/hello, it will pull in this project as part of the install

### Development Install

note: _instructions for PHP Version 7.4.x_

For testing and development of core features on a Windows 10 computer

1. Install PreRequisits
   * Install PHP : <http://windows.php.net/download/>
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

   * Install Git : <https://git-scm.com/>
     * Install the *Git Bash Here* option
   * Install Composer : <https://getcomposer.org/>
1. Clone this Repo
```bash
git clone https://github.com/bravedave/dvc.git dvc
```
1. Install dependencies
```bash
cd dvc
composer update
```
1. Run the test Environment from ./tests
```bash
cd tests
run.cmd
```

... the tests are visible on <http://localhost/>
