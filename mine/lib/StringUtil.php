<?php

class StringUtil {

  static function startsWith($string, $substring) {
    $startString = substr($string, 0, strlen($substring));
    return $startString == $substring;
  }

  static function endsWith($string, $substring) {
    $lenString = strlen($string);
    $lenSubstring = strlen($substring);
    $endString = substr($string, $lenString - $lenSubstring, $lenSubstring);
    return $endString == $substring;
  }

}

?>
