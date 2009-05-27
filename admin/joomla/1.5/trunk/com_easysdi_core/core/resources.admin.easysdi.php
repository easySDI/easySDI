<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

	
	function listResources($option) {
		global $mainframe;
		/*global $languages;
		global $mosConfig_lang, $mosConfig_absolute_path, $mosConfig_list_limit;
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$rows = array();
		
		// get current languages
		//$cur_language = $mosConfig_lang;
		$cur_language = &JFactory::getLanguage();
		$cur_language->load('com_easysdi', JPATH_ADMINISTRATOR);
		
		echo $cur_language."<br>";
		*/
		echo "TEST<br>";
		/*// Read the template dir to find templates
		//$languageBaseDir = mosPathName(mosPathName($mosConfig_absolute_path) . "/administrator/components/com_asitvd/lang");
		$languageBaseDir	= JPath::clean($client->path.DS.'language');
		
		$rowid = 0;

		$ini	= $client->path.DS.'templates'.DS.$template.DS.'en-GB'.DS.'en-GB.com_easysdi_core.ini';
		
		$xmlFilesInDir = mosReadDirectory($languageBaseDir,'.xml$');

		$dirName = $languageBaseDir;
		foreach($xmlFilesInDir as $xmlfile) {
			// Read the file to see if it's a valid template XML file
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors( true );
			if (!$xmlDoc->loadXML( $dirName . $xmlfile, false, true )) {
				continue;
			}

			$root = &$xmlDoc->documentElement;

			if ($root->getTagName() != 'mosinstall') {
				continue;
			}
			if ($root->getAttribute( "type" ) != "language") {
				continue;
			}

			$row 			= new StdClass();
			$row->id 		= $rowid;
			$row->language 	= substr($xmlfile,0,-4);
			$element 		= &$root->getElementsByPath('name', 1 );
			$row->name 		= $element->getText();

			$element		= &$root->getElementsByPath('creationDate', 1);
			$row->creationdate = $element ? $element->getText() : 'Unknown';

			$element 		= &$root->getElementsByPath('author', 1);
			$row->author 	= $element ? $element->getText() : 'Unknown';

			$element 		= &$root->getElementsByPath('copyright', 1);
			$row->copyright = $element ? $element->getText() : '';

			$element 		= &$root->getElementsByPath('authorEmail', 1);
			$row->authorEmail = $element ? $element->getText() : '';

			$element 		= &$root->getElementsByPath('authorUrl', 1);
			$row->authorUrl = $element ? $element->getText() : '';

			$element 		= &$root->getElementsByPath('version', 1);
			$row->version 	= $element ? $element->getText() : '';

			// if current than set published
			if ($cur_language == $row->language) {
				$row->published	= 1;
			} else {
				$row->published = 0;
			}

			$row->checked_out = 0;
			$row->mosname = strtolower( str_replace( " ", "_", $row->name ) );
			$rows[] = $row;
			$rowid++;
		}
		*/
		//$pageNav = new JPagination(count( $rows ), $limitstart, $limit );
	
		
		//$rows = array_slice( $rows, $pageNav->limitstart, $pageNav->limit );
		
		//HTML_ressources::listResources($rows, $pageNav, $option, $cur_language);
	}

	
	//id = 0 means new Config entry
	function editResource( $p_lname, $option ) {
		 $file = stripslashes( "../administrator/components/com_asitvd/lang/$p_lname.php" );

		if ($fp = fopen( $file, "r" )) {
			$content = fread( $fp, filesize( $file ) );
			$content = htmlspecialchars( $content );

			HTML_resources::editResource( $p_lname, $content, $option );
		} else {
			$mainframe->enqueueMessage("Operation Failed: Could not open $file","error");
			$mainframe->redirect("index.php?option=$option&task=listResources" );
		}
	}

}

?>
