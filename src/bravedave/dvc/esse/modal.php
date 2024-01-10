<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * This is the Bootstrap 4 modal
 *
 * php usage:
 *  $m = new dvc\pages\modal;
 *  $m->open();
 *  // implement page
 *
 **/

namespace bravedave\dvc\esse;

use bravedave\dvc\Response;
use config, strings, theme;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class modal {
  protected bool $_footer = false;
  protected bool $_form = false;
  protected string $_id = '';
  protected string $_modal_id = '';
  protected bool $_open = false;
  protected bool $_openform = false;

  protected string $className = '';
  protected string $headerClass = '';
  protected string $title = '';

  protected function closeform() {

    if (!$this->_openform) return $this;
    if ($this->_form) print "</form>\n";
    $this->_openform = false;
  }

  protected function load(string $path = null, array $options = []) : self {

    if ( $path) {

      if ( file_exists($path)) {

        if (substr_compare($path, '.md', -3) === 0) {

          $fc = file_get_contents($path);
          $mdo = [
            'html_input' => $options['html_input'] ?? 'strip',
            'allow_unsafe_links' => $options['allow_unsafe_links'] ?? false,
          ];
          $converter = new GithubFlavoredMarkdownConverter($mdo);
          printf('<div class="markdown-body">%s</div>', $converter->convert($fc));
          // printf('<div class="markdown-body">%s</div>', \Parsedown::instance()->text($fc));
        } else {

          require($view);
        }
      }
    }

    return $this;  // chain
  }

  protected function openform() {

    if ($this->_openform) return $this;
    if ($this->_form) printf("<form id=\"%s\" autocomplete=\"off\">\n", $this->_id);
    $this->_openform = true;
  }

  public function __construct(array $params = []) {

    $options = array_merge([
      'form' => false,
      'footer' => false,
      'title' => config::$WEBNAME,
      'class' => '',
      'header-class' => theme::modalHeader(),
    ], $params);

    $this->_id = strings::rand();
    $this->_modal_id = sprintf('%s-modal', $this->_id);
    $this->_footer = $options['footer'];
    $this->_form = $options['form'];
    $this->title = $options['title'];
    $this->className = $options['class'];
    $this->headerClass = $options['header-class'];
  }

  public function __destruct() {

    $this->close();
    $this->closeform();
  }

  public function __invoke($params = []) {

    $options = array_merge([
      'title' => sprintf('%s Modal', config::$WEBNAME),
      'class' => '',
      'header-class' => theme::modalHeader(),
      'load' => false,
      'text' => false,
    ], $params);

    Response::html_headers();
    $m = new self([
      'title' => $options['title'],
      'class' => $options['class'],
      'header-class' => $options['header-class'],
    ]);

    $m->open();

    if ($options['load']) {
      foreach ((array)$options['load'] as $_) {
        $this->load($_);
      }
    }

    if ($options['text']) {
      foreach ((array)$options['text'] as $_) {
        print $_;
      }
    }
  }

  public function close(): self {

    if (!$this->_open) return $this;

    print "\n\t\t\t\t</div>\n";  // <!-- div class="modal-body" -->

    if ($this->_footer) {

      print "\t\t\t\t<div class=\"modal-footer\">\n";

      print "\t\t\t\t\t<div class=\"ml-auto\"></div>\n";

      print "\t\t\t\t\t<button type=\"button\" class=\"btn btn-light\" data-dismiss=\"modal\">close</button>\n";

      if ($this->_form) print "\t\t\t\t\t<button type=\"submit\" class=\"btn btn-primary\">Save</button>\n";

      print "\t\t\t\t</div>\n";
    }

    print "\t\t\t</div>\n";  // <!-- div class="modal-content" -->

    print "\t\t</div>\n";  // <!-- div class="modal-dialog" role="document" -->

    print "\t</div>\n";    // <!-- div class="modal" -->

    $this->_open = false;

    return $this;
  }

  public function ID(): string {
    return $this->_id;
  }

  public function modalID(): string {
    return $this->_modal_id;
  }

  public function open(): self {

    if ($this->_open) return $this;

    Response::html_headers();
    $this->openform();

    printf(
      '	<div class="modal fade" tabindex="-1" role="dialog" id="%s" aria-labelledby="%s-label" aria-hidden="true">',
      $this->_modal_id,
      $this->_id
    );

    printf(
      '
		<div class="modal-dialog %s modal-dialog-centered" role="document">

			<div class="modal-content">

				<div class="modal-header %s">

					<h5 class="modal-title text-truncate" title="%s" id="%s-label">%s</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">',
      $this->className,
      $this->headerClass,
      $this->title,
      $this->_id,
      $this->title
    );

    $this->_open = true;

    return $this;
  }

  public static function form(array $options = []): self {

    return (new static(array_merge([
      'footer' => true,
      'form' => true,
    ], $options)));
  }
}
