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
    if (!StringUtil::isValidYear($this->year)) {
      FlashMessage::add('Anul trebuie să fie între 1800 și 2100.');
    }
    if (!$this->actTypeId) {
      FlashMessage::add('Actul trebuie să aibă un tip.');
    }
    return !FlashMessage::getMessage();
  }

  function save() {
    if ($this->issueDate == '') {
      $this->issueDate = null;
    }
    if ($this->id) {
      Reference::unassociateByReferredActId($this->id);
    }
    parent::save();
    Reference::associateReferredAct($this);
  }

  function countVersions() {
    return Model::factory('ActVersion')->where('actId', $this->id)->count();
  }

  function getDisplayId() {
    $at = ActType::get_by_id($this->actTypeId);
    $result = $at->artName . ' ';
    $result .= ($this->year && $this->number) ? "{$this->number} / {$this->year}" : "din {$this->issueDate}";
    return $result;
  }

  // Class to use when linking to this act
  function getDisplayClass() {
    $version = Model::factory('ActVersion')->where('actId', $this->id)->where('current', true)->find_one();
    return ($version->status == ACT_STATUS_VALID) ? 'valid' : 'repealed';
  }

  function delete() {
    $count = Model::factory('ActVersion')->where_not_equal('actId', $this->id)->where('modifyingActId', $this->id)->count();
    if ($count) {
      FlashMessage::add("Actul '{$this->getDisplayId()}' nu poate fi șters, deoarece el modifică alte acte.");
      return false;
    }

    $avs = Model::factory('ActVersion')->where('actId', $this->id)->order_by_desc('versionNumber')->find_many();
    foreach ($avs as $av) {
      $av->delete();
    }

    Reference::unassociateByReferredActId($this->id);
    return parent::delete();
  }
}

?>
