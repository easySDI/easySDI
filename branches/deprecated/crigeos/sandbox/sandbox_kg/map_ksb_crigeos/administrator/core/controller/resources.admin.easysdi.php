<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');

class ADMIN_resources {

	function editResource( $option ) {
		$file = $_GET['filename'];
		clearstatcache();
		if ($fp = fopen( $file, "r" )) {
			$content = fread( $fp, filesize($file));

			HTML_resources::editResource( $file, $content, $option );
		} else {
			$mainframe->enqueueMessage(JText::_(MAP_RESOURCE_FILENOTOPEN),"error");
			$mainframe->redirect("index.php?option=$option" );
		}
	}
	
	function saveResource( $option ) {
		global $mainframe;
		$file = $_POST['filename'];
		$content = stripcslashes($_POST['filecontent']);
		$enable_write = $_POST['enable_write'];
		$oldperms = fileperms($file);
		if ($enable_write) @chmod($file, $oldperms | 0222);

		clearstatcache();
		if (!is_writable( $file )) {
			$mainframe->enqueueMessage(JText::_(MAP_RESOURCE_FILENOTWRITABLE),"error");
			$mainframe->redirect("index.php?option=$option" );
		}
		
		if ($fp = fopen( $file, "w" )) {
			fwrite( $fp, $content);
			
			if ($enable_write) {
				//@chmod($file, $oldperms);
			} else {
				if ($_POST['disable_write'])
					@chmod($file, $oldperms & 0777555);
			} // if
			$mainframe->redirect("index.php?option=$option" );
		} else {
			if ($enable_write) @chmod($file, $oldperms);
			$mainframe->enqueueMessage(JText::_(MAP_RESOURCE_FILENOTSAVE),"error");
			$mainframe->redirect("index.php?option=$option" );
		}
	}
}
?>
