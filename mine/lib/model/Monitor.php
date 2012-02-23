<?php

class Monitor extends BaseObject {

  function validate() {
    if (!$this->number) {
      FlashMessage::add('Numărul nu poate fi vid.');
    }
    if (!StringUtil::isValidYear($this->year)) {
      FlashMessage::add('Anul trebuie să fie între 1800 și 2100.');
    }
    if (!StringUtil::isValidDate($this->issueDate)) {
      FlashMessage::add('Data trebuie să fie între 1800 și 2100.');
    }
    if ($this->year != date('Y', strtotime($this->issueDate))) {
      FlashMessage::add('Data trebuie să fie din anul monitorului.');
    }
    return !FlashMessage::getMessage();
  }

  function delete() {
    $count = Model::factory('Act')->where('monitorId', $this->id)->count();
    if ($count) {
      FlashMessage::add("Monitorul {$this->number} / {$this->year} nu poate fi șters, deoarece există acte care îl folosesc.");
      return false;
    }
    return parent::delete();
  }

}

?>
