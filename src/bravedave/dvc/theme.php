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

class theme {
  const layout_0 = 0;

  const layout_9_3 = 1;
  const layout_10_2 = 2;
  const layout_swap = 3;

	const primary = 0;
	const secondary = 1;
	const success = 2;
	const info = 3;
	const warning = 4;
	const danger = 5;

  static $layout = self::layout_0;

  protected static function _primary() {
    $norm = 'col-md-9 pt-sm-3 pb-4';

    if (self::layout_0 == self::$layout && 'yes' == currentUser::option('enable-left-layout')) {

      $norm = 'col pt-3 pb-4';
    }

    if (self::layout_10_2 == self::$layout) {

      return 'col pt-3 pb-4';
    } else {

      return $norm;
    }
  }

  protected static function _secondary() {

    $norm = 'col-md-3 py-3 pb-4';
    $colors = 'd-print-none';

    if (in_array(currentUser::option('theme'), [
      'blue',
      'pink'
    ])) {

      $colors .= ' text-secondary bg-light';
    } elseif (currentUser::option('theme') == 'orange') {

      $colors .= ' text-secondary bg-sidebar';
    } else {

      $colors .= ' text-secondary bg-white';
    }

    if (self::layout_0 == self::$layout && 'yes' == currentUser::option('enable-left-layout')) {

      $norm = 'col-md-3 col-lg-2 py-3 pb-4';
    }

    if (self::layout_10_2 == self::$layout) {

      return 'col-md-3 col-lg-2 py-3 pb-4 ' . $colors;
    } else {

      return $norm . ' ' . $colors;
    }
  }

  static function navbar($params = []) {

    $options = array_merge([
      'color' => 'navbar-dark bg-primary bg-gradient-navbar',
      'defaults' => 'navbar navbar-expand-md d-print-none',
      'sticky' => 'sticky-top',
    ], $params);

    return implode(' ', $options);
  }

  static function modalHeader(int $level = self::primary) {

    if (self::warning == $level) return 'text-white bg-warning';
    if (self::success == $level) return 'text-white bg-success';
    return 'text-white bg-primary';
  }

  public static function rootFont() {
    if (!userAgent::isMobileDevice() && currentUser::option('enable-smaller-desktop-font') == 'yes') {

      return '<style media="screen">html { font-size: .8rem }</style>';
    }

    return '';
  }

  public static function secondary() {
    return (self::layout_swap == self::$layout ? self::_primary() : self::_secondary());
  }
}
