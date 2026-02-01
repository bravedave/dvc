<?php
  // file: src/app/contacts/handler.php
  // MIT License

namespace contacts;

use bravedave\dvc\{ServerRequest, json};

final class handler {

  public static function contactsDelete(ServerRequest $request): json {

    $action = $request('action');
    if ($id = (int)$request('id')) {

      (new dao\contacts)->delete($id);
      return json::ack($action);
    }

    return json::ack($action);
  }

  public static function contactsGetByID(ServerRequest $request): json {

    $action = $request('action');
    if ($id = (int)$request('id')) {

      if ($dto = (new dao\contacts)->getByID($id)) {

        return json::ack($action, $dto);
      }
    }
    return json::nak($action);
  }

  public static function contactsGetMatrix(ServerRequest $request): json {

    $action = $request('action');
    return json::ack($action, (new dao\contacts)->getMatrix());
  }

  public static function contactsSave(ServerRequest $request): json {

    $action = $request('action');
    $a = [
      'name' => $request('name'),
      'email' => $request('email'),
      'mobile' => $request('mobile'),
    ];

    $dao = new dao\contacts;
    if ($id = (int)$request('id')) {

      $dao->UpdateByID($a, $id);
    } else {
      $dao->Insert($a);
    }

    return json::ack($action);

  }
}