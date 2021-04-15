<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace beds_list\dao;

use dao\_dao;

class beds_list extends _dao {
	protected $_db_name = 'beds_list';
	protected $template = '\beds_list\dao\dto\beds_list';

	public function getAll( $fields = '*', $order = 'ORDER BY beds' ) {
		return ( parent::getAll( $fields, $order ));

	}

	static function beds() {
		$dao = new self;
		if ( $res = $dao->getAll()) {
			return $dao->dtoSet( $res);

		}

		return [];

	}

	public function createDefaults() {
		$this->Insert(['beds' => '1', 'description' => '1']);
		$this->Insert(['beds' => '2', 'description' => '2']);
		$this->Insert(['beds' => '3', 'description' => '3']);
		$this->Insert(['beds' => '4', 'description' => '4']);
		$this->Insert(['beds' => '5', 'description' => '5']);
		$this->Insert(['beds' => '6', 'description' => '6']);

	}

}
