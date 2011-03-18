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

class ADMIN_annotationstyle 
{
	function listAnnotationStyle ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		
		$query ="SELECT COUNT(*) FROM #__sdi_annotationstyle";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_annotationstyle";
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
		
		HTML_annotationstyle::listAnnotationStyle($use_pagination, $rows, $pageNav, $option);
	}
	
	function editAnnotationStyle ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$annotationStyle = new annotationStyle ($db);
		$annotationStyle->load($id);

		HTML_annotationstyle::editAnnotationStyle($annotationStyle, $option);
	}
	
	function deleteAnnotationStyle($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
			exit;
		}
		foreach( $cid as $annotationStyle_id )
		{
			$annotationStyle = new annotationStyle ($db);
			$annotationStyle->load($annotationStyle_id);
				
			if (!$annotationStyle->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
			}				
		}	
	}
	
	function saveAnnotationStyle($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$annotationStyle = new annotationStyle ($db);
		if (!$annotationStyle->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
			exit();
		}		

		if (!$annotationStyle->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
			exit();
		}
	}
}
?>