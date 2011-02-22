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

class ADMIN_searchcriteria {
	
	function listSearchCriteria($option) {
		global  $mainframe;
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
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
			and $filter_order <> "tab_label" 
			and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		// R�cup�rer les crit�res syst�me ou ceux associ�s � ce contexte
		$query = "SELECT COUNT(*) FROM #__sdi_searchcriteria sc LEFT OUTER JOIN #__sdi_relation_context rc ON rc.relation_id=sc.relation_id WHERE sc.criteriatype_id=1 OR (sc.criteriatype_id=3 AND sc.context_id =".$context_id.") OR (sc.criteriatype_id=2 AND rc.context_id=".$context_id.")";
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT sc.*, sc.label as system_label, t.label as csw_label, c.name as criteriatype_name, c.label as criteriatype_label, tab.id as tab_id, tab.code as tab_code, tab.label as tab_label 
				  FROM #__sdi_searchcriteria sc 
				  LEFT OUTER JOIN #__sdi_relation_context rc ON rc.relation_id=sc.relation_id 
				  INNER JOIN #__sdi_list_criteriatype c ON c.id=sc.criteriatype_id 
				  LEFT OUTER JOIN #__sdi_searchcriteria_tab sc_tab ON (sc_tab.searchcriteria_id=sc.id AND sc_tab.context_id=".$context_id.")
				  LEFT OUTER JOIN #__sdi_list_searchtab tab ON tab.id=sc_tab.tab_id 
				  LEFT OUTER JOIN #__sdi_translation t ON t.element_guid=sc.guid
				  LEFT OUTER JOIN #__sdi_language l ON t.language_id=l.id
				  LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
				  WHERE (sc.criteriatype_id=1 AND cl.code = '".$language->_lang."')
				  		OR (sc.criteriatype_id=3 AND sc.context_id =".$context_id." AND cl.code = '".$language->_lang."') 
				  		OR (sc.criteriatype_id=2 AND rc.context_id=".$context_id." )"
					  ;
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
		
				// R�cuperer tous les labels et contr�ler qu'ils soient saisis
				var labelEmpty = 0;
				labels = document.getElementById('labels');
				fields = labels.getElementsByTagName('input');
				
				for (var i = 0; i < fields.length; i++)
				{
					if (fields.item(i).value == "")
						labelEmpty=1;
				}
				
				// R�cuperer tous les champs de tri et contr�ler qu'ils soient saisis
				var filterEmpty = 0;
				filterfields = document.getElementById('filterfields');
				fields = filterfields.getElementsByTagName('input');
				
				for (var i = 0; i < fields.length; i++)
				{
					if (fields.item(i).value == "")
						filterEmpty=1;
				}
				
				// do field validation
				if (form.name.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_CONTEXT_SUBMIT_NONAME', true ); ?>" );
				}
				else if (filterEmpty > 0) 
				{
					alert( "<?php echo JText::_( 'CATALOG_CONTEXT_SUBMIT_NOFILTERFIELDS', true ); ?>" );
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
		$language =& JFactory::getLanguage();
		
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
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_searchcriteria", false);
		
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
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_context_sc_filter", false);
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caract�res
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
		
		// Langues � g�rer
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
		
		// Champs de tri
		$filterfields = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT ogcsearchfilter FROM #__sdi_context_sc_filter WHERE context_id='".$context_id."' AND searchcriteria_id='".$row->id."' AND language_id=".$lang->id);
			$filterfield = $database->loadResult();
			
			$filterfields[$lang->id] = $filterfield;
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
		
		$tabList= array();
		$tabList[] = JHTML::_('select.option','0', JText::_("CATALOG_SEARCHCRITERIA_CHOICE_NOTAB") );
		$database->setQuery( "SELECT id as value, label as text FROM #__sdi_list_searchtab" );
		$tabList = array_merge( $tabList, $database->loadObjectList() );
		
		helper_easysdi::alter_array_value_with_JTEXT_($tabList);
		
		// get list of rendertypes for dropdown list
		$rendertypes = array();
		$rendertypes[] = JHTML::_('select.option','0', JText::_("EASYSDI_RENDERTYPE_LIST") );
		$database->setQuery( "SELECT rt.id AS value, rt.label as text 
							  FROM #__sdi_list_rendertype rt 
							  INNER JOIN #__sdi_list_rendercriteriatype rct ON rt.id = rct.rendertype_id 
							  INNER JOIN #__sdi_list_criteriatype ct ON rct.criteriatype_id=ct.id
							  WHERE ct.code='csw'
							  ORDER BY rt.label" );
		$rendertypes = array_merge( $rendertypes, $database->loadObjectList() );
		
		helper_easysdi::alter_array_value_with_JTEXT_($rendertypes);
		
		$tab_id = 0;
		$database->setQuery("SELECT tab_id FROM #__sdi_searchcriteria_tab WHERE searchcriteria_id=".$row->id." AND context_id=".$context_id);
		$tab_id = $database->loadResult();
		
		if ($row->id == 0 or $row->criteriatype_id == 3) // Crit�re OGC 
			HTML_searchcriteria::editOGCSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $filterfields, $context_id, $tabList, $tab_id, $rendertypes, $option);
		else if ($row->criteriatype_id == 1) // Crit�re system
			HTML_searchcriteria::editSystemSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $context_id, $tabList, $tab_id, $option);
		
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
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowSearchCriteria->guid == null)
			$rowSearchCriteria->guid = helper_easysdi::getUniqueId();
		
		// Onglet
		/*
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
		*/
			
		// Si le crit�re de recherche est de type CSW, indiquer le contexte associ�
		if ($rowSearchCriteria->criteriatype_id == 3)
		{
			$rowSearchCriteria->context_id = $context_id;
			
			if ($rowSearchCriteria->rendertype_id == 0)
				$rowSearchCriteria->rendertype_id = 5; // Rendu textbox par d�faut	
		}
		else
		{
			$rowSearchCriteria->context_id = null;
			$rowSearchCriteria->rendertype_id = null;
		}
		
		if (!$rowSearchCriteria->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
			exit();
		}
		
		// Stocker le tab si on cr�e le crit�re
		if ($_POST['id'] == 0)
		{
			$tab_id = $_POST['tabList'];
			if ($tab_id == 0)
				$tab_id = 'NULL';
			
			$query = "INSERT INTO #__sdi_searchcriteria_tab (searchcriteria_id, context_id, tab_id) VALUES (".$rowSearchCriteria->id.", ".$context_id.", ".$tab_id.")";
			$database->setQuery( $query);
			if (!$database->query())
			{	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
		else
		{
			$tab_id = $_POST['tabList'];
			if ($tab_id == 0)
				$tab_id = 'NULL';
			
			$query = "UPDATE #__sdi_searchcriteria_tab SET tab_id=".$tab_id." WHERE searchcriteria_id=".$rowSearchCriteria->id." AND context_id=".$context_id;
			$database->setQuery( $query);
			if (!$database->query())
			{	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}		
		
		// Langues � g�rer
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
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowSearchCriteria->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowSearchCriteria->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// Stocker les champs de tri par langue
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_context_sc_filter WHERE context_id='".$context_id."' AND searchcriteria_id='".$rowSearchCriteria->id."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_context_sc_filter SET ogcsearchfilter='".addslashes($_POST['filterfield_'.$lang->code])."' WHERE id='".$context_id."' AND searchcriteria_id='".$rowSearchCriteria->id."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_context_sc_filter (searchcriteria_id, context_id, language_id, ogcsearchfilter) VALUES ('".$rowSearchCriteria->id."', '".$context_id."', ".$lang->id.", '".addslashes($_POST['filterfield_'.$lang->code])."')");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		$rowSearchCriteria->checkin();
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applySearchCriteria' :
				// Reprendre en �dition l'objet
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
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
			exit();
		}
		foreach( $id as $searchcriteria_id )
		{
			$rowSearchCriteria= new searchcriteria( $database );
			$rowSearchCriteria->load( $searchcriteria_id );

			
			// Supprimer les labels 
			$query = "DELETE FROM #__sdi_translation WHERE element_guid= '".$rowSearchCriteria->guid."'";
			$database->setQuery( $query);
			if (!$database->query())
			{	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
				
			// Crit�re CSW
			if ($rowSearchCriteria->criteriatype_id == 3)
			{
				// Supprimer les champs de recherche 
				$query = "DELETE FROM #__sdi_context_sc_filter WHERE searchcriteria_id = ".$searchcriteria_id." AND context_id = ".$context_id;
				$database->setQuery( $query);
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				
				// Supprimer le positionnement dans les tabs 
				$query = "DELETE FROM #__sdi_searchcriteria_tab WHERE searchcriteria_id = ".$searchcriteria_id;
				$database->setQuery( $query);
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				
				if (!$rowSearchCriteria->delete()) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
					exit();
				}
			}
			else
			{
				$criteriatype = new criteriatype( $database );
				$criteriatype->load( 3 ); // Crit�re CSW
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
		
		// R�cup�rer les �tats du listing des objets, pour �viter que les �tats des versions soient utilis�s
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
				
				// TODO: Doit être corrigé car désormais le tab n'est plus stocké
				// dans ces colonnes mais dans la table #__sdi_searchcriteria_tab.
				// La meilleur façon de corriger ça est de stocker désormais l'ordre
				// dans la table #__sdi_searchcriteria_tab, afin de pouvoir avoir un ordre
				// différent pour le critère selon qu'il est dans un contexte ou dans un autre.
				
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
	
	function tab( &$row, $i)
	{
		$color_tab = 'style="color: black;"';
		if ( !$row->tab_id )  { // Aucun tab
			//$color_tab = 'style="color: green;"';
			$task_tab = "searchcriteria_tab_simple";
			$text_tab = JText::_( "CATALOG_SEARCHTAB_NONE");
		} else if ( $row->tab_id == 1 ) { // tab simple
			//$color_tab = 'style="color: red;"';
			$task_tab = "searchcriteria_tab_advanced";
			$text_tab = JText::_( $row->tab_label );
		} else { // tab avanc�
			//$color_tab = 'style="color: black;"';
			$task_tab = "searchcriteria_tab_none";
			$text_tab = JText::_( $row->tab_label );
		}
		
		
		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task_tab .'\')" '. $color_tab .'>
		'. $text_tab .'</a>'
		;

		return $href;
	}
	
	function tabSet($tab_id)
	{
		global $mainframe;
		$option	= JRequest::getCmd( 'option' );
		$task	= JRequest::getCmd( 'task' );
		$context_id = JRequest::getCmd( 'context_id' );
		
		// Initialize variables
		$db		= & JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$cid	= $cid[0];
		
		// Update the tab of the searchcriteria
		$rowSearchCriteria = new searchcriteria( $db );
		$rowSearchCriteria->load($cid);
		
		if ($tab_id == 0)
			$tab_id="NULL";

		$searchcriteriaCount=0;
		$query = "SELECT COUNT(*) FROM #__sdi_searchcriteria_tab WHERE searchcriteria_id = ".$rowSearchCriteria->id." AND context_id=".$context_id;
		$db->setQuery( $query);
		$searchcriteriaCount = $db->loadResult();
				
		if ($searchcriteriaCount == 0)
		{
			$query = "INSERT INTO #__sdi_searchcriteria_tab (searchcriteria_id, context_id, tab_id) VALUES (".$rowSearchCriteria->id.", ".$context_id.", ".$tab_id.")";
			$db->setQuery( $query);
			if (!$db->query())
			{	
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		else
		{
			$query = "UPDATE #__sdi_searchcriteria_tab SET tab_id=".$tab_id." WHERE searchcriteria_id=".$rowSearchCriteria->id." AND context_id=".$context_id;
			$db->setQuery( $query);
			if (!$db->query())
			{	
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		
		$mainframe->redirect("index.php?option=$option&task=listSearchCriteria&context_id=".$context_id );
	}
}
?>