<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequest {

  public function __invoke() : ServerRequestInterface {

    $psr17Factory = new Psr17Factory;

    $creator = new ServerRequestCreator(
      $psr17Factory, // ServerRequestFactory
      $psr17Factory, // UriFactory
      $psr17Factory, // UploadedFileFactory
      $psr17Factory  // StreamFactory
    );

    $request = $creator->fromGlobals();
    if ($request->getHeaderLine('Content-Type') === 'application/json') {

      $rawBody = (string) $request->getBody(); // Get the raw body as a string
      $parsedBody = json_decode($rawBody, true); // Decode JSON into an associative array
      if (json_last_error() === JSON_ERROR_NONE) {

        return $request->withParsedBody($parsedBody);
        // $request = $request->withParsedBody(json_decode(file_get_contents('php://input'), true));
      } else {

        $request = $request->withParsedBody(['Invalid JSON payload']);
      }
    }

    return $request;
  }
}
