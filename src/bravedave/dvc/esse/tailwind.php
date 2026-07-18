<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc\esse;

use bravedave\dvc\Response;
use Closure;
use strings;

// delta implemented: tailwind page lifecycle with navbar/content/aside/main/footer and destructor-safe close
class tailwind {
  protected bool $_open = false;
  protected bool $_head = false;
  protected bool $_body = false;
  protected bool $_content = false;
  protected bool $_aside = false;
  protected bool $_main = false;
  protected bool $_footer = false;

  public string $bodyClass = 'min-h-screen flex flex-col';
  public string $contentClass = 'mx-auto flex w-full max-w-screen-2xl flex-1 flex-col md:flex-row';
  public string $asideClass = 'w-full md:w-72 border-b border-slate-200 bg-slate-50 md:border-b-0 md:border-r';
  public string $mainClass = 'min-w-0 flex-1 bg-white';
  public string $footerClass = '';

  public array $meta = [];
  public array $css = [];
  public array $scripts = [];
  public array $late = [];

  public function __invoke(): self {
    $p = new static;

    $p->meta[] = '<meta charset="utf-8">';
    $p->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
    $p->css[] = sprintf('<link rel="stylesheet" href="%s">', strings::url('assets/tailwind'));

    return $p;
  }

  public function __destruct() {
    $this->close();
  }

  public function open(): static {
    if ($this->_open) return $this;

    Response::html_headers();
    print "<!doctype html>\n<html lang=\"en\">\n";

    $this->_open = true;
    return $this;
  }

  public function head(string $title = ''): static {
    if ($this->_head) return $this;

    $this->open();

    print "<head>\n";
    array_walk($this->meta, fn($meta) => printf("\t%s\n", $meta));
    array_walk($this->css, fn($css) => printf("\t%s\n", $css));
    array_walk($this->scripts, fn($script) => printf("\t%s\n", $script));
    if ($title) printf("\t<title>%s</title>\n", $title);

    $this->_head = true;
    return $this;
  }

  public function closehead(): static {
    if (!$this->_head) return $this;

    print "</head>\n";

    $this->_head = false;
    return $this;
  }

  public function body(): static {
    if ($this->_body) return $this;

    $this->open()->closehead();

    if ($this->bodyClass) {
      printf("<body class=\"%s\">\n", $this->bodyClass);
    } else {
      print "<body>\n";
    }

    $this->_body = true;
    return $this;
  }

  public function content(): static {
    if ($this->_content) return $this;

    $this->body()
      ->closefooter();

    printf("\t<div class=\"%s\" data-role=\"content-row\">\n", $this->contentClass);

    $this->_content = true;
    return $this;
  }

  public function aside(): static {
    if ($this->_aside) return $this;

    $this->content()->closemain();

    printf("\t\t<aside class=\"%s\" data-role=\"content-secondary\">\n", $this->asideClass);
    print "\t\t\t<div class=\"p-4\">\n";

    $this->_aside = true;
    return $this;
  }

  public function main(): static {
    if ($this->_main) return $this;

    $this->content()->closeaside();

    printf("\t\t<main class=\"%s\" data-role=\"content-primary\">\n", $this->mainClass);
    print "\t\t\t<div class=\"p-4\">\n";

    $this->_main = true;
    return $this;
  }

  public function footer(): static {
    if ($this->_footer) return $this;

    $this->body()
      ->closecontent();

    $this->_footer = true;
    return $this;
  }

  public function closeaside(): static {
    if (!$this->_aside) return $this;

    print "\t\t\t</div>\n";
    print "\t\t</aside>\n";

    $this->_aside = false;
    return $this;
  }

  public function closemain(): static {
    if (!$this->_main) return $this;

    print "\t\t\t</div>\n";
    print "\t\t</main>\n";

    $this->_main = false;
    return $this;
  }

  public function closecontent(): static {
    if (!$this->_content) return $this;

    $this->closemain()->closeaside();
    print "\t</div>\n";

    $this->_content = false;
    return $this;
  }

  public function closefooter(): static {
    if (!$this->_footer) return $this;

    $this->_footer = false;
    return $this;
  }

  public function closebody(): static {
    if (!$this->_body) return $this;

    $this
      ->closefooter()
      ->closecontent();

    array_walk($this->late, fn($late) => printf("\t%s\n", $late));

    print "</body>\n";

    $this->_body = false;
    return $this;
  }

  public function close(): static {
    if (!$this->_open) return $this;

    $this->closehead()->closebody();

    print "</html>\n";

    $this->_open = false;
    return $this;
  }

  public function then(Closure $code): static {
    $code();
    return $this;
  }
}
