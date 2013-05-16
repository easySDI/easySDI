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

class SITE_objectversion 
{
	function listObjectVersion($object_id, $option) {
		global  $mainframe;
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();
		
		$option=JRequest::getVar("option");
		//$limit = JRequest::getVar('limit', 20, '', 'int');
		//$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$context	= $option.'.listObjectVersion';
		$limit		= $mainframe->getUserStateFromRequest($option.'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		// Probl�me avec le retour au d�but ou � la page une, quand limitstart n'est pas pr�sent dans la session.
		// La mise � z�ro ne se fait pas, il faut donc la forcer
		if (! isset($_REQUEST['limitstart']))
			$limitstart=0;
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( $option.".filter_order",		'filter_order',		'title',	'word' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.".filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "title" 
			and $filter_order <> "description")
		{
			$filter_order		= "title";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		$query = "	SELECT COUNT(*) 
					FROM #__sdi_objectversion ov 
					INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id 
					INNER JOIN #__sdi_list_metadatastate s ON m.metadatastate_id=s.id 
					WHERE ov.object_id=".$object_id;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		
		// Recherche des enregistrements selon les limites
		$query = "	SELECT 	ov.*, 
							s.label as state,
							m.guid as metadata_guid,
							m.metadatastate_id as metadatastate_id 
					FROM #__sdi_objectversion ov 
					INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id 
					INNER JOIN #__sdi_list_metadatastate s ON m.metadatastate_id=s.id 
					WHERE ov.object_id=".$object_id;
		$query .= $orderby;
		
		
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			//exit();
		}
		
		$rowObject = new object($db);
		$rowObject->load($object_id);
		
		$lists['order_Dir'] 	= $filter_order_Dir;
		$lists['order'] 		= $filter_order;
		
		HTML_objectversion::listObjectVersion($pagination, $rows, $object_id, $rowObject->name, $option, $lists);
	}

	function editObjectVersion( $id, $option ) {
		global  $mainframe;
		$user = JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$rowObject = new object( $database );
		$rowObject->load( $id );

		// Gestion de la page recharg�e sur modification de la classe root du profil
		$pageReloaded=false;
		if (array_key_exists('metadata_guid', $_POST))
		{
			$pageReloaded=true;
		}
		
		$accounts = array();
		/*$accounts[] = JHTML::_('select.option','0', JText::_("CORE_OBJECT_LIST_ACCOUNT_SELECT") );
		
		if (!$pageReloaded and $rowObject->id <>0)
		{
			$database->setQuery( "SELECT a.id AS value, b.name AS text FROM #__sdi_account a, #__users b, #__sdi_account_objecttype c WHERE a.user_id = b.id AND a.id=c.account_id AND a.id IN 
										(SELECT account_id FROM #__sdi_actor
								    					 WHERE 
								    					 role_id = (SELECT id FROM #__sdi_list_role WHERE code ='PRODUCT'))
									     AND c.objecttype_id=".$rowObject->objecttype_id."
								ORDER BY b.name" );
			//echo $database->getQuery();
			$accounts = array_merge( $accounts, $database->loadObjectList());
		}
		else if ($pageReloaded)
		{
			$database->setQuery( "SELECT a.id AS value, b.name AS text FROM #__sdi_account a, #__users b, #__sdi_account_objecttype c WHERE a.user_id = b.id AND a.id=c.account_id AND a.id IN 
										(SELECT account_id FROM #__sdi_actor
								    					 WHERE 
								    					 role_id = (SELECT id FROM #__sdi_list_role WHERE code ='PRODUCT'))
									     AND c.objecttype_id=".$_POST['objecttype_id']."
								ORDER BY b.name" );
			//echo $database->getQuery();
			$accounts = array_merge( $accounts, $database->loadObjectList());
		}
		*/
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
		$database->setQuery("SELECT ot.id AS value, ot.name as text FROM #__sdi_objecttype ot, #__sdi_account_objecttype a WHERE ot.id=a.objecttype_id AND a.account_id=".$rootAccount." ORDER BY ot.name");
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
		
		HTML_object::editObject($rowObject, $rowMetadata, $id, $accounts, $objecttypes, $projections, $fieldsLength, $languages, $labels, $unselected_editors, $selected_editors, $unselected_managers, $selected_managers, $rowObjectType->hasVersioning, $pageReloaded, $option );
		//HTML_object::editObject($rowObject, $rowMetadata, $id, $accounts, $objecttypes, $projections, $fieldsLength, $languages, $labels, $unselected_editors, $selected_editors, $unselected_managers, $selected_managers, $option );
	}

	function saveObjectVersion($option){
		global  $mainframe;
		$database=& JFactory::getDBO();
		$option =  JRequest::getVar("option");
		$rowObject =& new object($database);
		$rowMetadata = new metadataByGuid( $database );
		$user =& JFactory::getUser();
		
		if (!$rowObject->bind( $_POST )) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
			exit();
		}

		$rowMetadata->load($_POST['metadata_guid']);
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObject->guid == null)
			$rowObject->guid = helper_easysdi::getUniqueId();
		
		// Comme projection_id n'est pas obligatoire et que par d�faut $rowObject->projection_id == '0',
		// on passe $rowObject->projection_id � NULL pour que la sauvegarde fonctionne
		if ($rowObject->projection_id == '0')
			$rowObject->projection_id = NULL;
		
		// Fournisseur = compte racine du compte courant
		$rowAccount = new accountByUserId($database);
		$rowAccount->load($user->id);
		$root_account = $rowAccount->id;
		if ($rowAccount->root_id)
			$root_account = $rowAccount->root_id;
			
		$rowObject->account_id = $root_account;
		
		// Si le produit n'existe pas encore, cr�er la m�tadonn�e
		if ($rowObject->id == 0)
		{
			// R�cup�rer l'attribut qui correspond au stockage de l'id
			$idrow = array();
			//$database->setQuery("SELECT CONCAT(ns.prefix,':',a.isocode) as attribute_isocode, CONCAT(atns.prefix,':',at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON ns.id=a.namespace_id LEFT OUTER JOIN #__sdi_namespace atns ON atns.id=a.namespace_id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
			$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, CONCAT(atns.prefix, ':', at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_list_attributetype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
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
		
			$rowMetadata = new metadata($database);
			$rowMetadata->guid = $_POST['metadata_guid'];
			$rowMetadata->name = $_POST['metadata_guid'];
			$rowMetadata->created = $_POST['created'];
			$rowMetadata->createdby = $_POST['createdby'];
			$rowMetadata->metadatastate_id = 4;
		}
		
		$rowMetadata->visibility_id = $_POST['visibility_id'];
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
			exit();
		}
		
		// Cr�er une entr�e dans la table des m�tadonn�es pour la nouvelle m�tadonn�e associ�e � cet objet
		$rowObject->metadata_id = $rowMetadata->id;	
			
			
		if (!$rowObject->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
			exit();
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
		$editor_rows = array();
		$query = "SELECT * FROM #__sdi_editor_object WHERE object_id=".$rowObject->id;
		$database->setQuery($query);
		$editor_rows = array_merge($editor_rows, $database->loadObjectList());
		
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
					// $mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listCodeValue'), false ));
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

function deleteObjectVersion($cid, $option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			//$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
			exit;
		}
		
		$object_id = JRequest::getVar('object_id',0);
		
		/*
		 * L'objet doit avoir au minimum une version, impossible donc de supprimer plus de versions
		 * que count - 1 
		 */
		$total = "";
		$database->setQuery( "SELECT count(*) FROM #__sdi_objectversion WHERE object_id=".$object_id);
		$total = $database->loadResult();
		
		if (count( $cid ) > $total-1) {
			$mainframe->enqueueMessage(JText::_("CATALOG_OBJECTVERSION_DELETE_COUNTVERSIONS_MSG"),"error");
			//$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
			exit;
		}
		
		foreach( $cid as $id )
		{
			$objectversion = new objectversion( $database );
			$objectversion->load( $id );

			$metadata = new metadata($database);
			$metadata->load( $objectversion->metadata_id );
			
			if ($metadata->metadatastate_id <> 2 and $metadata->metadatastate_id <> 4)
			{
				$mainframe->enqueueMessage(JText::_("CATALOG_OBJECTVERSION_DELETE_STATE_MSG","error"));
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
				exit;
			}
			
			// Tests supplémentaires si le SHOP est installé
			$shopExist = 0;
			$query = "	SELECT count(*) 
						FROM #__sdi_list_module 
						WHERE code='SHOP'";
			$database->setQuery($query);
			$shopExist = $database->loadResult();
			if($shopExist == 1)
			{
				// Si la version est li�e � un produit, emp�cher la suppression et afficher un message
				$childcount=0;
				$query = 'SELECT count(*)' .
						' FROM #__sdi_objectversion ov
						  INNER JOIN #__sdi_product p ON p.objectversion_id=ov.id
						  WHERE p.objectversion_id=' . $objectversion->id;
				$database->setQuery($query);
				$childcount = $database->loadResult();
				
				if ($childcount > 0)
				{
					$mainframe->enqueueMessage(JText::_("CATALOG_OBJECTVERSION_DELETE_PRODUCTEXIST_MSG","error"));
					$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
					exit;
				}
			}
			
			// Supprimer de Geonetwork la métadonnée
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
			
			/*$xpathDelete = new DOMXPath($deleteResults);
			$xpathDelete->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
			$deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
			
			if ($deleted <> 1)
			{
				$mainframe->enqueueMessage('Error on metadata delete',"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
				exit();
			}*/
			
			// Si une version suit, corriger son parent_id avec celui de la version qui va être supprim�e
			$query = 'SELECT *' .
					' FROM #__sdi_objectversion
					  WHERE parent_id=' . $objectversion->id;
			$database->setQuery($query);
			$child_version = $database->loadObject();
			if (count($child_version)>0)
			{
				$childobjectversion = new objectversion($database);
				$childobjectversion->load($child_version->id);
				$childobjectversion->parent_id=$objectversion->parent_id;
				if (!$childobjectversion->store(true)) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					//$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
					$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
				}
			}
			
			// Supprimer l'historique d'assignement
			$query = 'DELETE FROM #__sdi_history_assign
					  WHERE objectversion_id=' . $objectversion->id;
			$database->setQuery($query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			// Supprimer tous les liens vers la version, parents ou enfants
			$links = array();
			$query = 'SELECT l.id' .
					' FROM #__sdi_objectversionlink l
					  WHERE l.parent_id=' . $objectversion->id.
					'		OR l.child_id=' . $objectversion->id;
			$database->setQuery($query);
			$links = $database->loadObjectList();
			
			foreach($links as $link)
			{
				$objectversionlink = new objectversionlink($database);
				$objectversionlink->load($link->id);
				
				if (!$objectversionlink->delete()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					//$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
					$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
				}
			}
			
			if (!$objectversion->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
			}
			
			if (!$metadata->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id), false ));
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelObjectVersion($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowObject = new object( $database );
		$rowObject->load(JRequest::get('object_id'));
		$rowObject->checkin();

		//$mainframe->redirect("index.php?option=$option&task=listObject" );
		//$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ));
	}
	
	function saveObjectVersionLink($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		$object_id = $_POST['object_id'];
		$objectversion_id = $_POST['objectversion_id'];
		$objectversionlinks = explode(",", $_POST['objectlinks']);
		
		/* Supprimer les liens existants pour ce parent*/
		$database->setQuery("DELETE FROM #__sdi_objectversionlink WHERE parent_id=".$objectversion_id);
		if (!$database->query())
		{	
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		/* Ins�rer les nouveaux liens*/
		foreach ($objectversionlinks as $link)
		{
			if ($link <> "")
			{
				$rowObjectVersionLink = new objectversionlink($database);
				$rowObjectVersionLink->parent_id = $objectversion_id;
				$rowObjectVersionLink->child_id = $link;
				
				if (!$rowObjectVersionLink->store()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listObjectVersion&object_id=$object_id"), false));
					exit();
				}
			}
		}
	}
}

?>