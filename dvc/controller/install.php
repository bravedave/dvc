<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	Note to Self: This file gets distributed

	*/

class install extends Controller {
	public $RequireValidation = \config::lockdown;

	public function error( $sError = 'generic' ) {
		$p = new Page();
		$p->header();
		$p->primary();
		if ( $sError == 'nodatapath' )
			$this->loadview('error-nodatapath');

		else
			$this->loadview('error-generic');

	}

	protected function writeDBJson( $a) {
		$root = sprintf('%s', application::app()->getRootPath());
		$path = sprintf('%s%sdata', application::app()->getRootPath(), DIRECTORY_SEPARATOR );

		if ( is_writable( $root ) || is_writable( $path)) {
			if ( !is_dir( $path))
				mkdir( $path, '0777');

			if ( !is_dir( $path))
				Response::redirect( self::$url . 'error/nodatapath');

			$path = sprintf('%s%sdb.json', $path, DIRECTORY_SEPARATOR );
			if ( file_put_contents( $path, json_encode( $a)))
				return ( TRUE);


		}
		printf( 'please create a writable data folder : %s', $path );
		printf( '<br /><br />mkdir --mode=0777 %s', $path );

		return ( FALSE);

	}

	protected  function postHandler() {
		if ( 'disabled' == \config::$DB_TYPE) return;	// silent fail
		if ( 'dbname' != \config::$DB_NAME) return;	// silent fail

		\sys::logger( sprintf( 'dbname: %s', \config::$DB_NAME));

		// print 'it\'s post allright';
		// sys::dump( $this->getPost());
		$db_host = $this->getPost("db_host");
		$db_name = trim( str_replace( ' ', '', filter_var( $this->getPost("db_name"), FILTER_SANITIZE_STRING)));
		$db_user = $this->getPost("db_user");
		$db_pass = $this->getPost("db_pass");

		if ( $db_name == '' ) {
			throw new \Exception( "invalid db name");

		}
		elseif ( $db_user == '' ) {
			throw new \Exception( "invalid db user (cannot be blank)");

		}
		elseif ( $db_pass == '' ) {
			throw new \Exception( "invalid db password (cannot be blank)");

		}
		else {
			try {
				$db = new dvc\db( $db_host, $db_name, $db_user, $db_pass );  // will error if unable to connect
				$this->writeDBJson( array(
					'db_type' => "mysql",
					'db_host' => $db_host,
					'db_name' => $db_name,
					'db_user' => $db_user,
					'db_pass' => $db_pass));
				Response::redirect( url::$URL, 'linked database');

			}
			catch( \Exception $e) {

				die( $e);

				$rootpass = $this->getPost("root_password");
				$db = new dvc\db( $db_host, NULL, 'root', $rootpass );  // will error if unable to connect

				if ($this->writeDBJson( array(
					'db_type' => "mysql",
					'db_host' => $db_host,
					'db_name' => $db_name,
					'db_user' => $db_user,
					'db_pass' => $db_pass))) {

					$db->Q( sprintf( "CREATE DATABASE IF NOT EXISTS `%s`", $db_name ));
					$db->Q( sprintf( "GRANT ALL ON `%s`.* TO '%s' IDENTIFIED BY '%s'", $db_name, $db_user, $db_pass ));
					$db->Q( "FLUSH PRIVILEGES");

					Response::redirect( url::$URL, 'created database');

				}

			}

		}

	}

	public function index() {
		if ( $this->isPost()) {
			$this->postHandler();

		}

	}

	public function db() {
		if ( config::checkDBconfigured())
			Response::redirect( url::$URL );

		$p = new Page();
			$p
				->header()
				->primary();

			$this->loadview('db-parameters');

	}

}
