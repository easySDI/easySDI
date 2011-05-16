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

	function updateSampleRobotUrl(){

		$sampleUrl = document.getElementById("sampleRobotUrl");
		$sampleUrl.innerHTML= "index.php?"+document.getElementById("sitemapParams").value +"&id=xxx";
		
	}
	function submitbutton(pressbutton) 
	{
		var form = document.adminForm;
		if (pressbutton != 'saveObjectType' && pressbutton != 'applyObjectType') {
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
			alert( "<?php echo JText::_( 'CATALOG_OBJECTTYPE_SUBMIT_NONAME', true ); ?>" );
		}
		else if (form.code.value == "") 
		{
			alert( "<?php echo JText::_( 'CATALOG_OBJECTTYPE_SUBMIT_NOXSL', true ); ?>" );
		}
		else if (getSelectedValue('adminForm','profile_id') < 1) 
		{
			alert( "<?php echo JText::_( 'CATALOG_OBJECTTYPE_SUBMIT_NOPROFILE', true ); ?>" );
		}
		else if (labelEmpty > 0) 
		{
			alert( "<?php echo JText::_( 'CATALOG_OBJECTTYPE_SUBMIT_NOLABELS', true ); ?>" );
		}
		else 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 

class ADMIN_objecttype {

	
	function listObjectType($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$context	= $option.'.listAttribute';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "ordering" and $filter_order <> "description" and $filter_order <> "created" and $filter_order <> "updated") //and $filter_order <> "isoscopecode" 
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		
		$query = "SELECT COUNT(*) FROM #__sdi_objecttype";					
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__sdi_objecttype";// WHERE predefined=0";
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		

		//$rows = array_slice( $rows, $pageNav->limitstart, $pageNav->limit );
		HTML_objecttype::listObjectType($rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}

	
	//id = 0 means new Config entry
	function editObjectType( $id, $option ) {
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		$database =& JFactory::getDBO(); 
		$rowObjecttype = new objecttype( $database );
		$rowObjecttype->load( $id );

		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_objecttype", false);
		
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
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowObjecttype->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}

		// Comptes racine associ�s au type d'objet
		$selected_accounts = array();
		$database->setQuery( "SELECT c.id AS value, b.name AS text FROM #__sdi_account_objecttype a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.objecttype_id=".$id." ORDER BY b.name" );
		$selected_accounts = array_merge( $selected_accounts, $database->loadObjectList() );
		
		$accounts = array();
		$database->setQuery( "SELECT a.id AS value, b.name AS text FROM #__sdi_account a,#__users b WHERE a.user_id = b.id ORDER BY b.name" );
		$accounts = array_merge( $accounts, $database->loadObjectList() );
		$unselected_accounts=helper_easysdi::array_obj_diff($accounts, $selected_accounts);
		
		/*
		$unselected_accounts=array();
		$database->setQuery( "SELECT a.id AS value, b.name AS text FROM #__sdi_account a, #__users b WHERE a.user_id = b.id AND a.root_id is NULL AND a.id NOT IN (SELECT c.id FROM #__sdi_account_objecttype a, #__sdi_account c where a.account_id = c.id AND a.objecttype_id=".$id.") ORDER BY b.name" );
		$unselected_accounts=array_merge( $unselected_accounts, $database->loadObjectList() );
*/
		$profiles = array();
		$profiles[] = JHTML::_('select.option', '0', JText::_('SELECT_PROFILE'), 'value', 'text');
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_profile ORDER BY name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );
		
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
		$namespacelist[] = JHTML::_('select.option','0', " - " );
		$database->setQuery( "SELECT id AS value, prefix AS text FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		HTML_objecttype::editObjectType($rowObjecttype, $fieldsLength, $languages, $labels, $unselected_accounts, $selected_accounts, $profiles, $namespacelist, $option );
	}
	
	function saveObjectType( $option ) 
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$rowObjecttype= new objecttype( $database );
		
		if (!$rowObjecttype->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listObjectType" );
			exit();
		}	

		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObjecttype->guid == null)
			$rowObjecttype->guid = helper_easysdi::getUniqueId();
		
		if ($_POST['fragmentnamespace_id'] == 0)
		{
			$rowObjecttype->fragmentnamespace_id = null;
		}
		
		if (!$rowObjecttype->store(true)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObjectType" );
			exit();
		}
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
	
	
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowObjecttype->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowObjecttype->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowObjecttype->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		/*Sauvegarde de sitemapParams*/
		$sitemapParams = $_POST['sitemapParams'];
		$database->setQuery("UPDATE #__sdi_objecttype SET sitemapParams='".addslashes($sitemapParams)."' WHERE guid='".$rowObjecttype->guid."'");
		if (!$database->query())
		{	
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			return false;
		}
		
		/*Fin sauvegarde sitemap params*/
		
		// R�cup�rer toutes les relations avec les utilisateurs existantes
		$query = "SELECT * FROM #__sdi_account_objecttype WHERE objecttype_id=".$rowObjecttype->id;
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
				$rowAccountObjecttype= new account_objecttype($database);
				$rowAccountObjecttype->load($row->id);
				
				if (!$rowAccountObjecttype->delete()) {			
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
				//if (!in_array($selected, $rows))
				//{
					$rowAccountObjecttype= new account_objecttype($database);
					$rowAccountObjecttype->account_id=$selected;
					$rowAccountObjecttype->objecttype_id=$rowObjecttype->id;
					
					if (!$rowAccountObjecttype->store(false)) {			
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
						//exit();
					}
				//}
			}
		}
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyObjectType' :
				// Reprendre en �dition l'objet
				TOOLBAR_objecttype::_EDIT();
				ADMIN_objecttype::editObjectType($rowObjecttype->id,$option);
				break;

			case 'saveObjectType' :
			default :
				break;
		}
	}
	
	function removeObjectType($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit;
		}
		foreach( $id as $objecttype_id )
		{
			$rowObjecttype= new objecttype( $database );
			$rowObjecttype->load( $objecttype_id );
			
			if (!$rowObjecttype->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectType" );
				exit();
			}
		}
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
			$row = new objecttype( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listObjectType" );
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listObjectType" );
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
			$row = new objecttype( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listObjectType" );
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
		
		$query = 'UPDATE #__sdi_objecttype' .
				' SET '.$column.' = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObjectType" );
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
		$mainframe->redirect("index.php?option=$option&task=listObjectType" );
		exit();
	}
}

?>
