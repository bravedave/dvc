<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Creates a lib combined file for a js library
 * 	- requires a directory to write to -see tinymce for example:
 * 		=> requires appdir/app/public/js/tinymce to be writable
 *
 * then you can call one file in place of several, if the library is updated,
 * it will recompile it
 *
 * in theory - only used it once ...
*/

namespace bravedave\dvc;

use dvc\Response;
use FilesystemIterator;
use GlobIterator, MatthiasMullie;

abstract class jslib {
  public static $debug = false;
  public static $tinylib = false;
  public static $brayworthlib = false;
  // 'js/_brayworth_.bootstrapModalPop.js',
  // 'js/_brayworth_.logonModal.js',
  public static $brayworthlibFiles = [
    'js/jquery.visible.js',
    'js/_brayworth_.js',
    'js/_brayworth_.ask.js',
    'js/_brayworth_.browser.js',
    'js/_brayworth_.context.js',
    'js/_brayworth_.CopyToClipboard.js',
    'js/_brayworth_.decimal.js',
    'js/_brayworth_.email.js',
    'js/_brayworth_.extend.js',
    'js/_brayworth_.fetch.js',
    'js/_brayworth_.fileDragDropHandler.js',
    'js/_brayworth_.get.js',
    'js/_brayworth_.get.DataUri.js',
    'js/_brayworth_.growl.js',
    'js/_brayworth_.hashScroll.js',
    'js/_brayworth_.hourglass.js',
    'js/_brayworth_.html2text.js',
    'js/_brayworth_.initDatePickers.js',
    'js/_brayworth_.InitHRefs.js',
    'js/_brayworth_.inPrivate.js',
    'js/_brayworth_.isWindowHidden.js',
    'js/_brayworth_.lazyImageLoader.js',
    'js/_brayworth_.loadModal.js',
    'js/_brayworth_.mobile-nav-hider.js',
    'js/_brayworth_.modal.js',
    'js/_brayworth_.modalDialog.js',
    'js/_brayworth_.post.js',
    'js/_brayworth_.push.js',
    'js/_brayworth_.strings.js',
    'js/_brayworth_.swipe.js',
    'js/_brayworth_.table.js',
    'js/_brayworth_.textPrompt.js',
    'js/_brayworth_.toaster.js',
    'js/_brayworth_.Vue.js',
    'js/_brayworth_.Vue.block.js',
    'js/autofill.js',
    'js/autoResize.js',
    'js/brayworth.js',
    'js/spinner.js',
    'js/js.cookie.js',
    'js/placeholders.js',
    'js/templation.js',
    'js/templation.js',
    'js/timedate.js',
    'js/dayjs/dayjs.min.js',
    'js/dayjs/timezone.js',
    'js/dayjs/localeData.js',
    'js/dayjs/localizedFormat.js',
    'js/dayjs/updateLocale.js',
    'js/dayjs/utc.js',

  ];

  // buggy
  // 'js/dayjs/duration.js',
  // 'js/dayjs/customParseFormat.js',

  public static $brayworthlibDOPOFiles = [
    'js/dopo.js'

  ];

  protected static $rootPath = null;

  protected static function __createlib($libdir, $jslib, $files, $minify = false) {
    $debug = self::$debug;
    //~ $debug = TRUE;

    if (is_null(self::$rootPath)) {
      self::$rootPath = application::app()->getRootPath() . '/app/public/js';
    }

    if ($libdir) {
      $outputDIR = sprintf('%s/%s', self::$rootPath, $libdir);
    } else {
      $outputDIR = self::$rootPath;
    }

    $output = sprintf('%s/%s', $outputDIR, $jslib);
    // logger::info( $jslib);
    // logger::info( $output);

    // return ( FALSE);

    if (file_exists(application::app()->getRootPath() . '/app/public/')) {
      if (!(file_exists($outputDIR)) && is_writable(self::$rootPath)) {
        mkdir($outputDIR, 0777, true);
      }

      if (is_writable($outputDIR)) {
        $contents = [];
        foreach ($files as $file) {
          if (realpath($file)) {
            $contents[] = file_get_contents($file);
          } else {

            logger::info(sprintf('<cannot locate library file %s> %s', $file, __METHOD__));
            // logger::info( realpath( $file));
          }
        }

        $content = implode("\n", $contents);
        if ($minify) {
          $minifier = new \MatthiasMullie\Minify\JS();
          $minifier->add(implode("\n", $contents));
          $content = $minifier->minify();
        }

        file_put_contents($output, $content);
        return (true);
        // logger::info( 'no of files = ' . count( $contents));

      } else {
        logger::info(sprintf('<%s is not writable - cannot create a library here> %s', $outputDIR, __METHOD__));
        logger::info(sprintf('<please create a writable data folder : %s> %s', $outputDIR, __METHOD__));
        logger::info(sprintf('<mkdir --mode=0777 %s> %s', $outputDIR, __METHOD__));
      }
    } else {
      logger::info('[root]/app/public/ does not exist');
    }

    return (false);
  }

  public static function tinymce($lib = 'tinylib.js', $libdir = 'tinymce', $plugins = 'autolink,paste,lists,table,colorpicker,textcolor,image,imagetools,link') {
    $debug = self::$debug;
    //~ $debug = TRUE;

    $files = [sprintf('%s/public/js/%s/tinymce.min.js', __DIR__, $libdir)];

    if (file_exists($_file = sprintf('%s/public/js/%s/icons/default/icons.min.js', __DIR__, $libdir))) {
      $files[] = $_file;
    }

    if (file_exists($_file = sprintf('%s/public/js/%s/themes/silver/theme.min.js', __DIR__, $libdir))) {
      $files[] = $_file;
    } elseif (file_exists($_file = sprintf('%s/public/js/%s/themes/modern/theme.min.js', __DIR__, $libdir))) {
      $files[] = $_file;
    }

    foreach (explode(',', $plugins) as $plugin)
      $files[] = sprintf('%s/public/js/%s/plugins/%s/plugin.min.js', __DIR__, $libdir, trim($plugin));

    if ($debug) {
      foreach ($files as $file) {
        logger::debug(sprintf('<tinylib file: %s', $file, __METHOD__));
      }
    }

    if (!application::app())
      throw new Exceptions\ExternalUseViolation;

    self::$tinylib = sprintf('%sjs/%s/%s?v=', \url::$URL, $libdir, $lib);
    $jslib = sprintf('%s/app/public/js/%s/%s', application::app()->getRootPath(), $libdir, $lib);
    if (file_exists($jslib)) {

      if ($debug) logger::debug(sprintf('<jslib::tinymce found :: %s> %s', $jslib, __METHOD__));

      $modtime = 0;
      foreach ($files as $file) {
        if (realpath($file))
          $modtime = max([$modtime, filemtime($file)]);

        else
          logger::info(sprintf('<cannot locate tinymce library file %s> %s', $file, __METHOD__));
      }

      $libmodtime = filemtime($jslib);
      if ($libmodtime < $modtime) {
        if ($debug) logger::info(sprintf('<latest mod time = %s> %s', date('r', $modtime), __METHOD__));
        if ($debug) logger::info(sprintf('<you need to update %s> %s', $jslib, __METHOD__));

        if (self::__createlib($libdir, $lib, $files)) {
          $version = filemtime($jslib);
          self::$tinylib .= $version;

          return (true);
        }
      } else {
        if ($debug) logger::info(sprintf('<you have the latest version of ' . $jslib, __METHOD__));

        $version = filemtime($jslib);
        self::$tinylib .= $version;

        return (true);
      }
    } else {
      if ($debug) logger::info(sprintf('<jslib::tinymce not found :: %s - creating> %s', $jslib, __METHOD__));
      if (self::__createlib($libdir, $lib, $files)) {
        $version = filemtime($jslib);
        self::$tinylib .= $version;

        return (true);
      }
    }
  }

  public static function tinyserve(string $libname = 'tinymce', string $plugins = 'autolink,paste,lists,table,colorpicker,textcolor') {
    $debug = self::$debug;
    // $debug = TRUE;
    $debug = false;

    $path = implode(DIRECTORY_SEPARATOR, [
      \application::app()->getInstallPath(),
      'bravedave',
      'public',
      'js',
      'tinymce5'

    ]);

    // logger::info( sprintf('<%s> %s', $path, __METHOD__));

    $files = [
      implode(DIRECTORY_SEPARATOR, [$path, 'tinymce.min.js']),
      implode(DIRECTORY_SEPARATOR, [$path, 'icons', 'default', 'icons.min.js'])

    ];

    if (file_exists($_file = implode(DIRECTORY_SEPARATOR, [$path, 'themes', 'silver', 'theme.min.js']))) {
      $files[] = $_file;
    } elseif (file_exists($_file = implode(DIRECTORY_SEPARATOR, [$path, 'themes', 'modern', 'theme.min.js']))) {
      $files[] = $_file;
    }

    foreach (explode(',', $plugins) as $plugin) {
      $files[] = implode(DIRECTORY_SEPARATOR, [$path, 'plugins', trim($plugin), 'plugin.min.js']);
    }

    if ($debug) {

      foreach ($files as $file) {

        logger::debug(sprintf('<%s> <%s> %s', $file, \filesize($file), __METHOD__));
      }
    }

    jslib::viewjs([
      'debug' => false,
      'libName' => $libname,
      'jsFiles' => $files,
      'libFile' => \config::tempdir()  . '_' . $libname . '.js'
    ]);
  }

  public static function tiny6_dir() {
    $dir = realpath(__DIR__ . '/../../../../twbs');
    if (!$dir) {
      $dir = realpath(__DIR__ . '/../../vendor/tinymce/tinymce');
    }

    return $dir;
  }

  public static function tiny6serve(string $libname = 'tinymce', string $plugins = 'table,autolink,lists,advlist,editimage,link') {
    $debug = self::$debug;
    $debug = false;
    // $debug = true;

    $path = self::tiny6_dir();

    // logger::info(sprintf('<%s> %s', $path, __METHOD__));

    $files = [
      implode(DIRECTORY_SEPARATOR, [$path, 'tinymce.min.js']),
      implode(DIRECTORY_SEPARATOR, [$path, 'models', 'dom', 'model.min.js']),
      implode(DIRECTORY_SEPARATOR, [$path, 'icons', 'default', 'icons.min.js'])

    ];

    if (file_exists($_file = implode(DIRECTORY_SEPARATOR, [$path, 'themes', 'silver', 'theme.min.js']))) {
      $files[] = $_file;
    } elseif (file_exists($_file = implode(DIRECTORY_SEPARATOR, [$path, 'themes', 'modern', 'theme.min.js']))) {
      $files[] = $_file;
    }

    foreach (explode(',', $plugins) as $plugin) {
      $file = implode(DIRECTORY_SEPARATOR, [$path, 'plugins', trim($plugin), 'plugin.min.js']);
      if (file_exists($file)) {
        $files[] = $file;
      } else {
        logger::info(sprintf('<plugin not found %s> %s', $file, __METHOD__));
      }
    }

    if ($debug) {
      foreach ($files as $file) {
        logger::debug(sprintf('<%s> <%s> %s', $file, \filesize($file), __METHOD__));
      }
    }

    jslib::viewjs([
      'debug' => $debug,
      'libName' => $libname,
      'jsFiles' => $files,
      'minify' => false,
      'libFile' => \config::tempdir()  . '_' . $libname . '.js'

    ]);
  }

  public static function brayworth($lib = false, $libdir = '') {
    $debug = self::$debug;
    // $debug = true;

    if (!$lib) {
      $lib = 'brayworthlib.js';
    }

    $files = [];
    foreach (self::$brayworthlibFiles as $f) {
      $files[] = __DIR__ . '/' . $f;
    }

    if (!application::app()) {
      throw new Exceptions\ExternalUseViolation;
    }

    if ($libdir) {
      self::$brayworthlib = sprintf('%sjs/%s/%s?v=', \url::$URL, $libdir, $lib);
      $jslib = sprintf('%s/app/public/js/%s/%s', application::app()->getRootPath(), $libdir, $lib);
    } else {
      self::$brayworthlib = sprintf('%sjs/%s?vv=', \url::$URL, $lib);
      $jslib = sprintf('%s/app/public/js/%s', application::app()->getRootPath(), $lib);
    }

    if (realpath($jslib) && file_exists($jslib)) {

      if ($debug) logger::debug(sprintf('<found : %s> %s', $jslib, __METHOD__));

      $modtime = 0;
      foreach ($files as $file) {
        if (realpath($file))
          $modtime = max([$modtime, filemtime($file)]);

        else
          logger::info(sprintf( '<cannot locate library file %s>', $file, __METHOD__));
      }

      $libmodtime = filemtime($jslib);
      if ($libmodtime < $modtime) {
        if ($debug) logger::debug(sprintf( '<latest mod time = %s> %s', date('r', $modtime), __METHOD__));
        if ($debug) logger::debug(sprintf( '<you need to update %s> %s', $jslib, __METHOD__));
        if (self::__createlib($libdir, $lib, $files, true)) {

          $version = filemtime($jslib);
          self::$brayworthlib .= $version;
          return true;
        }
      } else {
        if ($debug) logger::debug(sprintf('you have the latest version of %s> %s', $jslib, __METHOD__));

        $version = filemtime($jslib);
        self::$brayworthlib .= $version;

        return (true);
      }
    } else {

      if ($debug) logger::debug(sprintf('<not found :: %s - creating> %s', $jslib, __METHOD__));
      if (self::__createlib($libdir, $lib, $files, true)) {

        $version = filemtime($jslib);
        self::$brayworthlib .= $version;
        return (true);
      }
    }

    return false;
  }

  protected static function _js_create($options) {
    $input = [];

    if (is_array($options->jsFiles)) {
      foreach ($options->jsFiles as $key => $item) {

        if ($options->leadKey && $key == $options->leadKey) {

          if ($options->debug) logger::debug(sprintf('<%s :: prepending leadKey %s> %s', $options->libName, $options->leadKey, __METHOD__));
          array_unshift($input, file_get_contents($item));
        } else {

          if ($options->debug) logger::debug(sprintf('<%s :: appending key %s> %s', $options->libName, $key, __METHOD__));
          if ($path = realpath($item)) {

            $input[] = file_get_contents($path);
          } else {

            logger::info(sprintf('<cannot find %s> %s', $item, __METHOD__));
          }
        }
      }
    } else {

      $gi = new GlobIterator($options->jsFiles, FilesystemIterator::KEY_AS_FILENAME);
      foreach ($gi as $key => $item) {
        if ($options->leadKey && $key == $options->leadKey) {
          if ($options->debug) logger::debug(sprintf('<%s :: prepending leadKey %s> %s', $options->libName, $options->leadKey, __METHOD__));
          array_unshift($input, file_get_contents($item->getRealPath()));
        } else {
          if ($options->debug) logger::debug(sprintf('<%s :: appending key %s> %s', $options->libName, $key, __METHOD__));
          $input[] = file_get_contents($item->getRealPath());
        }
      }
    }

    if (count($input)) {
      if ($options->minify) {
        $minifier = new MatthiasMullie\Minify\JS;
        $minifier->add($input);

        file_put_contents($options->libFile, $minifier->minify());
      } else {
        file_put_contents($options->libFile, implode($input));
      }
    } else {
      file_put_contents($options->libFile, '');
    }
  }

  protected static function _js_serve($options) {

    $expires = \config::$JS_EXPIRE_TIME;
    $modTime = filemtime($options->libFile);
    $age = time() - $modTime;
    if ($age < 3600)
      $expires = 36;

    if ($options->debug) logger::info(sprintf('<%s :: serving(%s) %s> %s', $options->libName, $expires, $options->libFile, __METHOD__));
    Response::javascript_headers(filemtime($options->libFile), $expires);
    print file_get_contents($options->libFile);
  }

  public static function viewjs($params) {
    $options = (object)array_merge([
      'debug' => false,
      'libName' => '',
      'leadKey' => false,
      'jsFiles' => false,
      'libFile' => false,
      'minify' => true
    ], $params);

    if ($options->libFile) {
      if ($options->jsFiles) {
        if (file_exists($options->libFile)) {
          /* test to see if requires update */
          $modtime = 0;

          if (is_array($options->jsFiles)) {
            foreach ($options->jsFiles as $item) {
              $modtime = max([$modtime, filemtime($item)]);
            }
          } else {
            $gi = new GlobIterator($options->jsFiles, FilesystemIterator::KEY_AS_FILENAME);
            foreach ($gi as $key => $item) {
              $modtime = max([$modtime, filemtime($item->getRealPath())]);
            }
          }

          $libmodtime = filemtime($options->libFile);
          if ($libmodtime < $modtime) {
            if ($options->debug) logger::debug(sprintf('<%s :: updating %s, latest mod time = %s> %s', $options->libName, $options->libFile, date('r', $modtime), __METHOD__));

            self::_js_create($options);
            self::_js_serve($options);
          } else {
            if ($options->debug) logger::debug(sprintf('<%s :: latest version (%s)> %s', $options->libName, $options->libFile, __METHOD__));
            self::_js_serve($options);
          }
        } else {
          /* create and serve */
          if ($options->debug) logger::debug(sprintf('<%s :: creating %s> %s', $options->libName, $options->libFile, __METHOD__));

          self::_js_create($options);
          self::_js_serve($options);
        }
      } else {
        throw new Exceptions\LibraryFilesNotSpecified;
      }
    } else {
      throw new Exceptions\FileNotSpecified;
    }
  }
}
