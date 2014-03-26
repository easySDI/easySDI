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

class ADMIN_profile {
	function listProfile($option)
	{
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
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "ordering" and $filter_order <> "created" and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		
		$query = "SELECT COUNT(*) FROM #__sdi_profile";					
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT *  FROM #__sdi_profile ";
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_profile::listProfile($rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}
	
	function editProfile($id, $option)
	{
		?>
		<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;
				if (pressbutton != 'saveProfile' && pressbutton != 'applyProfile') {
					submitform( pressbutton );
					return;
				}

				// Récuperer tous les labels et contrôler qu'ils soient saisis
				var labelEmpty = 0;
				var labels = Array();
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
					alert( "<?php echo JText::_( 'CATALOG_PROFILE_SUBMIT_NONAME', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','class_id') < 1) 
				{
					alert( "<?php echo JText::_( 'CATALOG_PROFILE_SUBMIT_NOCLASSID', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','metadataid') < 1) 
				{
					alert( "<?php echo JText::_( 'CATALOG_PROFILE_SUBMIT_NOMETADATAID', true ); ?>" );
				}
				else if (labelEmpty > 0) 
				{
					alert( "<?php echo JText::_( 'CATALOG_PROFILE_SUBMIT_NOLABELS', true ); ?>" );
				}
				else 
				{
					submitform( pressbutton );
				}
			}
		</script>
		<?php 
		$database =& JFactory::getDBO(); 
		$rowProfile = new profile( $database );
		$rowProfile->load( $id );
		
		// Gestion de la page recharg�e sur modification de la classe root du profil
		$pageReloaded=false;
		if (array_key_exists('metadataid', $_POST))
		{
			$pageReloaded=true;
		}
		
		$classes = array();
		$classes[] = JHTML::_('select.option','0', JText::_("EASYSDI_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_class WHERE isrootclass=true ORDER BY name" );
		$classes = array_merge( $classes, $database->loadObjectList() );
		
		$accounts = array();
		$accounts[] = JHTML::_('select.option','0', JText::_("EASYSDI_ACCOUNT_LIST") );
		$database->setQuery( "SELECT a.id AS value, b.name AS text FROM #__sdi_account a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$accounts = array_merge( $accounts, $database->loadObjectList() );
		
		/*
		$objecttypes= array();
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_objecttype ORDER BY name" );
		$objecttypes = array_merge( $objecttypes, $database->loadObjectList() );
		*/
		
		$metadataids= array();
		$metadataids[] = JHTML::_('select.option','0', JText::_("CATALOG_METADATAID") );
		if ($_POST['class_id'] or $rowProfile->class_id)
		{
			if ($pageReloaded)
			{
				$database->setQuery( "	SELECT a.id AS value, a.name as text 
										FROM #__sdi_attribute a 
										INNER JOIN #__sdi_relation rel ON a.id=rel.attributechild_id  
										WHERE a.attributetype_id=1 
											AND rel.parent_id=".$_POST['class_id']." 
										ORDER BY a.name" );
				$metadataids = array_merge( $metadataids, $database->loadObjectList() );
			}
			else if ($rowProfile->id <> 0)
			{
				$database->setQuery( "	SELECT a.id AS value, a.name as text 
										FROM #__sdi_attribute a 
										INNER JOIN #__sdi_relation rel ON a.id=rel.attributechild_id  
										WHERE a.attributetype_id=1 
											AND rel.parent_id=".$rowProfile->class_id." 
										ORDER BY a.name" );
				$metadataids = array_merge( $metadataids, $database->loadObjectList() );
			}
		}
		//echo $database->getQuery();
		/*
		$selected_objecttypes= array();
		if ($id <> 0)
		{
			$database->setQuery( "SELECT id FROM #__sdi_objecttype WHERE profile_id=".$id);
			$selected_objecttypes = array_merge( $selected_objecttypes, $database->loadResultArray() );
		}
		*/
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_profile", false);
		
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
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowProfile->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}
			
		//HTML_profile::editProfile($rowProfile, $classes, $accounts, $objecttypes, $selected_objecttypes, $fieldsLength, $metadataids, $pageReloaded, $languages, $labels, $option);
		HTML_profile::editProfile($rowProfile, $classes, $accounts, $fieldsLength, $metadataids, $pageReloaded, $languages, $labels, $option);
	}
	
	function saveProfile($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$rowProfile= new profile( $database );
		
		if (!$rowProfile->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listProfile" );
			exit();
		}		
		
		if ($rowProfile->class_id==0)
			$rowProfile->class_id=null;
			
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowProfile->guid == null)
			$rowProfile->guid = helper_easysdi::getUniqueId();
			
		if (!$rowProfile->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProfile" );
			exit();
		}
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
	
	
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowProfile->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowProfile->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowProfile->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// Mise � jour des types d'objets
		/*$query = "update #__sdi_objecttype set profile_id = null  where profile_id=".$rowProfile->id;
		$database->setQuery( $query);
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		if (array_key_exists('objecttypes', $_POST))
		{
			foreach ($_POST['objecttypes'] as $row)
			{
				$rowObjectType= new objecttype($database);
				$rowObjectType->load($row);
				$rowObjectType->profile_id=$rowProfile->id;
				
				if (!$rowObjectType->store(false)) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listProfile" );
					exit();
				}
			}
		}*/
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyProfile' :
				// Reprendre en �dition l'objet
				TOOLBAR_profile::_EDIT();
				ADMIN_profile::editProfile($rowProfile->id,$option);
				break;

			case 'saveProfile' :
			default :
				break;
		}
	}
	
	function removeProfile($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit;
		}
		foreach( $id as $profile_id )
		{
			$rowProfile= new profile( $database );
			$rowProfile->load( $profile_id );
			
			if (!$rowProfile->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProfile" );
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
			$row = new profile( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listProfile" );
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listProfile" );
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
			$row = new profile( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listProfile" );
		exit();
	}
}
?>