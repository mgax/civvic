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
 * Reads text from stdin, one word per line, and outputs it to stdout.
 * Replaces șȘțȚ with şŞţŢ along the way.
 *   This is necessary because the ron.traineddata uses sedilla-below, not comma-below.
 * If -o is specified, also replaces â with î, except in words like român*, and sunt(em|eți)? with sînt(em|eți)?
 *   This is necessary because this orthography was used up to 1993.
 *   The new orthography is being used since March 8, 1993 (M.O. nr. 51/1993).
 **/

$opts = getopt('o');

$f = fopen('php://stdin', 'r');
while (($line = fgets($f)) !== false) {
  if (isset($opts['o'])) {
    if (preg_match('/^sunt(em|eți)?$/', $line)) {
      $line = str_replace('sunt', 'sînt', $line);
    }
    if (!preg_match('/^român/', $line)) {
      $line = str_replace('â', 'î', $line);
    }
  }
  $line = str_replace(array('ș', 'Ș', 'ț', 'Ț'), array('ş', 'Ş', 'ţ', 'Ţ'), $line);
  echo $line;
}

?>
