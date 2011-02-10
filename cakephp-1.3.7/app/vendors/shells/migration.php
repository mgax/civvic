<?php

App::import('Lib', 'StringManipulation');
App::import('Lib', 'Db');
define('PATCH_DIR', 'app/schema-changes');
define('PATCH_REGEXP', '/^\d{5}\./');

class MigrationShell extends Shell {
  var $uses = array('Variable');
  var $dryRun = false;

  function main() {
    foreach ($this->args as $arg) {
      switch ($arg) {
      case 'dry': $this->dryRun = true; break;
      default: print "Unknown flag $arg -- ignored\n"; exit;
      }
    }
    $schemaVersion = $this->Variable->peek('Schema.version', '00000');
    print "Current schema version is $schemaVersion\n";

    $patchFiles = $this->__getPatches(PATCH_DIR, $schemaVersion);
    $lastPatch = null;
    foreach ($patchFiles as $fileName) {
      $this->__runPatch(PATCH_DIR . "/" . $fileName);
      $lastPatch = $this->__stripExtension($fileName);
    }
    if (!$this->dryRun && $lastPatch) {
      print "Schema updated to version $lastPatch\n";
      $this->Variable->poke('Schema.version', $lastPatch);
    }
  }

  function help() {
    print "Migrate the database schema and contents to the latest version\n";
    print "Optional argument: dry -- prints out the commands without executing them\n";
  }

  function __getPatches($dir, $after) {
    $result = array();
    if ($dirHandle = opendir($dir)) {
      while (($fileName = readdir($dirHandle)) !== false) {
        if (preg_match(PATCH_REGEXP, $fileName) && $this->__stripExtension($fileName) > $after && !string_endsWith($fileName, '~')) {
          $result[] = $fileName;
        }
      }
      closedir($dirHandle);
      sort($result);
    }
    return $result;
  }

  function __runPatch($fileName) {
    $extension = strrchr($fileName, '.');
    if ($extension == '.sql') {
      print "    $fileName -- executing with MySQL\n";
      if (!$this->dryRun) {
        db_executeSqlFile($fileName);
      }
    } else if ($extension == '.php') {
      print "    $fileName -- executing with PHP\n";
      if (!$this->dryRun) {
        require $fileName;
      }
    } else {
      print "    $fileName -- unknown extension, ignoring\n";
    }
  }

  function __stripExtension($fileName) {
    $dot = strrpos($fileName, '.');
    return ($dot === false) ? $fileName : substr($fileName, 0, $dot);
  }
}

?>
