<?php

class ActType extends BaseObject {

  static function mapById() {
    $map = array();
    foreach (Model::factory('ActType')->find_many() as $at) {
      $map[$at->id] = $at;
    }
    return $map;
  }

  function validate() {
    if (mb_strlen($this->name) < 3) {
      FlashMessage::add('Numele trebuie să aibă minim trei caractere.');
      return false;
    }
    return true;
  }

}

?>
