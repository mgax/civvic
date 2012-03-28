<?php

class MonitorReference extends BaseObject {

  static function deleteByActVersionId($avId) {
    $refs = Model::factory('MonitorReference')->where('actVersionId', $avId)->find_many();
    foreach ($refs as $ref) {
      $ref->delete();
    }
  }

  static function saveByActVersionId($references, $avId) {
    foreach ($references as $ref) {
      $ref->actVersionId = $avId;
      $m = Model::factory('Monitor')->where('number', $ref->number)->where('year', $ref->year)->find_one();
      if ($m) {
        $ref->monitorId = $m->id;
      }
      $ref->save();
    }
  }

  static function associateReferredMonitor($m) {
    $refs = Model::factory('MonitorReference')->where('number', $m->number)->where('year', $m->year)->find_many();
    foreach ($refs as $ref) {
      $ref->monitorId = $m->id;
      $ref->save();
      $av = ActVersion::get_by_id($ref->actVersionId);
      $av->contents = $av->contents; // Make the field dirty
      $av->save();
    }
  }

  static function unassociateByReferredMonitorId($monitorId) {
    $refs = Model::factory('MonitorReference')->where('monitorId', $monitorId)->find_many();
    foreach ($refs as $ref) {
      $ref->monitorId = null;
      $ref->save();
      $av = ActVersion::get_by_id($ref->actVersionId);
      $av->contents = $av->contents; // Make the field dirty
      $av->save();
    }
  }

}

?>
