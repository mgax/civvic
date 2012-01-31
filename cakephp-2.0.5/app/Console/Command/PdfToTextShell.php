<?php

App::import('Lib', 'PdfTools');
App::import('Lib', 'StringManipulation');
App::import('Lib', 'Sys');

class PdfToTextShell extends Shell {
  var $uses = array('PdfDocument');

  function main() {
    if (count($this->args) != 1) {
      $this->help();
      exit;
    }
    print pdf_pdfToText($this->args[0]);
  }

  function help() {
    print "Process a single PDF file and print the extracted text to screen.\n";
    print "Required arguments:\n";
    print "  (1)              PDF filename\n";
  }
}

?>
