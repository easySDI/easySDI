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

	
	function listResources($option) {
		global $mainframe;
		$easysdiCom = "com_easysdi_";
		$easysdiModMenu = "mod_menu_easysdi";
		$easysdiModCaddy = "mod_caddy";
		$currentLanguage = JFactory::getLanguage();
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 100 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		$search				= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
		$search				= JString::strtolower( $search );
		
		$rows = array();
		
		// Récupérer tous les fichiers de langue d'easysdi côté Admin, dans toutes les langues disponibles
		$languagesDir =  array();
		$path = JPATH_ADMINISTRATOR.DS.'language'.DS;
		
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle))) {
					if (is_dir($path.$file) and $file != "." and $file != "..")
			        {
			        		//$languagesDir[$file]=$path.$file;
			        		$languagesDir[]=$path.$file;
					}
			    }
			closedir($handle);
		}

		// Récupérer tous les fichiers de langue d'easysdi côté Site, dans toutes les langues disponibles
		$path = JPATH_ROOT.DS.'language'.DS;
		
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle))) {
					if (is_dir($path.$file) and $file != "." and $file != "..")
			        {
			        		$languagesDir[]=$path.$file;       
					}
			    }
			closedir($handle);
		}
		
		//print_r($languagesDir);
		//echo "<br>";
		$languagesFiles =  array();
		
		$rowid = 0;
		foreach($languagesDir as $dirName => $dir)
		{
			foreach(scandir($dir) as $languageFile)
			{
				if (strstr($languageFile, $easysdiCom))
				{
					if ($search =="" or strstr($languageFile, $search))
					{
						//echo $languageFile."<br>";
						$languagesFiles[$languageFile]=$dir.DS.$languageFile;
						
						//Créer une entrée dans $rows pour chaque fichier de langue récupéré
						$row 			= new StdClass();
						$row->id 		= $rowid;
						$row->language 	= basename($dir);
						$row->filename 	= $dir.DS.$languageFile;
						if (strstr($dir, "\\administrator\\") or strstr($dir, "/administrator/"))
							$row->side	 	= JText::_("EASYSDI_RESOURCE_SIDE_ADMIN");
						else
							$row->side	 	= JText::_("EASYSDI_RESOURCE_SIDE_SITE");
						$row->component	= substr($languageFile, strpos($languageFile, $easysdiCom), strpos($languageFile, ".ini")-strpos($languageFile, $easysdiCom));
						$row->updatedate = date ("d.m.Y H:i:s", filemtime($dir.DS.$languageFile));
						if ($currentLanguage->_lang == basename($dir))
							$row->published	= "1";
						else
							$row->published	= "0";
								
						$rows[]=$row;
						$rowid++;
					}
				}
				else if (strstr($languageFile, $easysdiModMenu))
				{
					if ($search =="" or strstr($languageFile, $search))
					{
						//echo $languageFile."<br>";
						$languagesFiles[$languageFile]=$dir.DS.$languageFile;
						
						//Créer une entrée dans $rows pour chaque fichier de langue récupéré
						$row 			= new StdClass();
						$row->id 		= $rowid;
						$row->language 	= basename($dir);
						$row->filename 	= $dir.DS.$languageFile;
						if (strstr($dir, "\\administrator\\") or strstr($dir, "/administrator/"))
							$row->side	 	= JText::_("EASYSDI_RESOURCE_SIDE_ADMIN");
						else
							$row->side	 	= JText::_("EASYSDI_RESOURCE_SIDE_SITE");
						$row->component	= substr($languageFile, strpos($languageFile, $easysdiModMenu), strpos($languageFile, ".ini")-strpos($languageFile, $easysdiModMenu));
						$row->updatedate = date ("d.m.Y H:i:s", filemtime($dir.DS.$languageFile));
						if ($currentLanguage->_lang == basename($dir))
							$row->published	= "1";
						else
							$row->published	= "0";
								
						$rows[]=$row;
						$rowid++;
					}
				}
				else if (strstr($languageFile, $easysdiModCaddy))
				{
					if ($search =="" or strstr($languageFile, $search))
					{
						//echo $languageFile."<br>";
						$languagesFiles[$languageFile]=$dir.DS.$languageFile;
						
						//Créer une entrée dans $rows pour chaque fichier de langue récupéré
						$row 			= new StdClass();
						$row->id 		= $rowid;
						$row->language 	= basename($dir);
						$row->filename 	= $dir.DS.$languageFile;
						if (strstr($dir, "\\administrator\\") or strstr($dir, "/administrator/"))
							$row->side	 	= JText::_("EASYSDI_RESOURCE_SIDE_ADMIN");
						else
							$row->side	 	= JText::_("EASYSDI_RESOURCE_SIDE_SITE");
						$row->component	= substr($languageFile, strpos($languageFile, $easysdiModCaddy), strpos($languageFile, ".ini")-strpos($languageFile, $easysdiModCaddy));
						$row->updatedate = date ("d.m.Y H:i:s", filemtime($dir.DS.$languageFile));
						if ($currentLanguage->_lang == basename($dir))
							$row->published	= "1";
						else
							$row->published	= "0";
								
						$rows[]=$row;
						$rowid++;
					}
				}
			}
		}
		
		//print_r($languagesFiles);
		//echo "<br>";
		
		//print_r($rows);
		

		$pageNav = new JPagination(count( $rows ), $limitstart, $limit );
	
		
		$rows = array_slice( $rows, $pageNav->limitstart, $pageNav->limit );
		
		HTML_resources::listResources($rows, $pageNav, $option, $search);
	}

	
	//id = 0 means new Config entry
	function editResource( $option ) {
		$file = $_GET['filename'];
		clearstatcache();
		if ($fp = fopen( $file, "r" )) {
			$content = fread( $fp, filesize($file));
			$content = htmlspecialchars( $content );

			HTML_resources::editResource( $file, $content, $option );
		} else {
			$mainframe->enqueueMessage("Operation Failed: Could not open $file","error");
			$mainframe->redirect("index.php?option=$option&task=listResources" );
		}
	}
	
	function saveResource( $option ) {
		global $mainframe;
		$file = $_POST['filename'];
		$content = $_POST['filecontent'];
		
		$enable_write = $_POST['enable_write'];
		$oldperms = fileperms($file);
		if ($enable_write) @chmod($file, $oldperms | 0222);

		clearstatcache();
		if (!is_writable( $file )) {
			$mainframe->enqueueMessage("Operation Failed: The file is not writable.","error");
			$mainframe->redirect("index.php?option=$option&task=listResources" );
		}
		
		if ($fp = fopen( $file, "w" )) {
			$content = htmlspecialchars_decode( $content );
			fwrite( $fp, $content);
			
			if ($enable_write) {
				@chmod($file, $oldperms);
			} else {
				if ($_POST['disable_write'])
					@chmod($file, $oldperms & 0777555);
			} // if
			$mainframe->redirect("index.php?option=$option&task=listResources" );
		} else {
			if ($enable_write) @chmod($file, $oldperms);
			$mainframe->enqueueMessage("Operation Failed: Could not save $file","error");
			$mainframe->redirect("index.php?option=$option&task=listResources" );
		}
	}

}

?>
