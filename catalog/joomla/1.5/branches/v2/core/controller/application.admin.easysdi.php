<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
				if (pressbutton != 'saveApplication' && pressbutton != 'applyApplication') {
					submitform( pressbutton );
					return;
				}
				
				// do field validation
				if (form.name.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_APPLICATION_SUBMIT_NONAME', true ); ?>" );
				}
				else if (form.windowname.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_APPLICATION_SUBMIT_NOWINDOWNAME', true ); ?>" );
				}
				else if (form.url.value == "") 
				{
				alert( "<?php echo JText::_( 'CATALOG_APPLICATION_SUBMIT_NOURL', true ); ?>" );
				} 
				else 
				{
					submitform( pressbutton );
				}
			}
		</script>
		
		<?php 
		
class ADMIN_application {
	function listApplication($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$filter	= null;
		
		$object_id = JRequest::getVar ('object_id');
		
		$context	= $option.'.listApplication';
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
			$filter_order <> "updated" and
			$filter_order <> "description" and
			$filter_order <> "windowname" and
			$filter_order <> "url")
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
		$where = (count($where) ? ' AND '.implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_application WHERE object_id=".$object_id;					
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__sdi_application WHERE object_id=".$object_id;
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_application::listApplication(&$rows, $pagination, $option,  $filter_order_Dir, $filter_order, $object_id);
	}
	
	function editApplication($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$rowApplication = new application( $database );
		$rowApplication->load( $id );
		
		$object_id = JRequest::getVar ('object_id');
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowApplication->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowApplication->name);
			$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id, $msg );
		}

		$rowApplication->checkout($user->get('id'));
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_application", false);
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caract�res
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
		
		HTML_application::editApplication($rowApplication, $fieldsLength, $object_id, $option);
	}
	
	function saveApplication($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$object_id = JRequest::getVar ('object_id');
		
		$rowApplication= new application( $database );
		
		if (!$rowApplication->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id );
			exit();
		}		
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowApplication->guid == null)
			$rowApplication->guid = helper_easysdi::getUniqueId();
		
		if (!$rowApplication->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id );
			exit();
		}
		
		$rowApplication->checkin();
		
		// Au cas où on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyApplication' :
				// Reprendre en édition l'objet
				TOOLBAR_application::_EDIT();
				ADMIN_application::editApplication($rowApplication->id,$option);
				break;

			case 'saveApplication' :
			default :
				break;
		}
	}
	
	function removeApplication($id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 

		$object_id = JRequest::getVar ('object_id');
		
		if (!is_array( $id ) || count( $id ) < 1) {
			$mainframe->enqueueMessage(JText::_('CATALOG_APPLICATION_SUBMIT_NOSELECTEDAPPLICATION'),"error");
			$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id );
			exit;
		}
		foreach( $id as $application_id )
		{
			$rowApplication= new application( $database );
			$rowApplication->load( $application_id );
			
			if (!$rowApplication->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelApplication($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		$object_id = JRequest::getVar ('object_id');
		
		// Check the attribute in if checked out
		$rowApplication= new application( $database );
		$rowApplication->bind(JRequest::get('post'));
		$rowApplication->checkin();

		$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id );
	}
	
	/**
	* Back
	*/
	function backApplication($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();
		$object_id = JRequest::getVar('object_id',0);
		
		// Récupérer les états du listing des objets, pour éviter que les états des applications soient utilisés
		// alors qu'on change de contexte
		JRequest::setVar('filter_order', $mainframe->getUserState($option."listObject.filter_order"));
		JRequest::setVar('filter_order_Dir', $mainframe->getUserState($option."listObject.filter_order_Dir"));
		
		// Check the object in if checked out
		$rowObject = new object( $database );
		$rowObject->load($object_id);
		$rowObject->checkin();
	}
	
	function saveOrder($option)
	{
		global $mainframe;

		// Initialize variables
		$db			= & JFactory::getDBO();

		$object_id = JRequest::getVar ('object_id');
		
		//print_r(JRequest::get());echo "<br>"; 
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
			$row = new application( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id );
					exit();
				}
				
				// remember to updateOrder this group
				$condition = 'object_id = '.(int) $object_id;
				$found = false;
				foreach ($conditions as $cond)
					if ($cond[1] == $condition) {
						$found = true;
						break;
					}
				if (!$found)
					$conditions[] = array ($row->id, $condition);
			}
		}
		
		// execute updateOrder for each group
		foreach ($conditions as $cond)
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}
		
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id );
		exit();
	}
	
	function orderContent($direction, $option)
	{
		global $mainframe;

		// Initialize variables
		$db		= & JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array());

		$object_id = JRequest::getVar ('object_id');
		
		if (isset( $cid[0] ))
		{
			$row = new application( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction, 'object_id = '.(int) $object_id);
			
			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id );
		exit();
	}
}
?>