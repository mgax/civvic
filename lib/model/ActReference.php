<?php

class ActReference extends BaseObject {

  static function deleteByActVersionId($avId) {
    $refs = Model::factory('ActReference')->where('actVersionId', $avId)->find_many();
    foreach ($refs as $ref) {
      $ref->delete();
    }
  }

  static function deleteByActTypeId($actTypeId) {
    $refs = Model::factory('ActReference')->where('actTypeId', $actTypeId)->find_many();
    foreach ($refs as $ref) {
      $ref->delete();
    }
  }

  static function saveByActVersionId($references, $avId) {
    foreach ($references as $ref) {
      $ref->actVersionId = $avId;
      $referredAct = Model::factory('Act')->where('actTypeId', $ref->actTypeId)->where('number', $ref->number)->where('year', $ref->year)
        ->find_one();
      if ($referredAct) {
        $ref->referredActId = $referredAct->id;
      }
      $ref->save();
    }
  }

  static function unassociateByReferredActId($actId) {
    $refs = Model::factory('ActReference')->where('referredActId', $actId)->find_many();
    foreach ($refs as $ref) {
      $ref->referredActId = null;
      $ref->save();
    }
  }

  static function associateReferredAct($act) {
    $refs = Model::factory('ActReference')->where('actTypeId', $act->actTypeId)->where('number', $act->number)->where('year', $act->year)
        ->find_many();
    foreach ($refs as $ref) {
      $ref->referredActId = $act->id;
      $ref->save();
    }
  }

  static function reconvertReferringActVersions($actId) {
    $refs = Model::factory('ActReference')->where('referredActId', $actId)->find_many();
    $avMap = array();
    foreach ($refs as $ref) {
      $avMap[$ref->actVersionId] = true;
    }
    foreach ($avMap as $actVersionId => $ignored) {
      $av = ActVersion::get_by_id($actVersionId);
      $av->htmlContents = MediaWikiParser::wikiToHtml($av);
      $av->save(); // Note that we haven't touched $av->contents
    }
  }
}

?>
