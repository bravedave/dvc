<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
*/  ?>

<table class="table">
  <thead class="small">
    <tr>
      <td class="text-center">input</td>
      <td class="text-center">cleanPhoneString</td>
      <td class="text-center">asLocalPhone</td>
      <td class="text-center">Valid</td>
    </tr>
  </thead>
  <tbody>
  <?php
    $tests = [
      '041 668 18 00',
      '+1 650 253 0000',
      '0161 496 0000'

    ];

    foreach ($tests as $test) {
      printf(
        '<tr><td class="text-center">%s</td><td class="text-center">%s</td><td class="text-center">%s</td><td class="text-center">%s</td></tr>',
        $test,
        strings::cleanPhoneString($test),
        strings::asLocalPhone($test),
        strings::isPhone($test) ? strings::html_tick : 'x'

      );

    }
  ?>
  </tbody>
</table>

