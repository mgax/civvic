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
    if ($this->year && $this->number) {
      $otherAct = Model::factory('Act')->where('actTypeId', $this->actTypeId)->where('year', $this->year)->where('number', $this->number)->find_one();
      if ($otherAct && $otherAct->id != $this->id) {
        FlashMessage::add('Există deja un act cu acest tip, număr și an.');
      }
    }
    return !FlashMessage::getMessage();
  }

  function save() {
    if ($this->issueDate == '') {
      $this->issueDate = null;
    }
    if ($this->id) {
      ActReference::unassociateByReferredActId($this->id);
    }
    parent::save();
    // The HTML has changed for all the actVersions this act modifies, and all future versions from those acts
    $modifiedAvs = Model::factory('ActVersion')->where('modifyingActId', $this->id)->find_many();
    foreach ($modifiedAvs as $modifiedAv) {
      $avs = Model::factory('ActVersion')->where('actId', $modifiedAv->actId)->where_gte('versionNumber', $modifiedAv->versionNumber)->find_many();
      foreach ($avs as $av) {
        $av->htmlContents = MediaWikiParser::wikiToHtml($av);
        $av->save();
      }
    }
    ActReference::associateReferredAct($this);
    ActReference::reconvertReferringActVersions($this->id);
  }

  function countVersions() {
    return Model::factory('ActVersion')->where('actId', $this->id)->count();
  }

  static function listYears($actTypeName) {
    $actType = ActType::get_by_shortName($actTypeName);
    $acts = Model::factory('Act')->select('year')->distinct()->where('actTypeId', $actType->id)->order_by_desc('year')->find_many();
    $results = array();
    foreach ($acts as $a) {
      $results[] = $a->year;
    }
    return $results;
  }

  function getDisplayId() {
    $at = ActType::get_by_id($this->actTypeId);
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
      return sprintf('<a class="actLink undefined" href="http://civvic.ro/act-inexistent?data=%s:%s:%s">%s</a>',
                     $actTypeId, $number, $year, $text);
    }

    $class = $act->getDisplayClass();
    return sprintf('<a class="actLink %s" href="http://civvic.ro/act?id=%s">%s</a>', $class, $act->id, $text);
  }

  /* Returns a map of versionNumber -> modifying act for that version */
  static function getModifyingActs($actId) {
    $map = array();
    $avs = Model::factory('ActVersion')->select('versionNumber')->select('modifyingActId')->where('actId', $actId)->find_many();
    foreach ($avs as $av) {
      $map[$av->versionNumber] = Act::get_by_id($av->modifyingActId);
    }
    return $map;
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
    ActReference::reconvertReferringActVersions($oldId);
    ActReference::unassociateByReferredActId($oldId);
    return true;
  }
}

?>
