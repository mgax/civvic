<?

require_once('common.php');
require_once('smarty/Smarty.class.php');

$TIF_FILE = 'combined.tif';
$BOX_FILE = 'combined.box';
$OUTPUT_DIR = 'man';
$PNG_FILE = 'img.png';
$PNG_REGEXP = '/^img(-[0-9]+)?.png$/';
$BOX_PNG_PREFIX = 'box-';
$PNG_PAD = 10; // Padding when splitting a png file into boxes

$pngFiles = convertTifToPng($TIF_FILE, $OUTPUT_DIR, $PNG_FILE, $PNG_REGEXP);
$boxMap = readBoxFile($BOX_FILE, $pngFiles);

foreach ($pngFiles as $page => $pngFile) {
  $numBoxes = count($boxMap[$page]);
  print "Splitting {$pngFile} into $numBoxes boxes.\n";
  foreach ($boxMap[$page] as $i => $b) {
    $boxPngName = sprintf("%s/%03d_%05d.png", $OUTPUT_DIR, $page, $i);
    executeAndAssert(sprintf("convert -crop %dx%d+%d+%d -fill none -stroke '#ff0000' -draw 'rectangle %d,%d %d,%d' %s %s",
			     $b->getWidth() + 2 * $PNG_PAD, $b->getHeight() + 2 * $PNG_PAD, $b->x1 - $PNG_PAD, $b->y1 - $PNG_PAD,
			     $PNG_PAD - 1, $PNG_PAD - 1, $b->getWidth() + $PNG_PAD, $b->getHeight() + $PNG_PAD,
			     $pngFile->filename, $boxPngName));
    if ($i % 100 == 0) {
      print "$i/$numBoxes\n";
    }
  }
}



/**************************************************************************/


?>
