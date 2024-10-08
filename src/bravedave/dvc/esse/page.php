<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\esse;

use bravedave\dvc\{bs, Response};
use Closure, strings;
use config;
use currentUser;
use theme;

/**
 * a class to control the
 * creation of a standard html page
 */
class page {
  protected bool $_aside = false;
  protected bool $_body = false;
  protected bool $_head = false;
  protected bool $_main = false;
  protected bool $_mainrow = false;
  protected bool $_open = false;

  public string $bodyClass = '';
  public string $container = 'container-fluid';

  public array $scripts = [];
  public array $meta = [];
  public array $css = [];
  public array $late = [];

  /**
   * instantiate a page and install bootstrap
   */
  public static function bootstrap(): self {
    $p = new self;

    bs::$VERSION = config::$BOOTSTRAP_VERSION = '5';
    $p->meta[] = '<meta charset="utf-8">';
    $p->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';

    $p->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/jquery'));
    $p->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/brayworth/js'));
    $p->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/brayworth/dopo'));

    $css = strings::url('assets/bootstrap/css/5');
    if ($theme = currentUser::option('theme')) {

      $css = strings::url('assets/bootstrap/css/5?t=' . $theme);
    } elseif ($theme = config::$THEME) {

      $css = strings::url('assets/bootstrap/css/5?t=' . $theme);
    }
    $icons = strings::url('assets/bootstrap/icons');
    $js = strings::url('assets/bootstrap/js/5');

    $p->css[] = sprintf('<link rel="stylesheet" href="%s">', $css);
    $p->css[] = sprintf('<link rel="stylesheet" href="%s">', $icons);
    $p->css[] = sprintf('<link rel="stylesheet" href="%s">', strings::url('assets/esse'));
    if ($_css = theme::rootFont()) $p->css[] = $_css;

    $p->late[] = sprintf('<script src="%s"></script>', $js);

    return $p;
  }

  public function __construct() {
  }

  public function __destruct() {
    $this->close();
  }

  /**
   * close the page,
   * close the body and head elements if they are open
   *
   * @return page a page control that can be chained
   */
  public function close(): self {

    if (!$this->_open) return $this;

    $this
      ->closehead()
      ->closebody();

    $this->_open = false;
    print "\n</html>\n";

    return $this;
  }

  /**
   * close the side panel of the page, if open
   *
   * @return page a page control that can be chained
   */
  public function closeaside(): self {

    if (!$this->_aside) return $this;

    print "\n\t\t\t</div><!-- theme end -->\n";
    print "\n\t\t</aside>\n";

    $this->_aside = false;
    return $this;
  }

  /**
   * close the html body including relevant elements
   *
   * @return page a page control that can be chained
   */
  public function closebody(): self {

    if (!$this->_body) return $this;

    $this->closemainrow();

    array_walk($this->late, fn ($late) => printf("\t%s\n", $late));

    print "\n</body>\n";

    $this->_body = false;
    return $this;
  }

  /**
   * close the html head element if open
   *
   * @return page a page control that can be chained
   */
  public function closehead(): self {

    if (!$this->_head) return $this;

    print "\n</head>\n";

    $this->_head = false;
    return $this;
  }

  /**
   * close the main panel if open
   *
   * @return page a page control that can be chained
   */
  public function closemain(): self {

    if (!$this->_main) return $this;

    print "\n\t\t</main>\n";

    $this->_main = false;
    return $this;
  }

  /**
   * close the main row including relevant elements
   *
   * @return page a page control that can be chained
   */
  public function closemainrow(): self {

    if (!$this->_mainrow) return $this;

    $this
      ->closemain()
      ->closeaside();

    print "\n\t</div></div>\n";

    $this->_mainrow = false;
    return $this;
  }

  /**
   * open the head element, creates the html element if required
   *
   * @return page a page control that can be chained
   */
  public function head(string $title = ''): self {

    if ($this->_head) return $this;

    $this->open();

    print "<head>\n";

    array_walk($this->meta, fn ($meta) => printf("\t%s\n", $meta));
    array_walk($this->css, fn ($css) => printf("\t%s\n", $css));
    array_walk($this->scripts, fn ($scripts) => printf("\t%s\n", $scripts));

    $this->_head = true;

    if ($title) printf("<title>%s</title>\n", $title);

    return $this;
  }

  /**
   * open the body element,
   *  creates the html element if required,
   *  closes the head element if it is open
   *
   * @return page a page control that can be chained
   */
  public function body(): self {

    if ($this->_body) return $this;

    $this->open()
      ->closeHead();

    if ( $this->bodyClass ) {

      printf( "<body class=\"%s\">\n", $this->bodyClass);
    }else {

      print "<body>\n";
    }

    $this->_body = true;
    return $this;
  }

  /**
   * open the aside element,
   *  creates a body and mainrow element if required
   *  closes the main element if it is open
   *
   * @return page a page control that can be chained
   */
  public function aside(): self {

    if ($this->_aside) return $this;

    $this->body()
      ->mainrow()
      ->closemain();

    printf( "\t\t<aside class=\"%s\" data-role=\"content-secondary\">\n", theme::secondary());
    print "\t\t\t<div class=\"sidebar pt-3 pb-5\"><!-- theme start -->\n";

    $this->_aside = true;
    return $this;
  }

  /**
   * prepare for a footer element,
   *  creates a body if required
   *  closes the mainrow element if it is open
   *
   * @return page a page control that can be chained
   */
  public function footer(): self {

    $this->body()
      ->closemainrow();

    return $this;
  }

  /**
   * open the head element,
   *  creates a body element if required
   *  closes the aside element if it is open
   *
   * @return page a page control that can be chained
   */
  public function main(): self {

    if ($this->_main) return $this;

    $this->body()
      ->mainrow()
      ->closeaside();

    print "\t\t<main class=\"col\" data-role=\"content-primary\">\n";

    $this->_main = true;
    return $this;
  }

  /**
   * open the mainrow element,
   *  creates the body element if required
   *
   * @return page a page control that can be chained
   */
  public function mainrow(): self {

    if ($this->_mainrow) return $this;

    $this->body();

    printf( "\n\t<div class=\"%s\"><div class=\"row\">\n", $this->container);

    $this->_mainrow = true;
    return $this;
  }

  /**
   * open the html element if required
   *
   * @return page a page control that can be chained
   */
  public function open(): self {

    if ($this->_open) return $this;
    $this->_open = true;

    Response::html_headers();
    print "<!doctype html>\n<html lang=\"en\">\n";

    return $this;
  }

  /**
   * executes a code block and returns itself
   *
   * @param Closure $code a code block to execute
   *
   * @return bravedave\esse\page itself
   */
  public function then(Closure $code): self {

    $code();
    return $this;
  }
}
