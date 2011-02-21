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

class ADMIN_projection 
{
	function listProjection ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		
		$query ="SELECT COUNT(*) FROM #__easysdi_map_projection";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__easysdi_map_projection";
		$query .= " ORDER BY name";
		if ($use_pagination) 
		{
			$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else
		{
			$db->setQuery( $query);
		}
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_projection::listProjection($use_pagination, $rows, $pageNav, $option);
	}
	
	function editProjection ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$projection = new projection ($db);
		$projection->load($id);

		HTML_projection::editProjection($projection, $option);
	}
	
	function deleteProjection($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=projection" );
			exit;
		}
		foreach( $cid as $projection_id )
		{
			$projection = new projection ($db);
			$projection->load($projection_id);
				
			if (!$projection->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=projection" );
			}				
		}	
	}
	
	function saveProjection($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$projection =& new projection ($db);
		if (!$projection->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=projection" );
			exit();
		}		

		$projection->enable =JRequest::getVar('enable','0');

		if (!$projection->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=projection" );
			exit();
		}
	}

}
?>