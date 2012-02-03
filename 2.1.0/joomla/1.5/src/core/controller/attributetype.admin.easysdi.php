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

?>
<script type="text/javascript">
	function submitbutton(pressbutton) 
	{
		var form = document.adminForm;
		if (pressbutton != 'saveAttributeType' && pressbutton != 'applyAttributeType') {
			submitform( pressbutton );
			return;
		}
		
		submitform( pressbutton );
	}
</script>

<?php 
class ADMIN_attributetype {
	function listAttributeType($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$filter	= null;
		
		$context	= $option.'.listAttributeType';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and 
			$filter_order <> "name" and 
			$filter_order <> "ordering" and
			$filter_order <> "attributetype_isocode" and 
			$filter_order <> "updated" and
			$filter_order <> "description")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		
		/*
		 * Add the filter specific information to the where clause
		 */
		$where = array();
		
		// Build the where clause of the content record query
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_list_attributetype";					
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT t.*, CONCAT(ns.prefix,':',t.isocode) as attributetype_isocode FROM #__sdi_list_attributetype t LEFT OUTER JOIN #__sdi_namespace ns ON ns.id=t.namespace_id";
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_attributetype::listAttributeType(&$rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editAttributeType($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$rowAttributeType = new attributetype( $database );
		$rowAttributeType->load( $id );
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_list_attributetype", false);
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caractères
		$fieldsLength = array();
		foreach($tableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$fieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}
		
		$namespacelist = array();
		$namespacelist[] = JHTML::_('select.option','0', " - " );
		$database->setQuery( "SELECT id AS value, prefix AS text FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
				
		HTML_attributetype::editAttributeType($rowAttributeType, $fieldsLength, $namespacelist, $option);
	}
	
	function saveAttributeType($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		$rowAttributeType= new attributetype( $database );
		
		if (!$rowAttributeType->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listAttributeType" );
			exit();
		}		
		
		if (!$rowAttributeType->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAttributeType" );
			exit();
		}
		
		// Au cas où on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyAttributeType' :
				// Reprendre en édition l'objet
				TOOLBAR_attributetype::_EDIT();
				ADMIN_attributetype::editAttributeType($rowAttributeType->id,$option);
				break;

			case 'saveAttributeType' :
			default :
				break;
		}
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

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			// Instantiate an article table object
			$row = new attributetype( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listAttributeType" );
					exit();
				}
			}
		}

		// Vider le cache
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		// Redirection vers la liste des types d'attributs
		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listAttributeType" );
		exit();
	}
	
	function orderContent($direction, $option)
	{
		global $mainframe;

		// Initialize variables
		$db		= & JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array());

		if (isset( $cid[0] ))
		{
			$row = new attributetype( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listAttributeType" );
		exit();
	}
}
?>