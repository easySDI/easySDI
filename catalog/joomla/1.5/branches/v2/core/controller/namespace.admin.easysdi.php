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

class ADMIN_namespace {
	function listNamespace($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$filter	= null;
		
		$context	= $option.'.listNamespace';
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
			$filter_order <> "prefix" and 
			$filter_order <> "uri" and
			$filter_order <> "description" and
			$filter_order <> "updated" and
			$filter_order <> "issystem")
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
		
		$query = "SELECT COUNT(*) FROM #__sdi_namespace";					
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__sdi_namespace";
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_namespace::listNamespace(&$rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editNamespace($id, $option)
	{
		?>
		<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;
				if (pressbutton != 'saveNamespace' && pressbutton != 'applyNamespace') {
					submitform( pressbutton );
					return;
				}
				// do field validation
				if (form.name.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_NAMESPACE_SUBMIT_NONAME', true ); ?>" );
				}
				else if (form.prefix.value == "") 
				{
				alert( "<?php echo JText::_( 'CATALOG_NAMESPACE_SUBMIT_NOPREFIX', true ); ?>" );
				} 
				else if (form.uri.value == "") 
				{
				alert( "<?php echo JText::_( 'CATALOG_NAMESPACE_SUBMIT_NOURI', true ); ?>" );
				} 
				else 
				{
					submitform( pressbutton );
				}
			}
		</script>
		
		<?php 
		global $mainframe;
		$database =& JFactory::getDBO(); 
		$rowNamespace = new namespace( $database );
		$rowNamespace->load( $id );
		
		if ($rowNamespace->issystem)
		{
			$mainframe->enqueueMessage(JText::_("CATALOG_NAMESPACE_ISSYSTEM_ERROR_MSG"),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listNamespace" );
			exit();
		}
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_namespace", false);
		
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
		
		HTML_namespace::editNamespace($rowNamespace, $fieldsLength, $option);
	}
	
	function saveNamespace($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		$rowNamespace= new namespace( $database );
		
		if (!$rowNamespace->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listNamespace" );
			exit();
		}		
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowNamespace->guid == null)
			$rowNamespace->guid = helper_easysdi::getUniqueId();
		
		if (!$rowNamespace->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listNamespace" );
			exit();
		}
	}
	
	function removeNamespace($id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('Sï¿½lectionnez un enregistrement ï¿½ supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("Sï¿½lectionnez un enregistrement ï¿½ supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listNamespace" );
			exit;
		}
		foreach( $id as $namespace_id )
		{
			$rowNamespace= new namespace( $database );
			$rowNamespace->load( $namespace_id );
			
			if ($rowNamespace->issystem)
			{
				$mainframe->enqueueMessage(JText::_("CATALOG_NAMESPACE_DELETE_ERROR_MSG"),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listNamespace" );
				exit();
			}
			
			if (!$rowNamespace->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listNamespace" );
				exit();
			}
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
			$row = new namespace( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listNamespace" );
					exit();
				}
			}
		}

		// Vider le cache
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		// Redirection vers la liste des types d'attributs
		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listNamespace" );
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
			$row = new namespace( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listNamespace" );
		exit();
	}
	
	function changeState( $column, $state = 0 )
	{
		global $mainframe;
		
		// Initialize variables
		$db		= & JFactory::getDBO();
		
		$cid = JRequest::getVar('cid', array());
		JArrayHelper::toInteger($cid);
		$option	= JRequest::getCmd( 'option' );
		$task	= JRequest::getCmd( 'task' );
		$total	= count($cid);
		$cids	= implode(',', $cid);
		
		$query = 'UPDATE #__sdi_namespace' .
				' SET '.$column.' = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listNamespace" );
			exit();
		}

		if (count($cid) == 1) {
			$row = new namespace( $db );
			$row->checkin($cid[0]);
		}

		$msg = JText::sprintf('State successfully changed');
				
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listNamespace" );
		exit();
	}
}
?>