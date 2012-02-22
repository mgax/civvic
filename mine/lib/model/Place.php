<?php

class Place extends BaseObject {

  function validate() {
    if (mb_strlen($this->name) < 3) {
      FlashMessage::add('Numele trebuie să aibă minim trei caractere.');
      return false;
    }
    return true;
  }

}

?>
