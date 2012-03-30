<?php

require_once __DIR__ . '/../lib/Util.php';

$fullDump = true;
foreach ($argv as $i => $arg) {
  if ($arg == "--public") {
    $fullDump = false;
  } else if ($i) {
    die("Unknown argument: $arg\n");
  }
}

$TMP_DIR = '/tmp';
$FILENAME = sprintf("civvic-database-%s.sql", $fullDump ? 'full' : 'public');
$GZ_FILENAME = "{$FILENAME}.gz";
$TARGET_DIR = Util::$rootPath . '/www/download';

// For public dumps, ignore some tables and dump only the schema for others
$IGNORE_TABLES = array();
$SCHEMA_ONLY = array('login_cookie', 'user');
$dbData = Db::parseDsn(Config::get('general.database'));
$commonCommand = sprintf("mysqldump -h %s -u %s --password='%s' %s ", $dbData['host'], $dbData['user'], $dbData['password'], $dbData['database']);

exec("rm -f $TMP_DIR/$FILENAME");

if ($fullDump) {
  exec("$commonCommand > $TMP_DIR/$FILENAME");
} else {
  // Dump tables with data
  $command = $commonCommand;
  foreach (array_merge($IGNORE_TABLES, $SCHEMA_ONLY) as $table) {
    $command .= sprintf(" --ignore-table=%s.%s", $dbData['database'], $table);
  }
  exec("$command > $TMP_DIR/$FILENAME");

  // Dump schema for tables in $SCHEMA_ONLY
  $command = $commonCommand . " --no-data " . implode(' ', $SCHEMA_ONLY);
  exec("$command >> $TMP_DIR/$FILENAME");
}

exec("gzip -f $TMP_DIR/$FILENAME");
exec("rm -f $TARGET_DIR/$GZ_FILENAME");
exec("mv $TMP_DIR/$GZ_FILENAME $TARGET_DIR");
exec("chmod 644 $TARGET_DIR/$GZ_FILENAME");

?>
