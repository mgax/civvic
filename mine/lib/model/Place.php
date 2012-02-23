<?php

class Place extends BaseObject {

  function validate() {
    if (mb_strlen($this->name) < 3) {
      FlashMessage::add('Numele trebuie să aibă minim trei caractere.');
      return false;
    }
    return true;
  }

  function delete() {
    $count = Model::factory('Act')->where('placeId', $this->id)->count();
    if ($count) {
      FlashMessage::add("Locul '{$this->name}' nu poate fi șters, deoarece există acte care îl folosesc.");
      return false;
    }
    return parent::delete();
  }

}

?>
