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

	const arrow_90deg_down = 20;
	const arrow_90deg_left = 21;
	const arrow_90deg_right = 22;
	const arrow_90deg_up = 23;
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

  /** 65 is free  */

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

  const bath = 101;
  const bath_fill = 102;
  const bed = 103;
  const bed_fill = 104;

  const box = 107;
  const box_arrow_down_left = 108;
  const box_arrow_down_right = 109;
  const box_arrow_down = 110;
  const box_arrow_in_down_left = 111;
  const box_arrow_in_down_right = 112;
  const box_arrow_in_down = 113;
  const box_arrow_in_left = 114;
  const box_arrow_in_right = 115;
  const box_arrow_in_up_left = 116;
  const box_arrow_in_up_right = 117;
  const box_arrow_in_up = 118;
  const box_arrow_left = 119;
  const box_arrow_right = 120;
  const box_arrow_up_left = 121;
  const box_arrow_up_right = 122;
  const box_arrow_up = 123;

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

  const car = 151;
  const car_fill = 152;

  const chat = 160;
  const chat_dots = 161;
  const chat_dots_fill = 162;

  const check = 170;
  const check_all = 171;
  const check2 = 172;
  const check2_all = 173;

  const chevronStart = 174;
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
  const file_rich_text = 200;
  const file_text = 201;
  const file_text_fill = 202;

  const gear = 210;
  const gear_fill = 211;
  const gear_wide = 212;
  const gear_wide_connected = 213;

  const github = 214;

  const house = 215;
  const house_plus = 216;

  const image = 220;
  const images = 221;

  const list = 240;
  const list_check = 241;
  const list_nested = 242;
  const list_ol = 243;
  const list_stars = 244;
  const list_ul = 245;

  const magic = 301;
  const man_walking = 302;
  const menu_down = 303;
  const menu_up = 304;

  const people = 345;
  const people_fill = 346;

  const person = 350;
  const person_check = 352;
  const person_check_fill = 354;

  const person_dash = 356;

  const person_fill = 360;
  const person_plus = 361;
  const person_plus_fill = 362;
  const person_reload = 363;

  const plus = 380;
  const plus_circle = 381;
  const plus_circle_fill = 382;
  const plus_square = 383;
  const plus_square_fill = 384;

  const phone = 410;
  const phone_fill = 411;
  const telephone = 412;
  const telephone_fill = 412;

  const question = 440;

  const sliders = 500;

  const x = 600;

  // const icon_dir = __DIR__ . '/resource/bootstrap-icons/icons/';
  const icon_dir = sys::bootstrap_icon_dir;

  static public function get( int $icon ) : string {
    if ( self::app == $icon) return file_get_contents( self::icon_dir . 'app.svg');

    elseif ( self::arrow_90deg_down == $icon) return file_get_contents( self::icon_dir . 'arrow-90deg-down.svg');
    elseif ( self::arrow_90deg_left == $icon) return file_get_contents( self::icon_dir . 'arrow-90deg-left.svg');
    elseif ( self::arrow_90deg_right == $icon) return file_get_contents( self::icon_dir . 'arrow-90deg-right.svg');
    elseif ( self::arrow_90deg_up == $icon) return file_get_contents( self::icon_dir . 'arrow-90deg-up.svg');
    elseif ( self::arrow_bar_down == $icon) return file_get_contents( self::icon_dir . 'arrow-bar-down.svg');
    elseif ( self::arrow_bar_left == $icon) return file_get_contents( self::icon_dir . 'arrow-bar-left.svg');
    elseif ( self::arrow_bar_right == $icon) return file_get_contents( self::icon_dir . 'arrow-bar-right.svg');
    elseif ( self::arrow_bar_up == $icon) return file_get_contents( self::icon_dir . 'arrow-bar-up.svg');
    elseif ( self::arrow_clockwise == $icon) return file_get_contents( self::icon_dir . 'arrow-clockwise.svg');
    elseif ( self::arrow_counterclockwise == $icon) return file_get_contents( self::icon_dir . 'arrow-counterclockwise.svg');
    elseif ( self::arrow_down_circle_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-down-circle-fill.svg');
    elseif ( self::arrow_down_circle == $icon) return file_get_contents( self::icon_dir . 'arrow-down-circle.svg');
    elseif ( self::arrow_down_left_circle_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-down-left-circle-fill.svg');
    elseif ( self::arrow_down_left_circle == $icon) return file_get_contents( self::icon_dir . 'arrow-down-left-circle.svg');
    elseif ( self::arrow_down_left_square_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-down-left-square-fill.svg');
    elseif ( self::arrow_down_left_square == $icon) return file_get_contents( self::icon_dir . 'arrow-down-left-square.svg');
    elseif ( self::arrow_down_left == $icon) return file_get_contents( self::icon_dir . 'arrow-down-left.svg');
    elseif ( self::arrow_down_right_circle_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-down-right-circle-fill.svg');
    elseif ( self::arrow_down_right_circle == $icon) return file_get_contents( self::icon_dir . 'arrow-down-right-circle.svg');
    elseif ( self::arrow_down_right_square_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-down-right-square-fill.svg');
    elseif ( self::arrow_down_right_square == $icon) return file_get_contents( self::icon_dir . 'arrow-down-right-square.svg');
    elseif ( self::arrow_down_right == $icon) return file_get_contents( self::icon_dir . 'arrow-down-right.svg');
    elseif ( self::arrow_down_short == $icon) return file_get_contents( self::icon_dir . 'arrow-down-short.svg');
    elseif ( self::arrow_down_square_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-down-square-fill.svg');
    elseif ( self::arrow_down_square == $icon) return file_get_contents( self::icon_dir . 'arrow-down-square.svg');
    elseif ( self::arrow_down_up == $icon) return file_get_contents( self::icon_dir . 'arrow-down-up.svg');
    elseif ( self::arrow_down == $icon) return file_get_contents( self::icon_dir . 'arrow-down.svg');
    elseif ( self::arrow_left_circle_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-left-circle-fill.svg');
    elseif ( self::arrow_left_circle == $icon) return file_get_contents( self::icon_dir . 'arrow-left-circle.svg');
    elseif ( self::arrow_left_right == $icon) return file_get_contents( self::icon_dir . 'arrow-left-right.svg');
    elseif ( self::arrow_left_short == $icon) return file_get_contents( self::icon_dir . 'arrow-left-short.svg');
    elseif ( self::arrow_left_square_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-left-square-fill.svg');
    elseif ( self::arrow_left_square == $icon) return file_get_contents( self::icon_dir . 'arrow-left-square.svg');
    elseif ( self::arrow_left == $icon) return file_get_contents( self::icon_dir . 'arrow-left.svg');
    elseif ( self::arrow_repeat == $icon) return file_get_contents( self::icon_dir . 'arrow-repeat.svg');
    elseif ( self::arrow_return_left == $icon) return file_get_contents( self::icon_dir . 'arrow-return-left.svg');
    elseif ( self::arrow_return_right == $icon) return file_get_contents( self::icon_dir . 'arrow-return-right.svg');
    elseif ( self::arrow_right_circle_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-right-circle-fill.svg');
    elseif ( self::arrow_right_circle == $icon) return file_get_contents( self::icon_dir . 'arrow-right-circle.svg');
    elseif ( self::arrow_right_short == $icon) return file_get_contents( self::icon_dir . 'arrow-right-short.svg');
    elseif ( self::arrow_right_square_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-right-square-fill.svg');
    elseif ( self::arrow_right_square == $icon) return file_get_contents( self::icon_dir . 'arrow-right-square.svg');
    elseif ( self::arrow_right == $icon) return file_get_contents( self::icon_dir . 'arrow-right.svg');
    elseif ( self::arrow_up_circle_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-up-circle-fill.svg');
    elseif ( self::arrow_up_circle == $icon) return file_get_contents( self::icon_dir . 'arrow-up-circle.svg');
    elseif ( self::arrow_up_left_circle_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-up-left-circle-fill.svg');
    elseif ( self::arrow_up_left_circle == $icon) return file_get_contents( self::icon_dir . 'arrow-up-left-circle.svg');
    elseif ( self::arrow_up_left_square_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-up-left-square-fill.svg');
    elseif ( self::arrow_up_left_square == $icon) return file_get_contents( self::icon_dir . 'arrow-up-left-square.svg');
    elseif ( self::arrow_up_left == $icon) return file_get_contents( self::icon_dir . 'arrow-up-left.svg');
    elseif ( self::arrow_up_right_circle_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-up-right-circle-fill.svg');
    elseif ( self::arrow_up_right_circle == $icon) return file_get_contents( self::icon_dir . 'arrow-up-right-circle.svg');
    elseif ( self::arrow_up_right_square_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-up-right-square-fill.svg');
    elseif ( self::arrow_up_right_square == $icon) return file_get_contents( self::icon_dir . 'arrow-up-right-square.svg');
    elseif ( self::arrow_up_right == $icon) return file_get_contents( self::icon_dir . 'arrow-up-right.svg');
    elseif ( self::arrow_up_short == $icon) return file_get_contents( self::icon_dir . 'arrow-up-short.svg');
    elseif ( self::arrow_up_square_fill == $icon) return file_get_contents( self::icon_dir . 'arrow-up-square-fill.svg');
    elseif ( self::arrow_up_square == $icon) return file_get_contents( self::icon_dir . 'arrow-up-square.svg');
    elseif ( self::arrow_up == $icon) return file_get_contents( self::icon_dir . 'arrow-up.svg');
    elseif ( self::at == $icon) return file_get_contents( self::icon_dir . 'at.svg');

    elseif ( self::bath == $icon) return file_get_contents( __DIR__ . '/icons/bath.svg');
    elseif ( self::bath_fill == $icon) return file_get_contents( __DIR__ . '/icons/bath-fill.svg');
    elseif ( self::bed == $icon) return file_get_contents( __DIR__ . '/icons/bed.svg');
    elseif ( self::bed_fill == $icon) return file_get_contents( __DIR__ . '/icons/bed-fill.svg');

    elseif ( self::box == $icon) return file_get_contents( self::icon_dir . 'box.svg');
    elseif ( self::box_arrow_down_left == $icon) return file_get_contents( self::icon_dir . 'box-arrow-down-left.svg');
    elseif ( self::box_arrow_down_right == $icon) return file_get_contents( self::icon_dir . 'box-arrow-down-right.svg');
    elseif ( self::box_arrow_down == $icon) return file_get_contents( self::icon_dir . 'box-arrow-down.svg');
    elseif ( self::box_arrow_in_down_left == $icon) return file_get_contents( self::icon_dir . 'box-arrow-in-down-left.svg');
    elseif ( self::box_arrow_in_down_right == $icon) return file_get_contents( self::icon_dir . 'box-arrow-in-down-right.svg');
    elseif ( self::box_arrow_in_down == $icon) return file_get_contents( self::icon_dir . 'box-arrow-in-down.svg');
    elseif ( self::box_arrow_in_left == $icon) return file_get_contents( self::icon_dir . 'box-arrow-in-left.svg');
    elseif ( self::box_arrow_in_right == $icon) return file_get_contents( self::icon_dir . 'box-arrow-in-right.svg');
    elseif ( self::box_arrow_in_up_left == $icon) return file_get_contents( self::icon_dir . 'box-arrow-in-up-left.svg');
    elseif ( self::box_arrow_in_up_right == $icon) return file_get_contents( self::icon_dir . 'box-arrow-in-up-right.svg');
    elseif ( self::box_arrow_in_up == $icon) return file_get_contents( self::icon_dir . 'box-arrow-in-up.svg');
    elseif ( self::box_arrow_left == $icon) return file_get_contents( self::icon_dir . 'box-arrow-left.svg');
    elseif ( self::box_arrow_right == $icon) return file_get_contents( self::icon_dir . 'box-arrow-right.svg');
    elseif ( self::box_arrow_up_left == $icon) return file_get_contents( self::icon_dir . 'box-arrow-up-left.svg');
    elseif ( self::box_arrow_up_right == $icon) return file_get_contents( self::icon_dir . 'box-arrow-up-right.svg');
    elseif ( self::box_arrow_up == $icon) return file_get_contents( self::icon_dir . 'box-arrow-up.svg');

    elseif ( self::car == $icon) return file_get_contents( __DIR__ . '/icons/car.svg');
    elseif ( self::car_fill == $icon) return file_get_contents( __DIR__ . '/icons/car-fill.svg');
    elseif ( self::calendar == $icon) return file_get_contents( self::icon_dir . 'calendar.svg');
    elseif ( self::calendar_date == $icon) return file_get_contents( self::icon_dir . 'calendar-date.svg');
    elseif ( self::calendar_date_fill == $icon) return file_get_contents( self::icon_dir . 'calendar-date-fill.svg');
    elseif ( self::calendar_day == $icon) return file_get_contents( self::icon_dir . 'calendar-day.svg');
    elseif ( self::calendar_day_fill == $icon) return file_get_contents( self::icon_dir . 'calendar-day-fill.svg');
    elseif ( self::calendar_event == $icon) return file_get_contents( self::icon_dir . 'calendar-event.svg');
    elseif ( self::calendar_event_fill == $icon) return file_get_contents( self::icon_dir . 'calendar-event-fill.svg');
    elseif ( self::calendar_minus == $icon) return file_get_contents( self::icon_dir . 'calendar-minus.svg');
    elseif ( self::calendar_minus_fill == $icon) return file_get_contents( self::icon_dir . 'calendar-minus-fill.svg');
    elseif ( self::calendar_plus == $icon) return file_get_contents( self::icon_dir . 'calendar-plus.svg');
    elseif ( self::calendar_plus_fill == $icon) return file_get_contents( self::icon_dir . 'calendar-plus-fill.svg');
    elseif ( self::chat == $icon) return file_get_contents( self::icon_dir . 'chat.svg');
    elseif ( self::chat_dots == $icon) return file_get_contents( self::icon_dir . 'chat-dots.svg');
    elseif ( self::chat_dots_fill == $icon) return file_get_contents( self::icon_dir . 'chat-dots-fill.svg');
    elseif ( self::check == $icon) return file_get_contents( self::icon_dir . 'check.svg');
    elseif ( self::check_all == $icon) return file_get_contents( self::icon_dir . 'check-all.svg');
    elseif ( self::check2 == $icon) return file_get_contents( self::icon_dir . 'check2.svg');
    elseif ( self::check2_all == $icon) return file_get_contents( self::icon_dir . 'check2-all.svg');
    elseif ( self::chevronStart == $icon) return file_get_contents( self::icon_dir . 'chevron-bar-left.svg');
    elseif ( self::chevronLeft == $icon) return file_get_contents( self::icon_dir . 'chevron-left.svg');
    elseif ( self::chevronRight == $icon) return file_get_contents( self::icon_dir . 'chevron-right.svg');

    elseif ( self::diamond == $icon) {
      return file_get_contents( self::icon_dir . 'diamond.svg');

    }
    elseif ( self::diamond_fill == $icon) {
      return file_get_contents( self::icon_dir . 'diamond-fill.svg');

    }
    elseif ( self::diamond_half == $icon) {
      return file_get_contents( self::icon_dir . 'diamond-half.svg');

    }
    elseif ( self::document == $icon) {
      return file_get_contents( self::icon_dir . 'file.svg');

    }
    elseif ( self::document_code == $icon) {
      return file_get_contents( self::icon_dir . 'file-code.svg');

    }
    elseif ( self::document_diff == $icon) {
      return file_get_contents( self::icon_dir . 'file-diff.svg');

    }
    elseif ( self::document_richtext == $icon) {
      return file_get_contents( self::icon_dir . 'file-richtext.svg');

    }
    elseif ( self::document_spreadsheet == $icon) {
      return file_get_contents( self::icon_dir . 'file-spreadsheet.svg');

    }
    elseif ( self::document_text == $icon) {
      return file_get_contents( self::icon_dir . 'file-text.svg');

    }
    elseif ( self::documents == $icon) {
      return file_get_contents( self::icon_dir . 'files.svg');

    }
    elseif ( self::documents_alt == $icon) {
      return file_get_contents( self::icon_dir . 'files-alt.svg');

    }

    elseif ( self::envelope == $icon) return file_get_contents( self::icon_dir . 'envelope.svg');
    elseif ( self::envelope_fill == $icon) return file_get_contents( self::icon_dir . 'envelope-fill.svg');
    elseif ( self::envelope_open == $icon) return file_get_contents( self::icon_dir . 'envelope-open.svg');
    elseif ( self::envelope_open_fill == $icon) return file_get_contents( self::icon_dir . 'envelope-open-fill.svg');

    elseif ( self::file_rich_text == $icon) return file_get_contents( self::icon_dir . 'file-richtext.svg');
    elseif ( self::file_text == $icon) return file_get_contents( self::icon_dir . 'file-text.svg');
    elseif ( self::file_text_fill == $icon) return file_get_contents( self::icon_dir . 'file-text-fill.svg');

    elseif ( self::gear == $icon) return file_get_contents( self::icon_dir . 'gear.svg');
    elseif ( self::gear_fill == $icon) return file_get_contents( self::icon_dir . 'gear-fill.svg');
    elseif ( self::gear_wide == $icon) return file_get_contents( self::icon_dir . 'gear-wide.svg');
    elseif ( self::gear_wide_connected == $icon) return file_get_contents( self::icon_dir . 'gear-wide-connected.svg');
    elseif ( self::github == $icon) return file_get_contents( __DIR__ . '/icons/github.svg');

    elseif ( self::house == $icon) return file_get_contents( self::icon_dir . 'house.svg');
    elseif ( self::house_plus == $icon) return file_get_contents( __DIR__ . '/icons/house-plus.svg');

    elseif ( self::image == $icon) return file_get_contents( self::icon_dir . 'image.svg');
    elseif ( self::images == $icon) return file_get_contents( self::icon_dir . 'images.svg');

    elseif ( self::list == $icon) return file_get_contents( self::icon_dir . 'list.svg');
    elseif ( self::list_check == $icon) return file_get_contents( self::icon_dir . 'list-check.svg');
    elseif ( self::list_nested == $icon) return file_get_contents( self::icon_dir . 'list-nested.svg');
    elseif ( self::list_ol == $icon) return file_get_contents( self::icon_dir . 'list-ol.svg');
    elseif ( self::list_stars == $icon) return file_get_contents( self::icon_dir . 'list-stars.svg');
    elseif ( self::list_ul == $icon) return file_get_contents( self::icon_dir . 'list-ul.svg');

    elseif ( self::magic == $icon) return file_get_contents( __DIR__ . '/icons/magic.svg');
    elseif ( self::man_walking == $icon) return file_get_contents( __DIR__ . '/icons/man-walking.svg');
    elseif ( self::menu_down == $icon) return file_get_contents( self::icon_dir . 'menu-down.svg');
    elseif ( self::menu_up == $icon) return file_get_contents( self::icon_dir . 'menu-up.svg');

    elseif ( self::people == $icon) return file_get_contents( self::icon_dir . 'people.svg');
    elseif ( self::people_fill == $icon) return file_get_contents( self::icon_dir . 'people-fill.svg');
    elseif ( self::person == $icon) return file_get_contents( self::icon_dir . 'person.svg');
    elseif ( self::person_check == $icon) return file_get_contents( self::icon_dir . 'person-check.svg');
    elseif ( self::person_check_fill == $icon) return file_get_contents( self::icon_dir . 'person-check-fill.svg');
    elseif ( self::person_dash == $icon) return file_get_contents( self::icon_dir . 'person-dash.svg');
    elseif ( self::person_fill == $icon) return file_get_contents( self::icon_dir . 'person-fill.svg');
    elseif ( self::person_plus == $icon) return file_get_contents( self::icon_dir . 'person-plus.svg');
    elseif ( self::person_plus_fill == $icon) return file_get_contents( self::icon_dir . 'person-plus-fill.svg');
    elseif ( self::person_reload == $icon) return file_get_contents( __DIR__ . '/icons/person-reload.svg');
    elseif ( self::plus == $icon) return file_get_contents( self::icon_dir . 'plus.svg');
    elseif ( self::plus_circle == $icon) return file_get_contents( self::icon_dir . 'plus_circle.svg');
    elseif ( self::plus_circle_fill == $icon) return file_get_contents( self::icon_dir . 'plus_circle_fill.svg');
    elseif ( self::plus_square == $icon) return file_get_contents( self::icon_dir . 'plus_square.svg');
    elseif ( self::plus_square_fill == $icon) return file_get_contents( self::icon_dir . 'plus_square_fill.svg');
    elseif ( self::phone == $icon) return file_get_contents( self::icon_dir . 'phone.svg');
    elseif ( self::phone_fill == $icon) return file_get_contents( self::icon_dir . 'phone-fill.svg');

    elseif ( self::question == $icon) return file_get_contents( self::icon_dir . 'question.svg');

    elseif ( self::telephone == $icon) {
      return file_get_contents( self::icon_dir . 'telephone.svg');

    }
    elseif ( self::telephone_fill == $icon) {
      return file_get_contents( self::icon_dir . 'telephone-fill.svg');

    }
    elseif ( self::sliders == $icon) {
      return file_get_contents( self::icon_dir . 'phone.svg');

    }
    elseif ( self::x == $icon) {
      return file_get_contents( self::icon_dir . 'x.svg');

    }

    return file_get_contents( self::icon_dir . 'question.svg');

  }

  static public function base64_data( int $icon) {
    $str = self::get( $icon);
    $data = base64_encode( $str);
    // $finfo = new \finfo(FILEINFO_MIME_TYPE);
    // $finfo->buffer($str)

    return 'data:image/svg+xml;base64,'.$data;

  }

  static public function inline( int $icon) {
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
