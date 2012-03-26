<?php

/*
  Adapted and extended from:

  Paul's Simple Diff Algorithm v 0.1
  (C) Paul Butler 2007 <http://www.paulbutler.org/>
  May be used and distributed under the zlib/libpng license.

  This code is intended for learning purposes; it was written with short
  code taking priority over performance. It could be used in a practical
  application, but there are a few ways it could be optimized.

  Given two arrays, the function diff will return an array of the changes.
  I won't describe the format of the array, but it will be obvious
  if you use print_r() on the result of a diff on some test data.
	
  htmlDiff is a wrapper for the diff command, it takes two strings and
  returns the differences in HTML. The tags used are <ins> and <del>,
  which can easily be styled with CSS.  
*/

class SimpleDiff {

  /** Returns an array with keys length, oldOffset, newOffset **/
  private static function longestSubarray(&$old, &$new) {
    $maxlen = 0;
    $omax = 0;
    $nmax = 0;
    foreach ($old as $oindex => $ovalue) {
      $nkeys = array_keys($new, $ovalue);
      foreach ($nkeys as $nindex) {
        $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
          $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
        if ($matrix[$oindex][$nindex] > $maxlen) {
          $maxlen = $matrix[$oindex][$nindex];
          $omax = $oindex + 1 - $maxlen;
          $nmax = $nindex + 1 - $maxlen;
        }
      }	
    }
    return array('length' => $maxlen, 'oldOffset' => $omax, 'newOffset' => $nmax);
  }

  static function diff($old, $new, $oldOffset){
    if (empty($old) && empty($new)) {
      return array();
    }
    $c = self::longestSubarray($old, $new);
    if ($c['length'] == 0) return array($oldOffset => array('d' => $old, 'i' => $new));
    return self::diff(array_slice($old, 0, $c['oldOffset']),
                      array_slice($new, 0, $c['newOffset']),
                      $oldOffset) +
      self::diff(array_slice($old, $c['oldOffset'] + $c['length']),
                 array_slice($new, $c['newOffset'] + $c['length']),
                 $c['oldOffset'] + $c['length'] + $oldOffset);
  }

  static function lineDiff($old, $new) {
    return self::diff(explode("\r\n", $old), explode("\r\n", $new), 1);
  }

  /**
   * This won't work for now. It assumes we preserve the unchanged lines, but we don't.
   */
  static function htmlDiff($old, $new) {
    $diff = self::diff(explode("\r\n", $old), explode("\r\n", $new), 1);
    $ret = '';
    foreach ($diff as $k) {
      if (is_array($k)) {
        $ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'') .
          (!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
      } else  {
        $ret .= $k . ' ';
      }
    }
    return $ret;
  }

  /** Diff that returns the correspondence between identical lines **/
  static function commonLinesDiff($old, $new, $oldOffset = 0, $newOffset = 0) {
    $c = self::longestSubarray($old, $new);
    if (!$c['length']) {
      return array();
    }
    $result = array();
    for ($i = 0; $i < $c['length']; $i++) {
      $result[$oldOffset + $c['oldOffset'] + $i] = $newOffset + $c['newOffset'] + $i;
    }
    return self::commonLinesDiff(array_slice($old, 0, $c['oldOffset']),
                                 array_slice($new, 0, $c['newOffset']),
                                 $oldOffset, $newOffset) +
      $result + 
      self::commonLinesDiff(array_slice($old, $c['oldOffset'] + $c['length']),
                            array_slice($new, $c['newOffset'] + $c['length']),
                            $oldOffset + $c['oldOffset'] + $c['length'], $newOffset + $c['newOffset'] + $c['length']);
  }

}

?>
