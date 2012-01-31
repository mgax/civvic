<?php

App::import('Lib', 'PdfTools');
App::import('Lib', 'StringManipulation');
App::import('Lib', 'Sys');

class PageCountShell extends Shell {

  function main() {
    if (count($this->args) != 1) {
      $this->help();
      exit;
    }
    $total = $this->__recursiveScan($this->args[0]);
    print "Grand total: {$total} pages.\n";
  }

  function help() {
    print "Scan an entire directory and count all the pages in all the PDF files.\n";
    print "Required arguments:\n";
    print "  --dir         Directory containing PDF files\n";
  }

  function __recursiveScan($path) {
    $result = 0;
    $files = scandir($path);                                                      

    foreach ($files as $file) {
      if ($file == '.' || $file == '..') {
        continue;
      }
      $full = "$path/$file";

      if (is_dir($full)) {
        $result += $this->__recursiveScan($full);
      }

      if (string_endsWith($full, '.pdf') || string_endsWith($full, '.PDF')) {
        $pc = pdf_getPageCount($full);
        $result += $pc;
        print "$full: {$pc} pages.\n";
      }
    }
    return $result;
  }
}

?>
