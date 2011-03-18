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
/*
foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';
*/
defined('_JEXEC') or die('Restricted access');

class ADMIN_baselayer 
{
	function listBaseLayer ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		$ordering_field = JRequest::getVar('order_field');
		
		$query ="SELECT COUNT(*) FROM #__sdi_baselayer";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_baselayer l ";
		if($ordering_field)
		{
			$query .= " order by l.".$ordering_field;
		}
		else
		{
			$query .= " ORDER BY l.name";
		}		
			
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
		
		HTML_baselayer::listBaseLayer($use_pagination, $rows, $pageNav, $option);
	}
	
	function editBaseLayer ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$baseLayer = new baseLayer ($db);
		$baseLayer->load($id);
		
		HTML_baselayer::editBaseLayer($baseLayer, $option);
	}
	
	function deleteBaseLayer($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit;
		}
		foreach( $cid as $baseLayer_id )
		{
			$baseLayer = new baseLayer ($db);
			$baseLayer->load($baseLayer_id);
			
			if (!$baseLayer->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			}				
		}	
	}
	
	function saveBaseLayer($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
		
		$baseLayer =& new baseLayer($db);
		if (!$baseLayer->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit();
		}				
		  
		if (!$baseLayer->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit();
		}
	}
	
	function orderUpBasemapLayer($id){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$baseLayer = new baseLayer( $db );
		$baseLayer->load( $id);
		$baseLayer->orderUp();
	}
	
	function orderDownBasemapLayer($id){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$baseLayer = new baseLayer( $db );
		$baseLayer->load( $id);
		$baseLayer->orderDown();
	}

}
?>