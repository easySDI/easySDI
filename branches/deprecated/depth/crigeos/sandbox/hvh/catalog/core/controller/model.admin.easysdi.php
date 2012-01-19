<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ€™Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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


class ADMIN_model {
	function listModel($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);

		$query = "SELECT COUNT(*) FROM #__sdi_model";					
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "ordering" and $filter_order <> "created" and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT *  FROM #__sdi_model ";
		$query .= $orderby;
		
		if ($use_pagination) {
			$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else{
			$db->setQuery( $query);
		}
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_model::listModel(&$rows, &$pageNav, $option,  $filter_order_Dir, $filter_order, $use_pagination);
	}
	
	function editModel($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$rowModel = new model( $database );
		$rowModel->load( $id );
		
		$profiles = array();
		$profiles[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT id AS value, label FROM #__sdi_profile ORDER BY name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );
		
		HTML_model::editModel($rowModel, $profiles, $option);
	}
	
	function saveModel($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		$rowModel= new model( $database );
		
		if (!$rowModel->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listModel" );
			exit();
		}		
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowModel->guid == null)
			$rowModel->guid = helper_easysdi::getUniqueId();
		
		
		if (!$rowModel->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listModel" );
			exit();
		}
	}
	
	function removeModel($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		$rowModel= new model( $database );
		$rowModel->load( $id );
		
		if (!$rowModel->delete()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listModel" );
			exit();
		}
	}
}
?>