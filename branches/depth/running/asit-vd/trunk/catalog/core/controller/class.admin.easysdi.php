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
		if (pressbutton != 'saveClass' && pressbutton != 'applyClass') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'CATALOG_CLASS_SUBMIT_NONAME', true ); ?>" );
		}
		else if (getSelectedValue('adminForm','namespace_id') < 1) 
		{
			alert( "<?php echo JText::_( 'CATALOG_CLASS_SUBMIT_NONAMESPACE', true ); ?>" );
		} 
		else if (form.isocode.value == "") 
		{
			alert( "<?php echo JText::_( 'CATALOG_CLASS_SUBMIT_NOISOCODE', true ); ?>" );
		}
		else 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 
class ADMIN_class {
	function listClass($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$context	= $option.'.listClass';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// Filtering
		$searchClass				= $mainframe->getUserStateFromRequest( 'searchClass', 'searchClass', '', 'string' );
		$searchClass				= JString::strtolower($searchClass);
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "class_isocode" and $filter_order <> "isrootclass"  and $filter_order <> "isextensible" and $filter_order <> "description" and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		
		/*
		 * Add the filter specific information to the where clause
		 */
		$where = array();
		// Keyword filter
		if ($searchClass) {
			$where[] = '(c.id LIKE '. (int) $searchClass .
				' OR LOWER( c.name ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchClass, true ).'%', false )
				//' OR LOWER( isocode ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchClass, true ).'%', false ) 
				.')';
		}
		
		// Build the where clause of the content record query
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_class";					
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT c.*, CONCAT(ns.prefix,':',c.isocode) as class_isocode FROM #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON ns.id=c.namespace_id";
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		// searchClass filter
		$lists['searchClass'] = $searchClass;
		
		HTML_class::listClass($rows, $lists, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editClass($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$rowClass = new classes( $database );
		$rowClass->load( $id );
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowClass->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowClass->name);
			$mainframe->redirect("index.php?option=$option&task=listClass", $msg );
		}

		$rowClass->checkout($user->get('id'));
				
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		$selected_accounts = array();
		$database->setQuery( "SELECT c.id AS value, b.name AS text FROM #__sdi_account_class a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.class_id=".$id." ORDER BY b.name" );
		$selected_accounts = array_merge( $selected_accounts, $database->loadObjectList() );
		
		$accounts = array();
		$database->setQuery( "SELECT a.id AS value, b.name AS text FROM #__sdi_account a,#__users b where a.user_id = b.id ORDER BY b.name" );
		$accounts = array_merge( $accounts, $database->loadObjectList() );
		
		$unselected_accounts=array();
		$unselected_accounts=helper_easysdi::array_obj_diff($accounts, $selected_accounts);
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_class", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false) );
		
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
		
		// Langues à gérer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// L'aide contextuelle
		$informations = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT information FROM #__sdi_translation WHERE element_guid='".$rowClass->guid."' AND language_id=".$lang->id);
			$information = $database->loadResult();
			
			$informations[$lang->id] = $information;
		}
		
		$namespacelist = array();
		$namespacelist[] = JHTML::_('select.option','0', " - " );
		$database->setQuery( "SELECT id AS value, prefix AS text FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		$stereotypelist = array();
		$stereotypelist[] = JHTML::_('select.option','0', JText::_("EASYSDI_ATTRIBUTETYPE_LIST") );
		$database->setQuery( "SELECT id AS value, alias AS text FROM #__sdi_sys_stereotype WHERE entity_id=2 ORDER BY alias" );
		$stereotypelist = array_merge( $stereotypelist, $database->loadObjectList() );
		
		HTML_class::editClass($rowClass, $unselected_accounts, $selected_accounts, $fieldsLength, $languages, $informations, $namespacelist,$stereotypelist, $option);
	}
	
	function saveClass($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		$rowClass= new classes( $database );
		
		if (!$rowClass->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listClass" );
			exit();
		}		
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowClass->guid == null)
			$rowClass->guid = helper_easysdi::getUniqueId();
		
		if (!$rowClass->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listClass" );
			exit();
		}

		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Stocker l'aide contextuelle
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowClass->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET information='".addslashes($_POST['information_'.$lang->code])."' WHERE element_guid='".$rowClass->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, information) VALUES ('".$rowClass->guid."', ".$lang->id.", '".addslashes($_POST['information_'.$lang->code])."')");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// R�cup�rer toutes les relations avec les utilisateurs existantes
		$query = "SELECT * FROM #__sdi_account_class WHERE class_id=".$rowClass->id;
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// D�stockage des relations avec les utilisateurs
		foreach ($rows as $row)
		{
			// Si la cl� existante n'est pas dans le tableau des relations, on la supprime
			if (!in_array($row->id, $_POST['selected']))
			{
				$rowAccountClass= new account_class($database);
				$rowAccountClass->load($row->id);
				
				if (!$rowAccountClass->delete()) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listClass" );
					exit();
				}
			}
		}
		
		if (array_key_exists('selected', $_POST))
		{
			// Stockage des relations avec les utilisateurs
			foreach($_POST['selected'] as $selected)
			{
				// Si la cl� du tableau des relations n'est pas encore dans la base, on l'ajoute
				if (!in_array($selected, $rows))
				{
					$rowAccountClass= new account_class($database);
					$rowAccountClass->account_id=$selected;
					$rowAccountClass->class_id=$rowClass->id;
					
					if (!$rowAccountClass->store(false)) {			
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						$mainframe->redirect("index.php?option=$option&task=listClass" );
						exit();
					}
				}
			}
		}
		
		$rowClass->checkin();
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyClass' :
				// Reprendre en �dition l'objet
				TOOLBAR_class::_EDIT();
				ADMIN_class::editClass($rowClass->id,$option);
				break;

			case 'saveClass' :
			default :
				break;
		}
	}
	
	function removeClass($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit;
		}
		foreach( $id as $class_id )
		{
			$rowClass= new classes( $database );
			$rowClass->load( $class_id );
			
			if (!$rowClass->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listClass" );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelClass($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowClass = new classes( $database );
		$rowClass->bind(JRequest::get('post'));
		$rowClass->checkin();

		//$mainframe->redirect("index.php?option=$option&task=listClass" );
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
			$row = new classes( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listClass" );
					exit();
				}
			}
		}
		
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listClass" );
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
			$row = new classes( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listClass" );
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
		
		$query = 'UPDATE #__sdi_class' .
				' SET '.$column.' = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listClass" );
			exit();
		}

		if (count($cid) == 1) {
			$row = new classes( $db );
			$row->checkin($cid[0]);
		}

		$msg = JText::sprintf('State successfully changed');
				
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listClass" );
		exit();
	}
}
?>