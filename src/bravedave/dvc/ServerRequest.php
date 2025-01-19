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

  static protected ServerRequestInterface $_request;

  static protected function _create_(): ServerRequestInterface {

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

  public function __construct() {

    if (!isset(self::$_request)) self::$_request = self::_create_();
  }

  public function __invoke(string $var = null): mixed {

    if ($var) {

      return self::$_request->getParsedBody()[$var] ?? null;
    } else {

      return self::$_request;
    }
  }

  public function getSegments(): array {

    $uri = self::$_request->getUri();
    $path = $uri->getPath();
    $path = trim($path, '/');
    $a = explode('/', $path);

    /**
     * verify each segment is valid, do not allow
     *  - empty segments or segments with . or spaces
     *  - only allow segments with a-z, A-Z, 0-9 and _
     */
    $ret = [];
    foreach ($a as $segment) {

      if (preg_match('/^[a-zA-Z0-9_]+$/', $segment)) {

        $ret[] = $segment;
      } else {

        // get the remote ip and referer from self::$_request
        $remoteIP = self::$_request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $referer = self::$_request->getServerParams()['HTTP_REFERER'] ?? null;

        logger::info(sprintf('<--- ---[invalid segment : %s]--- ---> %s', $remoteIP, __METHOD__));
        logger::info(sprintf('<uri: %s> %s', $path, __METHOD__));
        if ($referer) logger::info(sprintf('<referer: %s> %s', $referer, __METHOD__));
        logger::info(sprintf('<segment %s> %s', $segment, __METHOD__));
        logger::info(sprintf('<--- ---[/invalid segment]--- ---> %s', __METHOD__));
        break;
      }
    }

    return $ret;
  }

  public function getUploadedFiles(): array {

    return self::$_request->getUploadedFiles();
  }

  public function getQueryParam(string $k): mixed {

    $queryParams = $this->getQueryParams();
    return $queryParams[$k] ?? null;
  }

  public function getQueryParams(): array {

    return self::$_request->getQueryParams();
  }
}
