<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class sitemap extends Controller {
	function __construct( $rootPath ) {
		parent::__construct( $rootPath );
		application::app()->exclude_from_sitemap = TRUE;


	}

	public function txt() {
		Response::text_headers();

		print url::$PROTOCOL . url::$URL . PHP_EOL;

		$dao = new dao\sitemap();
		if ( $dtos = $dao->getSiteMap()) {
			foreach( $dtos as $dto)
				print $dto->path . PHP_EOL;

		}

	}

	public function toggle( $id = 0 ) {
		if ( currentUser::valid()) {
			if ( currentUser::isadmin()) {
				if ( (int)$id > 0 ) {
					$dao = new dao\sitemap();
					if ( $dto = $dao->getById( $id)) {
						$this->db->Update( 'sitemap', array(
							'exclude_from_sitemap' => ( $dto->exclude_from_sitemap ? 0 : 1 )), 'where id = ' . $id );

						Response::redirect( 'sitemap/report', 'Updated ..' );

					}

				}

			}

		}

	}

	public function report() {
		if ( currentUser::valid()) {
			if ( currentUser::isadmin()) {
				$p = new Page( 'Sitemap Report');
				$p->header();
				$p->title();

				$p->secondary();
				//~ $this->menu();

				$p->primary();
				component\sitemap::report();

			}

		}

	}

}
