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
		if (pressbutton != 'saveImportRef' && pressbutton != 'applyImportRef') {
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
		
		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'CATALOG_IMPORTREF_SUBMIT_NONAME', true ); ?>" );
		}
		else if (form.xslfile.value == "") 
		{
		alert( "<?php echo JText::_( 'CATALOG_IMPORTREF_SUBMIT_NOXSL', true ); ?>" );
		} 
		else if (getSelectedValue('adminForm','importtype_id') < 1)
		{
			alert( "<?php echo JText::_( 'CATALOG_IMPORTREF_SUBMIT_NOIMPORTTYPE', true ); ?>" );
		}
		else if (labelEmpty > 0) 
		{
			alert( "<?php echo JText::_( 'CATALOG_IMPORTREF_SUBMIT_NOLABELS', true ); ?>" );
		}
		else 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 
class ADMIN_importref {
	function listImportRef($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$filter	= null;
		
		$context	= $option.'.listImportRef';
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
			$filter_order <> "xslfile" and
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
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_importref";					
		$query .= $where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__sdi_importref";
		$query .= $where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_importref::listImportRef(&$rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editImportRef($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$rowImportRef = new importref( $database );
		$rowImportRef->load( $id );
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowImportRef->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowImportRef->name);
			$mainframe->redirect("index.php?option=$option&task=listImportRef", $msg );
		}

		$rowImportRef->checkout($user->get('id'));
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_importref", false);
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
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowImportRef->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}
		
		$importtypeList= array();
		$importtypeList[] = JHTML::_('select.option','0', JText::_("CATALOG_IMPORTREF_IMPORTTYPE_SELECT"));
		$database->setQuery( "SELECT id as value, label as text FROM #__sdi_list_importtype" );
		$importtypeList = array_merge( $importtypeList, $database->loadObjectList() );
		
		helper_easysdi::alter_array_value_with_JTEXT_($importtypeList);
		
		$style = "display:none";
		if ($rowImportRef->importtype_id == 2)
			$style = "display:inline";
		
		HTML_importref::editImportRef($rowImportRef, $fieldsLength, $languages, $labels, $importtypeList, $style, $option);
	}
	
	function saveImportRef($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$rowImportRef= new importref( $database );
		
		if (!$rowImportRef->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listImportRef" );
			exit();
		}		
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowImportRef->guid == null)
			$rowImportRef->guid = helper_easysdi::getUniqueId();
		
		if (!$rowImportRef->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listImportRef" );
			exit();
		}
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
	
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowImportRef->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowImportRef->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowImportRef->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		$rowImportRef->checkin();
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyImportRef' :
				// Reprendre en �dition l'objet
				TOOLBAR_importref::_EDIT();
				ADMIN_importref::editImportRef($rowImportRef->id,$option);
				break;

			case 'saveImportRef' :
			default :
				break;
		}
	}
	
	function removeImportRef($id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listImportRef" );
			exit;
		}
		foreach( $id as $importref_id )
		{
			$rowImportRef= new importref( $database );
			$rowImportRef->load( $importref_id );
			
			if (!$rowImportRef->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listImportRef" );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelImportRef($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowImportRef= new importref( $database );
		$rowImportRef->bind(JRequest::get('post'));
		$rowImportRef->checkin();

		$mainframe->redirect("index.php?option=$option&task=listImportRef" );
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
			$row = new importref( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listImportRef" );
					exit();
				}
			}
		}
		
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listImportRef" );
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
			$row = new importref( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listImportRef" );
		exit();
	}
}
?>