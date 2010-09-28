<?
/**
 * Copyright 2010 Cătălin Frâncu <cata@francu.com>
 *
 * This file is part of Civvic.
 *
 * Civvic is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Civvic is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Civvic.  If not, see <http://www.gnu.org/licenses/>.
 **/

define('WHITE', false);
define('BLACK', true);
define('COLUMN_NONE', 0);
define('COLUMN_LEFT', 1);
define('COLUMN_RIGHT', 2);
define('COLOR_THRESHOLD', 0x808080);     // Delimiter between black and white dots
define('PARAGRAPH_SPACE', 5);            // Vertical white space between distinct blocks
define('BLACK_ROW_THRESHOLD', 10);       // Minimum number of black pixels for a row to be considered black
define('BLOCK_DELIMITER', 20);           // Minimum number of white rows that separates two horizontal blocks
define('MEANINGFUL_BLOCK_HEIGHT', 20);   // If a black block is shorter, we just discard it
define('VERTICAL_LINE_THRESHOLD', 0.75); // If a block has this percentage of pixels on a column, it indicates a vertical delimiter bar
define('VERTICAL_LINE_THICKNESS', 3);    // A vertical line only qualifies as a column divider if it is at least this thick
define('VERTICAL_LINE_SPAN', 20);        // A vertical line only qualifies as a column divider if it spans at most this many pixels
define('VERTICAL_LINE_SHIFT', 0.02);     // A vertical line only qualifies as a column divider if it is this close to the center of the page
define('WHITE_DIVIDER_THRESHOLD', 0.02); // A white divider only qualifies as a column divider if it is at least this white
define('WHITE_DIVIDER_THICKNESS', 50);   // A white divider only qualifies as a column divider if it is at least this thick

class Block {
  public $x0, $y0, $width, $height, $color, $column;

  function __construct($x0, $y0, $width, $height, $color, $column) {
    $this->x0 = $x0;
    $this->y0 = $y0;
    $this->width = $width;
    $this->height = $height;
    $this->color = $color;
    $this->column = $column;
  }
}

$opts = getopt(null, array('file:', 'preview:', 'block-prefix:'));

if (!isset($opts['file'])) {
  usage();
}

$filename = $opts['file'];
$contents = file_get_contents($filename) or die("Cannot find the specified file\n");
$image = imagecreatefromstring($contents) or die("The file does not appear to contain an image\n");
list($width, $height) = getimagesize($filename); // It returns more data, but we only need the width and height
// colorHistogram($image, $width, $height);
$blackRows = computeBlackRows($image, $width, $height);
$rawBlocks = findRawBlocks($blackRows, $width);
$horizontalBlocks = compactBlocks($rawBlocks);
verifyConsistency($horizontalBlocks, $height);
$textBlocks = findTextBlocks($horizontalBlocks);
// At this point we have horizontal blocks. Some of these need to be split in two columns
$textBlocks = splitColumns($image, $textBlocks, $width);
$blocks = sortBlocks($textBlocks);
if (isset($opts['preview'])) {
  generatePreview($opts['preview'], $image, $blocks);
}
if ($opts['block-prefix']) {
  writeBlockFiles($filename, $opts['block-prefix'], $blocks);
}


function usage() {
  print "Required arguments:\n";
  print "--file           Image file to split (image type is autodetected)\n";
  print "\n";
  print "Optional arguments:\n";
  print "--preview        Generate a preview of the blocks\n";
  print "--block-prefix   Directory and prefix for the resulting blocks\n";
  exit(1);
}

function colorHistogram(&$image, $width, $height) {
  $map = array();
  for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
      $rgb = imagecolorat($image, $x, $y);
      $prev = array_key_exists($rgb, $map) ? $map[$rgb] : 0;
      $map[$rgb] = $prev + 1;
    }
  }
  ksort($map);

  print "Color histogram:\n";
  foreach ($map as $color => $numPixels) {
    print dechex($color) . ": $numPixels\n";
  }
  print "\n";
}

/**
 * Returns an array of $height elements. Each value is 1 if the row is black, i.e. if it contains
 * more than BLACK_ROW_THRESHOLD pixels under COLOR_THRESHOLD. Otherwise the value is 0.
 **/
function computeBlackRows(&$image, $width, $height) {
  $result = array();
  for ($y = 0; $y < $height; $y++) {
    $numBlack = 0;
    for ($x = 0; $x < $width; $x++) {
      $rgb = imagecolorat($image, $x, $y);
      if ($rgb < COLOR_THRESHOLD) {
	$numBlack++;
      }
    }
    $result[$y] = ($numBlack > BLACK_ROW_THRESHOLD) ? BLACK : WHITE;
  }
  // foreach ($result as $i => $black) {
  //   print "$i: " . (($black == BLACK) ? 'Text' : '') . "\n";
  // }
  return $result;
}

/**
 * From $blackRows (which holds BLACK for black rows and WHITE for white rows), compute contiguous blocks of 1's and 0's.
 * Returns is an array of Blocks;
 **/

function findRawBlocks(&$blackRows, $width)  {
  $result = array();
  $curStart = 0;
  $curColor = false;
  foreach ($blackRows as $i => $color) {
    if ($color != $curColor) {
      $result[] = new Block(0, $curStart, $width, $i - $curStart, $curColor, COLUMN_NONE);
      $curStart = $i;
      $curColor = $color;
    }
  }
  $result[] = new Block(0, $curStart, $width, count($blackRows) - $curStart, $curColor, COLUMN_NONE);
  return $result;
}

/**
 * Given a set of raw blocks, replace each group of (black, white, black) blocks by a black block
 * equal to the sum of the heights when the white block is shorter than BLOCK_DELIMITER.
 **/
function compactBlocks($rawBlocks) {
  $i = 1;
  while ($i < count($rawBlocks) - 1) {
    if ($rawBlocks[$i]->color == WHITE && $rawBlocks[$i]->height < BLOCK_DELIMITER) {
      // This is regular line spacing, not a block delimiter. Join the three adjacent blocks in order to remove this block.
      $rawBlocks[$i - 1]->height += $rawBlocks[$i]->height + $rawBlocks[$i + 1]->height;
      $rawBlocks = array_merge(array_slice($rawBlocks, 0, $i), array_slice($rawBlocks, $i + 2));
    } else {
      $i++;
    }
  }
  return $rawBlocks;
}

function verifyConsistency($blocks, $height) {
  for ($i = 1; $i < count($blocks); $i++) {
    assert($blocks[$i]->color == !$blocks[$i - 1]->color);
    assert($blocks[$i]->y0 == $blocks[$i - 1]->y0 + $blocks[$i - 1]->height);
  }
  $last = $blocks[count($blocks) - 1];
  assert($last->y0 + $last->height == $height);
}

/**
 * Given a (probably alternating) sequence of blocks, return an array of black blocks of meaningful height.
 **/
function findTextBlocks($blocks) {
  $result = array();
  foreach ($blocks as $b) {
    if ($b->color == BLACK && $b->height >= MEANINGFUL_BLOCK_HEIGHT) {
      $copy = $b;
      // Add almost BLOCK_DELIMITER / 2 on either side, since we know there has to be that much whitespace available.
      $copy->y0 -= BLOCK_DELIMITER / 2 - 1;
      $copy->height += BLOCK_DELIMITER - 2;
      $result[] = $copy;
    }
  }
  return $result;
}

/**
 * Given a list of text blocks, decides which of them need to be split vertically into two columns.
 * Returns a sorted list of the new, 1- and 2- column blocks.
 **/
function splitColumns(&$image, $blocks, $width) {
  $result = array();
  foreach ($blocks as $i => $b) {
    $weights = computeColumnWeights($image, $b);

    // Type 1: A vertical divider line around the middle of the page
    // First find the span and thickness of the black lines
    $numVerticalLines = 0;
    $minX = 0;
    $maxX = 0;
    foreach ($weights as $i => $weight) {
      if ($weight >= VERTICAL_LINE_THRESHOLD) {
	$numVerticalLines++;
	if (!$minX) {
	  $minX = $i;
	}
	$maxX = $i;
      }
    }

    // Then see if the span, thickness and shift ot the lines are acceptable
    if ($numVerticalLines >= VERTICAL_LINE_THICKNESS &&
	$maxX - $minX <= VERTICAL_LINE_SPAN &&
	($b->x0 + $minX) / $width >= 0.50 - VERTICAL_LINE_SHIFT &&
	($b->x0 + $maxX) / $width <= 0.50 + VERTICAL_LINE_SHIFT) {
      // Trim a few extra pixels on each side
      $result[] = new Block($b->x0, $b->y0, $minX - 3, $b->height, $b->color, COLUMN_LEFT);
      $result[] = new Block($b->x0 + $maxX + 3, $b->y0, $b->width - $maxX - 3, $b->height, $b->color, COLUMN_RIGHT);
      continue;
    }

    // Type 2: A vertical white space around the middle of the page
    // First find the white space around the center
    $minX = $width >> 1 - $b->x0;
    $maxX = $minX;
    while ($minX && $weights[$minX] < WHITE_DIVIDER_THRESHOLD) {
      $minX--;
    }
    while ($maxX < $b->width && $weights[$maxX] < WHITE_DIVIDER_THRESHOLD) {
      $maxX++;
    }
    $whiteSpace = $maxX - $minX + 1;
    // We want there to be white space, but we don't want it to extend all the way to one end
    // (that would mean that one of the columns is empty and we don't split those).
    if ($whiteSpace >= WHITE_DIVIDER_THICKNESS && $minX > 0 && $maxX < $b->width - 1) {
      // Split the columns evenly
      $result[] = new Block($b->x0, $b->y0, $b->width >> 1, $b->height, $b->color, COLUMN_LEFT);
      $result[] = new Block($b->x0 + $b->width >> 1, $b->y0, $b->width >> 1, $b->height, $b->color, COLUMN_RIGHT);
      continue;
    }

    $result[] = $b;
  }
  return $result;
}

/**
 * Returns an array with the percentage of black pixels on each column.
 */
function computeColumnWeights(&$image, $block) {
  $result = array(); // Number of black pixels on each column
  for ($x = 0; $x < $block->width; $x++) {
    $count = 0;
    for ($y = 0; $y < $block->height; $y++) {
      $rgb = imagecolorat($image, $block->x0 + $x, $block->y0 + $y);
      if ($rgb < COLOR_THRESHOLD) {
	$count++;
      }
    }
    $result[] = $count / $block->height;
  }
  return $result;
}

/**
 * Reorders left and right columns, so NLRLRLRLRN becomes NLLLLRRRRN (where L = left, R = right, N = no column layout).
 **/
function sortBlocks($blocks) {
  // Push a sentry
  $blocks[] = new Block(0, 0, 0, 0, 0, COLUMN_NONE);
  $result = array();
  $left = array();
  $right = array();
  foreach ($blocks as $b) {
    switch ($b->column) {
    case COLUMN_LEFT:
      $left[] = $b;
      break;
    case COLUMN_RIGHT:
      $right[] = $b;
      break;
    case COLUMN_NONE:
      array_splice($result, count($result), 0, $left);
      array_splice($result, count($result), 0, $right);
      $left = array();
      $right = array();
      $result[] = $b;
      break;
    }
  }
  // Remove the sentry
  array_splice($result, count($result) - 1, 1);
  return $result;
}

function generatePreview($filename, &$image, $blocks) {
  foreach($blocks as $i => $b) {
    printf("Block %2d: x0:%4d y0:%4d width:%4d height:%4d column:%d\n", $i, $b->x0, $b->y0, $b->width, $b->height, $b->column);
    for ($x = 0; $x < $b->width; $x++) {
      imagesetpixel($image, $b->x0 + $x, $b->y0, 0x00ff00);
      imagesetpixel($image, $b->x0 + $x, $b->y0 + $b->height - 1, 0xff0000);
    }
    for ($y = 0; $y < $b->height; $y++) {
      imagesetpixel($image, $b->x0, $b->y0 + $y, 0x00ff00);
      imagesetpixel($image, $b->x0 + $b->width - 1, $b->y0 + $y, 0xff0000);
    }
  }
  imagepng($image, $filename) or die("Cannot write file $filename");
}

function writeBlockFiles($filename, $prefix, $blocks) {
  foreach ($blocks as $i => $b) {
    $blockFilename = sprintf("%s%02d_%d_%d_%d_%d_%d.png", $prefix, $i, $b->x0, $b->y0, $b->width, $b->height, $b->column);
    $command = "convert -crop {$b->width}x{$b->height}+{$b->x0}+{$b->y0} {$filename} {$blockFilename}";
    executeAndAssert($command);
  }
}

function executeAndAssert($command) {
  print "Executing: $command\n";
  $output = array();
  $returnCode = 0;
  exec($command, $output, $returnCode);
  if ($returnCode) {
    print "Command exited unsuccessfully. Output follows:\n";
    print_r($output);
    exit(1);
  }
}

?>
