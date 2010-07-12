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

?>
<script type="text/javascript">
	function submitbutton(pressbutton) 
	{
		var form = document.adminForm;
		if (pressbutton != 'saveObjectVersion' && pressbutton != 'applyObjectVersion') {
			submitform( pressbutton );
			return;
		}

		var versionNames = form.versionNames.value;
		listVersionNames = versionNames.split(', ');
		
		var unique = true;
		for (i=0;i<listVersionNames.length;i++)
		{
			if (listVersionNames[0] == form.name.value)
			{
				unique = false;
				break;
			}
		}
		
		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'You must provide a name.', true ); ?>" );
		}
		else if (!unique) 
		{
			alert( "<?php echo JText::_( 'You must provide a version name that does not exist.', true ); ?>" );
		}
		else 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 
class ADMIN_objectversion {
	
	function listObjectVersion($option) {
		global  $mainframe;
		$db =& JFactory::getDBO();

		$object_id = JRequest::getVar ('object_id');
		 
		$context	= $option.'.listObjectVersion';
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
			and $filter_order <> "description" 
			and $filter_order <> "updated"
			and $filter_order <> "state")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by ov.'. $filter_order .' '. $filter_order_Dir;
		
		$query = "SELECT COUNT(*) FROM #__sdi_objectversion ov INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id INNER JOIN #__sdi_list_metadatastate s ON m.metadatastate_id=s.id WHERE ov.object_id=".$object_id;					
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT ov.*, s.label as state FROM #__sdi_objectversion ov INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id INNER JOIN #__sdi_list_metadatastate s ON m.metadatastate_id=s.id WHERE ov.object_id=".$object_id;
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			//exit();
		}
		
		HTML_objectversion::listObjectVersion($rows, $object_id, $pagination, $filter_order_Dir, $filter_order, $option);

	}

	function newObjectVersion($object_id, $option)
	{
		global $mainframe;
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$object_id = JRequest::getVar ('object_id');
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_objectversion", false);
		
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
		
		// Generate automatic guid for metadata id
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		$metadata_guid = helper_easysdi::getUniqueId();
		
		// Récupérer toutes les versions de l'objet, ordonnées de la plus récente à la plus ancienne
		$listVersionNames = array();
		$database->setQuery( "SELECT name FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY created DESC" );
		$listVersionNames = array_merge( $listVersionNames, $database->loadResultArray() );
		
		HTML_objectversion::newObjectVersion($object_id, $fieldsLength, $metadata_guid, $listVersionNames, $option);
	}

	function editObjectVersion($id, $option)
	{
		global $mainframe;
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$object_id = JRequest::getVar ('object_id');
		
		$row = new objectversion( $database );
		$row->load( $id );

		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_objectversion", false);
		
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
		
		if ($id == 0)
		{
			// Generate automatic guid for metadata id
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			$row->guid= helper_easysdi::getUniqueId();
		}
		
		
		// Récupérer toutes les versions de l'objet, ordonnées de la plus récente à la plus ancienne
		$listVersionNames = array();
		$database->setQuery( "SELECT name FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY created DESC" );
		$listVersionNames = array_merge( $listVersionNames, $database->loadResultArray() );
		
		HTML_objectversion::editObjectVersion($row, $object_id, $fieldsLength, $listVersionNames, $option);
	}
	
	function saveObjectVersion($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$object_id = $_POST['object_id'];
		$metadata_guid = $_POST['metadata_guid'];
		
		// Récupérer toutes les versions de l'objet, ordonnées de la plus récente à la plus ancienne
		$listVersions = array();
		$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY created DESC" );
		$listVersions = array_merge( $listVersions, $database->loadObjectList() );
		
		// Récupérer la métadonnée de la dernière version de l'objet
		$rowLastMetadata = new metadata( $database );
		if (count($listVersions) > 0)
			$rowLastMetadata->load($listVersions[0]->metadata_id);
		
		// Récupérer l'objet
		$rowObject = new object($database);
		$rowObject->load($_POST['object_id']);
		
		// Créer une nouvelle métadonnée
		$rowMetadata = new metadata( $database );
		$rowMetadata->guid = $_POST['metadata_guid'];
		$rowMetadata->name = $_POST['metadata_guid'];
		$rowMetadata->created = $_POST['created'];
		$rowMetadata->createdby = $_POST['createdby'];
		$rowMetadata->metadatastate_id = 4;
		if (count($listVersions) > 0)
			$rowMetadata->visibility_id = $rowLastMetadata->visibility_id;
		
		
		// S'il y a une version précédente, la copier, sinon créer un nouveau contenu
		if (count($listVersions) > 0)
		{
			ADMIN_objectversion::createVersion($rowObject, $rowLastMetadata->guid, $_POST['metadata_guid'], $option);
		}
		else
		{
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			$catalogUrlBase = config_easysdi::getValue("catalog_url");
			// Récupérer l'attribut qui correspond au stockage de l'id
			$idrow = "";
			//$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix,':',a.isocode) as attribute_isocode, at.isocode as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
			$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, CONCAT(atns.prefix, ':', at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_list_attributetype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
			$idrow = $database->loadObjectList();
			
			// Récupérer la classe racine
			$root = array();
			$database->setQuery("SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix,':',c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id);
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
			
			$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
			
			$insertResults = DOMDocument::loadXML($result);
			
			$xpathInsert = new DOMXPath($insertResults);
			$xpathInsert->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
			$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
			
			if ($inserted <> 1)
			{
				$mainframe->enqueueMessage('Error on metadata insert',"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
				exit();
			}
		}
		
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
			exit();
		}
				
		// Construire la nouvelle version
		$rowObjectVersion= new objectversion( $database );
		
		if (!$rowObjectVersion->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
			exit();
		}		
		$rowObjectVersion->object_id=$object_id;
		$rowObjectVersion->metadata_id=$rowMetadata->id;
		if (count($listVersions) > 0)
			$rowObjectVersion->parent_id=$listVersions[0]->id;
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowObjectVersion->guid == null)
			$rowObjectVersion->guid = helper_easysdi::getUniqueId();
		
		if (!$rowObjectVersion->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
			exit();
		}
		
		/* GESTION VIRALE DES VERSIONS
		 * 
		 * Si l'objet en question à des liens enfants, les étudier afin de voir s'il faut créer de nouvelles versions des enfants également
		*/
		/*
		// Récupérer tous les liens enfants
		$child_objectlinks=array();
		$query = 'SELECT l.child_id as child_id, child.objecttype_id as child_objecttype' .
				' FROM #__sdi_objectlink l
				  INNER JOIN #__sdi_object child ON child.id=l.child_id' .
				' WHERE parent_id=' . $rowObject->id.
				' ORDER BY child.name';
		$database->setQuery($query);
		
		$child_objectlinks = array_merge($child_objectlinks, $database->loadObjectList());
		
		if (count($child_objectlinks) >0)
		{
			foreach($child_objectlinks as $ol)
			{
				// Regarder le type d'objet de l'enfant
				$rowChild = new object($database);
				$rowChild->load($ol->child_id);
				
				// Regarder s'il existe un lien entre ces types d'objet
				$rowObjectTypeLink =array();
				$query = 'SELECT *' .
						' FROM #__sdi_objecttypelink' .
						' WHERE parent_id=' . $rowObject->objecttype_id.
						' 	AND child_id=' . $rowChild->objecttype_id;
				
				$database->setQuery($query);
				
				$rowObjectTypeLink = array_merge($rowObjectTypeLink, $database->loadObjectList());
							
				// Si le lien existe, regarder s'il demande une gestion virale des versions
				if (count($rowObjectTypeLink) >0)
				{
					foreach($rowObjectTypeLink as $otl)
					{
						if ($otl->flowdown_versioning)
						{
							// Récupérer toutes les versions de l'objet enfant, ordonnées de la plus récente à la plus ancienne
							$childListVersions = array();
							$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$rowChild->id." ORDER BY created DESC" );
							$childListVersions = array_merge( $childListVersions, $database->loadObjectList() );
							
							// Récupérer la métadonnée de la dernière version de l'objet
							$childLastMetadata = new metadata( $database );
							if (count($childListVersions) > 0)
								$childLastMetadata->load($childListVersions[0]->metadata_id);
								
							// Generate automatic guid for metadata id
							require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
							$new_metadata_guid = helper_easysdi::getUniqueId();
							
							// Créer une nouvelle version de l'enfant
							ADMIN_objectversion::createVersion($rowChild, $childLastMetadata->guid, $new_metadata_guid, $option);
							
							// Construire la nouvelle version
							$childObjectVersion= new objectversion( $database );
							
							if (!$childObjectVersion->bind( $_POST )) {
							
								$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
								$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
								exit();
							}

							// ET LE NOM? ET LA DESCRIPTION?
							$childObjectVersion->object_id=$rowChild->id;
							$childObjectVersion->metadata_id=$new_metadata_guid;
							if (count($childListVersions) > 0)
								$childObjectVersion->parent_id=$childListVersions[0]->id;
							
							// Générer un guid
							require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
							if ($childObjectVersion->guid == null)
								$childObjectVersion->guid = helper_easysdi::getUniqueId();
							
							if (!$childObjectVersion->store(false)) {			
								$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
								$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
								exit();
							}
						}
					}
				}
			}
		}*/
	}
	
	function createVersion($rowObject, $metadata_guid, $new_metadata_guid, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		//$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix,':',a.name) as attribute_isocode, at.isocode as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, CONCAT(atns.prefix, ':', at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_list_attributetype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		
		// Récupérer la classe racine
		$root = array();
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery($query);
		$root = array_merge( $root, $database->loadObjectList() );
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$metadata_guid;
		
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		$doc = new DOMDocument();
		$xmlContent = $doc->importNode($cswResults->getElementsByTagName($root[0]->name)->item(0), true);
		$doc->appendChild($xmlContent);				 
		
		/* Remplacer la valeur du noeud fileIdentifier par la valeur metadata_guid*/
        $nodeList = &$doc->getElementsByTagName($idrow[0]->name);

        foreach ($nodeList as $node)
        {
        	// Remplacer la valeur de fileIdentifier par celle de metadata_id pour que
        	// la métadonnée importée prenne son nouvel id 
        	if ($node->parentNode->nodeName == $root[0]->ns.":".$root[0]->name)
        	{
        		foreach ($node->childNodes as $child)
        		{
        			if ($child->nodeName == $idrow[0]->type_isocode)
        			{
        				$child->nodeValue = $new_metadata_guid;
        			}
        		}
        	}
        }
        
		// Insérer dans Geonetwork la nouvelle version de la métadonnée
		$xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<csw:Transaction service=\"CSW\"
		version=\"2.0.2\"
		xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" >
		<csw:Insert>
		".substr($doc->saveXML(), strlen('<?xml version="1.0"?>'))."
		</csw:Insert>
		</csw:Transaction>";
		
		//$doc->save("C:\\RecorderWebGIS\\".$new_metadata_guid.".xml");
		
		$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
		
		$insertResults = DOMDocument::loadXML($result);
		$xpathInsert = new DOMXPath($insertResults);
		$xpathInsert->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
		$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
		
		if ($inserted <> 1)
		{
			$mainframe->enqueueMessage('Error on metadata insert',"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
			exit();
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
		$object_id = JRequest::getVar('object_id',0);
		
		// Check the attribute in if checked out
		$rowObject = new objectversion( $database );
		$rowObject->load($object_id);
		$rowObject->checkin();
	}
	
	/**
	* Back
	*/
	function backObjectVersion($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();
		$object_id = JRequest::getVar('object_id',0);
		
		// Check the attribute in if checked out
		$rowObject = new objectversion( $database );
		$rowObject->load($object_id);
		$rowObject->checkin();
	}
	
	function viewObjectVersionLink($objectversion_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		if ($objectversion_id == 0 and !JRequest::getVar('objectversion_id'))
		{
			$msg = JText::_('CATALOG_OBJECT_VIEWOBJECTVERSIONLINK_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion", $msg);
			exit;
		}
		
		$object_id = JRequest::getVar('object_id',0);
		
		// get list of parents for this object
		$parent_objectlinks=array();
		$query = 'SELECT parent.id as value, parent.name as name' .
				' FROM #__sdi_objectversionlink l
				  INNER JOIN #__sdi_object parent ON parent.id=l.parent_id' .
				' WHERE child_id=' . $objectversion_id.
				' ORDER BY parent.name';
		$database->setQuery($query);
		$parent_objectlinks = array_merge($parent_objectlinks, $database->loadObjectList());
		
		// get list of childs for this object
		$child_objectlinks=array();
		$query = 'SELECT child.id as value, child.name as name' .
				' FROM #__sdi_objectversionlink l
				  INNER JOIN #__sdi_object child ON child.id=l.child_id' .
				' WHERE parent_id=' . $objectversion_id.
				' ORDER BY child.name';
		$database->setQuery($query);
		$child_objectlinks = array_merge($child_objectlinks, $database->loadObjectList());
		
		HTML_objectversion::viewObjectVersionLink($parent_objectlinks, $child_objectlinks, $objectversion_id, $object_id, $option);
	}
	
	function manageObjectVersionLink($object_id, $option)
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
		
		HTML_objectversion::manageObjectVersionLink($objectlinks, $selected_objectlinks, $listObjecttypes, $listStatus, $listManagers, $listEditors, $object_id, $option);
	}
	
	function getObjectVersionForLink($option)
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
	
	function saveObjectVersionLink($option)
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
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
				exit();
			}
		}
	}
}
?>