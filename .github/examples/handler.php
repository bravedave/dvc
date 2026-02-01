<?php
  // file: src/app/todo/handler.php
  // MIT License

namespace todo;

use bravedave\dvc\{ServerRequest, json};

final class handler {

  public static function todoDelete(ServerRequest $request): json {

    $action = $request('action');
    if ($id = (int)$request('id')) {

      (new dao\todo)->delete($id);
      return json::ack($action);
    }

    return json::ack($action);
  }

  public static function todoGetByID(ServerRequest $request): json {

    $action = $request('action');
    if ($id = (int)$request('id')) {

      if ($dto = (new dao\todo)->getByID($id)) {

        return json::ack($action, $dto);
      }
    }
    return json::nak($action);
  }

  public static function todoGetMatrix(ServerRequest $request): json {

    $action = $request('action');
    return json::ack($action, (new dao\todo)->getMatrix());
  }

  public static function todoSave(ServerRequest $request): json {

    $action = $request('action');
    $a = [
      'description' => $request('description'),
      'complete' => $request('complete'),
    ];

    $dao = new dao\todo;
    if ($id = (int)$request('id')) {

      $dao->UpdateByID($a, $id);
    } else {
      $dao->Insert($a);
    }

    return json::ack($action);

  }
}