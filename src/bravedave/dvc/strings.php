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

use function bravedave\dvc\esc;

use config;
use DateTime;
use finfo;
use libphonenumber;

abstract class strings {
  const html_tick = '&#10003;';
  const html_sad = '<span style="font-family: Segoe UI Symbol; Verdana;">&#9785;</span>';
  const html_happy = '<span style="font-family: Segoe UI Symbol; Verdana;">&#9786;</span>';

  static public function AMPM($hhmm, $short = true, $tailed = true) {
    $d = date('Y-m-d') . ' ' . $hhmm;
    if ($short) {
      if (date('i', strtotime($d)) == '00') {
        return date($tailed ? 'ga' : 'g:i', strtotime($d));
      } else {
        return date($tailed ? 'g:i a' : 'g:i', strtotime($d));
      }
    }

    return date($tailed ? 'h:i a' : 'h:i', strtotime($d));
  }

  /**
   * convert a ANSI type date to local format (as defined by the server)
   *
   * @param string $date ANSI date
   * @param bool $time include the time
   * @param float $epoch if specified, only consider dates > than this epoch year
   *
   * @return string the formated date - HTML-safe
   */
  static public function asLocalDate(?string $date, bool $time = false, float|null $epoch = null): string {

    if ((string)$date == '0000-00-00') return '';
    if ( is_null($epoch)) $epoch = strtotime('-100 years'); // default to 100 years ago

    if (($t = strtotime($date)) > $epoch) {

      if ($time) {

        return preg_replace('/m$/', '', date(config::$DATETIME_FORMAT, $t));
      } else {

        return date(config::$DATE_FORMAT, $t);
      }
    }

    return '';
  }

  /**
   * @return string HTML-safe
   */
  static public function asLocalPhone($_tel = ''): string {
    $debug = false;
    // $debug = true;

    if ($debug) logger::debug(sprintf('<original %s> %s', $_tel, logger::caller()));

    $_tel = preg_replace('@[^0-9\+,]@', '', $_tel);
    if ($_tel && strlen($_tel) >= 8) {

      if (substr($_tel, 0, 3) == '+01' || substr($_tel, 0, 3) == '+00') {

        $_tel = '+' . substr($_tel, 2); // remove the leading 0
        if ($debug) logger::debug(sprintf('<converted to %s> %s', $_tel, logger::caller()));
      }

      try {

        $phoneUtil = libphonenumber\PhoneNumberUtil::getInstance();
        if (substr($_tel, 0, 1) == '+') {

          $_mNo = $phoneUtil->parse($_tel);
          if ($debug) logger::debug(sprintf('<%s> : 1 : %s', $_mNo, __METHOD__));
        } else {

          $_mNo = $phoneUtil->parse($_tel, config::$PHONE_REGION);
          if ($debug) logger::debug(sprintf('<%s> : 2 : %s', $_mNo, __METHOD__));
        }

        // if ( $debug) logger::debug( sprintf( '<%s> %s', config::$PHONE_REGION, __METHOD__));
        if ($phoneUtil->isValidNumber($_mNo, config::$PHONE_REGION)) {

          if (config::$PHONE_REGION == $phoneUtil->getRegionCodeForNumber($_mNo)) {

            if ($debug) logger::debug(sprintf('<%s> : National : %s', $_mNo, __METHOD__));
            return $phoneUtil->format($_mNo, libphonenumber\PhoneNumberFormat::NATIONAL);
          } else {

            if ($debug) logger::debug(sprintf('<%s> : International : %s', $_mNo, __METHOD__));
            return $phoneUtil->format($_mNo, libphonenumber\PhoneNumberFormat::INTERNATIONAL);
          }
        } else {

          if ($debug) logger::debug(sprintf('<%s> : Invalid : %s', $_mNo, __METHOD__));
        }
      } catch (\Exception $e) {

        logger::info(sprintf('<%s : %s> %s', $_tel, $e->getMessage(), __METHOD__));
      }
    }

    return esc($_tel);
  }

  /**
   * @return string HTML-safe
   */
  static public function asMobilePhone($mobile = '') {
    //~ logger::info( sprintf( 'deprecated :: %s > use AsLocalPhone', __METHOD__));
    return self::asLocalPhone($mobile);
  }

  static public function asShortDate($date, $time = false) {
    if ((string)$date == '0000-00-00') {
      return (false);
    }

    if (($t = strtotime($date)) > 0) {
      if ($time && date('Y-m-d', $t) == date('Y-m-d')) {
        return (preg_replace('/m$/', '', date('g:ia', $t)));
      } elseif (date('Y', $t) == date('Y')) {
        return (date('d-M', $t));
      } else {
        return (date(config::$DATE_FORMAT, $t));
      }
    }

    return false;
  }

  static public function asLongDate($date, $time = false) {
    if ((string)$date == '0000-00-00') {
      return (false);
    }

    if (($t = strtotime($date)) > 0) {
      if ($time) {
        return preg_replace(
          '/m$/',
          '',
          date(config::$DATETIME_FORMAT_LONG, $t)

        );
      } else {
        return (date(config::$DATE_FORMAT_LONG, $t));
      }
    }

    return false;
  }

  static public function array2csv(array &$array) {
    if (count($array) == 0) {
      return null;
    }

    ob_start();
    $df = fopen("php://output", 'w');
    //~ fputcsv( $df, array_keys( reset( $array)));
    foreach ($array as $row) {
      fputcsv($df, $row);
    }

    fclose($df);

    $out = ob_get_contents();
    ob_end_clean();

    return $out;
  }

  static public function brief($text, $length = 100) {
    $debug = false;
    //~ $debug = true;
    //~ if ( $debug) logger::debug( sprintf( 'dao\dto\brief ( %s)', $length));

    $text = strip_tags($text);

    if (strlen($text) < $length) {

      if ($debug) logger::debug(sprintf('strings::brief returning ( %s)', strlen($text)));
      return ($text);
    }

    $a = explode(' ', $text);
    if ($debug) logger::debug(sprintf('strings::brief ( %s)', count($a)));

    $r = [];
    $i = 0;
    foreach ($a as $t) {
      $i += strlen($t);
      if ($i < $length) {
        $r[] = $t;
        $i++;
      } else {
        break;
      }
    }

    return trim(implode(' ', $r), " \t\n\r");
  }

  /**
   * convert a British type date to ANSI format
   *
   * @return string
   */
  static public function BRITISHDateAsANSI(string $strDate): string {

    // split it, must have 3 parts, dd/mm/yyyy
    $a = explode("/", $strDate);
    if (@checkdate((int)$a[1], (int)$a[0], (int)$a[2])) {

      if (2 == strlen($a[2])) $a[2] = substr(date('Y'), 0, 2) . $a[2];  // prefix current epoch

      return sprintf(
        '%s-%s-%s',
        $a[2],
        str_pad($a[1], 2, "0", STR_PAD_LEFT),
        str_pad($a[0], 2, "0", STR_PAD_LEFT)
      );
    }

    return '';
  }

  /**
   * returns a clean mobile string localised for Australia
   *
   * @param string $to
   * @return string
   */
  static public function cleanMobileString(string $to): string {
    $debug = false;
    // $debug = true;

    if ($debug) logger::debug(sprintf('<%s> %s', $to, __METHOD__));

    /**
     * Store Telephone numbers as straight string,
     * format after via javascript email should be lowercase
     */
    $to = preg_replace("/[^0-9]/", "", $to);  // to only be numbers -
    if ($debug) logger::debug(sprintf(
      '<%s.%s(%s)> %s',
      $to,
      substr($to, 4, 1),
      ord(substr($to, 4, 1)),
      __METHOD__
    ));

    if (substr($to, 0, 4) == "0011") $to = substr($to, 4);

    if (substr($to, 0, 1) == "+") {

      $to = substr($to, 1);
    } elseif (substr($to, 0, 2) == "61") {

      $to = "0" . substr($to, 2);  // aussie aussie aussie
    }

    return ($to);
  }

  static public function cleanPhoneString(string $tel): string {
    //~ $debug = true;
    $debug = false;

    if (strlen($tel) > 8) {

      if (substr($tel, 0, 3) == '+01' || substr($tel, 0, 3) == '+00') {

        $tel = '+' . substr($tel, 2); // remove the leading 0
        if ($debug) logger::debug(sprintf('<converted to %s> %s', $tel, logger::caller()));
      }

      try {

        $phoneUtil = libphonenumber\PhoneNumberUtil::getInstance();
        if (substr($tel, 0, 1) == '+') {

          $_mNo = $phoneUtil->parse($tel);
          if ($debug) logger::debug(sprintf('<%s> :1:%s', $_mNo, __METHOD__));
        } else {

          $_mNo = $phoneUtil->parse($tel, config::$PHONE_REGION);
          if ($debug) logger::debug(sprintf('<%s> :2:%s', $_mNo, __METHOD__));
        }

        if ($phoneUtil->isValidNumber($_mNo, config::$PHONE_REGION)) {

          if ($debug) logger::debug(sprintf('<%s> :getRegionCodeForNumber:%s', $phoneUtil->getRegionCodeForNumber($_mNo), __METHOD__));
          if ('AU' == $phoneUtil->getRegionCodeForNumber($_mNo)) {

            $mNo = $phoneUtil->format($_mNo, libphonenumber\PhoneNumberFormat::NATIONAL);
          } else {

            $mNo = $phoneUtil->format($_mNo, libphonenumber\PhoneNumberFormat::INTERNATIONAL);
          }

          if ($debug) logger::debug(sprintf('<%s> :3:%s', $mNo, __METHOD__));
          return (string)preg_replace('@[^0-9]@', '', $mNo);  // only numbers
        } else {

          return (string)preg_replace('@[^0-9]@', '', $tel);  // only numbers
        }
      } catch (libphonenumber\NumberParseException $e) {

        return (string)preg_replace('@[^0-9]@', '', $tel);  // only numbers
      }
    } else {

      return (string)preg_replace('@[^0-9]@', '', $tel);  // only numbers
    }
  }

  static public function CheckEmailAddress($email) {
    return (filter_var($email, FILTER_VALIDATE_EMAIL));
  }

  static public function ComparePhoneNumbers($p1, $p2) {
    return (self::CleanPhoneString($p1) == self::CleanPhoneString($p2));
  }

  static public function DateDiff($lowdate, $highdate = null, $format = '%R%a') {
    if ($lowdate && '0000-00-00' != (string)$lowdate) {
      //~ logger::info( sprintf( '%s : %s', $lowdate));
      if (!(strtotime($highdate) > 0)) $highdate = date('Y-m-d');

      $low = new \datetime($lowdate);
      $high = new \datetime($highdate);
      $interval = date_diff($low, $high);
      //~ logger::info( sprintf( '%s - %s = %s',  $lowdate, $highdate, $interval->format('%R%a')));
      return ($interval->format($format));
    }

    return false;
  }

  static public function esc(string|null $v): string {

    if ($v) return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
    return '';
  }

  static public function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen, TRUE) === 0;
  }

  static public function ExtendedStreetString($street) {
    /* the opposite of GoodStreetString */
    $find = [
      '@\sRd$@i',
      '@\sRd,@i',
      '@\sSt$@i',
      '@\sSt,@i',
      '@\sAv$@i',
      '@\sAv,@i',
      '@\sPd$@i',
      '@\sPd,@i',
      '@\sTc$@i',
      '@\sTc,@i',
      '@\sDr$@i',
      '@\sDr,@i',
      '@\sPl$@i',
      '@\sPl,@i',
      '@\sCt$@i',
      '@\sCt,@i'
    ];

    $replace = [
      ' Road',
      ' Road,',
      ' Street',
      ' Street,',
      ' Avenue',
      ' Avenue,',
      ' Parade',
      ' Parade,',
      ' Terrace',
      ' Terrace,',
      ' Drive',
      ' Drive,',
      ' Place',
      ' Place,',
      ' Court',
      ' Court,'
    ];

    return (preg_replace($find, $replace, $street));
  }

  static public function FirstNames(string $string): string {

    if (preg_match('/&/', $string)) {

      $x = explode('&', $string);
      if (count($x) > 1) {  // this might be david & lynne bray or david bray & lynne ralph

        //~ \sys::dump( $x);
        $a = [
          self::FirstWord(trim($x[0])),
          self::FirstWord(trim($x[1]))
        ];
        return implode(' & ', $a);
      }

      return self::FirstWord($string);
    }

    return self::FirstWord($string);
  }

  static public function FirstWord(string $string): string {

    return (explode(' ', trim($string))[0]);
  }

  static public function formatBytes($bytes, $precision = 2) {
    $units = ['b', 'kb', 'mb', 'gb', 'tb'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
  }

  static public function getCommonPath(array $paths): string {
    $lastOffset = 1;
    $common = '/';
    while (($index = strpos($paths[0], '/', $lastOffset)) !== FALSE) {
      $dirLen = $index - $lastOffset + 1;  // include /
      $dir = substr($paths[0], $lastOffset, $dirLen);
      foreach ($paths as $path) {
        if (substr($path, $lastOffset, $dirLen) != $dir)
          return $common;
      }
      $common .= $dir;
      $lastOffset = $index + 1;
    }
    return substr($common, 0, -1);
  }

  static public function getRelativePath(string $from, string $to, string $ps = DIRECTORY_SEPARATOR): string {
    // https://www.php.net/manual/en/function.realpath.php#105876

    $arFrom = explode($ps, rtrim($from, $ps));
    $arTo = explode($ps, rtrim($to, $ps));
    while (count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0])) {
      array_shift($arFrom);
      array_shift($arTo);
    }

    return str_pad("", count($arFrom) * 3, '..' . $ps) . implode($ps, $arTo);
  }

  static public function getDateAsANSI($strDate) {

    if (preg_match("@^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$@", $strDate))
      return (date('Y-m-d', strtotime($strDate)));

    if (config::$DATE_FORMAT == 'd/m/Y')
      return (self::BRITISHDateAsANSI($strDate));


    throw new \Exception('fix me:: strings::getDateAsANSI');
  }

  static public function getGUID(?string $prefix = null) {
    return (sprintf('{%s}', self::getUID($prefix)));
  }

  static public function getUID(?string $prefix = null) {
    $charid = strtoupper(md5(self::rand($prefix)));
    $uuid = sprintf(
      '%s-%s-%s-%s-%s',
      substr($charid, 0, 8),
      substr($charid, 8, 4),
      substr($charid, 12, 4),
      substr($charid, 16, 4),
      substr($charid, 20, 12)
    );

    return $uuid;
  }

  static public function GoodStreetString($street) {

    if (preg_match('/The\s?Drive/i', $street)) return ($street);
    if (preg_match('/The\s?Avenue/i', $street)) return ($street);
    if (preg_match('/The\s?Esplanade/i', $street)) return ($street);

    $find = [
      '@\savenue$@i',
      '@\savenue,@i',
      '@\save$@i',
      '@\save,@i',
      '@\screscent$@i',
      '@\screscent,@i',
      '@\sclose$@i',
      '@\sclose,@i',
      '@\scourt$@i',
      '@\scourt,@i',
      '@\sdrive$@i',
      '@\sdrive,@i',
      '@\sesplanade$@i',
      '@\sesplanade,@i',
      '@\slane$@i',
      '@\slane,@i',
      '@\sparade$@i',
      '@\spde$@i',
      '@\sparade,@i',
      '@\spde,@i',
      '@\splace$@i',
      '@\splace,@i',
      '@\sroad$@i',
      '@\sroad,@i',
      '@\sstreet$@i',
      '@\sstreet,@i',
      '@\sstreet\s@i',
      '@\sterrace$@i',
      '@\stce$@i',
      '@\sterrace,@i',
      '@\stce,@i',
    ];
    $replace = [
      ' Av',
      ' Av,',
      ' Av',
      ' Av,',
      ' Cres',
      ' Cres,',
      ' Cl',
      ' Cl,',
      ' Ct',
      ' Ct,',
      ' Dr',
      ' Dr,',
      ' Esp',
      ' Esp,',
      ' Ln',
      ' Ln,',
      ' Pd',
      ' Pd',
      ' Pd,',
      ' Pd,',
      ' Pl',
      ' Pl,',
      ' Rd',
      ' Rd,',
      ' St',
      ' St,',
      ' St, ',
      ' Tc',
      ' Tc',
      ' Tc,',
      ' Tc,',
    ];


    return (trim(preg_replace($find, $replace, $street), ', '));
  }

  static public function HoursMinutes($str) {
    $hm = self::HoursMinutesSeconds($str);
    return (str_pad((string)$hm["hours"], 2, "0", STR_PAD_LEFT) . ":" .
      str_pad((string)$hm["minutes"], 2, "0", STR_PAD_LEFT));
  }

  static public function HoursMinutesSeconds($str, $format = 'array') {
    $str = trim($str);
    if ($str == '') return 0;

    $iOffset = 0;
    $eTag = substr($str, strlen($str) - 1, 1);
    $iHours = 0;
    $iMinutes = 0;
    $iSeconds = 0;

    if ($eTag == 'p' || $eTag == 'P') {
      $iOffset = 12;
      $str = trim(substr($str, 0, strlen($str) - 1));
    } elseif ($eTag == 'a' || $eTag == 'A') {
      $str = trim(substr($str, 0, strlen($str) - 1));
    } elseif ($eTag == 'm' || $eTag == 'M') {
      $eTag = strtolower(substr($str, strlen($str) - 2, 2));
      if ($eTag == 'pm') {
        $iOffset = 12;
        $str = trim(substr($str, 0, strlen($str) - 2));
      } elseif ($eTag == 'am') {
        $str = trim(substr($str, 0, strlen($str) - 2));
      }
    }

    //echo "Lookin @:$str\n";
    if (is_integer(strpos($str, ':'))) {
      // hours and minutes
      $a = explode(":", $str);
      //print_r($a);
      $iHours = (int)$a[0];
      $iMinutes = (int)$a[1];
    } elseif (strpos($str, '.') > 0) {
      // hours and minutes
      $a = explode(".", $str);
      $iHours = (int)$a[0];
      $iMinutes = (int)$a[1];
    } else {
      $iHours = (int)$str;
      //echo "Nuffin:$str\n";

    }

    $aRet = [
      "hours" => ($iHours + ($iHours == 12 ? 0 : $iOffset)),
      "minutes" => $iMinutes,
      "seconds" => $iSeconds

    ];

    if ($format == "string") {
      return (str_pad((string)$aRet["hours"], 2, "0", STR_PAD_LEFT) . ":" .
        str_pad((string)$aRet["minutes"], 2, "0", STR_PAD_LEFT) . ":" .
        str_pad((string)$aRet["seconds"], 2, "0", STR_PAD_LEFT));
    } else {
      return ($aRet);
    }
  }

  static public function html2text($document): string {
    $search = array(
      '@<[\/\!]*?[^<>]*?>@si',      // trim blank lines from beginning and end
      '@<br[\s]/>@si',
      '@&nbsp;@si',
      '@&amp;@si'
    );
    $replace = array(
      PHP_EOL,
      PHP_EOL,
      ' ',
      '&'
    );

    $text = self::htmlSanitize($document);
    // die( $text);
    $text = preg_replace_callback('/<li[^>]*>([^<]*)<\\/li>/i', function ($s) {
      // logger::info( sprintf('<%s> %s', print_r( $s, true), __METHOD__));
      return '- ' . $s[1];
    }, $text);
    // die( $text);

    $text = preg_replace([
      "@<ul[^>]*>\n@",
      "@</ul[^>]*>\n@",
      "@</p[^>]*>@"
    ], "", $text);
    $text = trim(preg_replace($search, $replace, $text), "\n");
    // die( $text);

    /*
		$search = array('@<script[^>]*?>.*?</script>@si',	// Strip out javascript
						'@<style[^>]*?>.*?</style>@siU',	// Strip style tags properly
						'@<![\s\S]*?--[ \t\n\r]*>@',		// Strip multi-line comments including CDATA
						'@<[\/\!]*?[^<>]*?>@si'				// trim blank lines from beginning and end
		);

		$text = preg_replace($search, '', $text);
		*/
    //~ $text = $document;

    $x = preg_split("/\n/", $text);
    while (count($x) > 0 && trim($x[0]) == "")
      array_shift($x);

    while (count($x) > 0 && trim($x[(count($x) - 1)]) == "")
      array_pop($x);

    $text = implode("\n", $x);

    return $text;
  }

  static public function htmlSanitize($html): string {
    /*
			'@<style[^>]*?>.*?</style>@si',  	// Strip out javascript
			http://css-tricks.com/snippets/php/sanitize-database-inputs/
		*/

    $search = [];
    $replace = [];

    $search[] = '@<head[^>]*?>.*?</head>@si';      // Strip head element
    $replace[] = '';

    $search[] = '@<script[^>]*?>.*?</script>@si';    // Strip out javascript
    $replace[] = '';

    $search[] = '@<!doctype[\/\!]*?[^<>]*?>@si';    // Strip doctype tags
    $replace[] = '';

    $search[] = '@<(|/)html[^>]*?>@i';          // Strip <html> start/end tag
    $replace[] = '';

    $search[] = '@<body([^>]*)>@i';              // mod <body> start tag
    $replace[] = '<div data-x_type="body" ${1}>';

    $search[] = '@</body[^>]*?>@i';              // mod <body> end tag
    $replace[] = '</div>';

    $search[] = '@<link[^>]*?>@si';           // Strip link tags
    $replace[] = '';

    $search[] = '@<base[\/\!]*?[^<>]*?>@si';      // Strip base href tags
    $replace[] = '';

    $search[] = '@<style[^>]*?>.*?</style>@si';      // Strip style tags
    $replace[] = '';

    // $search[] = '@<![\s\S]*?--[ \t\n\r]*>@';      // Strip multi-line comments including CDATA
    $search[] = '@<!\[CDATA\[.*?\]\]>@s';      // Strip CDATA
    $replace[] = '';

    $search[] = '@^<br[\s]/>@i';            // Blank HTML at Start
    $replace[] = '';

    //~ '@(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n\']+@',	// Blank Lines at Start
    return (preg_replace($search, $replace, $html));
  }

  static public function imageInline($path) {
    $data = base64_encode(file_get_contents($path));
    if (preg_match('@\.svg$@', $path)) {
      return 'data:image/svg+xml;base64,' . $data;
    } else {
      return 'data:' . mime_content_type($path) . ';base64,' . $data;
    }
  }

  static public function initials($name) {
    if ((string)$name == "")
      return '';

    $aX = explode(" ", $name);
    $xX = "";
    foreach ($aX as $sX) {
      $xX .= substr($sX, 0, 1);
    }

    return ($xX);
  }

  static public function InLocalTimeZone($format = "r", $timestamp = false, $timezone = false) {
    $userTimezone = new \DateTimeZone(!empty($timezone) ? $timezone : 'GMT');
    $gmtTimezone = new \DateTimeZone('GMT');
    $myDateTime = new \DateTime(($timestamp != false ? date("r", (int)$timestamp) : date("r")), $gmtTimezone);
    $offset = $userTimezone->getOffset($myDateTime);
    return date($format, ($timestamp != false ? (int)$timestamp : $myDateTime->format('U')) + $offset);
  }

  static public function isDate(string $date): bool {
    if ($date) {
      $d = DateTime::createFromFormat('Y-m-d', $date);
      return $d && $d->format('Y-m-d') === $date;
    }

    return false;
  }

  static public function isDateTime(string $date): bool {
    if ($date) {
      $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
      return $d && $d->format('Y-m-d H:i:s') === $date;
    }

    return false;
  }

  #[\Deprecated]
  static public function IsEmailAddress($email) {
    return (self::CheckEmailAddress($email));
  }

  static public function isEmail($email, $rfc822 = false) {  // compatible case and naming with my javascript routine

    if (self::CheckEmailAddress($email)) return (true);

    if ($rfc822) {

      /**
       * Test it
       *
       * echo strings::isEmail('john.doe@example.com') ? 'Valid' : 'Invalid';  // Valid
       * echo strings::isEmail('"John Doe" <john.doe@example.com>', true) ? 'Valid' : 'Invalid';  // Valid
       * echo strings::isEmail('"John Doe" <invalid_email>') ? 'Valid' : 'Invalid';  // Invalid
       */

      $rfc822_pattern = '/^"?([^"]*)"?\s*<([^>\r\n]+)>$/';

      if (preg_match($rfc822_pattern, $email, $match)) {

        $name = $match[1];
        $addr = $match[2];
        return self::CheckEmailAddress($addr);
      }
    }

    return false;
  }

  static public function isMobilePhone(?string $_tel = ''): bool {
    try {
      $tel = preg_replace('@[^0-9\+]@', '', (string)$_tel);
      //~ logger::info( sprintf( 'IsMobilePhone :: %s', $tel));

      if ($tel && \strlen($tel) >= 10 && \strlen($tel) < 17) {
        /**
         * to prove
         * a mobile phone must contain 10 numbers
         */
        $phoneNumberUtil = libphonenumber\PhoneNumberUtil::getInstance();

        $phoneNumberObject = ('+' == substr($tel, 0, 1) ? $phoneNumberUtil->parse($tel) : $phoneNumberUtil->parse($tel, 'AU'));

        $numberType = $phoneNumberUtil->getNumberType($phoneNumberObject);

        if ($numberType == libphonenumber\PhoneNumberType::MOBILE) {
          return (true);
        }
      }
    } catch (\Exception $e) {

      logger::info(sprintf('%s : %s : %s', $_tel, $e->getMessage(), __METHOD__));
    }

    return false;
  }

  /**
   * @param string $string
   * @return string
   *
   * complimentary to isMonth,
   * but returns the string formatted as month or empty
   * noting that a valid date will be returned as a month string
   * Examples of Outputs:
   *  '2025-01' → '2025-01'
   *  '2025-13' → ''
   *  '2025-01-01' → '2025-01'
   *  '' → ''
   *  'invalid-string' → ''
   *  '2025-01-01 12:00:00' → '2025-01'
   */
  static public function asMonth(string|null $string): string {

    // Ensure the string is not empty
    if (empty($string)) return '';

    // Check if the string matches the YYYY-MM format using regex
    if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $string)) return '';

    // Use DateTime to ensure it's a valid date in the YYYY-MM format
    $date = DateTime::createFromFormat('Y-m', $string);

    // Ensure the DateTime object was created and matches the input string
    if ($date) return $date->format('Y-m');
    return '';
  }

  /**
   * @param string $string
   * @return bool
   *
   * Key Points:
   *  true if the string is a valid month in YYYY-MM.
   *  false for invalid strings, dates, or formats.
   *  Examples of Outputs:
   *  '2025-01' → true
   *  '2025-13' → false (invalid month)
   *  '2025-01-01' → false (invalid format)
   *  '' → false (empty string)
   *  'invalid-string' → false (non-date string)
   */
  static public function isMonth(string|null $string): bool {

    // Ensure the string is not empty
    if (empty($string)) return false;

    // Check if the string matches the YYYY-MM format using regex
    if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $string)) return false;

    // Use DateTime to ensure it's a valid date in the YYYY-MM format
    $date = DateTime::createFromFormat('Y-m', $string);

    // Ensure the DateTime object was created and matches the input string
    return $date;
  }

  static public function isPhone(string|null $_tel = ''): bool {

    try {

      $tel = preg_replace('@[^0-9\+]@', '', (string)$_tel);
      //~ logger::info( sprintf( 'IsMobilePhone :: %s', $tel));

      if ($tel && \strlen($tel) >= 10 && \strlen($tel) < 17) {
        /**
         * to prove
         * a mobile phone must contain 10 numbers
         */
        $phoneNumberUtil = libphonenumber\PhoneNumberUtil::getInstance();

        $phoneNumberObject = ('+' == substr($tel, 0, 1) ? $phoneNumberUtil->parse($tel) : $phoneNumberUtil->parse($tel, 'AU'));

        return (bool)$phoneNumberUtil->isValidNumber($phoneNumberObject, config::$PHONE_REGION);
      }
    } catch (\Exception $e) {

      logger::info(sprintf('%s : %s : %s', $_tel, $e->getMessage(), __METHOD__));
    }

    return (false);
  }

  static public function isOurEmailDomain($email) {
    $email_array = explode("@", $email);
    $domains = explode(',', config::$EMAILDOMAIN);

    foreach ($domains as $domain) {
      if (strtolower($email_array[1]) == trim($domain))
        return (true);
    }

    return (false);
  }

  static public function isValidMd5($md5 = '') {
    return preg_match('/^[a-f0-9]{32}$/', $md5);
  }

  static public function isValidJSON($str) {

    if ($str) {

      //Returns the Mime Type of a file or a string content - from: https://coursesweb.net/
      // $r = the resource: Path to the file; Or the String content
      // $t = type of the resource, needed to be specified as "str" if $r is a string-content
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $type = $finfo->buffer($str);

      if ('application/json' == $type) {

        // logger::info(sprintf('<%s> %s', $type, __METHOD__));
        json_decode($str);

        return json_last_error() == JSON_ERROR_NONE;
      }
    }

    return false;
  }

  static public function validJSON($str): mixed {

    if ($str) {

      //Returns the Mime Type of a file or a string content - from: https://coursesweb.net/
      // $r = the resource: Path to the file; Or the String content
      // $t = type of the resource, needed to be specified as "str" if $r is a string-content
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $type = $finfo->buffer($str);

      if ('application/json' == $type) {

        // logger::info(sprintf('<%s> %s', $type, __METHOD__));
        $json = json_decode($str);
        if (json_last_error() == JSON_ERROR_NONE) return $json;
      }
    }

    return false;
  }

  static public function lorem(int $characters = 0) {
    return html\element::lorem($characters);
  }

  static public function lorum(int $characters = 0) {
    return self::lorem($characters);
  }

  protected static $_pixel = false;
  static public function pixel() {
    if (!self::$_pixel) {
      $image = implode(DIRECTORY_SEPARATOR, [
        dirname(__DIR__),
        'public',
        'images',
        'pixel.png'
      ]);

      // logger::info(sprintf('pixel :: %s', $image));
      // $imageData = base64_encode( file_get_contents($image));
      // self::$_pixel = 'data:'.mime_content_type($image).';base64,'.$imageData;
      self::$_pixel = self::imageInline($image);
    }

    return self::$_pixel;
  }

  /**
   * @return string HTML-safe
   */
  static public function rand(?string $prefix = 'uid_') {
    return ($prefix . bin2hex(random_bytes(11)));
  }

  static public function replaceWordCharacters($text) {

    // Replaces commonly-used Windows 1252 encoded chars
    // that do not exist in ASCII or ISO-8859-1 with
    // ISO-8859-1 cognates.
    $str = $text;

    $s = [];
    $r = [];

    // smart single quotes and apostrophe
    //~ $s[] = sprintf( '@(\x{2018}|\x{2019}|\x{201A})@');
    $s[] = sprintf('@(%s|%s|%s)@', "\u{2018}", "\u{2019}", "\u{201A}");
    // logger::info( $s[0]);

    $r[] = "'";

    // smart double quotes
    $s[] = sprintf('@(%s|%s|%s)@', "\u{201C}", "\u{201D}", "\u{201E}");
    $r[] = '"';

    /**
     * * ellipsis
     * s = s.replace(/\u2026/g, "...");
     *
     * * dashes
     * s = s.replace(/[\u2013|\u2014]/g, "-");
     *
     * * circumflex
     * s = s.replace(/\u02C6/g, "^");
     *
     * * open angle bracket
     * s = s.replace(/\u2039/g, "<");
     *
     * * close angle bracket
     * s = s.replace(/\u203A/g, ">");
     *
     * * spaces
     * s = s.replace(/[\u02DC|\u00A0]/g, " ");
     */

    return preg_replace($s, $r, $str);
  }

  /**
   * returns rfc822 formated email address
   *
   * @return string
   */
  static public function rfc822(string $email, string $name = ''): string {
    // 8.1.0	flags changed from ENT_COMPAT to ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401.
    if ($name) {

      return sprintf('%s <%s>', htmlentities($name, ENT_COMPAT), $email);
    } else {

      return $email;
    }
  }

  static public function safe_file_name(string $str): string {

    if ($ext = pathinfo($str, PATHINFO_EXTENSION)) {

      // remove the extension, trim and trim again for any . characters
      $str = trim(trim(preg_replace('/' . preg_quote($ext, '/') . '$/', '', $str)), '.');

      $str = preg_replace('@\s+@', ' ', $str);  // replace multiple spaces with a single space
      $str = preg_replace('@\.+@', '.', $str);  // replace multiple periods with a single period

      $str = sprintf('%s.%s', preg_replace('@[^0-9a-z\-\_\s\.]@i', '', $str), $ext);

      return $str;
    } else {

      $str = preg_replace('!\s+!', ' ', $str);
      // logger::info( sprintf( '<<%s>> : %s', $str, __METHOD__));
      return preg_replace('@[^0-9a-z\-\_\s]@i', '', $str);
    }
  }

  static public function SmartCase($name) {
    $name = strtolower($name);
    $name = join("'", array_map('ucwords', explode("'", $name)));
    $name = join("-", array_map('ucwords', explode("-", $name)));
    $name = join("Mac", array_map('ucwords', explode("Mac", $name)));
    $name = join("Mc", array_map('ucwords', explode("Mc", $name)));
    return $name;
  }

  static public function street_index(string|null $street): string {
    /**
     * Makes a street no, name into an indexable value
     * 38 Chapel Street becomes
     * Chapel Street   38
     */
    $strStreetIndex = (string)$street;  // safety

    if ($street) {

      if (is_numeric(substr($street, 0, 1))) {

        $aStreet = explode(' ', $street);

        $no = array_shift($aStreet);
        if (false != strpos($no, '/')) {
          $_no = explode('/', $no);
          $_r = array_reverse($_no);
          $no = implode(' ', $_r);
        }

        $aStreet[] = str_pad(trim((string)$no), 6, ' ', STR_PAD_LEFT);
        $strStreetIndex = implode(' ', $aStreet);
        if ('' == $strStreetIndex)
          $strStreetIndex = $street;  // safety

      }
    }

    return (string)$strStreetIndex;
  }

  /**
   * @deprecated Use the function with the same name instead.
   */
  #[\Deprecated]
  static public function text2html($inText, $maxrows = -1, $allAsteriskAsList = false): string {

    $inText = esc($inText);

    if ($maxrows > 0) {
      $x = preg_split("/\n/", $inText);
      while (count($x) > ($maxrows + 1))
        array_pop($x);
      $inText = implode("<br>", $x);
    }

    $a = [
      "/---\\n/",
      "/\\\\n/",
      "/\r\n/",
      "/\n/",
      "/\r/",
      "/$\*/",
      "/\s\s/"
    ];

    $aR = array(
      '<hr align="left" style="width: 200px; margin: 0;">',
      '<br>',
      '<br>',
      '<br>',
      '<br>',
      '<br>&bull;',
      '&nbsp;&nbsp;'
    );

    if ($allAsteriskAsList) {
      $a[] = "/\*/";
      $aR[] = "<br>&bull;";
      $inText = rtrim($inText, " .*");
    }

    return (preg_replace($a, $aR, $inText));
  }

  static public function toEmail822($email, $name = ''): string {
    if (self::isEmail($email)) {
      if ($name) {
        return (sprintf('%s <%s>', $name, $email));
      } else {
        return (sprintf('%s <%s>', $email, $email));
      }
    }

    return '';
  }

  static public function url(string $url = '', bool $protocol = false): string {
    return url::toString($url, $protocol);
  }

  static public function xml_entities($text, $charset = 'UTF-8') {
    // Debug and Test
    // $text = "test &amp; &trade; &amp;trade; abc &reg; &amp;reg; &#45;";

    /*
			First we encode html characters that are also invalid in xml
			*/
    $text = htmlentities($text, ENT_COMPAT, $charset, false);

    /*
			XML character entity array from Wiki
			Note: &apos; is useless in UTF-8 or in UTF-16
			*/
    $arr_xml_special_char = array("&quot;", "&amp;", "&apos;", "&lt;", "&gt;");

    /*
			Building the regex string to exclude all strings with xml special char
			*/
    $arr_xml_special_char_regex = "(?";
    foreach ($arr_xml_special_char as $key => $value) {
      $arr_xml_special_char_regex .= "(?!$value)";
    }
    $arr_xml_special_char_regex .= ")";

    /*
			Scan the array for &something_not_xml; syntax
			*/
    $pattern = "/$arr_xml_special_char_regex&([a-zA-Z0-9]+;)/";

    /*
			Replace the &something_not_xml; with &amp;something_not_xml;
			*/
    $replacement = '&amp;${1}';
    return preg_replace($pattern, $replacement, $text);
  }
}
