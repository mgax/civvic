<?php

function sys_die($msg) {
  fprintf(STDERR, "ERROR: $msg\n");
  exit(1);
}

function sys_executeAndAssert($command) {
  $exit_code = 0;
  $output = null;
  print "Executing: [$command]\n";
  exec($command, $output, $exit_code);
  if ($exit_code) {
    sys_die("Failed command: $command (code $exit_code)");
  }
}

function sys_executeAndReturnOutput($command) {
  $exit_code = 0;
  $output = null;
  exec($command, $output, $exit_code);
  if ($exit_code) {
    sys_die("Failed command: $command (code $exit_code)\n");
    var_dump($output);
    exit;
  }
  return $output;
}

?>
