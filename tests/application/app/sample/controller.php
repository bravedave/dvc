<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace sample;

use Json;
use sys;

class controller extends \Controller {
  protected $viewPath = __DIR__ . '/views';

  protected function _index() {
    $this->render([
      'primary' => 'default',
      'secondary' => [
        'index-hello',
        'index',
        'index-modal',
        'index-option'

      ]

    ]);
  }

  protected function posthandler() {
    $action = $this->getPost('action');

    if ('set-option' == $action) {
      $key = $this->getPost('key');
      $val = $this->getPost('val');
      sys::option($key, $val);
      Json::ack($action);
    } else {
      parent::postHandler();
    }
  }

  public function changes() {
    $this->render([
      'title' => 'Change Log',
      'primary' => 'changes',
      'secondary' => 'index'

    ]);
  }

  function editPerson() {
    $this->title = 'Add a Person';
    $this->load('edit-person');
  }

  public function info() {
    /**
     * default setting
     * in case you forget to disable this on a production server
     * - only running on localhost
     */

    if ($this->Request->ServerIsLocal()) {

      $this->render(
        [
          'title' => 'PHP Info',
          'primary' => 'info',
          'secondary' => 'index'

        ]

      );
    }
  }

  public function phonenumbers() {
    $this->render(
      [
        'title' => 'Phone Number Tests',
        'primary' => 'phonenumbers',
        'secondary' => 'index'

      ]

    );
  }

  public function errTest() {
    throw new \Exceptions\TestException('test');
  }

  public function samplemodal() {
    $this->load('sample-modal');
  }
}
