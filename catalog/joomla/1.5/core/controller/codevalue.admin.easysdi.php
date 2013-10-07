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
		if (pressbutton != 'saveCodeValue' && pressbutton != 'applyCodeValue') {
			submitform( pressbutton );
			return;
		}

		var $ok = true;
		
		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'CATALOG_CODEVALUE_SUBMIT_NONAME', true ); ?>" );
			$ok = false;
		}
		if (form.task.value != "editCodeValue_Choice")
		{
			if (form.val.value == "") 
			{
				alert( "<?php echo JText::_( 'CATALOG_CODEVALUE_SUBMIT_NOVALUE', true ); ?>" );
				$ok = false;
			}
		}
		else
		{
			for(i=0;i<form.elements.length;i++)
			{
				el = form.elements[i];
				elName = "";
				if (el.name)
					elName = el.name;
				
				if (elName.match("^content_")=="content_")
				{
					if (el.value == "") 
					{
						alert( "<?php echo JText::_( 'CATALOG_CODEVALUE_SUBMIT_NOCONTENT', true ); ?>" );
						$ok = false;
						break;
					}
				}
			}

			if (form.attributetype_id.value == 9)
			{
				if (form.val.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_CODEVALUE_SUBMIT_NOVALUE', true ); ?>" );
					$ok = false;
				}
			}
		}
		
		if ($ok) 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 
class ADMIN_codevalue {
	function listCodeValue($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$attributeid = JRequest::getVar('attribute_id',0);
		
		$rowAttribute = new attribute( $db );
		$rowAttribute->load( $attributeid );
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowAttribute->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowAttribute->name);
			$mainframe->redirect("index.php?option=$option&task=listAttribute", $msg );
		}

		$rowAttribute->checkout($user->get('id'));
		
		
		$context	= $option.'.listCodeValue';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Filtering
		$filter_state = $mainframe->getUserStateFromRequest( 'filter_state',	'filter_state',	'',	'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "ordering" and $filter_order <> "created" and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		/*
		 * Add the filter specific information to the where clause
		 */
		$where = array();
		
		// State filter
		if ($filter_state) 
		{
			if ($filter_state == 'P')
				$where[] = 'published = 1';
			else if ($filter_state == 'U')
				$where[] = 'published =0';
		}
		
		// Build the where clause of the content record query
		$where = (count($where) ? implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_codevalue WHERE attribute_id=".$attributeid;					
		if ($where)
			$query .= " WHERE ".$where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT *  FROM #__sdi_codevalue WHERE attribute_id=".$attributeid;
		if ($where)
			$query .= ' AND '.$where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		// get list of published states for dropdown filter
		$lists['state'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');
		
		HTML_codevalue::listCodeValue($rows, $lists, $pagination, $option,  $filter_order_Dir, $filter_order, $attributeid);
	}
	
	function editCodeValue($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$attributeid = JRequest::getVar('attribute_id',0);
		
		$rowCodeValue = new codevalue( $database );
		$rowCodeValue->load( $id );
		
		$rowAttribute = new attribute( $database );
		$rowAttribute->load( $attributeid );
		if ($rowAttribute->attributetype_id == 9)
			ADMIN_CodeValue::editCodeValue_Choice($id, $option);
		else if ($rowAttribute->attributetype_id == 10)
			ADMIN_CodeValue::editCodeValue_Choice($id, $option);
		else
		{
			/*
			 * If the item is checked out we cannot edit it... unless it was checked
			 * out by the current user.
			 */
			if ( JTable::isCheckedOut($user->get('id'), $rowCodeValue->checked_out ))
			{
				$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowCodeValue->name);
				$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
			}
	
			$rowCodeValue->checkout($user->get('id'));
			
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
			$tableFields = $database->getTableFields("#__sdi_codevalue", false);
			
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
			
			// Langues � g�rer
			$languages = array();
			$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
			$languages = array_merge( $languages, $database->loadObjectList() );
			
			// Les labels
			$labels = array();
			foreach ($languages as $lang)
			{
				$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowCodeValue->guid."' AND language_id=".$lang->id);
				$label = $database->loadResult();
				
				$labels[$lang->id] = $label;
			}
				
			HTML_codevalue::editCodeValue($rowCodeValue, $unselected_accounts, $selected_accounts, $attributeid, $fieldsLength, $languages, $labels, $option);
		}
	}
	
	function editTextChoice($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$attributeid = JRequest::getVar('attribute_id',0);
		
		$rowCodeValue = new codevalue( $database );
		$rowCodeValue->load( $id );
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowCodeValue->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowCodeValue->name);
			$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
		}

		$rowCodeValue->checkout($user->get('id'));
		
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
		$tableFields = $database->getTableFields("#__sdi_codevalue", false);
		
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
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowCodeValue->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}
			
		HTML_codevalue::editTextChoice($rowCodeValue, $unselected_accounts, $selected_accounts, $attributeid, $fieldsLength, $languages, $labels, $option);
	}
	
	function editCodeValue_Choice($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$attributeid = JRequest::getVar('attribute_id',0);
		$rowAttribute = new attribute( $database );
		$rowAttribute->load( $attributeid );
		$attributetypeid =$rowAttribute->attributetype_id; 
		
		$rowCodeValue = new codevalue( $database );
		$rowCodeValue->load( $id );
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowCodeValue->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowCodeValue->name);
			$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
		}

		$rowCodeValue->checkout($user->get('id'));
		
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
		$tableFields = $database->getTableFields("#__sdi_codevalue", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false));
		
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
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Les titres
		$titles = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT title FROM #__sdi_translation WHERE element_guid='".$rowCodeValue->guid."' AND language_id=".$lang->id);
			$title = $database->loadResult();
			
			$titles[$lang->id] = $title;
		}
		
		// Les contenus
		$contents = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT content FROM #__sdi_translation WHERE element_guid='".$rowCodeValue->guid."' AND language_id=".$lang->id);
			$content = $database->loadResult();
			
			$contents[$lang->id] = $content;
		}
			
		HTML_codevalue::editCodeValue_Choice($rowCodeValue, $unselected_accounts, $selected_accounts, $attributeid, $attributetypeid, $fieldsLength, $languages, $titles, $contents, $option);
	}
	
	function saveCodeValue($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$rowCodeValue= new codevalue( $database );
		//print_r($_POST);
		if (!$rowCodeValue->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
			//exit();
		}		
		
		$rowAttribute = new attribute( $database );
		$rowAttribute->load( $_POST['attribute_id'] );
		
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowCodeValue->guid == null)
			$rowCodeValue->guid = helper_easysdi::getUniqueId();
		

		// Stocker la valeur "� la main", puisque le champ dans le formulaire d'�dition 
		// ne porte pas le nom qu'il faut (probl�me de mots cl�s)
		if ($rowAttribute->attributetype_id <> 10)
			$rowCodeValue->value = trim($_POST['val']);

		if (!$rowCodeValue->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
			//exit();
		}
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Supprimer tout ce qui avait été créé comme traductions jusqu'à présent pour ce code valeur
		$query = "delete from #__sdi_translation where element_guid='".$rowCodeValue->guid."'";
		$database->setQuery( $query);
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
	
		if ($rowAttribute->attributetype_id == 9 or $rowAttribute->attributetype_id == 10)
		{
			// Stocker les titres et les contenus
			foreach ($languages as $lang)
			{
				$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowCodeValue->guid."' AND language_id='".$lang->id."'");
				$total = $database->loadResult();
				
				if ($total > 0)
				{
					//Update
					$database->setQuery("UPDATE #__sdi_translation SET title='".addslashes(trim($_POST['title_'.$lang->code]))."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowCodeValue->guid."' AND language_id=".$lang->id);
					if (!$database->query())
						{	
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
							return false;
						}
					$database->setQuery("UPDATE #__sdi_translation SET content='".addslashes(trim($_POST['content_'.$lang->code]))."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowCodeValue->guid."' AND language_id=".$lang->id);
					if (!$database->query())
						{	
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
							return false;
						}
				}
				else
				{
					// Create
					$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, title, content, created, createdby) VALUES ('".$rowCodeValue->guid."', ".$lang->id.", '".addslashes(trim($_POST['title_'.$lang->code]))."', '".addslashes(trim($_POST['content_'.$lang->code]))."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
					if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
				}
			}
		}
		else
		{
			// Stocker les labels
			foreach ($languages as $lang)
			{
				$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowCodeValue->guid."' AND language_id='".$lang->id."'");
				$total = $database->loadResult();
				
				if ($total > 0)
				{
					//Update
					$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowCodeValue->guid."' AND language_id=".$lang->id);
					if (!$database->query())
						{	
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
							return false;
						}
				}
				else
				{
					// Create
					$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowCodeValue->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
					if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
				}
			}
		}
		
		// R�cup�rer toutes les relations avec les utilisateurs existantes
		$query = "SELECT * FROM #__sdi_account_codevalue WHERE codevalue_id=".$rowCodeValue->id;
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
				$rowAccountCodeValue= new account_codevalue($database);
				$rowAccountCodeValue->load($row->id);
				
				if (!$rowAccountCodeValue->delete()) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
					//exit();
				}
			}
		}
		
		// Stockage des relations avec les utilisateurs
		if (array_key_exists('selected', $_POST))
		{
			foreach($_POST['selected'] as $selected)
			{
				// Si la cl� du tableau des relations n'est pas encore dans la base, on l'ajoute
				if (!in_array($selected, $rows))
				{
					$rowAccountCodeValue= new account_codevalue($database);
					$rowAccountCodeValue->account_id=$selected;
					$rowAccountCodeValue->codevalue_id=$rowCodeValue>id;
					
					if (!$rowAccountCodeValue->store(false)) {			
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
						//exit();
					}
				}
			}
		}
		
		$rowCodeValue->checkin();
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyCodeValue' :
				// Reprendre en �dition l'objet
				TOOLBAR_codevalue::_EDIT();
				ADMIN_codevalue::editCodeValue($rowCodeValue->id,$option);
				break;

			case 'saveCodeValue' :
			default :
				break;
		}
	}
	
	function removeCodeValue($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit;
		}
		
		$attributeid = JRequest::getVar('attribute_id',0);
		
		foreach( $id as $codevalue_id )
		{
			$rowCodeValue= new codevalue( $database );
			$rowCodeValue->load( $codevalue_id );
			
			// Supprimer tout ce qui avait été créé comme traductions pour ce code valeur
			$query = "delete from #__sdi_translation where element_guid='".$rowCodeValue->guid."'";
			$database->setQuery( $query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		
			if (!$rowCodeValue->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelCodeValue($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		$attributeid = JRequest::getVar('attribute_id',0);
		
		// Check the attribute in if checked out
		$rowCodeValue = new codevalue( $database );
		$rowCodeValue->bind(JRequest::get('post'));
		$rowCodeValue->checkin();

		$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
	}
	
	/**
	* Cancels an edit operation
	*/
	function back($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();
		$attributeid = JRequest::getVar('attribute_id',0);
		
		// Check the attribute in if checked out
		$rowAttribute = new attribute( $database );
		$rowAttribute->load($attributeid);
		$rowAttribute->checkin();

		$mainframe->redirect("index.php?option=$option&task=listAttribute" );
	}
	
	
	function changeContent( $state = 0 )
	{
		global $mainframe;
		
		// Initialize variables
		$db		= & JFactory::getDBO();
		
		$cid = JRequest::getVar('cid', array());
		$attributeid = JRequest::getVar('attribute_id',0);
		
		JArrayHelper::toInteger($cid);
		$option	= JRequest::getCmd( 'option' );
		$task	= JRequest::getCmd( 'task' );
		$total	= count($cid);
		$cids	= implode(',', $cid);
		
		$query = 'UPDATE #__sdi_codevalue' .
				' SET published = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
			exit();
		}

		if (count($cid) == 1) {
			$row = new codevalue( $db );
			$row->checkin($cid[0]);
		}

		switch ($state)
		{
			case 1 :
				$msg = $total." ".JText::sprintf('Item(s) successfully Published');
				break;

			case 0 :
			default :
				$msg = $total." ".JText::sprintf('Item(s) successfully Unpublished');
				break;
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
		exit();
	}
	
	function saveOrder($option)
	{
		global $mainframe;

		// Initialize variables
		$db			= & JFactory::getDBO();
		$attributeid = JRequest::getVar('attribute_id',0);
		
		$cid		= JRequest::getVar( 'cid', array(0));
		$order		= JRequest::getVar( 'ordering', array (0));
		$total		= count($cid);
		
		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			// Instantiate an article table object
			$row = new codevalue( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
					exit();
				}
			
				// remember to updateOrder this group
				$condition = 'attribute_id = '.(int) $attributeid;
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
		$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
		exit();
	}
	
	function orderContent($direction, $option)
	{
		global $mainframe;

		// Initialize variables
		$db		= & JFactory::getDBO();
		$attributeid = JRequest::getVar('attribute_id',0);
		
		$cid	= JRequest::getVar( 'cid', array());

		if (isset( $cid[0] ))
		{
			$row = new codevalue( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction, 'attribute_id = '.(int) $attributeid);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$row->reorder();
		
		$mainframe->redirect("index.php?option=$option&task=listCodeValue&attribute_id=$attributeid" );
		exit();
	}
}
?>