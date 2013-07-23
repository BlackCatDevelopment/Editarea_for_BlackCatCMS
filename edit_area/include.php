<?php

/**
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 3 of the License, or (at
 *   your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful, but
 *   WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *   General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 *   @author          Website Baker Project, LEPTON Project, Black Cat Development
 *   @copyright       2004-2010, Website Baker Project
 *   @copyright       2011-2012, LEPTON Project
 *   @copyright       2013, Black Cat Development
 *   @link            http://blackcat-cms.org
 *   @license         http://www.gnu.org/licenses/gpl.html
 *   @category        CAT_Core
 *   @package         edit_area
 *
 */

if (defined('CAT_PATH')) {
    if (defined('CAT_VERSION')) include(CAT_PATH.'/framework/class.secure.php');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
    include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php');
} else {
    $subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));    $dir = $_SERVER['DOCUMENT_ROOT'];
    $inc = false;
    foreach ($subs as $sub) {
        if (empty($sub)) continue; $dir .= '/'.$sub;
        if (file_exists($dir.'/framework/class.secure.php')) {
            include($dir.'/framework/class.secure.php'); $inc = true;    break;
        }
    }
    if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}

function show_wysiwyg_editor($name, $id, $content, $width = '100%', $height = '350px', $print = true) {
	global $section_id, $page_id, $database, $preview;
	
	$syntax = 'php';
	$syntax_selection = true;
	$allow_resize = 'both';
	$allow_toggle = true;
	$start_highlight = true;
	$min_width = 600;
	$min_height = 300;
	$toolbar = 'default';

	// set default toolbar if no user defined was specified
	if ($toolbar == 'default') {
		$toolbar = 'search, fullscreen, |, undo, redo, |, select_font, syntax_selection, |, highlight, reset_highlight, |, help';
		$toolbar = (!$syntax_selection) ? str_replace('syntax_selection,', '', $toolbar) : $toolbar;
	}

	// check if used Website Baker backend language is supported by EditArea
	$language = 'en';
	if (defined('LANGUAGE') && file_exists(dirname(__FILE__) . '/langs/' . strtolower(LANGUAGE) . '.js')) {
		$language = strtolower(LANGUAGE);
	}

	// check if highlight syntax is supported by edit_area
	$syntax = in_array($syntax, array('css', 'html', 'js', 'php', 'xml','csv')) ? $syntax : 'php';

	// check if resize option is supported by edit_area
	$allow_resize = in_array($allow_resize, array('no', 'both', 'x', 'y')) ? $allow_resize : 'no';
	
	if (!isset($_SESSION['edit_area'])) {
		$script = CAT_URL.'/modules/edit_area/edit_area/edit_area_full.js';
		$register = "\n<script src=\"".$script."\" type=\"text/javascript\"></script>\n";

		if (!isset($preview)) {
			$last = $database->get_one("SELECT section_id from ".CAT_TABLE_PREFIX."sections where page_id='".$page_id."' order by position desc limit 1"); 
			$_SESSION['edit_area'] = $last;
		}

	} else {
		$register = "";
		if ($section_id == $_SESSION['edit_area']) unset($_SESSION['edit_area']);
	}
	
	// the Javascript code
	$register .= "
	<script type=\"text/javascript\">
		editAreaLoader.init({
			id: '".$id."',
			start_highlight: ".$start_highlight.",
			syntax: '".$syntax."',
			min_width: ".$min_width.",
			min_height: ".$min_height.",
			allow_resize: '".$allow_resize."',
			allow_toggle: ".$allow_toggle.",
			toolbar: '".$toolbar."',
			language: '".$language."'
		});
	</script>
	";
	
	$editor = sprintf("%s\n".'<textarea cols="80" rows="20"  id="%s" name="%s" style="width: %s; height: %s;">%s</textarea>', $register, $id, $name, $width, $height, $content);

    if($print) echo $editor;
    return $editor;
} // show_wysiwyg_editor()

?>