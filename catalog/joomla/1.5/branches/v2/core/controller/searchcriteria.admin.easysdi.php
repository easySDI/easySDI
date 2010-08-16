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

class ADMIN_searchcriteria {
	
	function listSearchCriteria($option) {
		global  $mainframe;
		$db =& JFactory::getDBO();
		$context	= 'listSearchCriteria';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		$context_id	= JRequest::getVar( 'context_id');
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( $option.$context.".filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.$context.".filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" 
			and $filter_order <> "name" 
			and $filter_order <> "ordering" 
			and $filter_order <> "description" 
			and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		$query = "SELECT COUNT(*) FROM #__sdi_searchcriteria sc LEFT OUTER JOIN #__sdi_relation_context rc ON rc.relation_id=sc.relation_id WHERE sc.issystem=1 OR rc.context_id=".$context_id;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT sc.* FROM #__sdi_searchcriteria sc LEFT OUTER JOIN #__sdi_relation_context rc ON rc.relation_id=sc.relation_id WHERE sc.issystem=1 OR rc.context_id=".$context_id;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			//exit();
		}
		
		
		HTML_searchcriteria::listSearchCriteria($rows, $pagination, $filter_order_Dir, $filter_order, $context_id, $option);

	}

	/**
	* Back
	*/
	function backSearchCriteria($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();
		$context_id = JRequest::getVar('context_id',0);
		
		// Récupérer les états du listing des objets, pour éviter que les états des versions soient utilisés
		// alors qu'on change de contexte
		JRequest::setVar('filter_order', $mainframe->getUserState($option."listContext.filter_order"));
		JRequest::setVar('filter_order_Dir', $mainframe->getUserState($option."listContext.filter_order_Dir"));
		
		// Check the attribute in if checked out
		$rowContext = new context( $database );
		$rowContext->load($context_id);
		$rowContext->checkin();
	}
	
	function saveOrder($option)
	{
		global $mainframe;

		// Initialize variables
		$db			= & JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(0));
		$order		= JRequest::getVar( 'ordering', array (0));
		$total		= count($cid);
		$conditions	= array ();
		$context_id	= JRequest::getVar( 'context_id');
		
		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			// Instantiate an article table object
			$row = new searchcriteria( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
		exit();
	}
	
	function orderContent($direction, $option)
	{
		global $mainframe;

		// Initialize variables
		$db		= & JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array());
		$context_id	= JRequest::getVar( 'context_id');
		
		if (isset( $cid[0] ))
		{
			$row = new searchcriteria( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
		exit();
	}
	
	function changeState( $column, $column2, $state = 0 )
	{
		global $mainframe;
		
		// Initialize variables
		$db		= & JFactory::getDBO();
		
		$cid = JRequest::getVar('cid', array());
		JArrayHelper::toInteger($cid);
		$option	= JRequest::getCmd( 'option' );
		$task	= JRequest::getCmd( 'task' );
		$context_id = JRequest::getCmd( 'context_id' );
		$total	= count($cid);
		$cids	= implode(',', $cid);
		
		$query = 'UPDATE #__sdi_searchcriteria' .
				' SET '.$column.' = '. (int) $state .
				', '.$column2.' = 0'.
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
			exit();
		}

		if (count($cid) == 1) {
			$row = new objecttype( $db );
			$row->checkin($cid[0]);
		}

		$msg = JText::sprintf('State successfully changed');
				
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
		exit();
	}
}
?>