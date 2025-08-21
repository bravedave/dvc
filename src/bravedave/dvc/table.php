<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
 * -------------------------------------------------------------
 * class table
 *
 * Debug utility to render an array of associative arrays as an HTML table.
 *
 * Usage:
 *   $table = new \bravedave\dvc\table();
 *   $table($array); // outputs a HTML table for quick inspection
 *
 * Pass an array of associative arrays (e.g. database rows, contacts, etc).
 * All unique keys are used as table headings. Output is suitable for browser display.
 * 
 * data will look like this:
    $contacts = [
      [
          'name' => 'Alice Johnson',
          'mobile' => '555-1234',
          'email' => 'alice.johnson@example.com'
      ],
      [
          'name' => 'Bob Smith',
          'mobile' => '555-2345',
          'email' => 'bob.smith@example.com'
      ]
    ];
 * -------------------------------------------------------------
 */

namespace bravedave\dvc;

final class table {

  public function __invoke(array $data) {
    // Find all unique keys
    $keys = [];
    foreach ($data as $row) {
      if (is_array($row)) {
        $keys = array_unique(array_merge($keys, array_keys($row)));
      }
    }

    // Start table
    printf('<table class="table table-sm table-hover">');
    // Table headings
    printf('<thead><tr>');
    foreach ($keys as $key) {
      printf('<th>%s</th>', esc($key));
    }
    printf('</tr></thead>');

    // Table body
    printf('<tbody>');
    foreach ($data as $row) {
      printf('<tr>');
      foreach ($keys as $key) {
        printf('<td>%s</td>', isset($row[$key]) ? esc($row[$key]) : '');
      }
      printf('</tr>');
    }
    printf('</tbody></table>');
  }
}
