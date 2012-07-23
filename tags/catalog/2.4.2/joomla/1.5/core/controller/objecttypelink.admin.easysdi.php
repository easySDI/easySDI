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
		if (pressbutton != 'saveObjectTypeLink' && pressbutton != 'applyObjectTypeLink') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (getSelectedValue('adminForm','parent_id') < 1) 
		{
			alert( "<?php echo JText::_( 'CATALOG_OBJECTTYPELINK_SUBMIT_NOPARENT', true ); ?>" );
		}
		else if (getSelectedValue('adminForm','parent_id') < 1) 
		{
			alert( "<?php echo JText::_( 'CATALOG_OBJECTTYPELINK_SUBMIT_NOCHILD', true ); ?>" );
		} 
		else 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 
class ADMIN_objecttypelink {
	function listObjectTypeLink($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$language =& JFactory::getLanguage();
		$filter	= null;
		
		$context	= $option.'.listObjectTypeLink';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and 
			$filter_order <> "parent_name" and
			$filter_order <> "child_name" and 
			$filter_order <> "ordering" and 
			$filter_order <> "updated" and
			$filter_order <> "flowdown_versioning" and
			$filter_order <> "escalate_versioning_update")
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
		
		$query = "SELECT COUNT(*) FROM #__sdi_objecttypelink";					
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		//$query = "SELECT l.*, parent.name as parent_name, child.name as child_name FROM #__sdi_objecttypelink as l INNER JOIN #__sdi_objecttype as parent ON l.parent_id=parent.id INNER JOIN #__sdi_objecttype as child ON l.child_id=child.id";
		$query = "SELECT l.*, parent_t.label as parent_name, child_t.label as child_name 
				  FROM #__sdi_objecttypelink as l 
				  INNER JOIN #__sdi_objecttype as parent ON l.parent_id=parent.id 
				  INNER JOIN #__sdi_objecttype as child ON l.child_id=child.id
				  INNER JOIN #__sdi_translation parent_t ON parent_t.element_guid=parent.guid
				  INNER JOIN #__sdi_language parent_l ON parent_t.language_id=parent_l.id
				  INNER JOIN #__sdi_list_codelang parent_cl ON parent_l.codelang_id=parent_cl.id
				  INNER JOIN #__sdi_translation child_t ON child_t.element_guid=child.guid
				  INNER JOIN #__sdi_language child_l ON child_t.language_id=child_l.id
				  INNER JOIN #__sdi_list_codelang child_cl ON child_l.codelang_id=child_cl.id
				  WHERE parent_cl.code='".$language->_lang."' 
				 	   AND child_cl.code='".$language->_lang."'
				  ";
		
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_objecttypelink::listObjectTypeLink(&$rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editObjectTypeLink($id, $option)
	{
		global  $mainframe;
				
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		$language =& JFactory::getLanguage();
		
		// Gestion de la page rechargée sur modification de la classe root du parentIdentifier
		$pageReloaded=false;
		if (array_key_exists('class_id', $_POST))
		{
			$pageReloaded=true;
		}
		
		$rowObjectTypeLink = new objecttypelink( $database );
		$rowObjectTypeLink->load( $id );
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowObjectTypeLink->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowObjectTypeLink->id);
			$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink", $msg );
			exit;
		}

		$rowObjectTypeLink->checkout($user->get('id'));
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_objecttypelink", false);
		
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
		
		// get list of objecttypes for dropdown filter
		/*$query = 'SELECT id as value, name as text' .
				' FROM #__sdi_objecttype' .
				//' WHERE predefined=false' .
				' ORDER BY name';*/
		$query = "SELECT ot.id AS value, t.label as text 
				 FROM #__sdi_objecttype ot 
				 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
				 INNER JOIN #__sdi_language l ON t.language_id=l.id
				 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
				 WHERE ot.predefined=false 
				 	   AND cl.code='".$language->_lang."'
				 ORDER BY t.label";
		
		$objecttypes[] = JHTML::_('select.option', '0', JText::_('SELECT_OBJECTTYPE'), 'value', 'text');
		$database->setQuery($query);
		$objecttypes = array_merge($objecttypes, $database->loadObjectList());
		
		$style="display:none";
		if ($pageReloaded)
		{
			if ($_POST['parentbound_upper'] == 1)
				$style="display:inline";
		}
		else
		{
			if ($rowObjectTypeLink->parentbound_upper == 1)
				$style="display:inline";
		}	
		
		$classes=array();
		$classes[] = JHTML::_('select.option','0', JText::_("CATALOG_OBJECTTYPELINK_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_class WHERE isrootclass=true ORDER BY name" );
		$classes = array_merge( $classes, $database->loadObjectList() );
		
		$attributes= array();
		$attributes[] = JHTML::_('select.option','0', JText::_("CATALOG_OBJECTTYPELINK_ATTRIBUTE_LIST") );
		if ($_POST['class_id'] or $rowObjectTypeLink->class_id)
		{
			if ($pageReloaded)
			{
				$database->setQuery( "SELECT a.id AS value, a.name as text FROM #__sdi_attribute a, #__sdi_relation rel WHERE a.id=rel.attributechild_id AND a.attributetype_id=1 AND rel.parent_id=".$_POST['class_id']." ORDER BY a.name" );
				$attributes = array_merge( $attributes, $database->loadObjectList() );
			}
			else if ($rowObjectTypeLink->id <> 0)
			{
				$database->setQuery( "SELECT a.id AS value, a.name as text FROM #__sdi_attribute a, #__sdi_relation rel WHERE a.id=rel.attributechild_id AND a.attributetype_id=1 AND rel.parent_id=".$rowObjectTypeLink->class_id." ORDER BY a.name" );
				$attributes = array_merge( $attributes, $database->loadObjectList() );
			}
		}
		
		HTML_objecttypelink::editObjectTypeLink($rowObjectTypeLink, $fieldsLength, $objecttypes, $classes, $attributes, $style, $pageReloaded, $option);
	}
	
	function saveObjectTypeLink($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$rowObjectTypeLink= new objecttypelink( $database );
		
		if (!$rowObjectTypeLink->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
			exit();
		}		
		
		if ($_POST['class_id'] == 0)
			$rowObjectTypeLink->class_id=null;
		
		if ($_POST['attribute_id'] == 0) 
			$rowObjectTypeLink->attribute_id=null;
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObjectTypeLink->guid == null)
			$rowObjectTypeLink->guid = helper_easysdi::getUniqueId();
		
		if (!$rowObjectTypeLink->store(true)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
			exit();
		}
		
		$rowObjectTypeLink->checkin();
		
		// Au cas où on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyObjectTypeLink' :
				// Reprendre en édition l'objet
				TOOLBAR_objecttypelink::_EDIT();
				ADMIN_objecttypelink::editObjectTypeLink($rowObjectTypeLink->id,$option);
				break;

			case 'saveObjectTypeLink' :
			default :
				break;
		}
	}
	
	function removeObjectTypeLink($id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('Sï¿½lectionnez un enregistrement ï¿½ supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("Sï¿½lectionnez un enregistrement ï¿½ supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
			exit;
		}
		foreach( $id as $objecttypelink_id )
		{
			$rowObjectTypeLink= new objecttypelink( $database );
			$rowObjectTypeLink->load( $objecttypelink_id );
			
			if (!$rowObjectTypeLink->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelObjectTypeLink($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowObjectTypeLink= new objecttypelink( $database );
		$rowObjectTypeLink->bind(JRequest::get('post'));
		$rowObjectTypeLink->checkin();

		$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
	}
	
	function saveOrder($option)
	{
		global $mainframe;

		// Initialize variables
		$db			= & JFactory::getDBO();

		print_r(JRequest::get());echo "<br>"; 
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
			$row = new objecttypelink( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
					exit();
				}
			}
		}
		
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
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
			$row = new objecttypelink( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
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
		
		$query = 'UPDATE #__sdi_objecttypelink' .
				' SET '.$column.' = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
			exit();
		}

		if (count($cid) == 1) {
			$row = new objecttypelink( $db );
			$row->checkin($cid[0]);
		}

		$msg = JText::sprintf('State successfully changed');
				
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listObjectTypeLink" );
		exit();
	}
}
?>