<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc;

use auth as rootAuth;
use dao\users as daoUsers;

/**
 * Static utilities used by the base controller for logon, view lookup, page setup and push subscriptions.
 */
final class handler {

  /** Validates the u and p POST fields via IMAP and the users DAO. */
  public static function authorizeIMAP(): bool {

    $debug = false;
    // $debug = getenv('DEVELOPER') == 'yes';

    $request = new ServerRequest;

    if ($u = $request('u')) {

      if ($p = $request('p')) {

        if (rootAuth::ImapTest($u, $p)) {

          if ($debug) logger::debug(sprintf('<successful logon for %s> %s', $u, __METHOD__));

          $dao = new daoUsers;
          if (method_exists($dao, 'validatedByIMAP')) {

            return $dao->{'validatedByIMAP'}($u, $p);
          }
        } else {

          if ($debug) logger::debug(sprintf('<unsuccessful logon for %s> %s', $u, __METHOD__));
        }
      }
    }

    return false;
  }

  /** Framework view directories; adds views/docs first when the controller is controller\docs. */
  public static function getSystemViewPaths(?string $controller = null): array {

    $a = [];
    if (!is_null($controller)) {

      if (controller\docs::class == $controller) {

        $a[] = implode(DIRECTORY_SEPARATOR, [__DIR__, 'views', 'docs']);
      }
    }

    $a[] = implode(DIRECTORY_SEPARATOR, [__DIR__, 'views']);
    return $a;
  }

  protected static $_viewPathsVerified = [];

  /** Resolves view search paths. The result is cached statically, so later calls ignore the arguments. */
  public static function getViewPaths(string $controller, string $rootPath, array $viewPath): array {

    if (self::$_viewPathsVerified) return self::$_viewPathsVerified;

    $_paths = (array)$viewPath;
    if ($_dir = realpath(implode(DIRECTORY_SEPARATOR, [$rootPath, 'views', $controller]))) {

      $_paths[] =  $_dir;
    }

    if ($_dir = realpath(implode(DIRECTORY_SEPARATOR, [$rootPath, 'app', 'views', $controller]))) {

      $_paths[] =  $_dir;
    }

    if ($_dir = realpath(implode(DIRECTORY_SEPARATOR, [$rootPath, 'app', 'views']))) {

      $_paths[] =  $_dir;
    }

    self::$_viewPathsVerified = $_paths;

    return self::$_viewPathsVerified;
  }

  /** Serves a file listed in a CRA asset-manifest.json, defaulting to index.html. */
  public static function offManifest(string $manifest, string $option = ''): void {

    if (!$option) $option = 'index.html';
    if ($_manifest_file = realpath(sprintf('%s/asset-manifest.json', $manifest))) {

      if ('manifest.json' == $option) {

        $_path = sprintf('%s/%s', $manifest, $option);
      } else {

        $_manifest = json_decode(file_get_contents($_manifest_file));
        $_path = false;
        foreach ($_manifest as $_p) {

          if (ltrim($_p, './') == $option) {

            $_path = sprintf('%s/%s', $manifest, ltrim($_p, './'));
          }
        }
      }

      if ($_path) {

        if ($_file = realpath($_path)) {

          Response::serve($_file);
        } else {

          printf('%s - file not found', $_path);
          //~ \sys::dump( $_manifest);
        }
      } else {

        printf('%s - not set<br />', $option);
      }
    } else {

      printf('%s - manifest not found', $_manifest_file);
    }

    // if we find a static file serve it, otherwise serve index
  }

  /** Builds and configures the page template from $params; returns the template object. */
  public static function page(array $params) {

    $defaults = [
      'css' => [],
      'data' => false,
      'footer' => true,
      'latescripts' => [],
      'meta' => [],
      'scripts' => [],
      'title' => '',
      'bodyClass' => false,
      'template' => config::$PAGE_TEMPLATE,
    ];

    $options = array_merge($defaults, $params);

    $p = new $options['template']($options['title']);
    $p->bodyClass = $options['bodyClass'];
    if ('string' == gettype($options['footer'])) {

      $p->footer = true;
      $options['template']::$footerTemplate = $options['footer'];
    } else {

      $p->footer = $options['footer'];
    }

    $p->data = (object)$options['data'];
    if (!(isset($p->data->title))) $p->data->title = $options['title'];

    foreach ($options['css'] as $css) {

      if (preg_match('/^<(link|style)/', $css)) {

        $p->css[] = $css;
      } else {

        $p->css[] = sprintf('<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css);
      }
    }

    foreach ($options['meta'] as $meta) {

      $p->meta[] = $meta;
    }

    foreach ($options['scripts'] as $script) {

      if (preg_match('/^<script/', $script)) {

        $p->scripts[] = $script;
      } else {

        $p->scripts[] = sprintf('<script type="text/javascript" src="%s"></script>', $script);
      }
    }

    /*
		* latescripts are prepended
		* - if something like tinymce is appended after it would be slower
		*/
    foreach ($options['latescripts'] as $script) {

      if (preg_match('/^<script/', $script)) {

        array_unshift($p->latescripts, $script);
      } else {

        array_unshift($p->latescripts, sprintf('<script type="text/javascript" src="%s"></script>', $script));
      }
    }

    return $p;
  }

  /** Removes the push subscription identified by the endpoint POST field. */
  public static function subscriptionDelete(ServerRequest $request): json {

    $action = $request('action');
    if ($endpoint = $request('endpoint')) {

      $dao = new dao\notifications;
      $dao->deleteByEndPoint($endpoint);
      return json::ack($action);
    }

    return json::nak($action);
  }

  /** Creates or updates the push subscription from the json POST field. */
  public static function subscriptionSave(ServerRequest $request): json {

    $action = $request('action');
    if ($json = $request('json')) {

      $subscription = (object)json_decode($json);
      if (isset($subscription->endpoint) && $subscription->endpoint) {

        $dao = new dao\notifications;
        if ($dto = $dao->getByEndPoint($subscription->endpoint)) {

          $dao->UpdateByID(['json' => $json], $dto->id);
        } else {

          $dao->Insert([
            'json' => $json,
            'endpoint' => $subscription->endpoint,
            'user_id' => currentUser::id()
          ]);
        }

        return json::ack($action);
      }
    }

    return json::nak($action);
  }

  /** Returns the existing view file for $path (.php preferred over .md), or '' if none. */
  public static function viewPath(string $path): string {

    $debug = false;
    // $debug = getenv('DEVELOPER') == 'yes';

    if (preg_match('/\.(php|md)$/', $path)) {    // extension was specified

      if (file_exists($path)) {

        if ($debug) logger::debug(sprintf('found view (specific) : %s :: %s', $path, logger::caller()));
        return $path;
      }
    }

    /**
     * 1. look for a php (.php) view
     * 2. then a markdown (.md)
     */
    if (file_exists($view = sprintf('%s.php', $path))) {  // php

      if ($debug) logger::debug(sprintf('found view (php) : %s :: %s', $view, logger::caller()));
      return $view;
    }

    if (file_exists($view = sprintf('%s.md', $path))) {  // md

      if ($debug) logger::debug(sprintf('found view (md) : %s :: %s', $view, logger::caller()));
      return $view;
    }

    return '';
  }
}
