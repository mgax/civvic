#!/usr/bin/php
<?

mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'ro_RO');
$LETTERS = 'aăâbcdefghiîjklmnopqrsștțuvwxyz';
$CAPS = mb_strtoupper($LETTERS);
$SIGNS = '!#$%&\'()*+,-./:;<=>?[]^{}„”—';

$passes = mb_strlen($CAPS) * 5;
$placeLetter = 0;
$placeSigns = 0;
for ($i = 0; $i < $passes; $i++) {
  $pos = $i % mb_strlen($CAPS);
  print mb_substr($CAPS, $pos, 1);
  for ($j = 0; $j < 5; $j++) {
    print mb_substr($LETTERS, $placeLetter++, 1);
    $placeLetter %= mb_strlen($LETTERS);
  }
  print mb_substr($SIGNS, $placeSigns++, 1);
  $placeSigns %= mb_strlen($SIGNS);
  print " ";
}

for ($i = 0; $i < 20; $i++) {
  print (rand() % 1000000) . ',' . (rand() % 100) . ' ';
}

print "\n";

?>
