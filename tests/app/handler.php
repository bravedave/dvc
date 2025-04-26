<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\dvc\{json, ServerRequest};

final class handler {

  public static function getTodoData(ServerRequest $request): json {

    $action = $request('action');
    return json::ack($action, (new dao\todo)->getMatrix());
  }

  public static function todoAdd(ServerRequest $request): json {

    $action = $request('action');
    (new dao\todo)->Insert([
      'description' => $request('description')
    ]);

    return json::ack($action);
  }

  public static function todoDelete(ServerRequest $request): json {

    $action = $request('action');
    if ($id = (int)$request('id')) {

      (new dao\todo)->delete($id);
      return json::ack($action);
    }

    return json::nak($action);
  }

  public static function todoUpdate(ServerRequest $request): json {

    $action = $request('action');
    if ($id = (int)$request('id')) {

      (new dao\todo)->UpdateByID([
        'description' => $request('description')
      ], $id);
      return json::ack($action);
    }

    return json::nak($action);
  }
}
