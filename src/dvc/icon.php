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

	const arrow_90deg_down = 19;
	const arrow_90deg_left = 20;
	const arrow_90deg_right = 21;
	const arrow_90deg_up = 22;
	const arrow_bar_bottom = 23;
	const arrow_bar_down = 24;
	const arrow_bar_left = 25;
	const arrow_bar_right = 26;
	const arrow_bar_up = 27;
	const arrow_clockwise = 280;
	const arrow_counterclockwise = 29;
	const arrow_down_circle_fill = 30;
	const arrow_down_circle = 31;
	const arrow_down_left_circle_fill = 32;
	const arrow_down_left_circle = 33;
	const arrow_down_left_square_fill = 34;
	const arrow_down_left_square = 35;
	const arrow_down_left = 36;
	const arrow_down_right_circle_fill = 37;
	const arrow_down_right_circle = 38;
	const arrow_down_right_square_fill = 39;
	const arrow_down_right_square = 40;
	const arrow_down_right = 41;
	const arrow_down_short = 42;
	const arrow_down_square_fill = 43;
	const arrow_down_square = 44;
	const arrow_down_up = 45;
	const arrow_down = 46;
	const arrow_left_circle_fill = 47;
	const arrow_left_circle = 48;
	const arrow_left_right = 49;
	const arrow_left_short = 50;
	const arrow_left_square_fill = 51;
	const arrow_left_square = 52;
	const arrow_left = 53;
	const arrow_repeat = 54;
	const arrow_return_left = 55;
	const arrow_return_right = 56;
	const arrow_right_circle_fill = 57;
	const arrow_right_circle = 58;
	const arrow_right_short = 59;
	const arrow_right_square_fill = 60;
	const arrow_right_square = 61;
	const arrow_right = 62;
	const arrow_up_circle_fill = 63;
	const arrow_up_circle = 64;
	const arrow_up_down = 65;
	const arrow_up_left_circle_fill = 66;
	const arrow_up_left_circle = 67;
	const arrow_up_left_square_fill = 68;
	const arrow_up_left_square = 69;
	const arrow_up_left = 70;
	const arrow_up_right_circle_fill = 71;
	const arrow_up_right_circle = 72;
	const arrow_up_right_square_fill = 73;
	const arrow_up_right_square = 74;
	const arrow_up_right = 75;
	const arrow_up_short = 76;
	const arrow_up_square_fill = 77;
	const arrow_up_square = 78;
	const arrow_up = 79;

  const at = 80;

  const box = 101;

  const calendar = 140;
  const calendar_date = 141;
  const calendar_date_fill = 142;
  const calendar_day = 143;
  const calendar_day_fill = 144;
  const calendar_event = 145;
  const calendar_event_fill = 146;
  const calendar_minus = 147;
  const calendar_minus_fill = 148;
  const calendar_plus = 149;
  const calendar_plus_fill = 150;

  const chat = 160;

  const check = 170;
  const check_all = 171;
  const check2 = 172;
  const check2_all = 173;

  const chevronLeft = 175;
  const chevronRight = 176;

  const diamond = 180;
  const diamond_fill = 181;
  const diamond_half = 182;

  const document = 185;
  const document_code = 186;
  const document_diff = 187;
  const document_richtext = 188;
  const document_spreadsheet = 189;
  const document_text = 190;

  const documents = 191;
  const documents_alt = 192;

  const envelope = 195;
  const envelope_fill = 196;
  const envelope_open = 197;
  const envelope_open_fill = 198;

  const fileRichText = 200;
  const file_rich_text = 201;
  const file_text = 202;
  const file_text_fill = 203;

  const gear = 210;
  const gear_fill = 211;
  const gear_wide = 212;
  const gear_wide_connected = 213;

  const github = 214;

  const house = 215;
  const house_plus = 216;

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
    if ( self::app == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/app.svg');

    elseif ( self::arrow_90deg_down == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-90deg-down.svg');
    elseif ( self::arrow_90deg_left == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-90deg-left.svg');
    elseif ( self::arrow_90deg_right == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-90deg-right.svg');
    elseif ( self::arrow_90deg_up == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-90deg-up.svg');
    elseif ( self::arrow_bar_bottom == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-bar-bottom.svg');
    elseif ( self::arrow_bar_down == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-bar-down.svg');
    elseif ( self::arrow_bar_left == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-bar-left.svg');
    elseif ( self::arrow_bar_right == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-bar-right.svg');
    elseif ( self::arrow_bar_up == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-bar-up.svg');
    elseif ( self::arrow_clockwise == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-clockwise.svg');
    elseif ( self::arrow_counterclockwise == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-counterclockwise.svg');
    elseif ( self::arrow_down_circle_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-circle-fill.svg');
    elseif ( self::arrow_down_circle == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-circle.svg');
    elseif ( self::arrow_down_left_circle_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-left-circle-fill.svg');
    elseif ( self::arrow_down_left_circle == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-left-circle.svg');
    elseif ( self::arrow_down_left_square_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-left-square-fill.svg');
    elseif ( self::arrow_down_left_square == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-left-square.svg');
    elseif ( self::arrow_down_left == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-left.svg');
    elseif ( self::arrow_down_right_circle_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-right-circle-fill.svg');
    elseif ( self::arrow_down_right_circle == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-right-circle.svg');
    elseif ( self::arrow_down_right_square_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-right-square-fill.svg');
    elseif ( self::arrow_down_right_square == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-right-square.svg');
    elseif ( self::arrow_down_right == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-right.svg');
    elseif ( self::arrow_down_short == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-short.svg');
    elseif ( self::arrow_down_square_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-square-fill.svg');
    elseif ( self::arrow_down_square == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-square.svg');
    elseif ( self::arrow_down_up == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down-up.svg');
    elseif ( self::arrow_down == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-down.svg');
    elseif ( self::arrow_left_circle_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-left-circle-fill.svg');
    elseif ( self::arrow_left_circle == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-left-circle.svg');
    elseif ( self::arrow_left_right == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-left-right.svg');
    elseif ( self::arrow_left_short == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-left-short.svg');
    elseif ( self::arrow_left_square_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-left-square-fill.svg');
    elseif ( self::arrow_left_square == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-left-square.svg');
    elseif ( self::arrow_left == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-left.svg');
    elseif ( self::arrow_repeat == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-repeat.svg');
    elseif ( self::arrow_return_left == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-return-left.svg');
    elseif ( self::arrow_return_right == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-return-right.svg');
    elseif ( self::arrow_right_circle_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-right-circle-fill.svg');
    elseif ( self::arrow_right_circle == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-right-circle.svg');
    elseif ( self::arrow_right_short == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-right-short.svg');
    elseif ( self::arrow_right_square_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-right-square-fill.svg');
    elseif ( self::arrow_right_square == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-right-square.svg');
    elseif ( self::arrow_right == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-right.svg');
    elseif ( self::arrow_up_circle_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-circle-fill.svg');
    elseif ( self::arrow_up_circle == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-circle.svg');
    elseif ( self::arrow_up_down == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-down.svg');
    elseif ( self::arrow_up_left_circle_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-left-circle-fill.svg');
    elseif ( self::arrow_up_left_circle == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-left-circle.svg');
    elseif ( self::arrow_up_left_square_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-left-square-fill.svg');
    elseif ( self::arrow_up_left_square == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-left-square.svg');
    elseif ( self::arrow_up_left == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-left.svg');
    elseif ( self::arrow_up_right_circle_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-right-circle-fill.svg');
    elseif ( self::arrow_up_right_circle == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-right-circle.svg');
    elseif ( self::arrow_up_right_square_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-right-square-fill.svg');
    elseif ( self::arrow_up_right_square == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-right-square.svg');
    elseif ( self::arrow_up_right == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-right.svg');
    elseif ( self::arrow_up_short == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-short.svg');
    elseif ( self::arrow_up_square_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-square-fill.svg');
    elseif ( self::arrow_up_square == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up-square.svg');
    elseif ( self::arrow_up == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/arrow-up.svg');
    elseif ( self::at == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/at.svg');

    elseif ( self::box == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/box.svg');
    elseif ( self::calendar == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar.svg');
    elseif ( self::calendar_date == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-date.svg');
    elseif ( self::calendar_date_fill == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-date-fill.svg');
    elseif ( self::calendar_day == $icon) return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-day.svg');
    elseif ( self::calendar_day_fill == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/calendar-day-fill.svg');

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
    elseif ( self::document == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/document.svg');

    }
    elseif ( self::document_code == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/document-code.svg');

    }
    elseif ( self::document_diff == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/document-diff.svg');

    }
    elseif ( self::document_richtext == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/document-richtext.svg');

    }
    elseif ( self::document_spreadsheet == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/document-spreadsheet.svg');

    }
    elseif ( self::document_text == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/document-text.svg');

    }
    elseif ( self::documents == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/documents.svg');

    }
    elseif ( self::documents_alt == $icon) {
      return file_get_contents( __DIR__ . '/bootstrap4/icons/documents-alt.svg');

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
