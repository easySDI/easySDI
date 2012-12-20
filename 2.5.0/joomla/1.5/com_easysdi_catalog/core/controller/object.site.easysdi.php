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
	function verify() 
	{
		var form = document.adminForm;
		
        
        /* test for at least one manager*/
        var manager_list_valid = false;
        for(var i = 0; i < form.selected_managers.options.length; i++) {  
            if(form.selected_managers.options[i].selected) {  
                manager_list_valid = true;  
                break;  
            }  
        } 

		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'You must provide a name.', true ); ?>" );
		}
		else if (getSelectedValue('adminForm','objecttype_id') < 1) 
		{
			alert( "<?php echo JText::_( 'Please select an object type.', true ); ?>" );
		}
        else if (!manager_list_valid) 
		{
			alert( "<?php echo JText::_( 'You must select at least one manager', true ); ?>" );
		}
		else 
		{
			//submitform( pressbutton );
			form.task.value='saveObject'; 
			form.submit()
		}
	}
</script>

<?php 

class SITE_object {

	function publish($cid,$published){
		global  $mainframe;
		$db =& JFactory::getDBO();
		if ($published){
			$query = "update #__sdi_object  set published = 1  where id=$cid[0]";

		}else{
			$query = "update #__sdi_object  set published = 0  where id=$cid[0]";
		}
		$db->setQuery( $query ,$limitstart,$limit);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

	}

	function listObject($option) {
		global  $mainframe;
		$db 		=& JFactory::getDBO();
		$user 		= JFactory::getUser();
		$language 	=& JFactory::getLanguage();
		$option		= JRequest::getVar("option");
		$context	= $option.'.listObject';
		$limit 		= JRequest::getVar('limit', 20 );
		$limitstart = JRequest::getVar('limitstart', 0 ); 
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		// table ordering
		$filter_order			= $mainframe->getUserStateFromRequest( $option.".filter_order",		'filter_order',		'name',	'word' );
		$filter_order_Dir		= $mainframe->getUserStateFromRequest( $option.".filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		$filter_objecttype_id 	= $mainframe->getUserStateFromRequest( $option.'filter_objecttype_id',	'filter_objecttype_id',	-1,	'int' );
		$filter_objectstate_id 	= $mainframe->getUserStateFromRequest( $option.'filter_objectstate_id',	'filter_objectstate_id',-1,	'int' );
		
		//Check user's rights
		if(!userManager::isUserAllowed($user,"PRODUCT"))
		{
			return;
		}
		
		// Test si le filtre est valide
		if ($filter_order <> "name" 
			and $filter_order <> "objecttype")
		{
			$filter_order		= "name";
			$filter_order_Dir	= "ASC";
		}
		
		$rootAccount = new accountByUserId($db);
		$rootAccount->load($user->id);		
		
		$account= new accountByUserId($db);
		$account->load($user->id);
		
		$search = $mainframe->getUserStateFromRequest( "searchObjectName{$option}", 'searchObjectName', '' );
		$filter = "";
		if ( $search ) {
			if(strripos ($search,'"') != FALSE)
			{
				$searchcontent = substr($search, 1,strlen($search)-2 );
				$searchcontent = $db->getEscaped( trim( strtolower( $searchcontent ) ) );
				$filter .= " AND (o.name = '$searchcontent')";
			}
			else
			{
				$searchcontent = $db->getEscaped( trim( strtolower( $search ) ) );
				$filter .= " AND (o.name LIKE '%".$searchcontent."%')";
			}
		}
		
		// Objecttype filter
		if ($filter_objecttype_id > 0) {
			$filter .= ' AND o.objecttype_id = ' . (int) $filter_objecttype_id;
		}
		
		// State filter
		if ($filter_objectstate_id != -1)
		{
 			$filter .= ' AND o.published = '.(int) $filter_objectstate_id;
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
			
		//$query = "SELECT COUNT(*) FROM #__sdi_object o INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id where ot.predefined=false";					
		$query = "	SELECT count(*)
						FROM 	#__sdi_manager_object e, 
								#__sdi_account a, 
								#__users u,
								#__sdi_object o
						INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id  
					WHERE e.object_id=o.id  
						AND e.account_id=a.id 
						AND a.user_id = u.id
						AND ot.predefined=false
						AND e.account_id = ".$account->id;
		$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Si le nombre de résultats retournés a changé, adapter la page affichée
		if ($limitstart >= $total)
		{
			$limitstart = ( $limit != 0 ? ((floor($total / $limit) * $limit)-1) : 0 );
			$mainframe->setUserState('limitstart', $limitstart);
		}	
		
		if ($limitstart < 0)
			$limitstart = 0;
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		
		// Recherche des enregistrements selon les limites
		$query = "	SELECT o.*, ot.name as objecttype 
						FROM 	#__sdi_manager_object e,  
								#__sdi_account a, 
								#__users u,
								#__sdi_object o
						INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id  
					WHERE e.object_id=o.id  
						AND e.account_id=a.id 
						AND a.user_id = u.id
						AND ot.predefined=false
						AND e.account_id = ".$account->id;
		$query .= $filter;
		$query .= $orderby;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query = "SELECT ot.id AS value, t.label as text 
				 FROM #__sdi_objecttype ot 
				 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
				 INNER JOIN #__sdi_language l ON t.language_id=l.id
				 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
				 WHERE ot.predefined=false 
				 	   AND cl.code='".$language->_lang."'
				 ORDER BY t.label";
		
		$listObjectType[] = JHTML::_('select.option', '0', JText::_('CATALOG_OBJECT_SELECT_OBJECTTYPE'), 'value', 'text');
		$db->setQuery($query);
		$listObjectType = array_merge($listObjectType, $db->loadObjectList());
		
		$lists['order_Dir'] 	= $filter_order_Dir;
		$lists['order'] 		= $filter_order;
		
		$listState[] = JHTML::_('select.option', '-1', JText::_('CATALOG_OBJECT_SELECT_OBJECTTYPE'), 'value', 'text');
		$listState[] = JHTML::_('select.option', '0', JText::_('UNPUBLISHED'), 'value', 'text');
		$listState[] = JHTML::_('select.option', '1', JText::_('PUBLISHED'), 'value', 'text');
		
		HTML_object::listObject($pagination, $rows, $option, $rootAccount, $listObjectType, $listState, $filter_objecttype_id, $filter_objectstate_id, $search,$lists);
	}

	function editObject( $id, $option ) {
		global  $mainframe;
		$user = JFactory::getUser();
		$database =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		
		$rowObject = new object( $database );
		$rowObject->load( $id );

		// Gestion de la page recharg�e sur modification de la classe root du profil
		$pageReloaded=false;
		if (array_key_exists('metadata_guid', $_POST))
		{
			$pageReloaded=true;
		}
		
		$accounts = array();
		
		// Compte racine du gestionnaire
		$currentAccount = new accountByUserId($database);
		$currentAccount->load($user->get('id'));
		//print_r($currentAccount);
		if ($currentAccount->root_id)
			$rootAccount = $currentAccount->root_id;
		else
			$rootAccount = $currentAccount->id;
		
		$objecttypes=array();
		$objecttypes[] = JHTML::_('select.option','0', JText::_("CORE_OBJECT_LIST_OBJECTTYPE_SELECT") );
		//$database->setQuery("SELECT ot.id AS value, ot.name as text FROM #__sdi_objecttype ot, #__sdi_account_objecttype a WHERE ot.id=a.objecttype_id AND a.account_id=".$rootAccount." ORDER BY ot.name");
		$database->setQuery("SELECT ot.id AS value, t.label as text 
							 FROM #__sdi_objecttype ot 
							 INNER JOIN #__sdi_account_objecttype a ON ot.id=a.objecttype_id
							 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
							 INNER JOIN #__sdi_language l ON t.language_id=l.id
							 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
							 WHERE ot.predefined=0 
							 	   AND cl.code='".$language->_lang."'
							 	   AND a.account_id=".$rootAccount."
							 ORDER BY t.label");
		
		//echo $database->getQuery();
		$objecttypes = array_merge( $objecttypes, $database->loadObjectList() );
		
		$projections=array();
		$projections[] = JHTML::_('select.option','0', JText::_("CORE_OBJECT_LIST_PROJECTION_SELECT") );
		$database->setQuery("SELECT id AS value, name as text FROM #__sdi_list_projection ORDER BY name");
		$projections = array_merge( $projections, $database->loadObjectList() );
		
		$rowMetadata = new metadata( $database );
		$rowMetadata->load( $rowObject->metadata_id );
		
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowObject->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowObject->name);
			//$mainframe->redirect("index.php?option=$option&task=listObject", $msg );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ), $msg);
		}

		$rowObject->checkout($user->get('id'));
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_object", false);
		
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
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_metadata", false);
		foreach($tableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$fieldsLength['metadata_'.$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}
		
		// Generate automatic guid for metadata id
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObject->id == 0)
			$rowMetadata->guid = helper_easysdi::getUniqueId();
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowObject->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}
			
		// Gestionnaires
		// Comptes racine associ�s au type d'objet
		$selected_managers = array();
		$database->setQuery( "	SELECT c.id AS value, b.name AS text 
								FROM	#__sdi_manager_object a,
										#__users b, 
										#__sdi_account c 
								WHERE	a.account_id = c.id 
										AND c.user_id=b.id 
										AND a.object_id=".$id." 
								ORDER BY b.name" );
		$selected_managers = array_merge( $selected_managers, $database->loadObjectList() );
		
		$managers = array();
		$database->setQuery( "	SELECT a.id AS value, b.name AS text 
							FROM 	#__sdi_account a,
									#__users b, 
									#__sdi_actor c, 
									#__sdi_list_role d 
							WHERE 	a.user_id = b.id 
									AND c.account_id=a.id 
									AND c.role_id=d.id 
									AND d.code='PRODUCT'
									AND a.root_id=".$rootAccount." 
							ORDER BY b.name" );
		$managers = array_merge( $managers, $database->loadObjectList() );
		//echo $database->getQuery();
		$unselected_managers=array();
		$unselected_managers=helper_easysdi::array_obj_diff($managers, $selected_managers);
		
		// Editeurs
		$selected_editors = array();
		$database->setQuery( "	SELECT c.id AS value, b.name AS text 
								FROM	#__sdi_editor_object a,
										#__users b, 
										#__sdi_account c 
								WHERE	a.account_id = c.id 
										AND c.user_id=b.id 
										AND a.object_id=".$id." 
								ORDER BY b.name" );
		$selected_editors = array_merge( $selected_editors, $database->loadObjectList() );
		
		$editors = array();
		$database->setQuery( "	SELECT a.id AS value, b.name AS text 
								FROM 	#__sdi_account a,
										#__users b, 
										#__sdi_actor c, 
										#__sdi_list_role d 
								WHERE 	a.user_id = b.id 
										AND c.account_id=a.id 
										AND c.role_id=d.id 
										AND d.code='METADATA'
										AND a.root_id=".$rootAccount." 
								ORDER BY b.name" );
		$editors = array_merge( $editors, $database->loadObjectList() );
		
		$unselected_editors=array();
		$unselected_editors=helper_easysdi::array_obj_diff($editors, $selected_editors);
		
		// Gestion des versions ou pas
		$rowObjectType = new objecttype($database);
		if (!$pageReloaded)
		{
			$rowObjectType->load($rowObject->objecttype_id);
		}
		else
		{
			$rowObjectType->load($_POST['objecttype_id']);
		}
		
		// Objet d'un type qui n'a pas de gestion des versions
		if ($rowObjectType->id <>0 and !$rowObjectType->hasVersioning)
		{
			$rowObjectVersion = new objectversionByObject_id( $database );
			$rowObjectVersion->load( $rowObject->id );
			
			$rowMetadata = new metadata( $database );
			$rowMetadata->load( $rowObjectVersion->metadata_id );
		}
		
		$visibilities=array();
		$database->setQuery( "SELECT id AS value, label AS text FROM #__sdi_list_visibility " );
		$visibilities = $database->loadObjectList() ;
		helper_easysdi::alter_array_value_with_JTEXT_($visibilities);
		
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		
		HTML_object::editObject($rowObject, $rowMetadata, $id, $accounts, $objecttypes, $visibilities, $projections, $fieldsLength, $languages, $labels, $unselected_editors, $selected_editors, $unselected_managers, $selected_managers, $rowObjectType->hasVersioning, $pageReloaded, $option );
	}

	function saveObject($option){
		global  $mainframe;
		$database=& JFactory::getDBO();
		$option =  JRequest::getVar("option");
		$rowObject =& new object($database);
		
		$user =& JFactory::getUser();
		
		if (!$rowObject->bind( $_POST )) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
			exit();
		}

		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObject->guid == null)
			$rowObject->guid = helper_easysdi::getUniqueId();
		
		// Fournisseur = compte racine du compte courant
		$rowAccount = new accountByUserId($database);
		$rowAccount->load($user->id);
		$root_account = $rowAccount->id;
		if ($rowAccount->root_id)
			$root_account = $rowAccount->root_id;
			
		$rowObject->account_id = $root_account;
		
		// Si l'objet n'existe pas encore, cr�er une version et une m�tadonn�e
		if ($rowObject->id == 0)
		{
			// R�cup�rer l'attribut qui correspond au stockage de l'id
			$idrow = array();
			//$database->setQuery("SELECT CONCAT(ns.prefix,':',a.isocode) as attribute_isocode, CONCAT(atns.prefix,':',at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace as ns ON a.namespace_id=ns.id LEFT OUTER JOIN #__sdi_namespace as atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
			$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, CONCAT(atns.prefix, ':', at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
			$idrow = array_merge( $idrow, $database->loadObjectList() );
			
			// R�cup�rer la classe racine
			$root = array();
			$database->setQuery("SELECT CONCAT(ns.prefix,':',c.isocode) as isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace as ns ON c.namespace_id=ns.id WHERE c.id=p.class_id AND p.id=ot.profile_id AND ot.id=".$rowObject->objecttype_id);
			$root = array_merge( $root, $database->loadObjectList() );
			
			// Cr�ation de la m�tadonn�e pour le nouveau guid
			// Ins�rer dans Geonetwork la nouvelle version de la m�tadonn�e
			$xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<csw:Transaction service=\"CSW\"
			version=\"2.0.2\"
			xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" >
				<csw:Insert>
					<".$root[0]->isocode."
						xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" 
						xmlns:gco=\"http://www.isotc211.org/2005/gco\" 
						xmlns:xlink=\"http://www.w3.org/1999/xlink\" 
						xmlns:gml=\"http://www.opengis.net/gml\" 
						xmlns:gts=\"http://www.isotc211.org/2005/gts\" 
						xmlns:srv=\"http://www.isotc211.org/2005/srv\"
						xmlns:ext=\"http://www.depth.ch/2008/ext\">
						
						<".$idrow[0]->attribute_isocode.">
							<".$idrow[0]->type_isocode.">".$_POST['metadata_guid']."</".$idrow[0]->type_isocode.">
						</".$idrow[0]->attribute_isocode.">
					</".$root[0]->isocode.">
				</csw:Insert>
			</csw:Transaction>";
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			$catalogUrlBase = config_easysdi::getValue("catalog_url");
			//$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
			$result = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase, $xmlstr);
			
			$insertResults = DOMDocument::loadXML($result);
			
			$xpathInsert = new DOMXPath($insertResults);
			$xpathInsert->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
			$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
			
			if ($inserted <> 1)
			{
				$mainframe->enqueueMessage('Error on metadata insert',"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listObject" );
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
				exit();
			}
		
			// Créer l'entrée de métadonnée dans la base
			$rowMetadata = new metadata($database);
			$rowMetadata->guid = $_POST['metadata_guid'];
			$rowMetadata->name = $_POST['metadata_guid'];
			$rowMetadata->created = $_POST['created'];
			$rowMetadata->createdby = $_POST['createdby'];
			$rowMetadata->metadatastate_id = 4;
			
			//$rowMetadata->visibility_id = $_POST['visibility_id'];
			if (!$rowMetadata->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listObject" );
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
				exit();
			}
			
			// Stocker l'objet
			if (!$rowObject->store()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listObject" );
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
				exit();
			}
			// Construire la premi�re version
			$rowObjectVersion= new objectversion( $database );
			$rowObjectVersion->object_id=$rowObject->id;
			$rowObjectVersion->metadata_id=$rowMetadata->id;
			$rowObjectVersion->description=$_POST['version_description'];
			$rowObjectVersion->title=$_POST['created'];
			$rowObjectVersion->created=$_POST['created'];
			$rowObjectVersion->createdby=$_POST['createdby'];
			
			// G�n�rer un guid
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			if ($rowObjectVersion->guid == null)
				$rowObjectVersion->guid = helper_easysdi::getUniqueId();
			
			if (!$rowObjectVersion->store(false)) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listObject" );
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
				exit();
			}
		}
		else
		{
			if (!$rowObject->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listObject" );
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
				exit();
			}
		}
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowObject->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".helper_easysdi::escapeString($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowObject->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowObject->guid."', ".$lang->id.", '".helper_easysdi::escapeString($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// R�cup�rer toutes les relations avec les managers existantes
		$query = "SELECT * FROM #__sdi_manager_object WHERE object_id=".$rowObject->id;
		$database->setQuery($query);
		$manager_rows = $database->loadObjectList();
		
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// D�stockage des relations avec les managers
		foreach ($manager_rows as $row)
		{
			// Si la cl� existante n'est pas dans le tableau des relations, on la supprime
			if (!in_array($row->id, $_POST['selected_manager']))
			{
				$rowManagerObject= new manager_object($database);
				$rowManagerObject->load($row->id);
				
				if (!$rowManagerObject->delete()) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
					//$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listCodeValue'), false ));
					//exit();
				}
			}
		}
		// Stockage des relations de type manager avec les utilisateurs
		if (array_key_exists('selected_manager', $_POST))
		{
			foreach($_POST['selected_manager'] as $selected)
			{
				// Si la cl� du tableau des relations n'est pas encore dans la base, on l'ajoute
				if (!in_array($selected, $rows))
				{
					$rowManagerObject= new manager_object($database);
					$rowManagerObject->account_id=$selected;
					$rowManagerObject->object_id=$rowObject->id;
					
					if (!$rowManagerObject->store(false)) {			
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
						//$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listCodeValue'), false ));
						//exit();
					}
				}
			}
		}
		
		// R�cup�rer toutes les relations avec les editeurs existantes
		$query = "SELECT * FROM #__sdi_editor_object WHERE object_id=".$rowObject->id;
		$database->setQuery($query);
		$editor_rows = $database->loadObjectList();
		
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// D�stockage des relations avec les editeurs
		foreach ($editor_rows as $row)
		{
			// Si la cl� existante n'est pas dans le tableau des relations, on la supprime
			if (!in_array($row->id, $_POST['selected_editor']))
			{
				$rowEditorObject= new editor_object($database);
				$rowEditorObject->load($row->id);
				
				if (!$rowEditorObject->delete()) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
					//$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listCodeValue'), false ));
					//exit();
				}
			}
		}
		
		// Stockage des relations de type editor avec les utilisateurs
		if (array_key_exists('selected_editor', $_POST))
		{
			foreach($_POST['selected_editor'] as $selected)
			{
				// Si la cl� du tableau des relations n'est pas encore dans la base, on l'ajoute
				if (!in_array($selected, $rows))
				{
					$rowEditorObject= new editor_object($database);
					$rowEditorObject->account_id=$selected;
					$rowEditorObject->object_id=$rowObject->id;
					
					if (!$rowEditorObject->store(false)) {			
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
						//$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listCodeValue'), false ));
						//exit();
					}
				}
			}
		}
		$rowObject->checkin();
		
	}

	function deleteObject($cid ,$option){

		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			//$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
			exit;
		}
		foreach( $cid as $id )
		{
			$object = new object( $database );
			$object->load( $id );

			// R�cup�rer toutes les versions
			$listVersion = array();
			$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$object->id." ORDER BY created DESC" );
			$listVersion = array_merge( $listVersion, $database->loadResultArray() );
			
			//S'assurer que toutes les versions sont dans l'�tat archiv� ou en travail
			$total=0;
			$database->setQuery( "SELECT COUNT(*) 
								  FROM #__sdi_objectversion v 
								  INNER JOIN #__sdi_metadata m ON m.id=v.metadata_id 
								  INNER JOIN #__sdi_list_metadatastate s ON s.id=m.metadatastate_id 
								  WHERE v.object_id=".$object->id." 
								  		AND (state == 'unpublished' or state == 'archived') 
								  ORDER BY v.created DESC" );
			$total = $database->loadResult();
			if ($total <> count($listVersion))
			{
				$mainframe->enqueueMessage(JText::_("CATALOG_OBJECT_DELETE_VERSIONSTATE_MSG"),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listObject" ), false));
				exit;
			}
			
			// Tests suppl�mentaires si le SHOP est install�
			$shopExist = 0;
			$query = "	SELECT count(*) 
						FROM #__sdi_list_module 
						WHERE code='SHOP'";
			$database->setQuery($query);
			$shopExist = $database->loadResult();
			if($shopExist == 1)
			{
				// Si la version est li�e � un produit, emp�cher la suppression et afficher un message
				$query = 'SELECT count(*)' .
						' FROM #__sdi_objectversion ov
						  INNER JOIN #__sdi_product object o ON ov.object_id=o.id
						  INNER JOIN #__sdi_product p ON p.objectversion_id=ov.id
						  WHERE ov.object_id=' . $object->id;
				$database->setQuery($query);
				$child_version = $database->loadObject();
				if ($metadata->metadatastate_id <> 2 and $metadata->metadatastate_id <> 4)
				{
					$mainframe->enqueueMessage(JText::_("CATALOG_OBJECTVERSION_DELETE_PRODUCTEXIST_MSG","error"));
					$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option=$option&task=listObject'), false) );
					exit;
				}
			}
			
			// Si la suppression de l'objet est possible, commencer par supprimer toutes les versions et leur m�tadonn�e
			foreach( $listVersion as $version )
			{
				$objectversion = new objectversion($database);
				$objectversion->load($version->id); 
				
				$metadata = new metadata($database);
				$metadata->load( $objectversion->metadata_id );
				
				// Supprimer de Geonetwork la m�tadonn�e
				$xmlstr = '<?xml version="1.0" encoding="UTF-8"?>
					<csw:Transaction service="CSW" version="2.0.2" xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:ogc="http://www.opengis.net/ogc" 
					    xmlns:apiso="http://www.opengis.net/cat/csw/apiso/1.0">
					    <csw:Delete>
					        <csw:Constraint version="1.0.0">
					            <ogc:Filter>
					                <ogc:PropertyIsLike wildCard="%" singleChar="_" escape="/">
					                    <ogc:PropertyName>apiso:identifier</ogc:PropertyName>
					                    <ogc:Literal>'.$metadata->guid.'</ogc:Literal>
					                </ogc:PropertyIsLike>
					            </ogc:Filter>
					        </csw:Constraint>
					    </csw:Delete>
					</csw:Transaction>'; 
				
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
				$catalogUrlBase = config_easysdi::getValue("catalog_url");
				//$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
				$result = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase, $xmlstr);
				
				$deleteResults = DOMDocument::loadXML($result);
				
				$xpathDelete = new DOMXPath($deleteResults);
				$xpathDelete->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
				$deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
				
				if ($deleted <> 1)
				{
					$mainframe->enqueueMessage('Error on metadata delete',"ERROR");
					$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listProduct"), false));
					exit();
				}
				
				if (!$metadata->delete()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listObject" ), false));
				}
				
				if (!$objectversion->delete()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listObject" ), false));
				}
			}
			
			if (!$object->delete()) {
				$mainframe->enqueueMessage('CATALOG_OBJECT_DELETE_SHOPLINK_MSG',"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listObject" ), false));
			}
		}

		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listObject" ), false));
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelObject($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		if (JRequest::get('object_id') <> 0)
		{
			$rowObject = new object( $database );
			$rowObject->load(JRequest::get('object_id'));
			$rowObject->checkin();
		}
		$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
	}
	
	function changeContent( $state = 0 )
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
		
		$query = 'UPDATE #__sdi_object' .
				' SET published = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
			exit();
		}

		if (count($cid) == 1) {
			$row = new object( $db );
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

		$cache = & JFactory::getCache('com_easysdi_core');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		//$mainframe->redirect("index.php?option=$option&task=listObject" );
		$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
		exit();
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
			$row = new object( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					//$mainframe->redirect("index.php?option=$option&task=listObject" );
					$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		//$mainframe->redirect("index.php?option=$option&task=listObject" );
		$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
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
			$row = new object( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		//$mainframe->redirect("index.php?option=$option&task=listObject" );
		$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
		exit();
	}
}

?>