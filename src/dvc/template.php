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

  function css(string $path = self::pdf_css): self {
    if ((bool)$path) {
      $this->_css[] = file_get_contents($path);
    }

    return ($this);  // chain

  }

  /**
   * replace( var, content) - replace the var with content
   *
   * @param var
   * @param string content
   *
   * @return self // chain
   */
  function replace($var, string $content = ''): self {
    if (\is_array($var)) {
      foreach ($var as $k => $v) {
        $this->replace($k, $v);
      }
    } else {
      $this->_template = str_replace(sprintf('{{%s}}', $var), $content, $this->_template);
    }
    return $this;  // chain

  }

  function render() {
    if (count($this->_css)) {
      // create instance
      $cssToInlineStyles = new CssToInlineStyles;

      if (preg_match('@^<!DOCTYPE html>@', $this->_template)) {

        // output
        return $cssToInlineStyles->convert(
          $this->_template,
          implode('', $this->_css)
        );
      } else {

        // output
        return $cssToInlineStyles->convert(
          sprintf(
            '<!DOCTYPE html><html lang="en"><head><title>%s</title></head><body>%s</body></html>',
            $this->title,
            $this->_template
          ),
          implode('', $this->_css)
        );
      }
    } else {

      return ($this->_template);
    }
  }
}
