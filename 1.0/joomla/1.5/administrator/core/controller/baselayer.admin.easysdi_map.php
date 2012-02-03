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
	function listBaseDefinition ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		
		$query ="SELECT COUNT(*) FROM #__easysdi_map_base_definition";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__easysdi_map_base_definition";
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
		
		HTML_baselayer::listBaseDefinition($use_pagination, $rows, $pageNav, $option);
	}
	
	function editBaseDefinition ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$base_definition = new base_definition ($db);
		$base_definition->load($id);

		HTML_baselayer::editBaseDefinition($base_definition, $option);
	}
	
	function deleteBaseDefinition($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=baseDefinition" );
			exit;
		}
		foreach( $cid as $base_id )
		{
			$base_definition = new base_definition ($db);
			$base_definition->load($base_id);
				
			if (!$base_definition->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=baseDefinition" );
			}				
		}	
	}
	
	function saveBaseDefinition($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$base_definition =& new base_definition($db);
		if (!$base_definition->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=baseDefinition" );
			exit();
		}				
		if (!$base_definition->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=baseDefinition" );
			exit();
		}
	}
	
	function listBaseLayer ($id_base, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		if(!$id_base)
		{
			$id_base = JRequest::getVar('id_base');
		}
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		$order_field = JRequest::getVar('order_field');
		
		$query ="SELECT COUNT(*) FROM #__easysdi_map_base_layer WHERE id_base=$id_base";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__easysdi_map_base_layer l WHERE l.id_base=$id_base";
		
		if($order_field)
		{
			$query .= " order by l.".$order_field;
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
		
		HTML_baselayer::listBaseLayer($use_pagination, $rows,$id_base, $pageNav, $option);
	}
	
	function editBaseLayer ($id,$id_base,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$base_layer = new base_layer ($db);
		$base_layer->load($id);
		$base_layer->id_base=$id_base;

		HTML_baselayer::editBaseLayer($base_layer, $option);
	}
	
	function deleteBaseLayer($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		$id_base = JRequest::getVar ('id_base', array(0) );
		$id_bases = array ();
		$id_bases[0]=$id_base;
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=baseLayer&cid=$id_bases" );
			exit;
		}
		foreach( $cid as $base_layer_id )
		{
			$base_layer = new base_layer ($db);
			$base_layer->load($base_layer_id);
				
			$query ="UPDATE #__easysdi_map_base_layer b SET b.order = b.order-1 WHERE b.order > $base_layer->order" ;
			$db->setQuery( $query );
			$db->query();
			
			if (!$base_layer->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=baseLayer&cid=$id_bases" );
			}				
		}	
	}
	
	function saveBaseLayer($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
		
		$cid = JRequest::getVar ('cid', array(0) );
			
		$base_layer =& new base_layer($db);
		if (!$base_layer->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=baseLayer&cid=$cid" );
			exit();
		}				
		if($base_layer->order == '' || $base_layer->order == 0 )
		{
			$query ="SELECT MAX(b.order) FROM #__easysdi_map_base_layer b " ;
			$db->setQuery( $query );
			$total = $db->loadResult();
			$base_layer->order = $total + 1;
		}
		if (!$base_layer->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=baseLayer&cid=$cid" );
			exit();
		}
	}
	
	/*
	 * Re order the layer in the base map
	*/
	function orderUpBasemapLayer($id,$basemapId)
	{
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		

		$query = "SELECT *  FROM #__easysdi_map_base_layer  WHERE id = $id AND id_base = $basemapId LIMIT 1";
		$database->setQuery( $query );
		$row1 = $database->loadObject() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}

		$query = "SELECT *  FROM #__easysdi_map_base_layer l  WHERE  l.id_base = $basemapId AND l.order< $row1->order  order by l.order DESC LIMIT 1";
		$database->setQuery( $query );
		$row2 = $database->loadObject() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		

		$query = "update #__easysdi_map_base_layer l set l.order= $row1->order where l.id =$row2->id";
			$database->setQuery( $query );				
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
			}		
				
		$query = "update #__easysdi_map_base_layer l set l.order= $row2->order where l.id =$row1->id";
			$database->setQuery( $query );				
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
			}		
	}
	
	/*
	 * Re order the base layer in the base map
	*/
	function orderDownBasemapLayer($id,$basemapId)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 

		$query = "SELECT *  FROM #__easysdi_map_base_layer  WHERE id = $id AND id_base = $basemapId LIMIT 1";
		$database->setQuery( $query );
		$row1 = $database->loadObject() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}

		$query = "SELECT *  FROM #__easysdi_map_base_layer l  WHERE  l.id_base = $basemapId AND l.order > $row1->order  order by l.order ASC LIMIT 1";
		$database->setQuery( $query );
		$row2 = $database->loadObject() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		

		$query = "update #__easysdi_map_base_layer l set l.order= $row1->order where l.id =$row2->id";
			$database->setQuery( $query );				
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
			}		
				
		$query = "update #__easysdi_map_base_layer l set l.order= $row2->order where l.id =$row1->id";
			$database->setQuery( $query );				
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
			}
	}

}
?>