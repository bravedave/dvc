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

class email {
  static $debug = false;

  static protected function print_xml_error($error) {

    $return  = $error->message . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";

    switch ($error->level) {
      case LIBXML_ERR_WARNING:

        $return .= "Warning $error->code: ";
        break;
      case LIBXML_ERR_ERROR:

        $return .= "Error $error->code: ";
        break;
      case LIBXML_ERR_FATAL:

        $return .= "Fatal Error $error->code: ";
        break;
    }

    $return .= trim($error->message) .
      "\n  Line: $error->line" .
      "\n  Column: $error->column";

    if ($error->file) $return .= "\n  File: $error->file";

    return "$return\n\n--------------------------------------------\n\n";
  }

  public static function image2cid($msg, &$attachments = []) {
    /**
     * The message may have embedded images,
     * these need to be converted to cid: type inline attachments
     *
     * Inline images are:
     * Pros
     * Much simpler to achieve
     * Much faster to do
     * Requires much less deep dive into MIME and application code
     *
     * Cons
     * Can really increase size of emails especially if you use more than one image
     * Most likely blocked by default in many webmail services
     * Blocked completely in Outlook
     **/

    //~ self::$debug = true;

    // This part could probably be better
    $tmp = sys_get_temp_dir();
    $types = [
      'image/jpg' => 'jpg',
      'image/jpeg' => 'jpg',
      'image/png'  => 'png',
      'image/gif'  => 'gif',
      'image/svg+xml' => 'svg',
      'image/webp' => 'webp'
    ];

    $msg = preg_replace('@\sid=@i', ' x-id=', $msg);  // to eliminate errors from duplicate id's

    $matches = [];
    if (preg_match_all('/src="data:image\/[^"]*?"/i', $msg, $matches)) {

      if (self::$debug) logger::debug(sprintf('<%d matches found> %s', count($matches[0]), __METHOD__));
      foreach ($matches[0] as $match) {

        $src = trim(substr($match, 5), '" ');

        // Deconstruct it, get all the parts
        $semicolon_place = strpos($src, ';');
        $comma_place = strpos($src, ',');
        $type = trim(substr($src, 5, $semicolon_place - 5));

        if ($type && isset($types[$type])) {

          if (self::$debug) logger::debug(sprintf('<%s => %s> %s', $type, substr($src, 0, 20), __METHOD__));
          $base64_data = substr($src, $comma_place + 1);
          $data = base64_decode($base64_data);
          $md5 = md5($data);
          $path = $tmp;
          if (!file_exists($tmp)) throw new Exceptions\PathNotFound($tmp);

          $path .= "/{$md5}.{$types[$type]}";
          $attachments[] = $path;
          if (!file_exists($path)) {
            $handle = fopen($path, 'w');
            fwrite($handle, $data);
            fclose($handle);
          }

          $msg = str_replace($src, "{$md5}.{$types[$type]}", $msg);
          if (self::$debug) logger::debug(sprintf('<src : %s> %s', "{$md5}.{$types[$type]}", __METHOD__));
        } else {

          logger::info($error = sprintf('<invalid type : %s( %d)> %s', $type, strlen($match), __METHOD__));
          throw new Exceptions\InvalidType($error);
        }
      }
    }

    /**
     * end convert to cid: type inline attachments
     **/

    return $msg;
  }
}
