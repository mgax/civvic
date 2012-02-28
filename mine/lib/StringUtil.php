<?php

class StringUtil {
  private static $months = array('ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie',
                                 'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie');

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

  static function parseRomanianDate($s) {
    $regexp = sprintf("/(?P<day>\\d{1,2})\\s+(?P<month>%s)\\s+(?P<year>\\d{4})/i", implode('|', self::$months));
    preg_match($regexp, $s, $matches);
    if (!count($matches)) {
      return null;
    }
    $month = 1 + array_search($matches['month'], self::$months);

    return sprintf("%4d-%02d-%02d", $matches['year'], $month, $matches['day']);
  }
}

?>
