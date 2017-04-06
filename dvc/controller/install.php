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
	public function error( $sError = 'generic' ) {
		$p = new Page();
		$p->header();
		$p->primary();
		if ( $sError == 'nodatapath' )
			$this->loadview('error-nodatapath');

		else
			$this->loadview('error-generic');

	}

	public function db() {
		if ( config::checkDBconfigured())
			Response::redirect( url::$URL );

		if ( $this->isPost()) {
			// print 'it\'s post allright';
			// sys::dump( $this->getPost());
			$db_host = $this->getPost("db_host");
			$rootpass = $this->getPost("root_password");
			$db_name = trim( str_replace( ' ', '', filter_var( $this->getPost("db_name"), FILTER_SANITIZE_STRING)));
			$db_user = $this->getPost("db_user");
			$db_pass = $this->getPost("db_pass");

			$db = new dvc\db( $db_host, NULL, 'root', $rootpass );  // will error if unable to connect
			if ( $db_name != '' ) {
				if ( $db_user != '' ) {
					if ( $db_pass != '' ) {

						$root = sprintf('%s', application::app()->getRootPath());
						$path = sprintf('%s%sdata', application::app()->getRootPath(), DIRECTORY_SEPARATOR );
						if ( is_writable( $root ) || is_writable( $path)) {
							if ( !is_dir( $path))
								mkdir( $path, '0777');

							if ( !is_dir( $path))
								Response::redirect( self::$url . 'error/nodatapath');

							$path = sprintf('%s%sdb.json', $path, DIRECTORY_SEPARATOR );
							$a = array(
								'db_type' => "mysql",
								'db_host' => $db_host,
								'db_name' => $db_name,
								'db_user' => $db_user,
								'db_pass' => $db_pass);
							if ( file_put_contents( $path, json_encode( $a))) {
								$db->Q( sprintf( "CREATE DATABASE `%s`", $db_name ));
								$db->Q( sprintf( "GRANT ALL ON `%s`.* TO '%s' IDENTIFIED BY '%s'", $db_name, $db_user, $db_pass ));
								$db->Q( "FLUSH PRIVILEGES");

								Response::redirect( url::$URL, 'created database');

							}

						}
						else {
							printf( 'please create a writable data folder : %s', $path );
							printf( '<br /><br />mkdir --mode=0777 %s', $path );

						}

					}
					else {
						Response::redirect( self::$url, "invalid db password (cannot be blank)");

					}

				}
				else {
					Response::redirect( self::$url, "invalid db user (cannot be blank)");

				}

			}
			else {
				Response::redirect( self::$url, "invalid db name");

			}

		}
		else {
			$p = new Page();
			$p->header();
			$p->primary();
			$this->loadview('db-parameters');

		}

	}

}
