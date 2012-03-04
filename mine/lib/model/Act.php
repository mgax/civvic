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
    Reference::reconvertReferringActVersions($this->id);
  }

  function countVersions() {
    return Model::factory('ActVersion')->where('actId', $this->id)->count();
  }

  function getDisplayId() {
    $at = ActType::get_by_id($this->actTypeId);
    if ($at->name == 'Diverse') {
      return 'Diverse';
    }
    $result = $at->artName . ' ';
    $result .= ($this->year && $this->number) ? "{$this->number} / {$this->year}" : "din {$this->issueDate}";
    return $result;
  }

  // Class to use when linking to this act
  function getDisplayClass() {
    $version = Model::factory('ActVersion')->select('status')->where('actId', $this->id)->where('current', true)->find_one();
    return ($version && $version->status == ACT_STATUS_VALID) ? 'valid' : 'repealed';
  }

  static function getLink($actTypeId, $number, $year, $text) {
    // See if we have an act with these parameters
    $act = Model::factory('Act')->where('actTypeId', $actTypeId)->where('number', $number)->where('year', $year)->find_one();

    if (!$act) {
      return sprintf('<a class="actLink undefined" href="#" title="Acest act nu este definit." onclick="return false;">%s</a>', $text);
    }

    $class = $act->getDisplayClass();

    // FIXME: Replace with civvic.ro once we make the switch.
    return sprintf('<a class="actLink %s" href="http://civvic.ro/act?id=%s">%s</a>', $class, $act->id, $text);
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

    $oldId = $this->id;
    parent::delete();
    Reference::reconvertReferringActVersions($oldId);
    Reference::unassociateByReferredActId($oldId);
    return true;
  }
}

?>
