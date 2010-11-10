<?php

function string_startsWith($string, $substring) {
  $startString = substr($string, 0, strlen($substring));
  return $startString == $substring;
}

function string_endsWith($string, $substring) {
  $lenString = strlen($string);
  $lenSubstring = strlen($substring);
  $endString = substr($string, $lenString - $lenSubstring, $lenSubstring);
  return $endString == $substring;
}

?>
