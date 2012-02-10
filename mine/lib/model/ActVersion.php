<?php

class ActVersion extends BaseObject {

  function validate() {
    $ma = Act::get_by_id($this->modifyingActId);
    if (!$ma) {
      FlashMessage::add('Actul modificator nu a fost găsit.');
    }
    if (!$this->status) {
      FlashMessage::add('Actul trebuie să aibă o stare.');
    }
    if (!$this->contents) {
      FlashMessage::add('Conținutul nu poate fi gol.');
    }
    return !FlashMessage::getMessage();
  }

}

?>
