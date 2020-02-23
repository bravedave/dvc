<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
namespace dvc;

class Json {
	protected $_json = [];
	protected $dumpOnDestruct = true;

	static function nak( $description) {
		return ( new Json( [ 'response' => 'nak', 'description' => $description]));

	}

	static function ack( $description) {
		return ( new Json( [ 'response' => 'ack', 'description' => $description]));

	}

	function __construct( $a = null) {
		if ( !is_null( $a))
			$this->_json = (array)$a;

	}

	public function add( $key, $data ) {
		$this->_json[$key] = $data;
		return ( $this);	// chain

	}

	public function append($data ) {
		$this->_json[] = $data;
		return ( $this);	// chain

	}

	public function __destruct() {
		if ( $this->dumpOnDestruct) {
			\Response::json_headers();
			print json_encode( $this->_json );

		}

	}

	public function count() {
		return count( $this->_json );

	}

	public function dump() {
		$this->dumpOnDestruct = false;
		\sys::dump( $this->_json );

	}

	public function merge($data ) {
		$a = array_merge( $this->_json, $data);
		$this->_json[] = $a;
		return ( $this);	// chain

	}

	public function prepend($data ) {
		array_unshift( $this->_json, $data);
		return ( $this);	// chain

	}

	public function print() {
		$this->dumpOnDestruct = false;
		print json_encode( $this->_json );

	}

	public function toArray() {
		$this->dumpOnDestruct = false;
		return $this->_json;

	}

}