<?php

class Author extends BaseObject {

  private static function _cmpDisplayName($a, $b) {
    $dna = $a->getDisplayName();
    $dnb = $b->getDisplayName();
    if ($a == $b) {
      return 0;
    }
    return ($a < $b) ? -1 : 1;
  }

  static function loadAllOrderByDisplayName() {
    $authors = Model::factory('Author')->find_many();
    usort($authors, 'self::_cmpDisplayName');
    return $authors;
  }

  function getDisplayName() {
    $bits = array();
    if ($this->institution) {
      $bits[] = $this->institution;
    }
    if ($this->position) {
      $bits[] = $this->position;
    }
    if ($this->title) {
      $bits[] = $this->title;
    }
    if ($this->name) {
      $bits[] = $this->name;
    }
    return implode(', ', $bits);
  }

  function validate() {
    if (!$this->institution && !$this->position && !$this->title && !$this->name) {
      FlashMessage::add('Cel puțin unul din câmpuri trebue să fie nevid.');
      return false;
    }
    return true;
  }

}

?>
