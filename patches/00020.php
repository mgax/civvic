<?php

$actVersions = Model::factory('ActVersion')->find_many();

foreach ($actVersions as $av) {
  print("Saving act version {$av->id}\n");
  $av->contents = StringUtil::cleanupUserInput($av->contents); // Force dirty field
  $av->save();
}

?>
