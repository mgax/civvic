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

require_once('phplib/config.php');
require_once('phplib/db.php');
require_once('phplib/activeRecords.php');

// Connect to the civvic_tools database
db_init(CONF_DATABASE_TOOLS);
$civvicDbResult = db_execute('select * from Word');

while(!$civvicDbResult->EOF) {
  $word = new Word();
  $word->set($civvicDbResult->fields);
  $civvicDbResult->MoveNext();
  $dexHits = db_getSingleValue("select count(*) from DEX.InflectedForm where formNoAccent = '{$word->form}'");
  if (!$dexHits) {
    $dexMatch = db_execute("select * from DEX.InflectedForm where formUtf8General = '{$word->form}' limit 1");
    if ($dexMatch && $dexMatch->fields['id']) {
      print "[$word->form] [" . $dexMatch->fields['formNoAccent'] . "]\n";
      $word->form = $dexMatch->fields['formNoAccent'];
      $word->save();
    }
  }
}


?>
