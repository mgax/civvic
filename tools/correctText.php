<?php

require_once __DIR__ . '/../lib/Util.php';

$DEX_DB = 'DEX';
$HARDCODE = array(
                  'Caras' => 'Caraș',
                  'cite' => 'câte',
                  'Constanta' => 'Constanța',
                  'Dimbovita' => 'Dâmbovița',
                  'Galati' => 'Galați',
                  'in' => 'în',
                  'In' => 'În',
                  'impreuna' => 'împreună',
                  'mai' => 'mai',
                  'Maramures' => 'Maramureș',
                  'Nasaud' => 'Năsăud',
                  'pina' => 'până',
                  'preturi' => 'prețuri',
                  'preturilor' => 'prețurilor',
                  'sa' => 'să',
                  'Salaj' => 'Sălaj',
                  'sau' => 'sau',
                  'sint' => 'sunt',
                  'Suceava' => 'Suceava',
                  'tara' => 'țară',
                  'va' => 'va',
                  );

if (count($argv) != 3) {
  die("Usage: {$argv[0]} input_file output_file\n");
}

$text = file_get_contents($argv[1]);
$text = str_replace(array('ş', 'Ş', 'ţ', 'Ţ'),
                    array('ș', 'Ș', 'ț', 'Ț'), $text); 

$output = '';
$len = mb_strlen($text);
$inWord = false;
$word = '';
for ($i = 0; $i < $len; $i++) {
  $c = mb_substr($text, $i, 1);
  if (isLetter($c)) {
    $word .= $c;
    $inWord = true;
  } else {
    if ($word != '') {
      $output .= findMatch($word);
    }
    $word = '';
    $output .= $c;
    $inWord = false;
  }
}
if ($word != '') {
  $output .= findMatch($word);
}

file_put_contents($argv[2], $output);

/*************************************************************************/

function isLetter($c) {
  return ctype_alpha($c) || (mb_strpos('ăâîșțĂÂÎȘȚ', $c) !== false);
}

function isUpper($c) {
  return ctype_upper($c) || (mb_strpos('ĂÂÎȘȚ', $c) !== false);
}

function capitalize($s, $like) {
  if (isUpper(mb_substr($like, 0, 1))) {
    return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
  } else {
    return $s;
  }
}

function hasDiacritics($s) {
  for ($i = 0; $i < mb_strlen($s); $i++) {
    $c = mb_substr($s, $i, 1);
    if (mb_strpos('ăâîșțĂÂÎȘȚ', $c) !== false) {
      return true;
    }
  }
  return false;
}

function findMatch($word, $allowIA = true) {
  global $DEX_DB;
  global $HARDCODE;

  if (hasDiacritics($word)) {
    return $word;
  }

  if (array_key_exists($word, $HARDCODE)) {
    return $HARDCODE[$word];
  }

  $query = sprintf("select i.formUtf8General, sum(frequency) as s from $DEX_DB.InflectedForm i, $DEX_DB.Lexem l " .
                   "where lexemId = l.id and i.formUtf8General = '{$word}' group by i.formNoAccent order by s desc");
  $matches = ORM::for_table('Variable')->raw_query($query, null)->find_many();
  if (count($matches) == 1) {
    return capitalize($matches[0]->formUtf8General, $word);
  }
  if (count($matches) > 1) {
    return (($matches[1]->s == 0) || ($matches[0]->s / $matches[1]->s >= 1.5)) ? capitalize($matches[0]->formUtf8General, $word) : $word;
  }
  // No matches -- try to replace i with â and search again
  $pos = strrpos($word, 'i');
  if ($pos !== false) {
    $choice = substr($word, 0, $pos) . 'a' . substr($word, $pos + 1);
    $otherWord = findMatch($choice, false);
    return ($otherWord == $choice) ? $word : $otherWord;
  }
  return $word;
}

?>
