<?php

$GLOBALS['string_unicode'] = array('ă', 'Ă', 'â', 'Â', 'á', 'Á', 'à', 'À', 'ä', 'Ä', 'å', 'Å', 'ç', 'Ç', 'ć', 'Ć', 'č', 'Č',
                                   'é', 'É', 'è', 'È', 'ê', 'Ê', 'ë', 'Ë', 'ĕ', 'Ĕ', 'ğ', 'Ğ', 'í', 'Í', 'ì', 'Ì', 'î', 'Î',
                                   'ï', 'Ï', 'ĩ', 'Ĩ', 'ñ', 'Ñ', 'ó', 'Ó', 'ò', 'Ò', 'ô', 'Ô', 'ö', 'Ö', 'õ', 'Õ', 'ř', 'Ř',
                                   'š', 'Š', 'ș', 'Ș', 'ț', 'Ț', 'ș', 'Ș', 'ț', 'Ț', 'ú', 'Ú', 'ù', 'Ù', 'û', 'Û',
                                   'ü', 'Ü', 'ŭ', 'Ŭ', 'ý', 'Ý', 'ÿ', 'Ÿ', 'ž', 'Ž');

// Prefixes after which î should not be replaced with â.
$GLOBALS['string_iPrefixes'] = array('ne', 're', 'micro', 'sub', 'bine', 'dez', 'nemai', 'pre', 'semi', 'supra');

function string_startsWith($str, $substr) {
  return strncmp($str, $substr, strlen($substr)) == 0;
}

function string_endsWith($str, $substr) {
  $lenStr = strlen($str);
  $lenSubstr = strlen($substr);
  $endStr = substr($str, $lenStr - $lenSubstr, $lenSubstr);
  return $endStr == $substr;
}

function string_getCharAt($s, $index) {
  return mb_substr($s, $index, 1);
}

function string_isLowercase($s) {
  return $s != mb_strtoupper($s);
}

function string_isUppercase($s) {
  return $s != mb_strtolower($s);
}

function string_isUnicodeLetter($char) {
  return ctype_alpha($char) || in_array($char, $GLOBALS['string_unicode']);
}

function string_repeatCapitalization($dest, $src) {
  $origChar = string_getCharAt($src, 0);
  if (string_isUppercase($origChar)) {
    $dest = mb_strtoupper(string_getCharAt($dest, 0)) . mb_substr($dest, 1);
  }
  return $dest;
}

function string_fixOcr($s) {
  $s = str_replace(array('ä', 'Ä', 'ã', 'Ã', 'å', 'Å', 'ş', 'Ş', 'ţ', 'Ţ', '~', '_', 'í', 'ì', '“', ',,'),
                   array('ă', 'Ă', 'ă', 'Ă', 'ă', 'Ă', 'ș', 'Ș', 'ț', 'Ț', '-', '-', 'i', 'i', '”', '„'),
                   $s);
  $s = str_replace('--', '-', $s);
  $s = preg_replace("/\b-\n\b/", '', $s);
  $s = preg_replace("/\b(S|s)(i|î)nt(em|eti|eți)?\b/", "$1unt$3", $s);
  $s = preg_replace("/\bin\b/", 'în', $s);
  $s = preg_replace("/\bIn\b/", 'În', $s);
  $s = preg_replace("/\bsi\b/", 'și', $s);
  $s = preg_replace("/\bSi\b/", 'Și', $s);
  $s = preg_replace("/\bln\b/", 'în', $s);
  $s = preg_replace("/ind\b/", 'ând', $s);
  $s = preg_replace("/privând\b/", 'privind', $s);
  $s = preg_replace("/\bintr-/", "într-", $s);
  $s = preg_replace("/\n-/", "\n*", $s);
  $s = preg_replace("/ ([;:?!.,])/", "$1", $s);
  $s = preg_replace("/(.*) +(d|cl) *[ce] *c *r *[ce] *t *[ce] *a *[z2] *(a|ă|á|à) *[:12z]/", "'''$1''' decretează:", $s);
  $s = preg_replace("/(.*) +(h|li) *[o0] *t *(a|ă) *r *(a|ă) *(ș|Ș|s|S) *t *(e|a) *[:12z]/", "'''$1''' hotărăște:", $s);
  $s = __string_fixOcrWords($s);
  return $s;
}

/**
 * Tokenize the string and try to improve each word individually.
 **/
function __string_fixOcrWords($s) {
  $len = mb_strlen($s);
  $result = '';
  $token = '';
  for ($i = 0; $i < $len; $i++) {
    $c = string_getCharAt($s, $i);
    if (string_isUnicodeLetter($c)) {
      $token .= $c;
    } else {
      if ($token !== '') {
        $result .= __string_fixOcrToken($token);
        $token = '';
      }
      $result .= $c;
    }
  }
  return $result;
}

/**
 * Fix a word extracted by the OCR by looking up better matches in the words table.
 **/
function __string_fixOcrToken($token) {
  if (mb_strlen($token) <= 1) {
    return $token; // Ignore single letters
  }

  $origToken = $token;
  // Replace î with â except (1) at the beginning or end of the token; (2) when prefixed by bine-, dez-, micro-, ne-, nemai-, pre-, re-, semi-, sub-, supra-
  $i = 0;
  $pos = false;
  $len = mb_strlen($token);
  while (($pos = mb_strpos($token, 'î', $i)) !== false) {
    if (($pos > 0) && ($pos < $len - 1) && !in_array(mb_substr($token, 0, $pos), $GLOBALS['string_iPrefixes'])) {
      $token = mb_substr($token, 0, $pos) . 'â' . mb_substr($token, $pos + 1);
    }
    $i = $pos + 1;
  }
  if ($token != $origToken) {
    $token = string_repeatCapitalization($token, $origToken);
    print "Replacing [$origToken] with [$token]\n";
  }

  //print "Token: [$token]\n";
  $wordObject = new Word();
  $words = $wordObject->findAllByForm($token);
  if (count($words)) {
    return $token; // It's a valid word
  }
  $words = $wordObject->findAllByFormUtf8General($token);
  if (count($words)) {
    // TODO: try to find a frequent one
    $result = string_repeatCapitalization($words[0]['Word']['form'], $token);
    print "Replacing [{$token}] with [{$result}]\n";
    return $result;
  }

  if (strlen($token) >= 40) {
    return $token; // Don't even bother, this isn't a word.
  }

  // Try to find a (preferably frequent) word at Levenshtein distance 1
  $words = $wordObject->find('all', array('conditions' => array("dist2('" . mb_strtolower($token) . "', form_utf8_general)" => true),
                                          'order' => 'Word.frequent desc'));
  if (count($words)) {
    $result = string_repeatCapitalization($words[0]['Word']['form'], $token);
    print "       Replacing [{$token}] with [{$result}]\n";
    return $result;
  }
  return $token; // No better replacement
}

?>
