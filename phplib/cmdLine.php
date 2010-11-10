<?php

/**
 * Almost like getopt(), but additionally it checks that all the arguments in $required have been passed.
 * If any required arguments are missing, returns false.
 **/
function cl_getArguments($longOpts, $required) {
  $result = getopt(null, $longOpts);
  foreach ($required as $arg) {
    if (!array_key_exists($arg, $result)) {
      return false;
    }
  }
  return $result;
}

?>
