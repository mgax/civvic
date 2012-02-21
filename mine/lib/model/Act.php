<?php

define('ACT_STATUS_VALID', 1);
define('ACT_STATUS_REPEALED', 2);

class Act extends BaseObject {
  static $statuses = array(ACT_STATUS_VALID => 'valabil',
                           ACT_STATUS_REPEALED => 'abrogat');

  function validate() {
    if (mb_strlen($this->name) < 3) {
      FlashMessage::add('Numele trebuie să aibă minim trei caractere.');
    }
    if ($this->year != '' && !StringUtil::isValidYear($this->year)) {
      FlashMessage::add('Anul trebuie să fie între 1800 și 2100.');
    }
    if (!$this->actTypeId) {
      FlashMessage::add('Actul trebuie să aibă un tip.');
    }
    if (!$this->monitorId) {
      FlashMessage::add('Monitorul Oficial nu poate fi vid.');
    }
    if (!StringUtil::isValidDate($this->issueDate)) {
      FlashMessage::add('Data trebuie să fie între 1800 și 2100.');
    }
    return !FlashMessage::getMessage();
  }

  function save() {
    if ($this->year == '') {
      $this->year = null;
    }
    parent::save();
  }

  function countVersions() {
    return Model::factory('ActVersion')->where('actId', $this->id)->count();
  }

}

?>
