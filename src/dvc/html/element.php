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

class element {
	protected $tag, $children, $_content, $attributes;
	protected $_rendered = false;

	protected static $indent = 1;

	public $selfClosing = false;

	public $id = '';

	static $EOL = PHP_EOL;

	function __construct( $tag, $content = null, $attributes = null ) {
		$this->tag = $tag;
		$this->children = [];
		$this->_attributes = (array)$attributes;

		if ( isset( $this->_attributes['id'])) {
			$this->id = $this->_attributes['id'];

		}

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

	public function addChild( element $element ) : element {
		$this->appendChild( $element );

		return $this;

	}

	public function addClass( string $class) : element {
		$_classes = explode( ' ', $class);
		$classes = [];
		if ( isset( $this->_attributes['class'])) {
			$classes = explode( ' ', $this->_attributes['class']);

		}

		foreach ($_classes as $_class) {
			if ( !\in_array( $_class, $classes)) {
				$classes[] = $_class;

			}

		}

		// \sys::logger( sprintf('before <%s> : %s', $this->_attributes['class'], __METHOD__));
		$this->_attributes['class'] = implode( ' ', $classes);
		// \sys::logger( sprintf('after <%s> : %s', $this->_attributes['class'], __METHOD__));

		return $this;

	}

	public function append( $tag, $content = null, array $attributes = null ) {

		$contentType = gettype( $tag);
		if ( $contentType == 'object' ) {
			$contentClass = get_class( $tag);
			if ( is_subclass_of( $tag, 'dvc\html\element' )) {
				$this->appendChild( $tag);
				return $tag;

			}
			else {
				throw new \Exception( "Invalid Content Type");

			}

		}
		else {
			if ( $tag == 'input' ) {
				$el = new input;
				$contentType = gettype( $content);
				if ( preg_match( '@(integer|string)@', $contentType ))
					$el->attributes( array( 'value' => $content ));


			}
			elseif ( file_exists( __DIR__ . '/' . $tag . '.php' )) {
				$class = __NAMESPACE__ . '\\' . $tag;
				$el = new $class( $content );

			}
			else {
				$el = new element( $tag, $content );

			}

			if ( !(is_null( $attributes)))
				$el->attributes( $attributes );

			$this->appendChild( $el);

			return ( $el);

		}

	}

	public function appendChild( element $element ) : element {
		$this->children[] = $element;

		return $this;

	}

	public function attributes( array $a ) : element {
		$this->_attributes = array_merge( $this->_attributes, $a );
		if ( isset( $this->_attributes['id'])) {
			$this->id = $this->_attributes['id'];

		}

		return ( $this);

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

	static function lorum() : string {
		return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

	}

	public function removeClass( string $class) : element {
		$_classes = explode( ' ', $class);
		$classes = [];
		if ( isset( $this->_attributes['class'])) {
			$classes = explode( ' ', $this->_attributes['class']);

		}

		foreach ($_classes as $_class) {
			if ( in_array( $_class, $classes)) {
				$_index = array_search( $_class, $classes);
				unset( $classes[ $_index]);

			}

		}

		$this->_attributes['class'] = implode( ' ', $classes);

		return $this;

	}

	public function render( $return = false ) {
		$selfCloser = ( $this->selfClosing ? ' /' : '' );

		$r = [];

		if ( count( $this->_attributes ) > 0 ) {
			$a = [];
			foreach ( $this->_attributes as $k => $v ) {
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

	public function setContent( string $s) {
		$this->_content = $s;

	}

}
