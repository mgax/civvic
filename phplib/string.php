<?php

$GLOBALS['string_unicode'] = array('ă', 'Ă', 'â', 'Â', 'á', 'Á', 'à', 'À', 'ä', 'Ä', 'å', 'Å', 'ç', 'Ç', 'ć', 'Ć', 'č', 'Č',
                                   'é', 'É', 'è', 'È', 'ê', 'Ê', 'ë', 'Ë', 'ĕ', 'Ĕ', 'ğ', 'Ğ', 'í', 'Í', 'ì', 'Ì', 'î', 'Î',
                                   'ï', 'Ï', 'ĩ', 'Ĩ', 'ñ', 'Ñ', 'ó', 'Ó', 'ò', 'Ò', 'ô', 'Ô', 'ö', 'Ö', 'õ', 'Õ', 'ř', 'Ř',
                                   'š', 'Š', 'ș', 'Ș', 'ț', 'Ț', 'ș', 'Ș', 'ț', 'Ț', 'ú', 'Ú', 'ù', 'Ù', 'û', 'Û',
                                   'ü', 'Ü', 'ŭ', 'Ŭ', 'ý', 'Ý', 'ÿ', 'Ÿ', 'ž', 'Ž');

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

function string_getCharAt($s, $index) {
  return mb_substr($s, $index, 1);
}

function string_isUnicodeLetter($char) {
  return ctype_alpha($char) || in_array($char, $GLOBALS['string_unicode']);
}

?>
