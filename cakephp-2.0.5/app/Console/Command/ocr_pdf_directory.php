<?php

App::import('Lib', 'Config');
App::import('Lib', 'PdfTools');
App::import('Lib', 'StringManipulation');
App::import('Lib', 'Sys');

class ocrPdfDirectoryShell extends Shell {
  var $uses = array('PdfDocument', 'RawText', 'Word');

  function main() {
    if (count($this->args) != 1) {
      $this->help();
      exit;
    }
    $this->__recursiveScan($this->args[0]);
  }

  function help() {
    print "Scan an entire directory and import all the PDF files found inside.\n";
    print "Required arguments:\n";
    print "  --dir         Directory containing PDF files\n";
  }

  function __recursiveScan($path) {
    $files = scandir($path);                                                      

    foreach ($files as $file) {
      if ($file == '.' || $file == '..') {
        continue;
      }
      $full = "$path/$file";

      if (is_dir($full)) {
        $this->__recursiveScan($full);
      }

      if (string_endsWith($full, '.pdf') || string_endsWith($full, '.PDF')) {
        $md5Sum = md5_file($full);
        $pdfDocument = $this->PdfDocument->findByMd5Sum($md5Sum);
        if ($pdfDocument) {
          $rawText = $this->RawText->findById($pdfDocument['PdfDocument']['raw_text_id']);
          print "$full already parsed as {$rawText['RawText']['script_type']}, {$rawText['RawText']['issue']}/{$rawText['RawText']['year']}\n";
        } else {
          print "Processing $full, " . pdf_getPageCount($full) . " pages.\n";
          $arr = pdf_getYearAndIssueFromFileName($full);
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
          } else {
            print "Cannot handle digital documents yet, skipping\n";
          }
        }
      }
    }
  }
}

?>
