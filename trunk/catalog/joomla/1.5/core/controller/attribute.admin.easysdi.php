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
		if (pressbutton != 'saveAttribute' && pressbutton != 'applyAttribute') {
			submitform( pressbutton );
			return;
		}

		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'CATALOG_ATTRIBUTE_SUBMIT_NONAME', true ); ?>" );
		}
		else if (getSelectedValue('adminForm','namespace_id') < 1) 
		{
			alert( "<?php echo JText::_( 'CATALOG_ATTRIBUTE_SUBMIT_NONAMESPACE', true ); ?>" );
		}
		else if (form.isocode.value == "") 
		{
			alert( "<?php echo JText::_( 'CATALOG_ATTRIBUTE_SUBMIT_NOISOCODE', true ); ?>" );
		} 
		else if (getSelectedValue('adminForm','attributetype_id') == 6 && getSelectedValue('adminForm','listnamespace_id') < 1) 
		{
			alert( "<?php echo JText::_( 'CATALOG_ATTRIBUTE_SUBMIT_NOLISTNAMESPACE', true ); ?>" );
		} 
		else if (getSelectedValue('adminForm','attributetype_id') < 1) 
		{
			alert( "<?php echo JText::_( 'CATALOG_ATTRIBUTE_SUBMIT_NOATTRIBUTETYPE', true ); ?>" );
		}
		else 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 
class ADMIN_attribute {
	function listAttribute($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$filter	= null;
		
		$context	= $option.'.listAttribute';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Filtering
		$filter_attributetype_id = $mainframe->getUserStateFromRequest( 'filter_attributetype_id',	'filter_attributetype_id',	-1,	'int' );
		$searchAttribute				= $mainframe->getUserStateFromRequest( 'searchAttribute', 'searchAttribute', '', 'string' );
		$searchAttribute				= JString::strtolower($searchAttribute);
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "attributetype_id" and $filter_order <> "attribute_isocode" and  $filter_order <> "description" and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		if ($filter_order <> "attribute_isocode")
			$orderby 	= ' order by a.'. $filter_order .' '. $filter_order_Dir;
		else
			$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		
		/*
		 * Add the filter specific information to the where clause
		 */
		$where = array();
		// RelationType filter
		if ($filter_attributetype_id > 0) {
			$where[] = 'a.attributetype_id = ' . (int) $filter_attributetype_id;
		}
		
		// Keyword filter
		if ($searchAttribute) {
			$where[] = '(a.id LIKE '. (int) $searchAttribute .
				' OR LOWER( a.name ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchAttribute, true ).'%', false )
				//.' OR LOWER( a.isocode ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchAttribute, true ).'%', false ) 
				.')';
		}
		
		// Build the where clause of the content record query
		$where = (count($where) ? implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_attribute a";					
		if ($where)
			$query .= " WHERE ".$where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT a.*, at.alias as attributetype_name, at.alias as attributetype_code, CONCAT(ns.prefix,':',a.isocode) as attribute_isocode FROM #__sdi_sys_stereotype at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON ns.id=a.namespace_id WHERE a.attributetype_id=at.id";
		if ($where)
			$query .= ' AND '.$where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		// get list of relationtypes for dropdown filter
		$query = 'SELECT id as value, alias as text' .
				' FROM #__sdi_sys_stereotype WHERE entity_id=1' .
				' ORDER BY alias';
		$attributetypes[] = JHTML::_('select.option', '0', JText::_('SELECT_ATTRIBUTETYPE'), 'value', 'text');
		$db->setQuery($query);
		$attributetypes = array_merge($attributetypes, $db->loadObjectList());
		$lists['attributetype_id'] = JHTML::_('select.genericlist',  $attributetypes, 'filter_attributetype_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_attributetype_id);
		
		// searchAttribute filter
		$lists['searchAttribute'] = $searchAttribute;
		
		HTML_attribute::listAttribute($rows, $lists, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editAttribute($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$rowAttribute = new attribute( $database );
		$rowAttribute->load( $id );
	
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
		
		$attributetypelist = array();
		$attributetypelist[] = JHTML::_('select.option','0', JText::_("EASYSDI_ATTRIBUTETYPE_LIST") );
		$database->setQuery( "SELECT id AS value, alias AS text FROM #__sdi_sys_stereotype WHERE entity_id=1 ORDER BY alias" );
		$attributetypelist = array_merge( $attributetypelist, $database->loadObjectList() );
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_attribute", false);
		$tableFields = array_merge( $tableFields, $database->getTableFields("#__sdi_translation", false) );
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caractéres
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
		
		// Liste déroulante pour la saisie de la valeur par défaut
		// + Champ de saisie de codeValueList
		if ($rowAttribute->attributetype_id <> 6)
			$style = "display:none";
		else
			$style = "display:inline";

		// Masquer les champs qui ne sont pas saisis lorsqu'on est du type Thesaurus Gemet 
		if ($rowAttribute->attributetype_id == 11)
			$styleAttributes = "display:none";
		else
			$styleAttributes = "display:inline";
		
		
		// Langues à gérer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// L'aide contextuelle
		$informations = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT information FROM #__sdi_translation WHERE element_guid='".$rowAttribute->guid."' AND language_id=".$lang->id);
			$information = $database->loadResult();
			
			$informations[$lang->id] = $information;
		}
		
		// L'aide sur les patterns de saisie
		$regexmsgs = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT regexmsg FROM #__sdi_translation WHERE element_guid='".$rowAttribute->guid."' AND language_id=".$lang->id);
			$regexmsg = $database->loadResult();
			
			$regexmsgs[$lang->id] = $regexmsg;
		}

		$namespacelist = array();
		$namespacelist[] = JHTML::_('select.option','0', " - " );
		$database->setQuery( "SELECT id AS value, prefix AS text FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		HTML_attribute::editAttribute($rowAttribute, $attributetypelist, $fieldsLength, $style, $styleAttributes, $languages, $informations, $regexmsgs, $namespacelist, $option);
	}
	
	function saveAttribute($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		$rowAttribute= new attribute( $database );
		
		if (!$rowAttribute->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit();
		}		
		
		if ($rowAttribute->attributetype_id <>6)
			$rowAttribute->type_isocode = null;
		
		if ($rowAttribute->listnamespace_id == 0)
			$rowAttribute->listnamespace_id = null;
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowAttribute->guid == null)
			$rowAttribute->guid = helper_easysdi::getUniqueId();
		
		// Enlever les éventuels retours à la ligne dans le pattern
		$rowAttribute->pattern = str_replace("\r\n", "", $rowAttribute->pattern);
			
		if (!$rowAttribute->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit();
		}
		
		// Langues à gérer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
	
		// Supprimer tout ce qui avait été créé comme traductions jusqu'à présent pour cet attribut
		$query = "delete from #__sdi_translation where element_guid='".$rowAttribute->guid."'";
		$database->setQuery( $query);
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		// Stocker l'aide contextuelle
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowAttribute->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET information='".addslashes($_POST['information_'.$lang->code])."' WHERE element_guid='".$rowAttribute->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, information) VALUES ('".$rowAttribute->guid."', ".$lang->id.", '".addslashes($_POST['information_'.$lang->code])."')");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// Stocker l'aide sur les regex
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowAttribute->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET regexmsg='".addslashes($_POST['regexmsg_'.$lang->code])."' WHERE element_guid='".$rowAttribute->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, regexmsg) VALUES ('".$rowAttribute->guid."', ".$lang->id.", '".addslashes($_POST['regexmsg_'.$lang->code])."')");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		$rowAttribute->checkin();
		
		// Au cas oé on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyAttribute' :
				// Reprendre en édition l'objet
				TOOLBAR_attribute::_EDIT();
				ADMIN_attribute::editAttribute($rowAttribute->id,$option);
				break;

			case 'saveAttribute' :
			default :
				break;
		}
	}
	
	function removeAttribute($id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('Sélectionnez un enregistrement à supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("Sélectionnez un enregistrement à supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit;
		}
		foreach( $id as $attribute_id )
		{
			$rowAttribute= new attribute( $database );
			$rowAttribute->load( $attribute_id );
			
			// Supprimer tout ce qui avait été créé comme traductions pour cet attribut
			$query = "delete from #__sdi_translation where element_guid='".$rowAttribute->guid."'";
			$database->setQuery( $query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			if (!$rowAttribute->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAttribute" );
				exit();
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelAttribute($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowAttribute = new attribute( $database );
		$rowAttribute->bind(JRequest::get('post'));
		$rowAttribute->checkin();

		$mainframe->redirect("index.php?option=$option&task=listAttribute" );
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

		//print_r($cid);echo "<br>"; 
		//print_r($order);echo "<br>";
		//print_r($total);echo "<br>";
		
		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			// Instantiate an article table object
			$row = new attribute( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listAttribute" );
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listAttribute" );
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
			$row = new attribute( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listAttribute" );
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
		
		$query = 'UPDATE #__sdi_attribute' .
				' SET '.$column.' = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit();
		}

		if (count($cid) == 1) {
			$row = new attribute( $db );
			$row->checkin($cid[0]);
		}

		$msg = JText::sprintf('State successfully changed');
				
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listAttribute" );
		exit();
	}
}
?>