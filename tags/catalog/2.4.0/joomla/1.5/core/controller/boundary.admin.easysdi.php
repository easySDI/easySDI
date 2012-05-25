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


class ADMIN_boundary {
	function listBoundary($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$filter	= null;
		
		$context	= $option.'.listBoundary';
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
			$filter_order <> "updated" )
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		
		//Add the filter specific information to the where clause
		$where = array();
		
		// Build the where clause of the content record query
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_boundary";					
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT b.*, bc.title as category_title FROM #__sdi_boundary b LEFT JOIN #__sdi_boundarycategory bc ON b.category_id = bc.id";
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_boundary::listBoundary($rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editBoundary($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$rowBoundary = new boundary( $database );
		$rowBoundary->load( $id );
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowBoundary->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowBoundary->name);
			$mainframe->redirect("index.php?option=$option&task=listBoundary", $msg );
		}

		$rowBoundary->checkout($user->get('id'));
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_boundary", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false) );
		
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
		
		// Langues à gérer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowBoundary->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			$labels[$lang->id] = $label;
		}
		
		// Titles
		$titles = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT title FROM #__sdi_translation WHERE element_guid='".$rowBoundary->guid."' AND language_id=".$lang->id);
			$title = $database->loadResult();
			$titles[$lang->id] = $title;
		}
		
		// Contents
		$contents = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT content FROM #__sdi_translation WHERE element_guid='".$rowBoundary->guid."' AND language_id=".$lang->id);
			$content = $database->loadResult();
			$contents[$lang->id] = $content;
		}
		
		//Categories
		$categories = array();
		$categories[] = JHTML::_('select.option',null, JText::_("CATALOG_PERIMETER_CATEGORY_LIST_CHOICE") );
		$database->setQuery( "SELECT id AS value, title as text FROM #__sdi_boundarycategory ORDER BY title" );
		$categories = array_merge( $categories, $database->loadObjectList() );
		
		//Parents
		$parents = array();
		$parents[] = JHTML::_('select.option',null, JText::_("CATALOG_PERIMETER_PARENT_LIST_CHOICE") );
		if($rowBoundary->category_id != null){
			$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_boundary WHERE category_id = (SELECT parent_id FROM #__sdi_boundarycategory WHERE id=$rowBoundary->category_id) ORDER BY name" );
			$parents = array_merge( $parents, $database->loadObjectList() );
		}
		HTML_boundary::editBoundary($rowBoundary, $fieldsLength, $languages, $labels, $titles, $contents,$categories,$parents, $option);
	}
	
	function saveBoundary($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$rowBoundary= new boundary( $database );
		
		if (!$rowBoundary->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listBoundary" );
			exit();
		}		
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowBoundary->guid == null)
			$rowBoundary->guid = helper_easysdi::getUniqueId();
		
		if ($rowBoundary->parent_id == '')
			$rowBoundary->parent_id = null;
		if ($rowBoundary->category_id == '')
			$rowBoundary->category_id = null;
		if ($rowBoundary->northbound == '')
			$rowBoundary->northbound = null;
		if ($rowBoundary->southbound == '')
			$rowBoundary->southbound = null;
		if ($rowBoundary->eastbound == '')
			$rowBoundary->eastbound = null;
		if ($rowBoundary->westbound == '')
			$rowBoundary->westbound = null;
		
		if (!$rowBoundary->store(true)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBoundary" );
			exit();
		}
		
		// Langues à gérer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
			
		foreach ($languages as $lang)
		{
			
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowBoundary->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0){//Update
				// Labels
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."',title='".addslashes($_POST['title_'.$lang->code])."', content='".addslashes($_POST['content_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowBoundary->guid."' AND language_id=".$lang->id);
				if (!$database->query()){	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}else{// Create
				//Labels
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label,title,content, created, createdby) VALUES ('".$rowBoundary->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."','".addslashes($_POST['title_'.$lang->code])."','".addslashes($_POST['content_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query()){	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		$rowBoundary->checkin();
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyBoundary' :
				// Reprendre en �dition l'objet
				TOOLBAR_boundary::_EDIT();
				ADMIN_boundary::editBoundary($rowBoundary->id,$option);
				break;

			case 'saveBoundary' :
			default :
				break;
		}
	}
	
	function removeBoundary($id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listBoundary" );
			exit;
		}
		foreach( $id as $boundary_id )
		{
			$rowBoundary= new boundary( $database );
			$rowBoundary->load( $boundary_id );
			
			if (!$rowBoundary->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listBoundary" );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelBoundary($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowBoundary= new boundary( $database );
		$rowBoundary->bind(JRequest::get('post'));
		$rowBoundary->checkin();

		$mainframe->redirect("index.php?option=$option&task=listBoundary" );
	}
	
	function saveOrder($option)
	{
		global $mainframe;

		// Initialize variables
		$db			= & JFactory::getDBO();

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
			$row = new boundary( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listBoundary" );
					exit();
				}
			}
		}
		
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listBoundary" );
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
			$row = new boundary( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listBoundary" );
		exit();
	}
	
	function listBoundaryCategory($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		$filter	= null;
	
		$context	= $option.'.listBoundaryCategory';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
	
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
	
	
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
	
		// Test si le filtre est valide
		if ($filter_order <> "id" and
				$filter_order <> "title" and
				$filter_order <> "updated" and
				$filter_order <> "alias")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
	
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
	
	
		//Add the filter specific information to the where clause
		$where = array();
	
		// Build the where clause of the content record query
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
	
		$query = "SELECT COUNT(*) FROM #__sdi_boundarycategory";
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
	
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
	
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__sdi_boundarycategory";
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
	
		HTML_boundary::listBoundaryCategory($rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editBoundaryCategory($id, $option)
	{
		$database =& JFactory::getDBO();
		$user = & JFactory::getUser();
	
		$rowBoundaryCategory = new boundarycategory( $database );
		$rowBoundaryCategory->load( $id );
	
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		* out by the current user.
		*/
		if ( JTable::isCheckedOut($user->get('id'), $rowBoundaryCategory->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowBoundary->name);
			$mainframe->redirect("index.php?option=$option&task=listBoundary", $msg );
		}
	
		$rowBoundaryCategory->checkout($user->get('id'));
	
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_boundarycategory", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false) );
	
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
	
		// Langues à gérer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
	
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowBoundaryCategory->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
				
			$labels[$lang->id] = $label;
		}
		
		//Parents
		$parents = array();
		$parents[] = JHTML::_('select.option',null, JText::_("CATALOG_PERIMETER_CATEGORY_PARENT_LIST_CHOICE") );
		$database->setQuery( "SELECT id AS value, title as text FROM #__sdi_boundarycategory WHERE id <> $rowBoundaryCategory->id  ORDER BY title" );
		$parents = array_merge( $parents, $database->loadObjectList() );
	
		HTML_boundary::editBoundaryCategory($rowBoundaryCategory, $fieldsLength, $languages, $labels,$parents, $option);
	}
	
	function saveBoundaryCategory($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO();
		$user =& JFactory::getUser();
	
		$rowBoundaryCategory= new boundarycategory( $database );
	
		if (!$rowBoundaryCategory->bind( $_POST )) {
	
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBoundaryCategory" );
			exit();
		}
	
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowBoundaryCategory->guid == null)
			$rowBoundaryCategory->guid = helper_easysdi::getUniqueId();
	
		if ($rowBoundaryCategory->parent_id == '')
			$rowBoundaryCategory->parent_id = null;
		
		if (!$rowBoundaryCategory->store(true)) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBoundaryCategory" );
			exit();
		}
	
		// Langues à gérer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
	
	
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowBoundaryCategory->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
				
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['modified']."', updatedby=".$_POST['modified_by']." WHERE element_guid='".$rowBoundaryCategory->guid."' AND language_id=".$lang->id);
				if (!$database->query())
				{
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowBoundaryCategory->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
	
		$rowBoundaryCategory->checkin();
	
		// Au cas où on sauve avec Apply, recharger la page
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyBoundaryCategory' :
				// Reprendre en �dition l'objet
				TOOLBAR_boundary::_EDIT();
				ADMIN_boundary::editBoundaryCategory($rowBoundaryCategory->id,$option);
				break;
	
			case 'saveBoundaryCategory' :
			default :
				break;
		}
	}
	function cancelBoundaryCategory($option)
	{
		global $mainframe;
	
		// Initialize variables
		$database = & JFactory::getDBO();
	
		// Check the attribute in if checked out
		$rowBoundaryCategory= new boundarycategory( $database );
		$rowBoundaryCategory->bind(JRequest::get('post'));
		$rowBoundaryCategory->checkin();
	
		$mainframe->redirect("index.php?option=$option&task=listBoundaryCategory" );
	}
	
	function removeBoundaryCategory($id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO();
	
		if (!is_array( $id ) || count( $id ) < 1) {
			$mainframe->enqueueMessage(JText::_("CATALOG_NO_ENTRY_SELECTED_ERROR"),"error");
			$mainframe->redirect("index.php?option=$option&task=listBoundaryCategory" );
			exit;
		}
		foreach( $id as $boundarycategory_id )
		{
			$rowBoundaryCategory= new boundarycategory( $database );
			$rowBoundaryCategory->load( $boundarycategory_id );
				
			if (!$rowBoundaryCategory->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listBoundaryCategory" );
				exit();
			}
		}
	}
	
	function getParentPerimeterList($category_id){
		$database=& JFactory::getDBO();
		
		$parents = array();
		$parents[] = JHTML::_('select.option',null, JText::_("CATALOG_PERIMETER_PARENT_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_boundary WHERE category_id =  (SELECT parent_id FROM #__sdi_boundarycategory WHERE id = $category_id) ORDER BY name" );
		$parents = array_merge( $parents, $database->loadObjectList() );
	
		$encoded = json_encode($parents);
		echo $encoded;
		die();
	}
}
?>