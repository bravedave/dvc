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

/**
 * bravedave\dvc\esc
 * @return string HTML-safe
 */
function esc(string|null $v) : string {
  
  if ($v) return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
  return '';
}