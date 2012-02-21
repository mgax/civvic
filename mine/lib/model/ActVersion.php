<?php

class ActVersion extends BaseObject {

  static function insertVersion($act, $before, $otherVersion) {
    $newAv = Model::factory('ActVersion')->create();
    $newAv->actId = $act->id;
    $newAv->modifyingActId = null;
    $newAv->status = ACT_STATUS_VALID;
    $newAv->contents = '';
    $newAv->htmlContents = '';
    $newAv->diff = '';
    $newAv->versionNumber = $before ? $otherVersion : ($otherVersion + 1);
    $newAv->current = true;

    $avs = Model::factory('ActVersion')->where('actId', $act->id)->find_many();
    foreach ($avs as $av) {
      if ($av->versionNumber >= $newAv->versionNumber) {
        $av->versionNumber++;
      }

      if ($av->versionNumber < $newAv->versionNumber) {
        $av->current = false;
      } else {
        $newAv->current = false;
      }

      $av->save();
    }

    $copyVersion = ($newAv->versionNumber == 1) ? 2 : ($newAv->versionNumber - 1);
    $copyAv = Model::factory('ActVersion')->where('actId', $act->id)->where('versionNumber', $copyVersion)->find_one();
    $newAv->contents = $copyAv->contents;
    $newAv->htmlContents = $copyAv->htmlContents;

    return $newAv;
  }

  static function createVersionOne($act) {
    $av = Model::factory('ActVersion')->create();
    $av->actId = $act->id;
    $av->modifyingActId = $act->id;
    $av->status = ACT_STATUS_VALID;
    $av->contents = '';
    $av->htmlContents = '';
    $av->diff = '';
    $av->versionNumber = 1;
    $av->current = true;
    return $av;
  }

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
