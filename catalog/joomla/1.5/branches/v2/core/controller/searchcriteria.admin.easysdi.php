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
			and $filter_order <> "ogcsearchfilter" 
			and $filter_order <> "criteriatype_label" 
			and $filter_order <> "simpletab" 
			and $filter_order <> "advancedtab" 
			and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		$query = "SELECT COUNT(*) FROM #__sdi_searchcriteria sc LEFT OUTER JOIN #__sdi_relation_context rc ON rc.relation_id=sc.relation_id WHERE rc.context_id IS NULL OR rc.context_id=".$context_id;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT sc.*, c.name as criteriatype_name, c.label as criteriatype_label FROM #__sdi_searchcriteria sc LEFT OUTER JOIN #__sdi_relation_context rc ON rc.relation_id=sc.relation_id INNER JOIN #__sdi_list_criteriatype c ON c.id=sc.criteriatype_id WHERE rc.context_id IS NULL OR rc.context_id=".$context_id;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			//exit();
		}
		
		
		HTML_searchcriteria::listSearchCriteria($rows, $pagination, $filter_order_Dir, $filter_order, $context_id, $option);

	}

	function editSearchCriteria($id, $option)
	{
		?>
		<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;
				if (pressbutton != 'saveSearchCriteria' && pressbutton != 'applySearchCriteria') {
					submitform( pressbutton );
					return;
				}
		
				// Récuperer tous les labels et contrôler qu'ils soient saisis
				var labelEmpty = 0;
				labels = document.getElementById('labels');
				fields = labels.getElementsByTagName('input');
				
				for (var i = 0; i < fields.length; i++)
				{
					if (fields.item(i).value == "")
						labelEmpty=1;
				}
				
				// do field validation
				if (form.name.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_CONTEXT_SUBMIT_NONAME', true ); ?>" );
				}
				else if (labelEmpty > 0) 
				{
					alert( "<?php echo JText::_( 'CATALOG_CONTEXT_SUBMIT_NOLABELS', true ); ?>" );
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
		$user = & JFactory::getUser();

		$context_id = JRequest::getVar('context_id',0);
		
		$row = new searchcriteria( $database );
		$row->load( $id );
		
		if ($row->id <>0 and $row->criteriatype_id == 2)
		{
			$criteriatype = new criteriatype( $database );
			$criteriatype->load( $row->criteriatype_id );
			$mainframe->enqueueMessage(JText::sprintf("CATALOG_SEARCHCRITERIA_ISSYSTEM_ERROR_MSG", JText::_($criteriatype->label)),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
			exit();
		}
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $row->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $row->name);
			$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id, $msg );
		}

		$row->checkout($user->get('id'));
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_searchcriteria", false);
		
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
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$row->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}
		
		// Onglets
		$tab = array();
		$tab[] = JHTML::_('select.option','0', JText::_("CATALOG_SEARCHCRITERIA_CHOICE_NOTAB") );
		$tab[] = JHTML::_('select.option','1', JText::_("CATALOG_SEARCHCRITERIA_CHOICE_SIMPLETAB") );
		$tab[] = JHTML::_('select.option','2', JText::_("CATALOG_SEARCHCRITERIA_CHOICE_ADVANCEDTAB") );
		
		if ($row->simpletab == 1)
			$selectedTab = 1;
		else if ($row->advancedtab == 1)
			$selectedTab = 2;
		else
			$selectedTab = 0;
		
		if ($row->id == 0 or $row->criteriatype_id == 3) // Critère OGC 
			HTML_searchcriteria::editOGCSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $context_id, $option);
		else if ($row->criteriatype_id == 1) // Critère system
			HTML_searchcriteria::editSystemSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $context_id, $option);
		
	}
	
	function saveSearchCriteria($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		$context_id = JRequest::getVar('context_id',0);
		
		$rowSearchCriteria= new searchcriteria( $database );
		
		if (!$rowSearchCriteria->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
			exit();
		}		
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowSearchCriteria->guid == null)
			$rowSearchCriteria->guid = helper_easysdi::getUniqueId();
		
		// Onglet
		if ($_POST['tab'] == 0)
		{
			$rowSearchCriteria->simpletab = 0;
			$rowSearchCriteria->advancedtab = 0;
		}
		else if ($_POST['tab'] == 1)
		{
			$rowSearchCriteria->simpletab = 1;
			$rowSearchCriteria->advancedtab = 0;
		}
		else if ($_POST['tab'] == 2)
		{	
			$rowSearchCriteria->simpletab = 0;
			$rowSearchCriteria->advancedtab = 1;
		}
			
		
		if (!$rowSearchCriteria->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
			exit();
		}
		
		// Langues à gérer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
	
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowSearchCriteria->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".str_replace("'","\'",$_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowSearchCriteria->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowSearchCriteria->guid."', ".$lang->id.", '".str_replace("'","\'",$_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		$rowSearchCriteria->checkin();
		
		// Au cas où on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applySearchCriteria' :
				// Reprendre en édition l'objet
				TOOLBAR_searchcriteria::_EDIT();
				ADMIN_searchcriteria::editSearchCriteria($rowSearchCriteria->id,$option);
				break;

			case 'saveSearchCriteria' :
			default :
				break;
		}
	}
	
	function removeSearchCriteria($id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 
		$context_id = JRequest::getVar('context_id',0);
		
		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('Sï¿½lectionnez un enregistrement ï¿½ supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("Sï¿½lectionnez un enregistrement ï¿½ supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
			exit();
		}
		foreach( $id as $searchcriteria_id )
		{
			$rowSearchCriteria= new searchcriteria( $database );
			$rowSearchCriteria->load( $searchcriteria_id );

			if ($rowSearchCriteria->criteriatype_id == 3)
			{
				if (!$rowSearchCriteria->delete()) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
					exit();
				}
			}
			else
			{
				$criteriatype = new criteriatype( $database );
				$criteriatype->load( 3 );
				$mainframe->enqueueMessage(JText::sprintf("CATALOG_SEARCHCRITERIA_DELETE_ISSYSTEM_MSG", JText::_($criteriatype->label)),"error");
				$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelSearchCriteria($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();
		$context_id = JRequest::getVar('context_id',0);
		
		// Check the attribute in if checked out
		$rowSearchCriteria= new searchcriteria( $database );
		$rowSearchCriteria->bind(JRequest::get('post'));
		$rowSearchCriteria->checkin();

		$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
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
				
				// remember to updateOrder this group
				$condition = 'simpletab = '.(int) $row->simpletab.
							 ' AND advancedtab = '.(int) $row->advancedtab;
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
			$row->move($direction, 'simpletab = '.(int) $row->simpletab.' AND advancedtab = '.(int) $row->advancedtab);
			
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