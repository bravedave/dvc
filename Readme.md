# DVC - Data View Controller

> PHP framework for web applications and APIs.
Configured with Bootstrap4, but could just as easily support others.

* <https://brayworth.com/docs/>

## Development Install

note: _instructions for PHP Version 7.4.x_

For testing and development of core features on a Windows 10 computer

### Install PreRequisits

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

### Clone this Repo

```bash
git clone https://github.com/bravedave/dvc.git dvc
```

### Install dependencies

```bash
cd dvc
composer update
```

### Run the test Environment from ./tests

```bash
cd tests
run.cmd
```

... the tests are visible on <http://localhost/>

There is a tutorial at <src/dvc/views/docs/risorsa.md>
