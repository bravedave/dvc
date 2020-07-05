###### [Docs](/docs/) | Installation

note _https://github.com/bravedave/dvc_ may be more up to date

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

3. Run the test Environment from ./tests
   ```
   cd tests
   run.cmd
   ```

   ... the tests are visible on http://localhost/
