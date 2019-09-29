<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
namespace dvc;

class menuitem {
	var $style,
		$className,
		$label, $url, $target;

	function __construct( $label, $url = FALSE, $style = '' ) {
		$this->className = 'ui-menu-item';
		$this->style = $style;
		$this->label = $label;
		$this->url = $url;

	}

	static function separator() {
		return new menuitem( '<div class="menu-item-separator" />' );

	}

	public function resolve() {
		if ( $this->url ) {
			return sprintf( '<li %s %s><a href="%s" %s>%s</a></li>',
				( $this->className == '' ? '' : 'class="' . $this->className . '"' ),
				$this->style,
				$this->url,
				( is_null( $this->target) ? '' : 'target="' . $this->target . '"' ),
				$this->label );

		}
		else {
			return sprintf( '<li %s %s>%s</li>',
				( $this->className == '' ? '' : 'class="' . $this->className . '"' ),
				$this->style,
				$this->label );

		}

	}

	public function toElement() {
		$a = array();
		if ( $this->className != '' )
			$a['class'] = $this->className;

		if ( $this->style != '' ) {
			/*
			 * legacy handle, style will contain style="styles"
			 */
			$a['style'] = preg_replace( '@(style|=|")@', '', $this->style );

		}

		if ( !is_null( $this->target))
			$a['target'] = $this->target;

		if ( $this->url ) {
			$li = new html\li();
				$li->attributes( $a);
				$li->append( 'a', $this->label, array( 'href' => $this->url ));
			return ( $li);

		}
		else {
			$li= new html\li( $this->label);
				$li->attributes( $a);
				if ( $this->className != '' )
					$li->attributes( array( 'class' => $this->className ));
				return ( $li);

		}

	}

}
