<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
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

class ADMIN_display 
{
	function listDisplay ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		$query ="SELECT value FROM #__sdi_configuration WHERE code ='enableQueryEngine' ";
		$db->setQuery( $query );
		$enableQueryEngine = $db->loadResult();
					
		$query ="SELECT COUNT(*) FROM #__sdi_mapdisplayoption";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_mapdisplayoption";
		if($enableQueryEngine == '0')
		{
			$query .= " WHERE name NOT IN('SimpleSearch','AdvancedSearch','DataPrecision') ";
			
			//Disable the display option for the query engine 
			$db->setQuery( "UPDATE #__sdi_mapdisplayoption SET enable=0 WHERE name IN('SimpleSearch','AdvancedSearch','DataPrecision')");
			$db->query();
		}
		$query .= " ORDER BY object ";
		$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_display::listDisplay( $rows, $pageNav, $option);
	}
	
	function saveDisplay($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$id = 	JRequest::getVar('id');
		$enable =JRequest::getVar('enable','0');

		$db->setQuery( "UPDATE #__sdi_mapdisplayoption SET enable=$enable WHERE id=$id");
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$mainframe->redirect("index.php?option=$option&task=display");
	}
}
?>