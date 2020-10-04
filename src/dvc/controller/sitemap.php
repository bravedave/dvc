<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class sitemap extends Controller {
	public $RequireValidation = \config::lockdown;

	protected function before() {
		application::app()->exclude_from_sitemap = true;
		parent::before();

	}

	public function txt() {
		Response::text_headers();

		$url = url::$PROTOCOL . url::$URL;

		print $url . PHP_EOL;

		$dao = new dao\sitemap;
		if ( $dtos = $dao->getSiteMap()) {
			foreach( $dtos as $dto) {
				if ( substr($dto->path, 0, strlen($url)) === $url)
					print $dto->path . PHP_EOL;

			}

		}

	}

	public function toggle( $id = 0 ) {
		if ( currentUser::valid()) {
			if ( currentUser::isadmin()) {
				if ( (int)$id > 0 ) {
					$dao = new dao\sitemap;
					if ( $dto = $dao->getByID( $id)) {
						$dao->UpdateByID([
							'exclude_from_sitemap' => ( $dto->exclude_from_sitemap ? 0 : 1 )
						], $id);

						Response::redirect( 'sitemap/report', 'Updated ..' );

					}

				}

			}

		}

	}

	public function report() {
		if ( $this->Request->ServerIsLocal()) {
			$this->render([ 'primary' => 'report']);

		}
		elseif ( currentUser::valid() && currentUser::isadmin()) {
			$this->render([ 'primary' => 'report' ]);

		}

	}

}
