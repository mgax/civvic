<?php

require_once __DIR__ . '/../lib/Util.php';

$archivePath = Config::get('moArchiveSync.moArchivePath');
if (!$archivePath) {
  die ("Path to the local MO archive is undefined\n");
}

$dryRun = false;
foreach (array_slice($argv, 1) as $arg) {
  if ($arg == '--dry-run') {
    $dryRun = true;
  } else {
    die("Unknown argument [$arg]\n");
  }
}

foreach (Config::get('moArchiveSync.moSource') as $sourceName) {
  for ($year = 1989; $year <= date('Y'); $year++) {
    scan($sourceName, $year, $archivePath);
  }
}

/*************************************************************************/

function scan($sourceName, $year, $localDir) {
  global $dryRun;

  $yearListing = Config::get("moArchiveSync.{$sourceName}YearListing");
  $pdfLink = Config::get("moArchiveSync.{$sourceName}PdfLink");
  $replacePatternPairs = Config::get("moArchiveSync.{$sourceName}ReplacePattern");
  $ignoreLinks = Config::get("moArchiveSync.{$sourceName}IgnoreLink");

  $replacePatterns = array();
  foreach ($replacePatternPairs as $pair) {
    $replacePatterns[] = explode("::", $pair);
  }

  $url = sprintf($yearListing, $year);
  print "************** Syncing with archive [$url]\n";

  // Fetch the URL, parse it and process each anchor
  $dom = new DOMDocument();
  if (!@$dom->loadHTMLFile($url)) {
    print("WARNING: Cannot retrieve index $url\n");
  }
  $anchors = $dom->getElementsByTagName('a');
  foreach ($anchors as $a) {
    $href = $a->attributes->getNamedItem('href')->textContent;
    $matches = array();
    if (preg_match($pdfLink, $href, $matches)) {
      $issue = $matches['issue'];

      // Apply the replacement patterns
      foreach ($replacePatterns as $rp) {
        $issue = preg_replace($rp[0], $rp[1], $issue);
      }
      $issue = mb_strtolower($issue);

      // Zero-pad numeric prefix to exactly four digits
      $len = strlen($issue);
      for ($i = 0; $i < $len && ctype_digit($issue[$i]); $i++);
      $issue = str_repeat('0', 4 - $i) . $issue;
      // print("Match: $href issue $issue\n");

      // Create the directory
      $fullDir = "{$localDir}{$year}";
      if (!@mkdir($fullDir, 0755, true) && !file_exists($fullDir)) {
        die("Cannot create directory [$fullDir]\n");
      }

      $fullPath = "{$fullDir}/{$issue}.pdf";
      if (file_exists($fullPath)) {
        // print "$fullPath already exists, skipping\n";
      } else {
        // Fetch the PDF document and save it
        $pdfUrl = "{$url}{$href}";
        // print("Will fetch [$pdfUrl]\n");
        $contents = $dryRun ? true : @file_get_contents($pdfUrl);
        if ($contents) {
          if (!$dryRun && !file_put_contents($fullPath, $contents)) {
            die("Cannot save file $fullPath\n");
          }
          print "Saved $pdfUrl as $fullPath\n";
        } else {
          print("WARNING: Cannot retrieve document $pdfUrl\n");
        }
      }
    } else {
      $ignored = false;
      foreach ($ignoreLinks as $il) {
        if (preg_match($il, $href)) {
          $ignored = true;
        }
      }
      if (!$ignored) {
        print("No match: $href\n");
      }
    }
  }
}

?>
