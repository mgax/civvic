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

  static function randomCapitalLetters($length) {
    $result = '';
    for ($i = 0; $i < $length; $i++) {
      $result .= chr(rand(0, 25) + ord("A"));
    }
    return $result;
  }

  static function isValidYear($s) {
    return self::isNumberBetween($s, 1800, 2100);
  }

  static function isNumberBetween($s, $min, $max) {
    if (!preg_match('/^\d+$/', $s)) {
      return false;
    }
    $i = (int)$s;
    return $i >= $min && $i <= $max;
  }

  static function isValidDate($s) {
    return self::isDateBetween($s, '1800-01-01', '2100-12-31');
  }

  static function isDateBetween($s, $ymd1, $ymd2) {
    $a = date_parse($s);
    return $a && ($a['error_count'] == 0) && ($a['warning_count'] == 0) && ($s >= $ymd1) && ($s <= $ymd2);
  }

}

?>
