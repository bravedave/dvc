<?php
/*
 * file: src/app/todo/controller.php
 * MIT License
 */

namespace todo;

use bravedave\dvc\{ controller as dvcController, ServerRequest};

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

    config::todo_checkdatabase();
    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';
  }

  protected function postHandler() {

    $request = new ServerRequest;
    $action = $request('action');

    /*
      _brayworth_.fetch.post(_brayworth_.url('todo'),{
        action: 'delete',
        id : 1
      }).then(console.log);

      _brayworth_.fetch.post(_brayworth_.url('todo'),{
        action: 'get-by-id',
        id : 1
      }).then(console.log);

      _brayworth_.fetch.post(_brayworth_.url('todo'),{
        action: 'get-matrix'
      }).then(console.log);
    */
    return match ($action) {
      'todo-delete' => handler::todoDelete($request),
      'get-by-id' => handler::todoGetByID($request),
      'get-matrix' => handler::todoGetMatrix($request),
      'todo-save' => handler::todoSave($request),
      default => parent::postHandler()
    };
  }

  public function edit($id = 0) {
    // tip : the structure is available in the view at $this->data->dto
    $this->data = (object)[
      'title' => $this->title = config::label,
      'dto' => new dao\dto\todo
    ];

    if ($id = (int)$id) {
      $dao = new dao\todo;
      $this->data->dto = $dao->getByID($id);
      $this->data->title .= ' edit';
    }

    $this->load('edit');
  }
}