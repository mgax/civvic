<?php

require_once '../../lib/Util.php';
Util::requireAdmin();

$term = Util::getRequestParameter('term');

$authors = Model::factory('Author')->find_many();
$words = preg_split("/\\s+/", trim($term));
foreach ($words as $word) {
  $keep = array();
  foreach ($authors as $a) {
    if (stripos($a->institution, $word) !== false ||
        stripos($a->position, $word) !== false ||
        stripos($a->title, $word) !== false ||
        stripos($a->name, $word) !== false) {
      $keep[] = $a;
    }
  }
  $authors = $keep;
}

$results = array();
foreach ($authors as $a) {
  $results[] = $a->getDisplayName();
}
print json_encode($results);

?>
