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

use strings;

class logon extends page {
  protected $_title = '';

  public function __construct($title = '') {

    parent::__construct();

    $this->meta[] = '<meta charset="utf-8">';
    $this->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
    $this->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/jquery'));
    $this->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/brayworth/js'));
    $this->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/brayworth/dopo'));
    $this->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/bootstrap/js/5'));
    $this->css[] = sprintf('<link rel="stylesheet" href="%s">', strings::url('assets/bootstrap/css/5'));
    $this->css[] = sprintf('<link rel="stylesheet" href="%s">', strings::url('assets/bootstrap/icons'));

    $this->late['logon'] = '<script>(_ => _.ready( () => _.get.modal(_.url(\'logon/form\'))))( _brayworth_);</script>';
    $this->_title = $title;
  }

  public function header(): static {

    return $this->head($this->_title);
  }

  public function content(): static {

    return $this->body();
  }
}
