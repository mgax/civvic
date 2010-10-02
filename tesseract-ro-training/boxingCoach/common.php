<?

mb_internal_encoding("UTF-8");
ini_set('memory_limit', '256000000');

class Box {
  public $text, $x1, $y1, $x2, $y2, $page;

  /**
   * Tesseract represents the Y coord starting from the bottom-left. HTML favors the top-left origin, so we convert
   * the Y-coord here, taking the height of the image into account.
   **/
  function __construct($text = null, $x1 = 0, $y1 = 0, $x2 = 0 , $y2 = 0, $page = 0, $height = 0) {
    $this->text = $text;
    $this->x1 = $x1;
    $this->y1 = $height - 1 - $y2;
    $this->x2 = $x2;
    $this->y2 = $height - 1 - $y1;
    $this->page = $page;
  }

  function getWidth() {
    return $this->x2 - $this->x1 + 1;
  }

  function getHeight() {
    return $this->y2 - $this->y1 + 1;
  }

  function getTesseractNotation($height) {
    return sprintf("%s %d %d %d %d %d", $this->text, $this->x1, $height - 1 - $this->y2, $this->x2, $height - 1 - $this->y1, $this->page);
  }

  function __toString() {
    return "Box[{$this->text}]({$this->x1},{$this->y1})-({$this->x2},{$this->y2})p{$this->page}";
  }
}

class Image {
  public $filename, $width, $height;

  function __construct($filename) {
    $this->filename = $filename;
    list($width, $height) = getimagesize($filename);
    $this->width = $width;
    $this->height = $height;
  }

  function __toString() {
    return "Image[{$this->filename}({$this->width}x{$this->height})";
  }
}

/**
 * Returns a list of boxes, mapped by page number
 */
function loadBoxMapFromFile($boxFile, $heights) {
  $result = array();
  $lines = file($boxFile, FILE_IGNORE_NEW_LINES);
  foreach ($lines as $line) {
    $parts = preg_split('/\s+/', $line);
    assert(count($parts) == 6);
    $box = new Box($parts[0], $parts[1], $parts[2], $parts[3], $parts[4], $parts[5], $heights[$parts[5]]);
    if (!isset($result[$box->page])) {
      $result[$box->page] = array();
    }
    $result[$box->page][] = $box;
  }
  return $result;
}

function loadBoxMapFromMemcache() {
  $memcache = memcache_connect('localhost', 11211);
  return memcache_get($memcache, 'mo_boxMap');
}

function saveBoxMapToFile($boxFile, $boxMap, $heights) {
  $f = fopen($boxFile, 'w');
  if (!$f) {
    setFlashMessage("Could not open $boxFile for writing.");
    return;
  }
  foreach ($boxMap as $pageId => $boxes) {
    foreach ($boxes as $box) {
      fprintf($f, "%s\n", $box->getTesseractNotation($heights[$pageId]));
    }
  }
  fclose($f);
}

function saveBoxMapToMemcache($boxMap) {
  $memcache = memcache_connect('localhost', 11211);
  memcache_set($memcache, 'mo_boxMap', $boxMap);
}

/**
 * Returns a list of Images with the PNG file names and dimensions (there can be more than one output file for multiple page TIF files).
 */
function convertTifToPng($tifFile, $outputDir, $pngFile, $pngRegexp) {
  // Delete existing files
  $files = scandir($outputDir);
  foreach ($files as $file) {
    if (preg_match($pngRegexp, $file)) {
      unlink("{$outputDir}/{$file}");
    }
  }

  // Actual conversion
  executeAndAssert("convert {$tifFile} {$outputDir}/{$pngFile}");

  // Return resulting files
  $result = array();
  $files = scandir($outputDir);
  foreach ($files as $file) {
    if (preg_match($pngRegexp, $file)) {
      $result[] = new Image("$outputDir/$file");
    }
  }
  return $result;
}

function drawBoxes($inputPng, &$boxes, $outputPng) {
  print "Drawing " . count($boxes) . " on $inputPng to $outputPng<br/>";
  $image = imagecreatefrompng($inputPng) or die("Cannot open " . $pngFilename);
  foreach ($boxes as $box) {
    imagerectangle($image, $box->x1 - 1, $height - $box->y1 + 1, $box->x2 + 1, $height - $box->y2 - 1, 0xff0000);
  }
  imagepng($image, $outputPng);
}

function getPageHeights($tifFile) {
  $output = executeAndAssert("identify -format '%h ' $tifFile");
  return preg_split("/\s+/", trim($output[0]));
}

function getSurroundingBox($boxes) {
  $result = clone reset($boxes);
  foreach ($boxes as $b) {
    $result->x1 = min($result->x1, $b->x1);
    $result->y1 = min($result->y1, $b->y1);
    $result->x2 = max($result->x2, $b->x2);
    $result->y2 = max($result->y2, $b->y2);
  }
  return $result;
}

/**
 * Takes a PNG containing the symbol to be split and finds the X split point.
 * This algorithm is particular to the text I am working with.
 * The failsafe is to return 1/2 of the image width, if the heuristics don't work.
 */
function findSplitPoint($filename) {
  $contents = file_get_contents($filename) or die("Cannot open file $filename\n");
  $image = imagecreatefromstring($contents) or die("The file does not appear to contain an image\n");
  list($width, $height) = getimagesize($filename);

  $pixels = array(); // pixel counts on each column

  for ($x = 0; $x < $width; $x++) {
    $pixels[$x] = 0;
    for ($y = 0; $y < $height; $y++) {
      $rgb = imagecolorat($image, $x, $y);
      if (!$rgb) {
        $pixels[$x]++;
      }
    }
  }
  
  // Look for a letter (3 columns with 10 or more pixels on the average)
  $i = 1;
  while (($i < $width - 1) && ($pixels[$i - 1] + $pixels[$i] + $pixels[$i + 1] <= 30)) {
    $i++;
  }
  if ($i >= $width - 1) {
    return intval($width / 2);
  }

  // Now look for a gap (3 columns with 4 or less pixels on the average
  while (($i < $width - 1) && ($pixels[$i - 1] + $pixels[$i] + $pixels[$i + 1] > 12)) {
    $i++;
  }  
  if ($i >= $width - 1) {
    return intval($width / 2);
  }

  // Now look for the start of the next letter
  $j = $i;
  while (($j < $width - 1) && ($pixels[$j - 1] + $pixels[$j] + $pixels[$j + 1] <= 30)) {
    $j++;
  }  
  if ($j >= $width - 1) {
    return intval($width / 2);
  }

  return intval(($i + $j) / 2);
}

function getRequestParameter($name, $default) {
  return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

function setFlashMessage($message) {
  $oldMessage = isset($_SESSION['flashMessage']) ? $_SESSION['flashMessage'] : '';
  $_SESSION['flashMessage'] = "{$oldMessage}{$message}<br/>";
}

function executeAndAssert($command) {
  // print "Executing: $command\n";
  $output = array();
  $returnCode = 0;
  exec($command, $output, $returnCode);
  if ($returnCode) {
    print "Command exited unsuccessfully. Output follows:\n";
    print_r($output);
    exit(1);
  }
  return $output;
}

?>
