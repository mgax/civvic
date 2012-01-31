<?php

App::import('Lib', 'PdfTools');
App::import('Lib', 'StringManipulation');
App::import('Lib', 'Sys');

class FetchMoArchiveShell extends Shell {
  static $depth = array();

  function main() {
    if (count($this->args) != 2) {
      $this->help();
      exit;
    }
    $this->__recursiveScan($this->args[0], $this->args[1]);
  }

  function help() {
    print "Retrieve a web archive of MO PDF files and store them locally. Avoids refetching existing documents.";
    print "Required arguments:\n";
    print "  (1)         Base URL for the archive\n";
    print "  (2)         Local directory containing archive\n";
  }
  
  function __recursiveScan($url, $localDir) {
    if (string_endsWith($url, '.pdf') || string_endsWith($url, '.PDF')) {
      $parts = pdf_getYearAndIssueFromFileName($url);
      if (!preg_match('/^[0-9]+(bis)?$/', $parts['issue'])) {
        die("Cannot extract issue from URL [$url]\n");
      }

      // Zero-pad issue to exactly four digits
      $bis = string_endsWith($parts['issue'], 'bis');
      while (($bis && strlen($parts['issue']) < 7) || (!$bis && strlen($parts['issue']) < 4)) {
        $parts['issue'] = '0' . $parts['issue'];
      }

      // Create the directory
      $fullDir = "{$localDir}/{$parts['year']}";
      if (!@mkdir($fullDir, 0755, true) && !file_exists($fullDir)) {
        die("Cannot create directory $fullDir\n");
      }

      $fullPath = "{$fullDir}/{$parts['issue']}.pdf";
      if (file_exists($fullPath)) {
        print "$fullPath already exists, skipping\n";
      } else {
        // Fetch the PDF document and save it
        $contents = @file_get_contents($url);
        if ($contents) {
          if (!file_put_contents($fullPath, $contents)) {
            die("Cannot save file $fullPath\n");
          }
          print "Saved $url as $fullPath\n";
        } else {
          print("WARNING: Cannot retrieve document $url\n");
        }
      }
      return;
    }

    if (!string_endsWith($url, '/')) {
      $url .= '/';
    }

    // Fetch the URL, parse it and recurse on each relative anchor
    $dom = new DOMDocument();
    $dom->loadHTMLFile($url);
    $anchors = $dom->getElementsByTagName('a');
    foreach ($anchors as $a) {
      $href = $a->attributes->getNamedItem('href')->textContent;
      if (!string_startsWith($href, '..') && !string_startsWith($href, '/')) {
        $this->__recursiveScan($url . $href, $localDir);
      }
    }
  }
}

?>
