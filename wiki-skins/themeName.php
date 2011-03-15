<?php
/**
 * Installation instructions:
 * 1. Replace themeName with a theme name of your choice
 * 2. Check out the directory: svn checkout http://voronet.francu.com/repos/civvic/wiki-skins/themeName <theme name>
 * 3. Rename the classes below to Skin<ThemeName> and <ThemeName>Template
 */
define('CIVVIC_SKIN_NAME', 'themeName');
require_once(CIVVIC_SKIN_NAME . '/civvic.php');

class SkinBlana extends SkinCivvicBase {
}

class BlanaTemplate extends CivvicTemplateBase {
}

?>
