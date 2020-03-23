<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\html;
//~ Use \dvc;

class element {
	protected $tag, $children, $_content, $attributes;
	protected $_rendered = false;

	protected static $indent = 1;

	public $selfClosing = false;
	static $EOL = PHP_EOL;

	function __construct( $tag, $content = null, $attributes = null ) {
		$this->tag = $tag;
		$this->children = [];
		$this->attributes = (array)$attributes;

		$this->selfClosing = in_array( $tag, [ 'img', 'hr', 'br', 'link' ]);

		$contentType = gettype( $content);
		if ( $contentType == 'object' ) {
			$contentClass = get_class( $content);
			if ( $contentClass = 'dvc\html\element' || is_subclass_of( $content, 'dvc\html\element' ))
				$this->appendChild( $content);

			else
				$this->_content = $content;

		}
		else {
			$this->_content = $content;

		}

	}

	function __destruct() {
		if ( !$this->_rendered )
			$this->render();

	}

	public function content( $return = false) {
		$t = '';
		if ( !( empty( $this->_content ))) {
			if ( is_string( $this->_content))
				$t = $this->_content;

		}

		if ( $return) {
			return ( $t);

		}
		else {
			print $t;
			$t = '';

		}

	}

	public function render( $return = false ) {
		$selfCloser = ( $this->selfClosing ? ' /' : '' );

		$r = [];

		if ( count( $this->attributes ) > 0 ) {
			$a = [];
			foreach ( $this->attributes as $k => $v ) {
				$a[] = sprintf( '%s="%s"', $k, $v );

			}

			$r[] = sprintf( '%s%s<%s %s%s>',
				self::$EOL,
				str_repeat( chr(9), self::$indent ),
				$this->tag,
				implode( ' ', $a ),
				$selfCloser );

		}
		else {
			$r[] = sprintf( '%s%s<%s%s>',
				self::$EOL,
				str_repeat( chr(9), self::$indent ),
				$this->tag,
				$selfCloser);

		}

		self::$indent ++;

		if ( !$return) {
			print implode('', $r);
			$r = [];

		}

		$r[] = $this->content( $return);

		foreach ( $this->children as $child ) {
			$r[] = $child->render( $return);

		}

		self::$indent --;
		if ( !$this->selfClosing ) {
			if ( count( $this->children ) > 0 ) {
				$r[] = sprintf( '%s</%s>%s',
					str_repeat( chr(9), self::$indent ),
					$this->tag,
					self::$EOL

				);

			}
			else {
				$r[] = sprintf( '</%s>%s',
					$this->tag,
					self::$EOL

				);

			}

		}

		$this->_rendered = true;

		if ( $return) {
			return implode('', $r);

		}
		else {
			print implode('', $r);
			return '';

		}

	}

	public function attributes( array $a ) {
		$this->attributes = array_merge( $this->attributes, $a );
		return ( $this);

	}

	public function append( $tag, $content = null, array $attributes = null ) {

		$contentType = gettype( $tag);
		if ( $contentType == 'object' ) {
			$contentClass = get_class( $tag);
			if ( is_subclass_of( $tag, 'dvc\html\element' ))
				$this->appendChild( $tag);

			else
				throw new \Exception( "Invalid Content Type");

		}
		else {
			if ( $tag == 'input' ) {
				$el = new input();

				$contentType = gettype( $content);

				if ( preg_match( '@(integer|string)@', $contentType ))
					$el->attributes( array( 'value' => $content ));


			}
			elseif ( file_exists( __DIR__ . '/' . $tag . '.php' )) {
				$class = __NAMESPACE__ . '\\' . $tag;
				//~ \dvc\sys::logger( "using: new $class( '$content' )");
				$el = new $class( $content );

			}
			else {
				//~ \dvc\sys::logger( 'did not find:' . __DIR__ . $tag . '.php');
				$el = new element( $tag, $content );

			}

			if ( !(is_null( $attributes)))
				$el->attributes( $attributes );

			$this->appendChild( $el);

			return ( $el);

		}

	}

	public function appendChild( element $element ) {
		$this->children[] = $element;

	}

	public function addChild( element $element ) {
		$this->appendChild( $element );

	}

	static function lorum() {
		return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

	}

}
