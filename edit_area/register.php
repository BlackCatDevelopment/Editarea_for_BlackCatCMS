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

if (!function_exists('registerEditArea'))
{
	function registerEditArea(
                $id = 'code_area',
                $syntax = 'php',
                $syntax_selection = true,
                $allow_resize = 'both',
                $allow_toggle = true,
                $start_highlight = true,
                $min_width = 600,
                $min_height = 300,
                $toolbar = 'default'
				)
	{

		// set default toolbar if no user defined was specified
		if ($toolbar == 'default') {
			$toolbar = 'search, fullscreen, |, undo, redo, |, select_font, syntax_selection, |, highlight, reset_highlight, |, help';
			$toolbar = (!$syntax_selection) ? str_replace('syntax_selection,', '', $toolbar) : $toolbar;
		}

		// check if used Website Baker backend language is supported by EditArea
		$language = 'en';
		if (defined('LANGUAGE') && file_exists(dirname(__FILE__) . '/langs/' . strtolower(LANGUAGE) . '.js'))
        {
			$language = strtolower(LANGUAGE);
		}

		// check if highlight syntax is supported by edit_area
		$syntax = in_array($syntax, array('css', 'html', 'js', 'php', 'xml','csv')) ? $syntax : 'php';

		// check if resize option is supported by edit_area
		$allow_resize = in_array($allow_resize, array('no', 'both', 'x', 'y')) ? $allow_resize : 'no';

		/**
		 *	Try to load the basic js only one time.
		 */
		$return_value = "";
		if (!defined('EDIT_AREA_LOADED')) {
			define('EDIT_AREA_LOADED', true);
			$script_url = CAT_URL.'/modules/edit_area/edit_area/edit_area_full.js';
			$return_value .= "\n<script src='".$script_url."' type='text/javascript'></script>\n";
		}
		
		$return_value .= "
		<script type='text/javascript'>
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
		
		return $return_value;	
	}
}

if (!function_exists('getEditAreaSyntax')) {
	function getEditAreaSyntax($file) 
	{
		// returns the highlight scheme for edit_area
		$syntax = 'php';
		if (is_readable($file)) {
			// extract file extension
			$file_info = pathinfo($file);
		
			switch ($file_info['extension']) {
				case 'htm': case 'html': case 'htt':
					$syntax = 'html';
	  				break;

	 			case 'css':
					$syntax = 'css';
	  				break;

				case 'js':
					$syntax = 'js';
					break;

				case 'xml':
					$syntax = 'xml';
					break;

	 			case 'php': case 'php3': case 'php4': case 'php5':
					$syntax = 'php';
	  				break;

				default:
					$syntax = 'php';
					break;
			}
		}
		return $syntax ;
	}
}

?>