<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\controller;

use config, Controller, dvc, Response;

class install extends Controller {
  public $RequireValidation = config::lockdown;

  protected function _index() {
  }

  protected function before() {
    self::application()::app()->exclude_from_sitemap = true;
    parent::before();
  }

  protected function writeDBJson($a) {
    $root = sprintf('%s', self::application()::app()->getRootPath());
    $path = sprintf('%s%sdata', self::application()::app()->getRootPath(), DIRECTORY_SEPARATOR);

    if (is_writable($root) || is_writable($path)) {
      if (!is_dir($path))
        mkdir($path, '0777');

      if (!is_dir($path))
        Response::redirect(self::$url . 'error/nodatapath');

      $path = sprintf('%s%sdb.json', $path, DIRECTORY_SEPARATOR);
      if (file_put_contents($path, json_encode($a)))
        return (TRUE);
    }
    printf('please create a writable data folder : %s', $path);
    printf('<br /><br />mkdir --mode=0777 %s', $path);

    return (false);
  }

  protected  function postHandler() {
    $action = $this->getPost('action');

    if ('create-database' == $action) {
      if ('disabled' == config::$DB_TYPE) return;  // silent fail
      if ('sqlite' == config::$DB_TYPE) return;  // silent fail, only creates sqlite file in application/data
      if ('dbname' != config::$DB_NAME) return;  // silent fail

      $type = $this->getPost('db_type');
      if ('sqlite' == $type) {
        $this->writeDBJson(['db_type' => 'sqlite']);
        Response::redirect(\url::$URL, 'set database sqlite');
      } elseif ('mysql' == $type) {
        \sys::logger(sprintf('dbname : %s', config::$DB_NAME));

        // print 'it\'s post allright';
        // sys::dump( $this->getPost());
        $db_host = $this->getPost("db_host");
        $db_name = trim(str_replace(' ', '', htmlspecialchars($this->getPost("db_name"))));
        $db_user = $this->getPost("db_user");
        $db_pass = $this->getPost("db_pass");

        if ($db_name == '') {
          throw new \Exception("invalid db name");
        } elseif ($db_user == '') {
          throw new \Exception("invalid db user (cannot be blank)");
        } elseif ($db_pass == '') {
          throw new \Exception("invalid db password (cannot be blank)");
        } else {
          try {
            $db = new dvc\db($db_host, $db_name, $db_user, $db_pass);  // will error if unable to connect
            $this->writeDBJson([
              'db_type' => 'mysql',
              'db_host' => $db_host,
              'db_name' => $db_name,
              'db_user' => $db_user,
              'db_pass' => $db_pass
            ]);

            Response::redirect(\url::$URL, 'linked database');
          } catch (\Exception $e) {

            die($e);

            $rootpass = $this->getPost("root_password");
            $db = new dvc\db($db_host, null, 'root', $rootpass);  // will error if unable to connect

            if ($this->writeDBJson([
              'db_type' => 'mysql',
              'db_host' => $db_host,
              'db_name' => $db_name,
              'db_user' => $db_user,
              'db_pass' => $db_pass
            ])) {

              $db->Q(sprintf("CREATE DATABASE IF NOT EXISTS `%s`", $db_name));
              $db->Q(sprintf("GRANT ALL ON `%s`.* TO '%s' IDENTIFIED BY '%s'", $db_name, $db_user, $db_pass));
              $db->Q("FLUSH PRIVILEGES");

              Response::redirect(\url::$URL, 'created database');
            }
          }
        }
      }
    } else {
      parent::postHandler();
    }
  }

  public function error($sError = 'generic') {
    $load = 'nodatapath' == $sError ? 'error-nodatapath' : 'error-generic';

    $this->render([
      'title' => $this->title = 'Configure Database',
      'primary' => $load,
      'secondary' => 'blank',

    ]);
  }

  public function db() {
    if (config::checkDBconfigured())
      Response::redirect();

    $this->render([
      'title' => $this->title = 'Configure Database',
      'primary' => 'db-parameters',
      'secondary' => 'blank',

    ]);
  }
}
