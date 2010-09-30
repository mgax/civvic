<?
/**
 * Copyright 2010 Cătălin Frâncu <cata@francu.com>
 *
 * This file is part of Civvic.
 *
 * Civvic is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Civvic is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Civvic.  If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * Takes a wordmap in the format
 *
 *    <word> <frequency>
 *
 * The wordmap is sorted by frequency in ascending order.
 * Outputs two dictionaries, one of frequent words and one with regular words.
 **/

define('MIN_FREQUENCY', 4);
define('NUM_FREQUENT', 5000);

$opts = parseOptions();
$wordmap = loadWordMap($opts['input']);
print "Wordmap has " . count($wordmap) . " entries with a frequency of at least " . MIN_FREQUENCY . "\n";
saveDictionaries($wordmap, $opts['frequent'], $opts['regular']);

/*************************************************************************/

function parseOptions() {
  $opts = getopt(null, array('input:', 'frequent:', 'regular:'));
  if (!isset($opts['input']) || !isset($opts['frequent']) || !isset($opts['regular'])) {
    usage();
  }
  return $opts;
}

function usage() {
  print "Required arguments:\n";
  print "--input        Input file\n";
  print "--frequent     Output file (frequent words)\n";
  print "--regular      Output file (regular words)\n";
  exit(1);
}

function loadWordMap($filename) {
  $result = array();
  $lines = file($filename, FILE_IGNORE_NEW_LINES);
  foreach ($lines as $line) {
    $parts = preg_split('/\s+/', $line);
    assert(count($parts) == 2);
    if (mb_strlen($parts[0]) > 1 && $parts[1] >= MIN_FREQUENCY) {
      $result[$parts[0]] = $parts[1];
    }
  }
  return $result;
}

function saveDictionaries($wordmap, $frequentFilename, $regularFilename) {
  arsort($wordmap);
  $f = fopen($frequentFilename, 'w');
  $r = fopen($regularFilename, 'w');
  $i = 0;
  foreach ($wordmap as $word => $count) {
    if ($i < NUM_FREQUENT) {
      fwrite($f, "$word\n");
      $i++;
    } else {
      fwrite($r, "$word\n");
    }
  }
  fclose($f);
  fclose($r);
}

?>
