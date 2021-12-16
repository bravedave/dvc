<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class template {

  const pdf_css = __DIR__ . '/css/brayworth.pdf.css';
  protected $_template = '', $_css = [];

  public $title = '';

  function __construct($filepath, $css = null) {
    if ((bool)$css) {
      $this->_css[] = file_get_contents($css);
    }

    $this->_template = file_get_contents($filepath);
  }

  function css(string $path = self::pdf_css) : self {
    if ((bool)$path) {
      $this->_css[] = file_get_contents($path);
    }

    return ($this);  // chain

  }

  function replace($var, $content) {
    $this->_template = str_replace(sprintf('{{%s}}', $var), $content, $this->_template);

    return ($this);  // chain

  }

  function render() {
    if (count($this->_css)) {
      // create instance
      $cssToInlineStyles = new CssToInlineStyles;
      // output
      return $cssToInlineStyles->convert(
        sprintf(
          '<!DOCTYPE html><html lang="en"><head><title>%s</title></head><body>%s</body></html>',
          $this->title,
          $this->_template
        ),
        implode('', $this->_css)
      );
    } else {
      return ($this->_template);
    }
  }
}
