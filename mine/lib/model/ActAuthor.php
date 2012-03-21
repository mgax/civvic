<?php

class ActAuthor extends BaseObject {

  /**
   * Updates the list of authors for an act, preserving the existing records when possible.
   * Adds ActAuthor records if needed. Deletes leftover records.
   **/
  static function saveAuthors($actId, $authorString) {
    $lines = preg_split("/[\\r\\n]+/", trim($authorString));
    $authorMap = Author::loadAllMapByDisplayName();
    $oldAas = Model::factory('ActAuthor')->where('actId', $actId)->order_by_asc('rank')->find_many();

    $rank = 1;
    foreach ($lines as $line) {
      $authorId = $authorMap[$line];
      if ($authorId) {
        $aa = empty($oldAas) ? Model::factory('ActAuthor')->create() : array_shift($oldAas);
        $aa->actId = $actId;
        $aa->authorId = $authorId;
        $aa->rank = $rank++;
        $aa->save();
      }
    }
    foreach ($oldAas as $aa) {
      $aa->delete();
    }
  }

}

?>
