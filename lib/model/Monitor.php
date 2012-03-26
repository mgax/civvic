<?php

class Monitor extends BaseObject {

  function getPdfNumber() {
    // Count digits (ignore suffixes like "bis")
    $len = mb_strlen($this->number);
    $i = 0;
    while ($i < $len && ctype_digit($this->number[$i])) {
      $i++;
    }
    return str_repeat('0', 4 - $i) . $this->number;
  }

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
