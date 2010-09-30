<?

require_once('common.php');
require_once('smarty/Smarty.class.php');
$TIF_FILE = 'combined.tif';
$BOX_FILE = 'combined.box';
$BOX_PNG_NAME = 'man/box.png';
$CONTEXT = 10; // Number of boxes on either side to show (at most)
$PNG_PAD = 10;  // Padding around the entire PNG image
$BOX_PAD = 2;  // Padding around each box

$pageId = getRequestParameter('pageId', null);
$boxId = getRequestParameter('boxId', null);
$text = getRequestParameter('text', null);
$submitButton = getRequestParameter('submitButton', null);
$prevButton = getRequestParameter('prevButton', null);
$mergeButton = getRequestParameter('mergeButton', null);
$growTop = getRequestParameter('growTop', null);
$shrinkTop = getRequestParameter('shrinkTop', null);
$growRight = getRequestParameter('growRight', null);
$shrinkRight = getRequestParameter('shrinkRight', null);
$growBottom = getRequestParameter('growBottom', null);
$shrinkBottom = getRequestParameter('shrinkBottom', null);
$growLeft = getRequestParameter('growLeft', null);
$shrinkLeft = getRequestParameter('shrinkLeft', null);
$pixels = getRequestParameter('pixels', null);
$skipButton = getRequestParameter('skipButton', null);

$heights = getPageHeights($TIF_FILE);
$boxMap = readBoxFile($BOX_FILE, $heights);

if ($prevButton) {
  $boxId = max(0, $boxId - 1);
} else if ($growTop || $shrinkTop || $growRight || $shrinkRight || $growBottom || $shrinkBottom || $growLeft || $shrinkLeft) {
  // No width/height boundary checks. Let's pray for no abuse...
  $box = $boxMap[$pageId][$boxId];
  $box->x1 += $growLeft ? -$pixels : ($shrinkLeft ? +$pixels : 0);
  $box->y1 += $growTop ? -$pixels : ($shrinkTop ? +$pixels : 0);
  $box->x2 += $growRight ? +$pixels : ($shrinkRight ? -$pixels : 0);
  $box->y2 += $growBottom ? +$pixels : ($shrinkBottom ? -$pixels : 0);
  $boxMap[$pageId][$boxId] = $box;
  saveBoxMap($BOX_FILE, $boxMap, $heights);
} else if ($skipButton) {
  if (!isset($boxMap[$pageId]) || !isset($boxMap[$pageId][$boxId])) {
    setFlashMessage("Page {$pageId}, symbol {$boxId} doesn't exist. Skipping to the beginning.");
    $pageId = 0;
    $boxId = 0;
  }
} else if ($submitButton) {
  $boxMap[$pageId][$boxId]->text;
  if ($text != $boxMap[$pageId][$boxId]->text) {
    $boxMap[$pageId][$boxId]->text = $text;
    saveBoxMap($BOX_FILE, $boxMap, $heights);
  }
  $boxId++;
  if ($boxId >= count($boxMap[$pageId])) {
    $boxId = 0;
    $pageId++;
  }
} else if ($mergeButton) {
  $crop = getSurroundingBox(array_slice($boxMap[$pageId], $boxId, 2));
  $crop->text .= $boxMap[$pageId][$boxId + 1]->text;
  array_splice($boxMap[$pageId], $boxId, 2, array($crop));
  saveBoxMap($BOX_FILE, $boxMap, $heights);
  //  array_splice
} else {
  $pageId = 0;
  $boxId = 0;
}

$boxes = $boxMap[$pageId];

// Extend $CONTEXT boxes back and forth, but do not skip rows
$firstBoxId = $boxId;
while ($firstBoxId && ($boxId - $firstBoxId) < $CONTEXT && ($boxes[$firstBoxId - 1]->x1 <= $boxes[$boxId]->x1)) {
  $firstBoxId--;
}
$lastBoxId = $boxId;
while (($lastBoxId < count($boxes) -1) && ($lastBoxId - $boxId) < $CONTEXT && ($boxes[$lastBoxId + 1]->x1 >= $boxes[$boxId]->x1)) {
  $lastBoxId++;
}

$visibleBoxes = array_slice($boxes, $firstBoxId, $lastBoxId - $firstBoxId + 1, true); // preserve the keys
$activeBox = $boxes[$boxId];
$crop = getSurroundingBox($visibleBoxes);

// Generate the temporary image
$offX = $crop->x1 - $PNG_PAD;
$offY = $crop->y1 - $PNG_PAD;
$cmd = sprintf("convert -crop %dx%d+%d+%d -fill none -stroke '#aaaaaa' ", $crop->getWidth() + 2 * $PNG_PAD, $crop->getHeight() + 2 * $PNG_PAD, $offX, $offY);
// Gray rectangles for all the boxes
foreach ($visibleBoxes as $id => $b) {
  if ($id != $boxId) {
    $cmd .= sprintf("-draw 'rectangle %d,%d %d,%d' ", $b->x1 - $offX - $BOX_PAD, $b->y1 - $offY - $BOX_PAD, $b->x2 - $offX + $BOX_PAD, $b->y2 - $offY + $BOX_PAD);
  }
}
// Red rectangle for the active box
$cmd .= sprintf("-stroke '#ff0000' -strokewidth 2 -draw 'rectangle %d,%d %d,%d' ",
		 $activeBox->x1 - $offX - $BOX_PAD, $activeBox->y1 - $offY - $BOX_PAD, $activeBox->x2 - $offX + $BOX_PAD, $activeBox->y2 - $offY + $BOX_PAD);
$cmd .= "{$TIF_FILE}[{$pageId}] {$BOX_PNG_NAME}";
executeAndAssert($cmd);

$smarty = new Smarty();
$smarty->template_dir = '.';
$smarty->compile_dir = '/tmp';
$smarty->assign('image', $BOX_PNG_NAME);
$smarty->assign('box', $boxes[$boxId]);
$smarty->assign('pageId', $pageId);
$smarty->assign('boxId', $boxId);
$smarty->assign('numBoxes', count($boxes));
$smarty->display('boxingCoach.tpl');

/**************************************************************************/


?>
