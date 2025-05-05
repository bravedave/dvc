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

  public function __invoke(?string $var = null, ?string $default = null): mixed {

    if ($var) {

      return self::$_request->getParsedBody()[$var] ?? $default;
    } else {

      return self::$_request;
    }
  }

  public function clientIsLocal(): bool {

    if ($this->serverIsLocal()) return true;

    $thisIP = $this->getServerIP();
    $remoteIP = $this->getRemoteIP();

    $thisSubNet = $this->getSubNet($thisIP);
    $remoteSubNet = $this->getSubNet($remoteIP);

    //~ logger::info( sprintf( '%s/%s :: %s/%s', $thisIP, $thisSubNet, $remoteIP, $remoteSubNet));

    return ($thisSubNet == $remoteSubNet);
  }

  public function getPath(): string {

    $uri = self::$_request->getUri();
    return $uri->getPath();
  }

  public function getParsedBody(): null|array|object {

    return self::$_request->getParsedBody();
  }

  public function getRemoteIP(): string {

    // https://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php
    foreach (
      [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
      ] as $key
    ) {
      if ($ip = $this->getServerParam($key)) {

        foreach (explode(',', $_SERVER[$key]) as $ip) {

          $ip = trim($ip); // just to be safe
          if ($this->serverIsLocal()) {

            if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
              // logger::info( sprintf('<%s> %s', $ip, __METHOD__));
              return $ip;
            }
          } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false) {

            return $ip;
          }
        }
      }
    }

    return '0.0.0.0';
  }

  public function getSubNet(string $ip): string {

    $a = explode('.', $ip);
    if (count($a) == 4) return sprintf('%s.%s.%s', $a[0], $a[1], $a[2]);
    return '';
  }

  public function getSegments(): array {

    $uri = self::$_request->getUri();
    $path = $uri->getPath();
    $path = trim($path, '/');
    $a = explode('/', $path);

    /**
     * verify each segment is valid, do not allow
     *  - segments that are empty or start with . or spaces
     *  - only allow segments with a-z, A-Z, 0-9, _, @, - and %
     *  - a period is allowed in the segment (e.g. image.png)
     *
     * would https://cmss.darcy.com.au/rapp/document/16538/employer-letter
     * be a valid segment?
     *
     */
    $a = array_filter($a, function ($segment) use ($path) {

      if (empty($segment)) return false;

      $ignore = [
        '.well-known/appspecific/com.chrome.devtools.json'
      ];

      if (in_array($segment, $ignore)) return false;

      /**
       * not the - hyphen is at the end of the character class,
       * if it is not it has to be escaped
       */
      if (preg_match('/^(?![.\s])[\w\d][\w\d.%@-]*$/', $segment)) {

        return true;
      }

      $remoteIP = self::$_request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
      $referer = self::$_request->getServerParams()['HTTP_REFERER'] ?? null;


      logger::info(sprintf('<--- ---[invalid segment : %s]--- ---> %s', $remoteIP, __METHOD__));
      logger::info(sprintf('<uri: %s> %s', $path, __METHOD__));
      if ($referer) logger::info(sprintf('<referer: %s> %s', $referer, __METHOD__));
      logger::info(sprintf('<segment %s> %s', $segment, __METHOD__));
      logger::info(sprintf('<--- ---[/invalid segment]--- ---> %s', __METHOD__));
      return false;
    });
    return $a;
  }

  public function getServerIP(): string {

    if ($ip = $this->getServerParam('SERVER_ADDR')) return $ip;
    return '0.0.0.0';
  }

  public function getUploadedFiles(): array {

    return self::$_request->getUploadedFiles();
  }

  public function getUri(): string {

    $uri = self::$_request->getUri();
    return (string) $uri;
  }

  #[\Deprecated] //Use getUri() instead
  public function getUrl(): string {

    $uri = self::$_request->getUri();
    return (string) $uri;
  }

  public function getQueryParam(string $k, ?string $default = null): mixed {

    $queryParams = $this->getQueryParams();
    return $queryParams[$k] ?? $default;
  }

  public function getQueryParams(): array {

    return self::$_request->getQueryParams();
  }

  public function getServerParam(string $var): string {

    return self::$_request->getServerParams()[$var] ?? '';
  }

  public function getServerParams(): array {

    return self::$_request->getServerParams();
  }

  public function serverIsLocal(): bool {

    return ($this->getServerParam('SERVER_NAME') == 'localhost');
  }
}
