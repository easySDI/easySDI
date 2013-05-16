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

class ADMIN_object {

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
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		$context	= 'listObject';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
	
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( $option.$context.".filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.$context.".filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" 
			and $filter_order <> "ordering" 
			and $filter_order <> "name" 
			and $filter_order <> "hasVersioning" 
			and $filter_order <> "published" 
			and $filter_order <> "account_name" 
			and $filter_order <> "description"
			and $filter_order <> "objecttype_name" 
			and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		// Filtering
		$filter_account_id = $mainframe->getUserStateFromRequest( $option.$context.'filter_account_id',	'filter_account_id',	-1,	'int' );
		$filter_objecttype_id = $mainframe->getUserStateFromRequest( $option.$context.'filter_objecttype_id',	'filter_objecttype_id',	-1,	'int' );
		$filter_state = $mainframe->getUserStateFromRequest( $option.$context.'filter_state',	'filter_state',	'',	'word' );
		
		$searchObject				= $mainframe->getUserStateFromRequest( 'searchObject', 'searchObject', '', 'string' );
		$searchObject				= JString::strtolower($searchObject);
		
		/*
		 * Add the filter specific information to the where clause
		 */
		$where = array();
		// State filter
		if ($filter_state) 
		{
			if ($filter_state == 'P')
				$where[] = 'o.published = 1';
			else if ($filter_state == 'U')
				$where[] = 'o.published =0';
		}
		
		// Objecttype filter
		if ($filter_objecttype_id > 0) {
			$where[] = 'o.objecttype_id = ' . (int) $filter_objecttype_id;
		}
		
		// Account filter
		if ($filter_account_id > 0) {
			$where[] = 'o.account_id = ' . (int) $filter_account_id;
		}
		
		// Text filter
		if ($searchObject) {
			$where[] = '(o.id LIKE '. (int) $searchObject .
				' OR LOWER( o.name ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchObject, true ).'%', false ) .
				' OR LOWER( o.description ) LIKE ' .$db->Quote( '%'.$db->getEscaped( $searchObject, true ).'%', false ).
				')';
		}
		
		// Build the where clause of the content record query
		$where = (count($where) ? implode(' AND ', $where) : '');
		
		$query = "	SELECT COUNT(*) 
					FROM #__sdi_object o 
					INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id
					WHERE ot.predefined=false ";					
		if ($where)
			$query .= " AND ".$where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT o.*, 
						 b.name as account_name, 
						 ot.name as objecttype_name, 
						 ot.hasVersioning as hasVersioning 
				  FROM 	#__sdi_account a, 
				  		#__users b, 
				  		#__sdi_object o 
				  INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id 
				  WHERE a.root_id is null 
				  		AND a.user_id = b.id 
				  		AND a.id=o.account_id 
				  		AND ot.predefined=false";
		if ($where)
			$query .= ' AND '.$where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			//exit();
		}
		
		// get list of accounts for dropdown filter
		// Liste de tous les comptes root qui sont fournisseur d'un objet
		$query = '	SELECT DISTINCT a.id AS value, b.name AS text 
					FROM #__sdi_account a, #__users b 
					WHERE a.user_id = b.id AND a.id IN ( 
						SELECT account_id 
						FROM #__sdi_object
					)
					ORDER BY b.name';
		$accounts[] = JHTML::_('select.option', '0', JText::_('CATALOG_OBJECT_SELECT_ACCOUNT'), 'value', 'text');
		$db->setQuery($query);
		$accounts = array_merge($accounts, $db->loadObjectList());
		$lists['account_id'] = JHTML::_('select.genericlist',  $accounts, 'filter_account_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_account_id);
		
		// get list of objecttypes for dropdown filter
		/*$query = 'SELECT id as value, name as text' .
				' FROM #__sdi_objecttype' .
				' WHERE predefined=false' .
				' ORDER BY name';*/
		$query = "SELECT ot.id AS value, t.label as text 
				 FROM #__sdi_objecttype ot 
				 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
				 INNER JOIN #__sdi_language l ON t.language_id=l.id
				 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
				 WHERE ot.predefined=false 
				 	   AND cl.code='".$language->_lang."'
				 ORDER BY t.label";
		
		$objecttypes[] = JHTML::_('select.option', '0', JText::_('CATALOG_OBJECT_SELECT_OBJECTTYPE'), 'value', 'text');
		$db->setQuery($query);
		$objecttypes = array_merge($objecttypes, $db->loadObjectList());
		$lists['objecttype_id'] = JHTML::_('select.genericlist',  $objecttypes, 'filter_objecttype_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_objecttype_id);
		
		// get list of published states for dropdown filter
		$lists['state'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');
		
		// searchAttributeRelation filter
		$lists['searchObject'] = $searchObject;
		
		HTML_object::listObject($rows, $lists, $pagination, $filter_order_Dir, $filter_order, $option);

	}

	function editObject( $id, $option ) {
		?>
		<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;
				if (pressbutton != 'saveObject' && pressbutton != 'applyObject') {
					submitform( pressbutton );
					return;
				}

				// do field validation
				if (form.name.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_OBJECT_SUBMIT_NONAME', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','objecttype_id') < 1) 
				{
					alert( "<?php echo JText::_( 'CATALOG_OBJECT_SUBMIT_NOOBJECTTYPE', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','account_id') < 1) 
				{
					alert( "<?php echo JText::_( 'CATALOG_OBJECT_SUBMIT_NOACCOUNT', true ); ?>" );
				} 
				else 
				{
					submitform( pressbutton );
				}
			}
		</script>
		
		<?php 
		
				
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
		$accounts[] = JHTML::_('select.option','0', JText::_("CORE_OBJECT_LIST_ACCOUNT_SELECT") );
		
		if (!$pageReloaded and $rowObject->id <>0)
			$database->setQuery( "SELECT a.id AS value, b.name AS text FROM #__sdi_account a, #__users b, #__sdi_account_objecttype c WHERE a.user_id = b.id AND a.id=c.account_id AND a.id IN 
										(SELECT account_id FROM #__sdi_actor
								    					 WHERE 
								    					 role_id = (SELECT id FROM #__sdi_list_role WHERE code ='PRODUCT'))
									     AND c.objecttype_id=".$rowObject->objecttype_id."
								ORDER BY b.name" );

		else
			$database->setQuery( "SELECT a.id AS value, b.name AS text FROM #__sdi_account a, #__users b, #__sdi_account_objecttype c WHERE a.user_id = b.id AND a.id=c.account_id AND a.id IN 
										(SELECT account_id FROM #__sdi_actor
								    					 WHERE 
								    					 role_id = (SELECT id FROM #__sdi_list_role WHERE code ='PRODUCT'))
									     AND c.objecttype_id=".$_POST['objecttype_id']."
								ORDER BY b.name" );
		
		$accounts = array_merge( $accounts, $database->loadObjectList());
		
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
		//$database->setQuery("SELECT ot.id AS value, ot.name as text FROM #__sdi_objecttype ot, #__sdi_account_objecttype a WHERE ot.id=a.objecttype_id AND ot.predefined=0 AND a.account_id=".$rootAccount." ORDER BY ot.name");
		$database->setQuery("SELECT ot.id AS value, t.label as text 
							 FROM #__sdi_objecttype ot 
							 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
							 INNER JOIN #__sdi_language l ON t.language_id=l.id
							 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
							 WHERE ot.predefined=0 
							 	   AND cl.code='".$language->_lang."'
							 ORDER BY t.label");
		//echo $database->getQuery();
		$objecttypes = array_merge( $objecttypes, $database->loadObjectList() );
		//print_r($objecttypes);
		$projections=array();
		$projections[] = JHTML::_('select.option','0', JText::_("CORE_OBJECT_LIST_PROJECTION_SELECT") );
		$database->setQuery("SELECT id AS value, name as text FROM #__sdi_list_projection ORDER BY name");
		$projections = array_merge( $projections, $database->loadObjectList() );
		
		/*$visibilities=array();
		$visibilities[] = JHTML::_('select.option','0', JText::_("CORE_OBJECT_LIST_VISIBILITY_SELECT") );
		$database->setQuery("SELECT id AS value, name as text FROM #__sdi_list_visibility ORDER BY name");
		$visibilities = array_merge( $visibilities, $database->loadObjectList() );
		*/
		
		$rowMetadata = new metadata( $database );
		$rowMetadata->load( $rowObject->metadata_id );
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowObject->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowObject->name);
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg );
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
		if (!$pageReloaded)
		{
			$database->setQuery( "SELECT c.id AS value, b.name AS text FROM #__sdi_manager_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$id." ORDER BY b.name" );
			$selected_managers = array_merge( $selected_managers, $database->loadObjectList() );
		}
		
		$managers = array();
		if (!$pageReloaded)
			$database->setQuery( "	SELECT a.id AS value, b.name AS text 
								FROM 	#__sdi_account a,
										#__users b, 
										#__sdi_actor c, 
										#__sdi_list_role d 
								WHERE 	a.user_id = b.id 
										AND c.account_id=a.id 
										AND c.role_id=d.id 
										AND d.code='PRODUCT'
										AND a.root_id=".$rowObject->account_id." 
								ORDER BY b.name" );
		else
			$database->setQuery( "	SELECT a.id AS value, b.name AS text 
								FROM 	#__sdi_account a,
										#__users b, 
										#__sdi_actor c, 
										#__sdi_list_role d 
								WHERE 	a.user_id = b.id 
										AND c.account_id=a.id 
										AND c.role_id=d.id 
										AND d.code='PRODUCT'
										AND a.root_id=".$_POST['account_id']." 
								ORDER BY b.name" );
		$managers = array_merge( $managers, $database->loadObjectList() );
		
		$unselected_managers=array();
		$unselected_managers=helper_easysdi::array_obj_diff($managers, $selected_managers);
		
		// Editeurs
		$selected_editors = array();
		if (!$pageReloaded)
		{
			$database->setQuery( "SELECT c.id AS value, b.name AS text FROM #__sdi_editor_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$id." ORDER BY b.name" );
			$selected_editors = array_merge( $selected_editors, $database->loadObjectList() );
		}
		$editors = array();
		if (!$pageReloaded)
			$database->setQuery( "	SELECT a.id AS value, b.name AS text 
									FROM 	#__sdi_account a,
											#__users b, 
											#__sdi_actor c, 
											#__sdi_list_role d 
									WHERE 	a.user_id = b.id 
											AND c.account_id=a.id 
											AND c.role_id=d.id 
											AND d.code='METADATA'
											AND a.root_id=".$rowObject->account_id." 
									ORDER BY b.name" );
		else
			$database->setQuery( "	SELECT a.id AS value, b.name AS text 
									FROM 	#__sdi_account a,
											#__users b, 
											#__sdi_actor c, 
											#__sdi_list_role d 
									WHERE 	a.user_id = b.id 
											AND c.account_id=a.id 
											AND c.role_id=d.id 
											AND d.code='METADATA'
											AND a.root_id=".$_POST['account_id']." 
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
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__sdi_list_visibility " );
		$visibilities = $database->loadObjectList() ;
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
			$mainframe->redirect("index.php?option=$option&task=listObject" );
			exit();
		}

		// Générer un guid pour l'objet
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObject->guid == null)
		{
			$rowObject->guid = helper_easysdi::getUniqueId();
		}
			
		// Si l'objet n'existe pas encore, créer une version et une métadonnée
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
				//$mainframe->enqueueMessage(htmlspecialchars($xmlstr),"INFO");
				//$mainframe->enqueueMessage($catalogUrlBase,"INFO");
				//$mainframe->enqueueMessage(uniqid(),"INFO");

				//$mainframe->enqueueMessage(htmlspecialchars($result),"INFO");
				//$mainframe->enqueueMessage('Error on metadata insert',"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
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
				$mainframe->redirect("index.php?option=$option&task=listObject" );
				exit();
			}
			
			// Stocker l'objet
			if (!$rowObject->store()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
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
				$mainframe->redirect("index.php?option=$option&task=listObject" );
				exit();
			}
		}
		else
		{
			if (!$rowObject->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
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
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowObject->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowObject->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
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
				if (!in_array($selected, $manager_rows))
				{
					$rowManagerObject= new manager_object($database);
					$rowManagerObject->account_id=$selected;
					$rowManagerObject->object_id=$rowObject->id;
					
					if (!$rowManagerObject->store(false)) {			
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
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
				if (!in_array($selected, $editor_rows))
				{
					$rowEditorObject= new editor_object($database);
					$rowEditorObject->account_id=$selected;
					$rowEditorObject->object_id=$rowObject->id;
					
					if (!$rowEditorObject->store(false)) {			
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						//$mainframe->redirect("index.php?option=$option&task=listCodeValue" );
						//exit();
					}
				}
			}
		}
		$rowObject->checkin();
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyObject' :
				// Vider le flag qui permet de savoir si on est en train de recharger la page en cours d'�dition
				unset($_POST['metadata_guid']);
				// Reprendre en �dition l'objet
				TOOLBAR_object::_EDIT();
				ADMIN_object::editObject($rowObject->id,$option);
				break;

			case 'saveObject' :
			default :
				break;
		}
	}

	function deleteObject($cid ,$option){

		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			//$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listObject" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$object = new object( $database );
			$object->load( $id );

			// R�cup�rer toutes les versions
			$listVersion = array();
			$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$object->id." ORDER BY created DESC" );
			$listVersion = array_merge( $listVersion, $database->loadObjectList() );
			
			//S'assurer que toutes les versions sont dans l'�tat archiv� ou en travail
			$total=0;
			$database->setQuery( "SELECT COUNT(*) FROM #__sdi_objectversion v INNER JOIN #__sdi_metadata m ON m.id=v.metadata_id INNER JOIN #__sdi_list_metadatastate s ON s.id=m.metadatastate_id WHERE v.object_id=".$object->id." AND (s.code = 'unpublished' or s.code = 'archived') ORDER BY v.created DESC" );
			$total = $database->loadResult();
			
			if ($total <> count($listVersion))
			{
				$mainframe->enqueueMessage(JText::_("CATALOG_OBJECT_DELETE_VERSIONSTATE_MSG"),"error");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
				exit;
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
				/*
				$deleteResults = DOMDocument::loadXML($result);
				
				$xpathDelete = new DOMXPath($deleteResults);
				$xpathDelete->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
				$deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
				
				if ($deleted <> 1)
				{
					$mainframe->enqueueMessage('Error on metadata delete',"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listObject" );
					exit();
				}*/
				
				if (!$objectversion->delete()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listObject" );
					exit;
				}
				
				if (!$metadata->delete()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listObject" );
					exit;
				}
			}
			
			//Supprimer tous les liens vers des editeurs ou des managers
			$query = "DELETE FROM #__sdi_manager_object WHERE object_id=".$object->id;
			$database->setQuery( $query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$query = "DELETE FROM #__sdi_editor_object WHERE object_id=".$object->id;
			$database->setQuery( $query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			if (!$object->delete()) {
				$mainframe->enqueueMessage('CATALOG_OBJECT_DELETE_SHOPLINK_MSG',"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
			}
		}

		//$mainframe->redirect("index.php?option=$option&task=listObject" );
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
		$rowObject = new object( $database );
		$rowObject->bind(JRequest::get('post'));
		$rowObject->checkin();

		$mainframe->redirect("index.php?option=$option&task=listObject" );
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
			$mainframe->redirect("index.php?option=$option&task=listObject" );
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
		$mainframe->redirect("index.php?option=$option&task=listObject" );
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
					$mainframe->redirect("index.php?option=$option&task=listObject" );
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listObject" );
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

		$mainframe->redirect("index.php?option=$option&task=listObject" );
		exit();
	}
	
	
}

?>