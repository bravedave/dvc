<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\bootstrap;

use strings;

/**
 * a bootstrap accordion element
 */
class accordion {
  protected string $_id = '';
  protected bool $_open = false;
  protected bool $_openpanel = false;
  protected int $_panelCount = 0;

  public function __construct(array $params = []) {

    $options = array_merge([], $params);
    $this->_id = strings::rand();
  }

  public function __destruct() {

    $this->close();
  }

  public function close(): self {

    if ($this->_open) {

      $this->panelClose();
      print "\n</div>\n";  // <!-- /div class="accordion" -->
      $this->_open = false;
    }

    return $this;
  }

  public function ID(): string {

    return $this->_id;
  }

  public function open(): self {

    if ($this->_open) return $this;

    printf('<div class="accordion" id="%s">', $this->_id);
    $this->_open = true;
    return $this;
  }

  public function panel(bool $show = false): string {

    $this
      ->open()
      ->panelClose();

    $panelID = sprintf('%s-%s', $this->_id, ++$this->_panelCount);

    printf(
      '%s<div id="%s" class="collapse fade %s" data-parent="#%s">',
      "\n\t",
      $panelID,
      $show ? 'show' : '',
      $this->_id
    );

    $this->_openpanel = true;
    return $panelID;
  }

  public function panelClose(): self {

    if ($this->_openpanel) {

      print "\n\t</div>\n";  // <!-- div class="collapse" -->
      $this->_openpanel = false;
    }

    return $this;
  }
}
