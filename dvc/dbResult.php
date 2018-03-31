<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

Namespace dvc;

class dbResult {
	var $result;
	protected $db = FALSE;

	public function __construct( \mysqli_result  $result = NULL, dbi $db = NULL) {
		if ( $result)
			$this->result = $result;
		if ( $db)
			$this->db = $db;

	}

	public function __destruct() {
		if ( $this->result)
			$this->result->free();

	}

	public function data_seek( $i = 0) {
		if ( $this->result)
			$this->result->data_seek(0);

	}

	public function reset() {
		$this->data_seek( 0);

	}

	public function fetch() {
		return ( $this->result->fetch_assoc());

	}

	public function fetch_object() {
		return ( $this->result->fetch_object());

	}

	public function dto( $template = NULL) {
		if ( $dto = $this->result->fetch_assoc()) {
			if ( is_null( $template))
				return ( new \dao\dto\dto( $dto));

			return ( new $template( $dto));

		}

		return ( FALSE);

	}

	public function dtoSet( $func = NULL, $template = NULL) {
		/**
		 * extend like:
		 * $dtoSet = $res->dtoSet( function( $dto) {
		 * 	return $dto;
		 * });
		 */
		$ret = [];
		if ( is_callable( $func)) {
			while ( $dto = $this->dto( $template)) {
				if ( $d = $func( $dto))
					$ret[] = $d;

			}

		}
		else {
			while ( $dto = $this->dto( $template))
				$ret[] = $dto;

		}

		return ( $ret);

	}

	public function fetch_row() {
		return ( $this->result->fetch_row());

	}

	public function num_rows() {
		return ( $this->result->num_rows );

	}

	public function num_fields() {
		return ( $this->result->field_count );

	}

	public function fetch_fields() {
		return ( $this->result->fetch_fields());

	}

	public function csv( $formatter = NULL) {

		$finfo = $this->fetch_fields();

		$a = array();
		foreach ($finfo as $val) {
			if ( $val->name == "thumbnail" ) continue;
			if ( $val->name == "propertykey" ) continue;
			if ( $val->name == "query" ) continue;
			$a[] = $val->name;

		}

		$array[] = $a;
		while ( $row = $this->fetch()) {
			if ( is_null( $formatter)) {
				$a = array();
				foreach ( $row as $k => $v )
					$a[] = $v;

			}
			else {
				$a = $formatter( $row);

			}

			$array[] = $a;

		}

		if (count($array) == 0)
			return null;

		ob_start();
		$df = fopen("php://output", 'w');
		foreach ( $array as $row)
			fputcsv($df, $row);

		fclose($df);

		return ob_get_clean();

	}

	public function report() {
		$tf = $this->num_fields();

		$tr = new html\tr;

		$finfo = $this->fetch_fields();
		foreach ($finfo as $val)
			$tr->td( sprintf( '<td>%s</td>', $val->name ));

		$head = new html\thead;
			$head->append( $tr);

		$i = 0;
		$body = new html\tbody;
		while ( $row = $this->fetch()) {
			$i++;
			$tr = new html\tr( NULL, array( 'role' => 'item'));
			foreach ($finfo as $val) {
				$v = $row[$val->name];
				if ( $this->db && $this->db->field_type( $val->type) == 'DATE' ) {
					$t = strtotime($v);
					if ( $t > 0 )
						$v = date( config::$DATE_FORMAT, $t );

				}

				$tr->td( sprintf( '<td data-field="%s">%s</td>', $val->name, $v ));

			}
			$body->append( $tr);

		}

		$table = new html\table( 'table table-sm table-striped');
			$table->append( $head);
			$table->append( $body);

		$div = new html\div;
			$div->append( $table);
			$div->append( 'span', sprintf( '%d row(s)',  $i ));

		return ( $div);

	}

}
