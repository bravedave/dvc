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

  const chat = 45;

  const chevronLeft = 48;
  const chevronRight = 49;

  const diamond = 50;
  const diamond_fill = 51;
  const diamond_half = 52;

  const envelope = 60;
  const envelope_fill = 61;
  const envelope_open = 62;
  const envelope_open_fill = 63;

  const fileRichText = 170;

  const gear = 180;
  const gear_fill = 181;
  const gear_wide = 182;
  const gear_wide_connected = 183;

  const github = 184;

  const house = 190;

  const question = 220;

  const people = 345;
  const people_fill = 346;

  const person = 350;
  const person_check = 352;
  const person_check_fill = 354;

  const person_dash = 356;

  const person_fill = 360;

  const phone = 410;
  const phone_fill = 411;

  const sliders = 500;


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
    elseif ( self::chat == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/chat.svg');

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
    elseif ( self::fileRichText == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/file-richtext.svg');

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
    elseif ( self::question == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/question.svg');

    }
    elseif ( self::people == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/people.svg');

    }
    elseif ( self::people_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/people_fill.svg');

    }
    elseif ( self::person == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person.svg');

    }
    elseif ( self::person_check == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-check.svg');

    }
    elseif ( self::person_check_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-check_fill.svg');

    }
    elseif ( self::person_dash == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-dash.svg');

    }
    elseif ( self::person_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/person-fill.svg');

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
