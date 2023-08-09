<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class template {

  const pdf_css = __DIR__ . '/css/brayworth.pdf.css';
  protected $_template = '', $_css = [];

  public $title = '';
  public $subject = '';
  public $author = '';
  public $keywords = '';

  function __construct($filepath, $css = null) {

    if ((bool)$css) $this->_css[] = file_get_contents($css);

    $this->author = config::$WEBNAME;
    $this->_template = file_get_contents($filepath);
  }

  function css(string $path = self::pdf_css): self {

    if ((bool)$path) $this->_css[] = file_get_contents($path);
    return $this;  // chain
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

  function html() : string {

    if (count($this->_css)) {

      // create instance
      $cssToInlineStyles = new CssToInlineStyles;

      return $cssToInlineStyles->convert(
        $this->_template,
        implode('', $this->_css)
      );
    } else {

      return $this->_template;
    }
  }

  function render() : string {

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

        $meta = [
          sprintf('<meta name="description" content="%s">', $this->subject),
          sprintf('<meta name="author" content="%s">', $this->author),
          sprintf('<meta name="keywords" content="%s">', $this->keywords)
        ];

        // output
        return $cssToInlineStyles->convert(
          sprintf(
            '<!DOCTYPE html><html lang="en"><head><title>%s</title>%s</head><body>%s</body></html>',
            $this->title,
            implode('', $meta),
            $this->_template
          ),
          implode('', $this->_css)
        );
      }
    } else {

      return $this->_template;
    }
  }
}
