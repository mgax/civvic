<?php

function db_executeSqlFile($fileName) {
  $db = ConnectionManager::getDataSource('default');
  $statements = file_get_contents($fileName);
  $statements = explode(';', $statements);
  foreach ($statements as $statement) {
    if (trim($statement) != '') {
      $db->query($statement);
    }
  }
}

?>
