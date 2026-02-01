<?php
/*
 * file: src/app/contacts/controller.php
 * MIT License
 */

namespace contacts;

use bravedave\dvc\{controller as dvcController, ServerRequest};

class controller extends dvcController {

  protected function _index() {

    $this->data = (object)[
      'title' => $this->title = config::label,
    ];

    $this->renderBS5([
      'aside' => fn() => $this->load('index'),
      'main' => fn() => $this->load('matrix')
    ]);
  }

  protected function before() {

    config::contacts_checkdatabase();
    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';
  }

  protected function postHandler() {

    $request = new ServerRequest;
    $action = $request('action');

    /*
      _brayworth_.fetch.post(_brayworth_.url('contacts'),{
        action: 'delete',
        id : 1
      }).then(console.log);

      _brayworth_.fetch.post(_brayworth_.url('contacts'),{
        action: 'get-by-id',
        id : 1
      }).then(console.log);

      _brayworth_.fetch.post(_brayworth_.url('contacts'),{
        action: 'get-matrix'
      }).then(console.log);
    */
    return match ($action) {
      'contacts-delete' => handler::contactsDelete($request),
      'get-by-id' => handler::contactsGetByID($request),
      'get-matrix' => handler::contactsGetMatrix($request),
      'contacts-save' => handler::contactsSave($request),
      default => parent::postHandler()
    };
  }

  public function edit($id = 0) {
    // tip : the structure is available in the view at $this->data->dto
    $this->data = (object)[
      'title' => $this->title = config::label,
      'dto' => new dao\dto\contacts
    ];

    if ($id = (int)$id) {
      $dao = new dao\contacts;
      $this->data->dto = $dao->getByID($id);
      $this->data->title = config::label_edit;
    }

    $this->load('edit');
  }

  public function view($id = 0) {

    if ($id = (int)$id) {

      $dao = new dao\contacts;
      if ($dto = $dao->getByID($id)) {

        $this->data = (object)[
          'title' => $this->title = config::label_view,
          'dto' => $dto
        ];

        $this->load('view');
      } else {

        print 'Not Found';
      }
    } else {

      print 'Invalid ID';
    }
  }
}
