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
    print "Process a PDF directory and print the extracted text to screen.\n";
    print "Required arguments:\n";
    print "  (1)              PDF filename\n";
  }

  function __recursiveScan($path) {
    $type = pdf_getDocumentType($full);
    if ($type == PDF_TYPE_ANALOG) {
      $text = pdf_ocr($full);
      $text = string_fixOcr($text);
      $rawText = new RawText();
      $rawText->set(array('year' => $arr['year'],
                          'issue' => $arr['issue'],
                          'extracted_text' => $text,
                          'script_type' => 'analog',
                          'script_version' => PDF_ANALOG_SCRIPT_VERSION,
                          'progress' => 0, // PROGRESS_NEW
                          ));
      $rawText->save();
      
      $pdfDocument = new PdfDocument();
      $pdfDocument->set(array('raw_text_id' => $rawText->id,
                              'contents' => file_get_contents($full),
                              'md5_sum' => $md5Sum,
                              'page_count' => pdf_getPageCount($full),
                              ));
      $pdfDocument->save();
      
      print "$full saved as analog {$arr['issue']}/{$arr['year']} md5=$md5Sum\n";
    }
  }
}

?>
