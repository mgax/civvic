<?php

function string_startsWith($str, $substr) {
  return strncmp($str, $substr, strlen($substr)) == 0;
}

function string_endsWith($str, $substr) {
  $lenStr = strlen($str);
  $lenSubstr = strlen($substr);
  $endStr = substr($str, $lenStr - $lenSubstr, $lenSubstr);
  return $endStr == $substr;
}

?>
