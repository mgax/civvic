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

  function delete() {
    $count = Model::factory('Act')->where('actTypeId', $this->id)->count();
    if ($count) {
      FlashMessage::add("Tipul de act '{$this->name}' nu poate fi șters, deoarece există acte care îl folosesc.");
      return false;
    }
    ActReference::deleteByActTypeId($this->id);
    return parent::delete();
  }

}

?>
