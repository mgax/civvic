<?php

class ActVersion extends BaseObject {

  static function insertVersion($act, $before, $otherVersion) {
    $newAv = Model::factory('ActVersion')->create();
    $newAv->actId = $act->id;
    $newAv->modifyingActId = null;
    $newAv->status = ACT_STATUS_VALID;
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
    return !FlashMessage::getMessage();
  }

  function save() {
    $contentsChanged = $this->is_dirty('contents');
    if ($contentsChanged) {
      $references = array();
      $this->htmlContents = MediaWikiParser::wikiToHtml($this->contents, $references);

      // Recompute the diff from the previous version
      if ($this->versionNumber > 1) {
        $previousAv = Model::factory('ActVersion')->where('actId', $this->actId)->where('versionNumber', $this->versionNumber - 1)->find_one();
        $this->diff = json_encode(SimpleDiff::lineDiff($previousAv->contents, $this->contents));
      }

      // Recompute the next version's diff from us
      $nextAv = Model::factory('ActVersion')->where('actId', $this->actId)->where('versionNumber', $this->versionNumber + 1)->find_one();
      if ($nextAv) {
        $nextAv->diff = json_encode(SimpleDiff::lineDiff($this->contents, $nextAv->contents));
        $nextAv->save();
      }
    }

    parent::save();

    if ($contentsChanged) {
      Reference::deleteByActVersionId($this->id);
      Reference::saveByActVersionId($references, $this->id);
    }
  }

  function delete() {
    $prev = Model::factory('ActVersion')->where('actId', $this->actId)->where('versionNumber', $this->versionNumber - 1)->find_one();
    $next = Model::factory('ActVersion')->where('actId', $this->actId)->where('versionNumber', $this->versionNumber + 1)->find_one();

    if ($next) {
      $next->diff = $prev ? json_encode(SimpleDiff::lineDiff($prev->contents, $next->contents)) : '';
      $next->save();
    }

    if ($this->current && $prev) {
      $prev->current = true;
      $prev->save();
    }

    $avs = Model::factory('ActVersion')->where('actId', $this->actId)->where_gt('versionNumber', $this->versionNumber)->find_many();
    foreach ($avs as $av) {
      $av->versionNumber--;
      $av->save();
    }

    Reference::deleteByActVersionId($this->id);
    
    return parent::delete();
  }

}

?>
