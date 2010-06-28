<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ€™Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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

		$context	= $option.'.listObject';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" 
			and $filter_order <> "name" 
			and $filter_order <> "ordering" 
			and $filter_order <> "published" 
			and $filter_order <> "account_name" 
			and $filter_order <> "description" 
			and $filter_order <> "updated"
			and $filter_order <> "state"  
			and $filter_order <> "objecttype_name")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		// Filtering
		$filter_objecttype_id = $mainframe->getUserStateFromRequest( 'filter_objecttype_id',	'filter_objecttype_id',	-1,	'int' );
		$filter_state = $mainframe->getUserStateFromRequest( 'filter_state',	'filter_state',	'',	'word' );
		
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
		
		
		// Build the where clause of the content record query
		$where = (count($where) ? implode(' AND ', $where) : '');
		
		$query = "SELECT COUNT(*) FROM #__sdi_object o INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id where ot.predefined=false";					
		if ($where)
			$query .= " WHERE ".$where;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT o.*, b.name as account_name, s.label as state, ot.name as objecttype_name FROM #__sdi_account a, #__users b, #__sdi_object o LEFT OUTER JOIN #__sdi_metadata m ON o.metadata_id=m.id LEFT OUTER JOIN #__sdi_list_metadatastate s ON m.metadatastate_id=s.id INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id WHERE a.root_id is null AND a.user_id = b.id AND a.id=o.account_id AND ot.predefined=false";
		if ($where)
			$query .= ' AND '.$where;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			//exit();
		}
		
		// get list of objecttypes for dropdown filter
		$query = 'SELECT id as value, name as text' .
				' FROM #__sdi_objecttype' .
				' WHERE predefined=false' .
				' ORDER BY name';
		$objecttypes[] = JHTML::_('select.option', '0', '- '.JText::_('SELECT_OBJECTTYPE').' -', 'value', 'text');
		$db->setQuery($query);
		$objecttypes = array_merge($objecttypes, $db->loadObjectList());
		$lists['objecttype_id'] = JHTML::_('select.genericlist',  $objecttypes, 'filter_objecttype_id', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_objecttype_id);
		
		// get list of published states for dropdown filter
		$lists['state'] = JHTML::_('grid.state', $filter_state, 'Published', 'Unpublished');
		
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
					alert( "<?php echo JText::_( 'You must provide a name.', true ); ?>" );
				}
				else if (getSelectedValue('adminForm','account_id') < 1) 
				{
					alert( "<?php echo JText::_( 'Please select an account.', true ); ?>" );
				} 
				else if (getSelectedValue('adminForm','objecttype_id') < 1) 
				{
					alert( "<?php echo JText::_( 'Please select an object type.', true ); ?>" );
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
		
		$rowObject = new object( $database );
		$rowObject->load( $id );

		// Gestion de la page rechargée sur modification de la classe root du profil
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
		$database->setQuery("SELECT ot.id AS value, ot.name as text FROM #__sdi_objecttype ot WHERE ot.predefined=0 ORDER BY ot.name");
		//echo $database->getQuery();
		$objecttypes = array_merge( $objecttypes, $database->loadObjectList() );
		//print_r($objecttypes);
		$projections=array();
		$projections[] = JHTML::_('select.option','0', JText::_("CORE_OBJECT_LIST_PROJECTION_SELECT") );
		$database->setQuery("SELECT id AS value, name as text FROM #__sdi_list_projection ORDER BY name");
		$projections = array_merge( $projections, $database->loadObjectList() );
		
		$visibilities=array();
		$visibilities[] = JHTML::_('select.option','0', JText::_("CORE_OBJECT_LIST_VISIBILITY_SELECT") );
		$database->setQuery("SELECT id AS value, name as text FROM #__sdi_list_visibility ORDER BY name");
		$visibilities = array_merge( $visibilities, $database->loadObjectList() );
		
		
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
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_object", false);
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caractères
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
		
		// Langues à gérer
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
		// Comptes racine associés au type d'objet
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
		
		HTML_object::editObject($rowObject, $rowMetadata, $id, $accounts, $objecttypes, $projections, $fieldsLength, $languages, $labels, $unselected_editors, $selected_editors, $unselected_managers, $selected_managers, $visibilities, $pageReloaded, $option );
	}

	function saveObject($option){
		global  $mainframe;
		$database=& JFactory::getDBO();
		$option =  JRequest::getVar("option");
		$rowObject =& new object($database);
		$rowMetadata = new metadataByGuid( $database );
		$user =& JFactory::getUser();
		
		if (!$rowObject->bind( $_POST )) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObject" );
			exit();
		}

		$rowMetadata->load($_POST['metadata_guid']);
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObject->guid == null)
			$rowObject->guid = helper_easysdi::getUniqueId();
		
		// Comme projection_id n'est pas obligatoire et que par défaut $rowObject->projection_id == '0',
		// on passe $rowObject->projection_id à NULL pour que la sauvegarde fonctionne
		if ($rowObject->projection_id == '0')
			$rowObject->projection_id = NULL;
		
		// Si le produit n'existe pas encore, créer la métadonnée
		if ($rowObject->id == 0)
		{
			// Récupérer l'attribut qui correspond au stockage de l'id
			$idrow = array();
			//$database->setQuery("SELECT CONCAT(ns.prefix,':',a.isocode) as attribute_isocode, CONCAT(atns.prefix,':',at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace as ns ON a.namespace_id=ns.id LEFT OUTER JOIN #__sdi_namespace as atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
			$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_list_attributetype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
			$idrow = array_merge( $idrow, $database->loadObjectList() );
			
			// Récupérer la classe racine
			$root = array();
			$database->setQuery("SELECT CONCAT(ns.prefix,':',c.isocode) as isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace as ns ON c.namespace_id=ns.id WHERE c.id=p.class_id AND p.id=ot.profile_id AND ot.id=".$rowObject->objecttype_id);
			$root = array_merge( $root, $database->loadObjectList() );
			
			// Création de la métadonnée pour le nouveau guid
			// Insérer dans Geonetwork la nouvelle version de la métadonnée
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
			$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
			
			$insertResults = DOMDocument::loadXML($result);
			
			$xpathInsert = new DOMXPath($insertResults);
			$xpathInsert->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
			$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
			
			if ($inserted <> 1)
			{
				$mainframe->enqueueMessage('Error on metadata insert',"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
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
			$mainframe->redirect("index.php?option=$option&task=listObject" );
			exit();
		}
		/* Obsolète, maintenant on passe par une version */
		// Créer une entrée dans la table des métadonnées pour la nouvelle métadonnée associée à cet objet
		//$rowObject->metadata_id = $rowMetadata->id;	
		if (!$rowObject->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObject" );
			exit();
		}
		
		// Construire la nouvelle version
		$rowObjectVersion= new objectversion( $database );
		
		$rowObjectVersion->object_id=$rowObject->id;
		$rowObjectVersion->metadata_id=$rowMetadata->id;
		$rowObjectVersion->name=$_POST['version_name'];
		$rowObjectVersion->description=$_POST['version_description'];
		$rowObjectVersion->created=$_POST['created'];
		$rowObjectVersion->createdby=$_POST['createdby'];
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObjectVersion->guid == null)
			$rowObjectVersion->guid = helper_easysdi::getUniqueId();
		
		if (!$rowObjectVersion->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObject" );
			exit();
		}	
			
		// Langues à gérer
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
				$database->setQuery("UPDATE #__sdi_translation SET label='".str_replace("'","\'",$_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowObject->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowObject->guid."', ".$lang->id.", '".str_replace("'","\'",$_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		
		// Récupérer toutes les relations avec les managers existantes
		$query = "SELECT * FROM #__sdi_manager_object WHERE object_id=".$rowObject->id;
		$database->setQuery($query);
		$manager_rows = $database->loadObjectList();
		
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// Déstockage des relations avec les managers
		foreach ($manager_rows as $row)
		{
			// Si la clé existante n'est pas dans le tableau des relations, on la supprime
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
				// Si la clé du tableau des relations n'est pas encore dans la base, on l'ajoute
				if (!in_array($selected, $rows))
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
		
		// Récupérer toutes les relations avec les editeurs existantes
		$query = "SELECT * FROM #__sdi_editor_object WHERE object_id=".$rowObject->id;
		$database->setQuery($query);
		$editor_rows = $database->loadObjectList();
		
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// Déstockage des relations avec les editeurs
		foreach ($editor_rows as $row)
		{
			// Si la clé existante n'est pas dans le tableau des relations, on la supprime
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
				// Si la clé du tableau des relations n'est pas encore dans la base, on l'ajoute
				if (!in_array($selected, $rows))
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
	}

	function deleteObject($cid ,$option){

		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			//$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->enqueueMessage("Sï¿½lectionnez un enregistrement ï¿½ supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listObject" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$object = new object( $database );
			$object->load( $id );

			$metadata = new metadata($database);
			$metadata->load( $object->metadata_id );
			
			if ($metadata->metadatastate_id <> 2 and $metadata->metadatastate_id <> 4) // Impossible de supprimer si le statut n'est pas "ARCHIVED" ou "UNPUBLISHED
			{
				$msg = JText::sprintf('CATALOG_OBJECT_DELETEMETADATA_MSG', $object->name);
				$mainframe->enqueueMessage($msg, "error");
				continue;
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
			$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
			
			$deleteResults = DOMDocument::loadXML($result);
			
			$xpathDelete = new DOMXPath($deleteResults);
			$xpathDelete->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
			$deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
			
			if ($deleted <> 1)
			{
				$mainframe->enqueueMessage('Error on metadata delete',"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
			
			if (!$metadata->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
			}
			
			if (!$object->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listObject" );
	}
	
	function archiveObject($cid ,$option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) or count( $cid ) < 1 or $cid[0] == 0) {
			$msg = JText::_('CATALOG_OBJECT_ARCHIVE_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg);
			exit;
		}		
		
		foreach( $cid as $id )
		{
			$object = new object( $database );
			$object->load( $id );

			$metadata = new metadata($database);
			$metadata->load( $object->metadata_id );
			
			if ($metadata->metadatastate_id <> 1) // Impossible de supprimer si le statut n'est pas "PUBLISHED"
			{
				$msg = JText::sprintf('CATALOG_OBJECT_ARCHIVEMETADATA_MSG', $object->name);
				$mainframe->enqueueMessage($msg, "error");
				continue;
			}
			
			$metadata->metadatastate_id=2;
			
			if (!$metadata->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listObject" );
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
	
	function historyAssignMetadata($id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		if ($id == 0 and !JRequest::getVar('object_id'))
		{
			$msg = JText::_('CATALOG_OBJECT_HISTORYASSIGN_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg);
			exit;
		}
		
		if (JRequest::getVar('object_id'))
			$id = JRequest::getVar('object_id');
			
		//$limit = JRequest::getVar('limit', 20 );
		//$limitstart = JRequest::getVar('limitstart', 0 );
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		$rowObject = new object($database);
		$rowObject->load($id);
		
		$query = "SELECT COUNT(*) FROM #__sdi_history_assign h
				  WHERE h.object_id=".$rowObject->id;					
		$database->setQuery($query);
		$total = $database->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		
		$query = "SELECT h.assigned as date, aa.username as assignedby, bb.username as assignedto, o.name as object_name 
                  FROM #__sdi_history_assign h
					INNER JOIN #__sdi_account a ON h.assignedby=a.id
					INNER JOIN #__users aa ON a.user_id=aa.id
					INNER JOIN #__sdi_account b ON h.account_id=a.id
					INNER JOIN #__users bb ON b.user_id=bb.id
					INNER JOIN #__sdi_object o ON h.object_id=o.id
				  WHERE h.object_id=".$rowObject->id." ORDER BY date DESC";
		$database->setQuery( $query, $pagination->limitstart, $pagination->limit);
		$rowHistory = $database->loadObjectList();
		
		HTML_object::historyAssignMetadata($rowHistory, $pagination, $id, $option);
	}
	
	function viewObjectLink($object_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		if ($object_id == 0 and !JRequest::getVar('object_id'))
		{
			$msg = JText::_('CATALOG_OBJECT_VIEWOBJECTLINK_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg);
			exit;
		}
		
		// get list of parents for this object
		$parent_objectlinks=array();
		$query = 'SELECT parent.id as value, parent.name as name' .
				' FROM #__sdi_objectlink l
				  INNER JOIN #__sdi_object parent ON parent.id=l.parent_id' .
				' WHERE child_id=' . $object_id.
				' ORDER BY parent.name';
		$database->setQuery($query);
		$parent_objectlinks = array_merge($parent_objectlinks, $database->loadObjectList());
		
		// get list of childs for this object
		$child_objectlinks=array();
		$query = 'SELECT child.id as value, child.name as name' .
				' FROM #__sdi_objectlink l
				  INNER JOIN #__sdi_object child ON child.id=l.child_id' .
				' WHERE parent_id=' . $object_id.
				' ORDER BY child.name';
		$database->setQuery($query);
		$child_objectlinks = array_merge($child_objectlinks, $database->loadObjectList());
		
		HTML_object::viewObjectLink($parent_objectlinks, $child_objectlinks, $object_id, $option);
	}
	
	function manageObjectLink($object_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		if ($object_id == 0 and !JRequest::getVar('object_id'))
		{
			$msg = JText::_('CATALOG_OBJECT_MANAGEOBJECTLINK_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg);
			exit;
		}
		
		// get list of childs for this object
		$selected_objectlinks=array();
		$query = 'SELECT child.id as value, child.name as name' .
				' FROM #__sdi_objectlink l
				  INNER JOIN #__sdi_object child ON child.id=l.child_id' .
				' WHERE parent_id=' . $object_id.
				' ORDER BY child.name';
		$database->setQuery($query);
		$selected_objectlinks = array_merge($selected_objectlinks, $database->loadObjectList());
		
		// get list of objects which are not childs
		$unselected_objectlinks=array();
		$temp_objectlinks=array();
		$objectlinks=array();
		$query = 'SELECT id as value, name as name' .
				' FROM #__sdi_object' .
				' WHERE id<>' . $object_id.
				' ORDER BY name';
		$database->setQuery($query);
		$unselected_objectlinks = array_merge($unselected_objectlinks, $database->loadObjectList());
		$temp_objectlinks = helper_easysdi::array_obj_diff($unselected_objectlinks, $selected_objectlinks);
		
		// Recréer le tableau afin d'avoir des clés qui se suivent
		foreach ($temp_objectlinks as $object)
		{
			$objectlinks[] = $object;
		}
		
		$objecttypes = array();
		$listObjecttypes = array();
		$database->setQuery( "SELECT id as value, name as text FROM #__sdi_objecttype WHERE predefined=0 ORDER BY name" );
		$objecttypes= array_merge( $objecttypes, $database->loadObjectList() );
		foreach($objecttypes as $ot)
		{
			$listObjecttypes[$ot->value] = $ot->text;
		}
		$listObjecttypes = HTML_metadata::array2extjs($listObjecttypes, true);
		
		$status = array();
		$listStatus = array();
		$database->setQuery( "SELECT id as value, name as text FROM #__sdi_list_metadatastate ORDER BY name" );
		$status= array_merge( $status, $database->loadObjectList() );
		foreach($status as $s)
		{
			$listStatus[$s->value] = $s->text;
		}
		$listStatus = HTML_metadata::array2extjs($listStatus, true);
		
		$managers = array();
		$listManagers = array();
		$database->setQuery( "SELECT id as value, name as text FROM #__sdi_account ORDER BY name" );
		$managers= array_merge( $managers, $database->loadObjectList() );
		foreach($managers as $m)
		{
			$listManagers[$m->value] = $m->text;
		}
		$listManagers = HTML_metadata::array2extjs($listManagers, true);
		
		$editors = array();
		$listEditors = array();
		$database->setQuery( "SELECT id as value, name as text FROM #__sdi_account ORDER BY name" );
		$editors= array_merge( $editors, $database->loadObjectList() );
		foreach($editors as $e)
		{
			$listEditors[$e->value] = $e->text;
		}
		$listEditors = HTML_metadata::array2extjs($listEditors, true);
		
		HTML_object::manageObjectLink($objectlinks, $selected_objectlinks, $listObjecttypes, $listStatus, $listManagers, $listEditors, $object_id, $option);
	}
	
	function getObjectForLink($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$dir = $_POST['dir'];
		$sort = $_POST['sort'];
		$object_id = $_POST['object_id'];
		$selectedObjects = $_POST['selectedObjects'];
		
		$objecttype_id = null;
		$id=null;
		$name=null;
		$status=null;
		$editor=null;
		$manager=null;
		$fromDate=null;
		$toDate=null;
		if (array_key_exists('objecttype_id', $_POST))
			$objecttype_id = $_POST['objecttype_id'];
		if (array_key_exists('id', $_POST))
			$id = $_POST['id'];
		if (array_key_exists('name', $_POST))
			$name = $_POST['name'];
		if (array_key_exists('status', $_POST))
			$status = $_POST['status'];
		if (array_key_exists('editor', $_POST))
			$editor = $_POST['editor'];
		if (array_key_exists('manager', $_POST))
			$manager = $_POST['manager'];
		if (array_key_exists('fromDate', $_POST))
			$fromDate = $_POST['fromDate'];
		if (array_key_exists('toDate', $_POST))
			$toDate = $_POST['toDate'];
		
		// Récupérer tous les objets du type d'objet sélectionné,
		// qui ne sont ni l'objet courant, ni dans la liste des objets sélectionnés
		$query = "SELECT DISTINCT o.id as value, o.name as name 
				  FROM #__sdi_objecttype ot, #__sdi_metadata m, #__sdi_object o
				  LEFT OUTER JOIN #__sdi_manager_object ma ON o.id = ma.object_id
				  LEFT OUTER JOIN #__sdi_editor_object e ON o.id = e.object_id 
				  WHERE o.metadata_id=m.id AND o.objecttype_id=ot.id AND o.id<>".$object_id;
	
		// Ajout des filtres
		if ($objecttype_id)
			$query .= " AND ot.id=".$objecttype_id;
		if ($id)
			$query .= " AND o.id LIKE '%".$id."%'";
		if ($name)
			$query .= " AND o.name LIKE '%".$name."%'";
		if ($status)
			$query .= " AND m.metadatastate_id=".$status;
		if ($editor)
			$query .= " AND e.account_id=".$editor;
		if ($manager)
			$query .= " AND ma.account_id=".$manager;
		if ($fromDate)
			$query .= " AND o.updated >= '".$fromDate."'";
		if ($toDate)
			$query .= " AND o.updated <= '".$toDate."'";
		
		// Suppression des entrées déjà sélectionnées
		if (strlen($selectedObjects) > 0)
			$query .= " AND o.id NOT IN (".$selectedObjects.")";
			
		$query .= " ORDER BY $sort $dir";
			
		$database->setQuery($query);
		//echo $database->getQuery()."\r\n";
		$results= $database->loadObjectList();
		
		// Construire le tableau de résultats
		$return = array ("total"=>count($results), "links"=>$results);
		
		print_r(HTML_metadata::array2json($return));
		die();
	}
	
	function saveObjectLink($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		$object_id = $_POST['object_id'];
		$objectlinks = explode(",", $_POST['objectlinks']);
		
		/* Supprimer les liens existants pour ce parent*/
		$database->setQuery("DELETE FROM #__sdi_objectlink WHERE parent_id=".$object_id);
		if (!$database->query())
		{	
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		/* Insérer les nouveaux liens*/
		foreach ($objectlinks as $link)
		{
			$rowObjectLink = new objectlink($database);
			$rowObjectLink->parent_id = $object_id;
			$rowObjectLink->child_id = $link;
			
			if (!$rowObjectLink->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObject" );
				exit();
			}
		}
	}		
}

?>