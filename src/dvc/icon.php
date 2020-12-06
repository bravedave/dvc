<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

class icon {
  const app = 10;
  const at = 15;
  const box = 20;

  const calendar_event = 40;
  const calendar_event_fill = 41;
  const calendar_minus = 42;
  const calendar_minus_fill = 43;
  const calendar_plus = 44;
  const calendar_plus_fill = 45;

  const chat = 46;

  const check = 47;
  const check_all = 48;
  const check2 = 49;
  const check2_all = 50;

  const chevronLeft = 51;
  const chevronRight = 52;

  const diamond = 80;
  const diamond_fill = 81;
  const diamond_half = 82;

  const envelope = 120;
  const envelope_fill = 121;
  const envelope_open = 122;
  const envelope_open_fill = 123;

  const fileRichText = 170;
  const file_rich_text = 170;
  const file_text = 171;
  const file_text_fill = 172;

  const gear = 180;
  const gear_fill = 181;
  const gear_wide = 182;
  const gear_wide_connected = 183;

  const github = 184;

  const house = 190;
  const house_plus = 191;

  const list = 220;
  const list_check = 221;
  const list_nested = 222;
  const list_ol = 223;
  const list_stars = 224;
  const list_ul = 225;

  const menu_down = 301;
  const menu_up = 302;

  const people = 345;
  const people_fill = 346;

  const person = 350;
  const person_check = 352;
  const person_check_fill = 354;

  const person_dash = 356;

  const person_fill = 360;
  const person_plus = 361;
  const person_plus_fill = 362;

  const plus = 380;
  const plus_circle = 381;
  const plus_circle_fill = 382;
  const plus_square = 383;
  const plus_square_fill = 384;

  const phone = 410;
  const phone_fill = 411;

  const question = 440;

  const sliders = 500;

  const x = 600;

  static function get( int $icon ) : string {
    if ( self::app == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/app.svg');

    }
    elseif ( self::at == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/at.svg');

    }
    elseif ( self::box == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/box.svg');

    }
    elseif ( self::calendar_event == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-event.svg');

    }
    elseif ( self::calendar_event_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-event-fill.svg');

    }
    elseif ( self::calendar_minus == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-minus.svg');

    }
    elseif ( self::calendar_minus_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-minus-fill.svg');

    }
    elseif ( self::calendar_plus == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-plus.svg');

    }
    elseif ( self::calendar_plus_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-plus-fill.svg');

    }
    elseif ( self::chat == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/chat.svg');

    }
    elseif ( self::check == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/check.svg');

    }
    elseif ( self::check_all == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/check-all.svg');

    }
    elseif ( self::check2 == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/check2.svg');

    }
    elseif ( self::check2_all == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/check2-all.svg');

    }
    elseif ( self::chevronLeft == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/chevron-left.svg');

    }
    elseif ( self::chevronRight == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/chevron-right.svg');

    }
    elseif ( self::diamond == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/diamond.svg');

    }
    elseif ( self::diamond_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/diamond-fill.svg');

    }
    elseif ( self::diamond_half == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/diamond-half.svg');

    }
    elseif ( self::envelope == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/envelope.svg');

    }
    elseif ( self::envelope_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/envelope-fill.svg');

    }
    elseif ( self::envelope_open == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/envelope-open.svg');

    }
    elseif ( self::envelope_open_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/envelope-open-fill.svg');

    }
    elseif ( self::file_rich_text == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/file-richtext.svg');

    }
    elseif ( self::file_text == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/file-text.svg');

    }
    elseif ( self::file_text_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/file-text-fill.svg');

    }
    elseif ( self::gear == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/gear.svg');

    }
    elseif ( self::gear_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/gear-fill.svg');

    }
    elseif ( self::gear_wide == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/gear-wide.svg');

    }
    elseif ( self::gear_wide_connected == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/gear-wide-connected.svg');

    }
    elseif ( self::github == $icon) {
      return file_get_contents( __DIR__ . '/icons/github.svg');

    }
    elseif ( self::house == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/house.svg');

    }
    elseif ( self::house_plus == $icon) {
      return file_get_contents( __DIR__ . '/icons/house-plus.svg');

    }
    elseif ( self::list == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/list.svg');

    }
    elseif ( self::list_check == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/list-check.svg');

    }
    elseif ( self::list_nested == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/list-nested.svg');

    }
    elseif ( self::list_ol == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/list-ol.svg');

    }
    elseif ( self::list_stars == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/list-stars.svg');

    }
    elseif ( self::list_ul == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/list-ul.svg');

    }
    elseif ( self::menu_down == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/menu-down.svg');

    }
    elseif ( self::menu_up == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/menu-up.svg');

    }
    elseif ( self::question == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/question.svg');

    }
    elseif ( self::people == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/people.svg');

    }
    elseif ( self::people_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/people-fill.svg');

    }
    elseif ( self::person == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person.svg');

    }
    elseif ( self::person_check == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-check.svg');

    }
    elseif ( self::person_check_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-check-fill.svg');

    }
    elseif ( self::person_dash == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-dash.svg');

    }
    elseif ( self::person_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-fill.svg');

    }
    elseif ( self::person_plus == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-plus.svg');

    }
    elseif ( self::person_plus_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-plus-fill.svg');

    }
    elseif ( self::plus == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/plus.svg');

    }
    elseif ( self::plus_circle == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/plus_circle.svg');

    }
    elseif ( self::plus_circle_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/plus_circle_fill.svg');

    }
    elseif ( self::plus_square == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/plus_square.svg');

    }
    elseif ( self::plus_square_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/plus_square_fill.svg');

    }
    elseif ( self::phone == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/phone.svg');

    }
    elseif ( self::phone_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/phone-fill.svg');

    }
    elseif ( self::sliders == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/phone.svg');

    }
    elseif ( self::x == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/x.svg');

    }

    return file_get_contents( __DIR__ . '/bootstrap4/icons/question.svg');

  }

  static function inline( int $icon) {
    /*
      <style>
      .bi {
          display: inline-block;
          content: "";
          background-repeat: no-repeat;
          background-size: 1rem 1rem;
          background-position: 0px 1px;
          width: 1rem;
          height: 1rem;
      }

      .bi-github { background-image: url("<?= dvc\icon::inline( dvc\icon::github ) ?>"); }
      .bi-chevron-left { background-image: url("<?= dvc\icon::inline( dvc\icon::chevronLeft ) ?>"); }
      .bi-chevron-right { background-image: url("<?= dvc\icon::inline( dvc\icon::chevronRight ) ?>"); }

      </style>
    */
    $icon = self::get( $icon);

    $icon = \preg_replace( [
        "@#<@",
        "@^(\t|\s)*@m",
        "@(\r?\n|\r)@",
        '@"@',
    ], [
        '%23',
        '',
        '',
        "'"
    ], $icon);

    return 'data:image/svg+xml,'.$icon;

  }

}
