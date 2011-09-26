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

class ADMIN_context {
	
	function listContext($option) {
		global  $mainframe;
		$db =& JFactory::getDBO();
		$context	= 'listContext';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( $option.$context.".filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.$context.".filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" 
			and $filter_order <> "name" 
			and $filter_order <> "code"
			and $filter_order <> "ordering" 
			and $filter_order <> "description" 
			and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		
		$query = "SELECT COUNT(*) FROM #__sdi_context";					
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__sdi_context";
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			//exit();
		}
		
		
		HTML_context::listContext($rows, $pagination, $filter_order_Dir, $filter_order, $option);

	}

	function editContext($id, $option)
	{
		?>
		<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;
				if (pressbutton != 'saveContext' && pressbutton != 'applyContext') {
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
				var sortEmpty = 0;
				sortfields = document.getElementById('sortfields');
				fields = sortfields.getElementsByTagName('input');
				
				for (var i = 0; i < fields.length; i++)
				{
					if (fields.item(i).value == "")
						sortEmpty=1;
				}
				
				// do field validation
				if (form.name.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_CONTEXT_SUBMIT_NONAME', true ); ?>" );
				}
				else if (form.code.value == "")
				{
					alert( "<?php echo JText::_( 'CATALOG_CONTEXT_SUBMIT_NOCODE', true ); ?>" );
				}
				else if (sortEmpty > 0) 
				{
					alert( "<?php echo JText::_( 'CATALOG_CONTEXT_SUBMIT_NOSORTFIELDS', true ); ?>" );
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
		
		$row = new context( $database );
		$row->load( $id );

		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $row->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $row->name);
			$mainframe->redirect("index.php?option=$option&task=listContext", $msg );
		}

		$row->checkout($user->get('id'));
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_context", false);
		
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
		
		$listObjectTypes = array();
		/*$listObjectTypes[] = JHTML::_('select.option', '0', JText::_('CATALOG_CONTEXT_SELECT_OBJECTTYPE'), 'value', 'text');
		$database->setQuery( "SELECT id as value, name as text FROM #__sdi_objecttype ORDER BY name" );
		$listObjectTypes= array_merge( $listObjectTypes, $database->loadObjectList() );
		*/	
		
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
		$sortfields = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT ogcsearchsorting FROM #__sdi_context_sort WHERE context_id='".$row->id."' AND language_id=".$lang->id);
			$sortfield = $database->loadResult();
			
			$sortfields[$lang->id] = $sortfield;
		}
		
		$objecttypes = array();
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_objecttype ORDER BY name" );
		$objecttypes = array_merge( $objecttypes, $database->loadObjectList() );
		
		$selected_objecttypes = array();
		if ($row->id <> 0)
		{
			$database->setQuery( "SELECT objecttype_id FROM #__sdi_context_objecttype WHERE context_id=".$row->id);
			$selected_objecttypes = array_merge( $selected_objecttypes, $database->loadResultArray() );
		}
		
		HTML_context::editContext($row, $listObjectTypes, $fieldsLength, $languages, $labels, $sortfields, $objecttypes, $selected_objecttypes, $option);
	}
	
	function saveContext($option)
	{	
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		
		$rowContext= new context( $database );
		
		if (!$rowContext->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listContext" );
			exit();
		}		
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowContext->guid == null)
			$rowContext->guid = helper_easysdi::getUniqueId();
		
		if (!$rowContext->store(true)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listContext" );
			exit();
		}
		
		//Cr�er les crit�res syst�me du contexte pour la gestion des tabs
		if ($_POST['id'] == 0)
		{
			// R�cup�rer tous les crit�res syst�mes
			$searchcriteriaList= array();
			$query = "SELECT * 
					  FROM #__sdi_searchcriteria 
					  WHERE criteriatype_id=1";
			$database->setQuery($query);
			$searchcriteriaList = $database->loadObjectList();
			
			foreach ($searchcriteriaList as $searchcriteria)
			{
				// Cr�er la relation crit�re/contexte
				$query = "INSERT INTO #__sdi_searchcriteria_tab (searchcriteria_id, context_id, tab_id) VALUES (".$searchcriteria->id.", ".$rowContext->id.", 1)";
				$database->setQuery( $query);
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
			}
		}
			
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
	
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowContext->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowContext->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowContext->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
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
			$database->setQuery("SELECT count(*) FROM #__sdi_context_sort WHERE context_id='".$rowContext->id."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_context_sort SET ogcsearchsorting='".addslashes($_POST['sortfield_'.$lang->code])."' WHERE context_id='".$rowContext->id."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_context_sort (context_id, language_id, ogcsearchsorting) VALUES ('".$rowContext->id."', ".$lang->id.", '".addslashes($_POST['sortfield_'.$lang->code])."')");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// Sauvegarde des contextes li�s � la relation
		$objecttypes = array();
		$objecttypes = $_POST['objecttypes'];
		
		// Supprimer tout ce qui avait �t� cr�� jusqu'� pr�sent pour cette relation
		$query = "delete from #__sdi_context_objecttype where context_id=".$rowContext->id;
		$database->setQuery( $query);
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		// Ne recr�er les liens que si la relation doit �tre un filtre de recherche
		foreach($objecttypes as $objecttype)
		{
			$rowContext_Objecttype= new contextobjecttype( $database );
			$rowContext_Objecttype->context_id=$rowContext->id;
			$rowContext_Objecttype->objecttype_id=$objecttype;
			
			if (!$rowContext_Objecttype->store(false)) {	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listContext" );
				exit();
			}
		}
			
		$rowContext->checkin();
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyContext' :
				// Reprendre en �dition l'objet
				TOOLBAR_context::_EDIT();
				ADMIN_context::editContext($rowContext->id,$option);
				break;

			case 'saveContext' :
			default :
				break;
		}
	}
	
	function deleteContext($cid, $option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listContext" );
			exit;
		}
		
		foreach( $cid as $context_id )
		{
			// Supprimer tous les r�f�rencements dans les relations
			$selected_contexts = array();
			$database->setQuery( "DELETE FROM #__sdi_relation_context where context_id=".$context_id);
			if (!$database->query())
			{	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			// Supprimer tout ce qui avait �t� cr�� jusqu'� pr�sent pour cette relation
			$query = "delete from #__sdi_context_objecttype where context_id=".$context_id;
			$database->setQuery( $query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			// Supprimer les crit�res de recherche directement li�s � cette relation
			$query = "delete from #__sdi_searchcriteria_tab WHERE context_id = ".$context_id;
			$database->setQuery( $query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			$query = "delete from #__sdi_searchcriteria where context_id=".$context_id;
			$database->setQuery( $query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			// Supprimer le contexte
			$rowContext= new context( $database );
			$rowContext->load( $context_id );
			
			if (!$rowContext->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listContext" );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelContext($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();
		
		// Check the attribute in if checked out
		$rowContext = new context( $database );
		$rowContext->bind($_POST);
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

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			// Instantiate an article table object
			$row = new context( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listContext" );
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listContext" );
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
			$row = new context( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listContext" );
		exit();
	}
}
?>