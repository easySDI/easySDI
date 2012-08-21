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

class ADMIN_objectversion {
	
	function listObjectVersion($option) {
		global  $mainframe;
		$db =& JFactory::getDBO();
		$object_id = JRequest::getVar ('object_id');
		$context	= 'listObjectVersion';
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
			and $filter_order <> "created" 
			and $filter_order <> "description" 
			and $filter_order <> "state"
			and $filter_order <> "title"
			and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' ORDER BY ov.'. $filter_order .' '. $filter_order_Dir;
		
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
		
		// R�cup�rer toutes les versions de l'objet, ordonn�es de la plus r�cente � la plus ancienne
		$listVersions = array();
		$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY created DESC" );
		$listVersions = array_merge( $listVersions, $database->loadObjectList() );
		
		// R�cup�rer la m�tadonn�e de la derni�re version de l'objet
		$rowLastMetadata = new metadata( $database );
		$rowLastObjectVersion = new objectversion( $database );
		
		if (count($listVersions) > 0)
		{
			$rowLastMetadata->load($listVersions[0]->metadata_id);
			$rowLastObjectVersion->load($listVersions[0]->id);
		}
	
		if ($rowLastMetadata->metadatastate_id <> 1 and $rowLastMetadata->metadatastate_id <> 2)
		{
			$mainframe->enqueueMessage(JText::_("CATALOG_OBJECTVERSION_NEW_LASTNOTPUBLISHED_ERROR_MSG"),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
			exit();
		}
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_objectversion", false);
		
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
		
		// Generate automatic guid for metadata id
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		$metadata_guid = helper_easysdi::getUniqueId();
		
		// R�cup�rer toutes les versions de l'objet, ordonn�es de la plus r�cente � la plus ancienne
		$listVersionNames = array();
		$database->setQuery( "SELECT name FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY created DESC" );
		$listVersionNames = array_merge( $listVersionNames, $database->loadResultArray() );
		
		HTML_objectversion::newObjectVersion($object_id, $fieldsLength, $metadata_guid, $listVersionNames, $option);
	}

	function editObjectVersion($id, $option)
	{
		?>
		<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;
				if (pressbutton != 'saveObjectVersion' && pressbutton != 'applyObjectVersion') {
					submitform( pressbutton );
					return;
				}
		
				// do field validation
				submitform( pressbutton );
			}
		</script>
		
		<?php 
		global $mainframe;
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$object_id = JRequest::getVar ('object_id');
		
		// R�cup�rer toutes les versions de l'objet, ordonn�es de la plus r�cente � la plus ancienne
		$listVersions = array();
		$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY created DESC" );
		$listVersions = array_merge( $listVersions, $database->loadObjectList() );
		
		// R�cup�rer la m�tadonn�e de la derni�re version de l'objet
		$rowLastMetadata = new metadata( $database );
		$rowLastObjectVersion = new objectversion( $database );
		
		if (count($listVersions) > 0)
		{
			$rowLastMetadata->load($listVersions[0]->metadata_id);
			$rowLastObjectVersion->load($listVersions[0]->id);
		}
		
		if ($id==0)
		{
			if ($rowLastMetadata->metadatastate_id <> 1 and $rowLastMetadata->metadatastate_id <> 2)
			{
				$mainframe->enqueueMessage(JText::_("CATALOG_OBJECTVERSION_NEW_LASTNOTPUBLISHED_ERROR_MSG"),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
				exit();
			}
		}
		
		$row = new objectversion( $database );
		$row->load( $id );

		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_objectversion", false);
		
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
		
		if ($id == 0)
		{
			// Generate automatic guid for metadata id
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			$row->guid = helper_easysdi::getUniqueId();
			
			$metadata_guid = helper_easysdi::getUniqueId();
		}
		else
		{
			$rowMetadata = new metadata($database);
			$rowMetadata->load($row->metadata_id);
			$metadata_guid = $rowMetadata->guid;
		}
		
		
		HTML_objectversion::editObjectVersion($row, $object_id, $fieldsLength, $metadata_guid, $option);
	}
	
	function saveObjectVersion($option)
	{	
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$objectversion_id = $_POST['objectversion_id'];
		$object_id = $_POST['object_id'];
		$metadata_guid = $_POST['metadata_guid'];
		
		if ($objectversion_id == 0) // Nouvelle version
		{
			// R�cup�rer toutes les versions de l'objet, ordonn�es de la plus r�cente � la plus ancienne
			$listVersions = array();
			$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY created DESC" );
			$listVersions = array_merge( $listVersions, $database->loadObjectList() );
			
			// R�cup�rer la m�tadonn�e de la derni�re version de l'objet
			$rowLastMetadata = new metadata( $database );
			$rowLastObjectVersion = new objectversion( $database );
			if (count($listVersions) > 0)
			{
				$rowLastMetadata->load($listVersions[0]->metadata_id);
				$rowLastObjectVersion->load($listVersions[0]->id);
			}
			// R�cup�rer l'objet
			$rowObject = new object($database);
			$rowObject->load($_POST['object_id']);
			
			// Cr�er une nouvelle m�tadonn�e
			$rowMetadata = new metadata( $database );
			$rowMetadata->guid = $_POST['metadata_guid'];
			$rowMetadata->name = $_POST['metadata_guid'];
			$rowMetadata->created = $_POST['created'];
			$rowMetadata->createdby = $_POST['createdby'];
			$rowMetadata->metadatastate_id = 4;
			/*if (count($listVersions) > 0)
				$rowMetadata->visibility_id = $rowLastMetadata->visibility_id;*/
			
			
			// S'il y a une version pr�c�dente, la copier, sinon cr�er un nouveau contenu
			if (count($listVersions) > 0)
			{
				ADMIN_objectversion::createVersion($rowObject, $object_id, $rowLastMetadata->guid, $_POST['metadata_guid'], $option);
			}
			else
			{
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
				$catalogUrlBase = config_easysdi::getValue("catalog_url");
				// R�cup�rer l'attribut qui correspond au stockage de l'id
				$idrow = "";
				//$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix,':',a.isocode) as attribute_isocode, at.isocode as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
				$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, CONCAT(atns.prefix, ':', at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
				$idrow = $database->loadObjectList();
				
				// R�cup�rer la classe racine
				$root = array();
				$database->setQuery("SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix,':',c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id);
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
				
				//$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
				$result = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase, $xmlstr);
				
				$insertResults = DOMDocument::loadXML($result);
				
				$xpathInsert = new DOMXPath($insertResults);
				$xpathInsert->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
				$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
				
				if ($inserted <> 1)
				{
					$mainframe->enqueueMessage(JText::_('CATALOG_METADATA_SAVE_INSERTPROBLEM_MSG'),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
					exit();
				}
			}
			
			if (!$rowMetadata->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
				exit();
			}
					
			// Construire la nouvelle version
			$rowObjectVersion= new objectversion( $database );
			
			if (!$rowObjectVersion->bind( $_POST )) {
			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
				exit();
			}		
			$rowObjectVersion->object_id=$object_id;
			$rowObjectVersion->metadata_id=$rowMetadata->id;
			$rowObjectVersion->title=$_POST['title'];
			if (count($listVersions) > 0)
				$rowObjectVersion->parent_id=$listVersions[0]->id;
			
			// G�n�rer un guid
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			if ($rowObjectVersion->guid == null)
				$rowObjectVersion->guid = helper_easysdi::getUniqueId();
			
			if (!$rowObjectVersion->store(false)) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
				exit();
			}
			
			/* GESTION VIRALE DES VERSIONS
			 * 
			 * Si l'objet en question a des liens enfants, les étudier afin de voir s'il faut créer de nouvelles versions des enfants également
			*/
			// R�cup�rer tous les liens enfants de la derni�re version
			$child_objectlinks=array();
			$query = 'SELECT l.child_id as child_id, 
							 o_parent.id as parentobject_id, 
							 o_child.id as childobject_id, 
							 o_child.objecttype_id as child_objecttype, 
							 o_parent.objecttype_id as parent_objecttype' .
					' FROM #__sdi_objectversionlink l
					  INNER JOIN #__sdi_objectversion child ON child.id=l.child_id
					  INNER JOIN #__sdi_objectversion parent ON parent.id=l.parent_id
					  INNER JOIN #__sdi_object o_parent ON o_parent.id=parent.object_id
					  INNER JOIN #__sdi_object o_child ON o_child.id=child.object_id' .
					' WHERE l.parent_id=' . $rowLastObjectVersion->id;
			$database->setQuery($query);
			
			$child_objectlinks = array_merge($child_objectlinks, $database->loadObjectList());
			 
			if (count($child_objectlinks) >0)
			{
				foreach($child_objectlinks as $ol)
				{
					// Récupérer l'objet lié à la version enfant
					$rowChildObject = new object($database);
					$rowChildObject->load($ol->childobject_id);
					
					// Regarder s'il existe un lien entre ces types d'objet
					$rowObjectTypeLink =array();
					$query = 'SELECT *' .
							' FROM #__sdi_objecttypelink' .
							' WHERE parent_id=' . $ol->parent_objecttype.
							' 	AND child_id=' . $ol->child_objecttype;
					
					$database->setQuery($query);
					$rowObjectTypeLink = array_merge($rowObjectTypeLink, $database->loadObjectList());
					
					// Si le lien existe, regarder s'il demande une gestion virale des versions
					if (count($rowObjectTypeLink) >0)
					{
						foreach($rowObjectTypeLink as $otl)
						{
							// Récupérer toutes les versions de l'objet enfant, ordonnées de la plus récente à la plus ancienne
							$childListVersions = array();
							$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$rowChildObject->id." ORDER BY created DESC" );
							$childListVersions = array_merge( $childListVersions, $database->loadObjectList() );
							
							// Récupérer la métadonnée de la dernière version de l'objet
							$childLastObjectVersion = new objectversion( $database );
							$childLastMetadata = new metadata( $database );
							if (count($childListVersions) > 0)
							{
								$childLastMetadata->load($childListVersions[0]->metadata_id);
								$childLastObjectVersion->load($childListVersions[0]->id);
							}
								
							// Le lien indique qu'il faut r�percuter la cr�ation de nouvelles versions sur les liens enfants
							if ($otl->flowdown_versioning)
							{
								/*
								 * FLOWDOWN_VERSIONING
								 * Faire une nouvelle version de la version li�e
								 */
								// Generate automatic guid for metadata id
								require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
								$new_metadata_guid = helper_easysdi::getUniqueId();
								
								// Créer une nouvelle métadonnée
								$childMetadata = new metadata( $database );
								$childMetadata->guid = $new_metadata_guid;
								$childMetadata->name = $new_metadata_guid;
								$childMetadata->created = $_POST['created'];
								$childMetadata->createdby = $_POST['createdby'];
								$childMetadata->metadatastate_id = 4;
								
								// Créer une nouvelle version de l'enfant
								ADMIN_objectversion::createVersion($rowChildObject, $object_id, $childLastMetadata->guid, $new_metadata_guid, $option);
	
								if (!$childMetadata->store()) 
								{
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
									$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
									exit();
								}
								
								// Construire la nouvelle version
								$childObjectVersion= new objectversion( $database );
								
								if (!$childObjectVersion->bind( $_POST )) {
								
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
									$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
									exit();
								}
	
								// Indiquer l'objet, la m�tadonn�e et la description de la nouvelle version enfant
								$childObjectVersion->object_id=$rowChildObject->id;
								$childObjectVersion->metadata_id=$childMetadata->id;
								$childObjectVersion->description=$_POST['description'];
								$childObjectVersion->title=$_POST['title'];
								
								// Indiquer le parent de cette nouvelle version enfant
								if (count($childListVersions) > 0)
									$childObjectVersion->parent_id=$childListVersions[0]->id;
								
								// G�n�rer un guid
								require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
								$childObjectVersion->guid = helper_easysdi::getUniqueId();
								
								if (!$childObjectVersion->store(false)) {			
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
									$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
									exit();
								}
							
								/*
								 * NOUVEAUX LIENS
								 * Lier la nouvelle version de l'enfant � la nouvelle version du parent
								 */
								$newLink = new objectversionlink($database);
								$newLink->parent_id=$rowObjectVersion->id;
								$newLink->child_id=$childObjectVersion->id;
								
								if (!$newLink->store(false)) 
								{			
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
									$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
									exit();
								}
							}
							else // Sinon lier ces enfants � la nouvelle version
							{
								/*
								 * NOUVEAUX LIENS
								 * Lier la version de l'enfant � la nouvelle version du parent
								 */
								$newLink = new objectversionlink($database);
								$newLink->parent_id=$rowObjectVersion->id;
								$newLink->child_id=$ol->child_id;
								
								if (!$newLink->store(false)) 
								{			
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
									$mainframe->redirect("index.php?option=$option&task=listObjectVersion" );
									exit();
								}
							}
						}
					}
				}
			}
		}
		else // Mettre � jour la version
		{
			$rowObjectVersion= new objectversion( $database );
			
			$rowObjectVersion->load( $objectversion_id);		
			$rowObjectVersion->description = $_POST['description'];
			
			if (!$rowObjectVersion->store()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
				exit();
			}
		}
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyObjectVersion' :
				// Reprendre en �dition l'objet
				TOOLBAR_objectversion::_EDIT();
				ADMIN_objectversion::editObjectVersion($rowObjectVersion->id,$option);
				break;

			case 'saveObjectVersion' :
			default :
				break;
		}
	}
	
	function deleteObjectVersion($cid, $option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
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
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
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
				$mainframe->enqueueMessage(JText::_("CATALOG_OBJECTVERSION_DELETE_STATE_MSG"),"error");
				$mainframe->redirect(JRoute::_('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id, false ));
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
					$mainframe->redirect(JRoute::_('index.php?option='.$option.'&task=listObjectVersion&object_id='.$object_id, false ));
					exit;
				}
			}
			
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
			/*
			$xpathDelete = new DOMXPath($deleteResults);
			$xpathDelete->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
			$deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
			
			if ($deleted <> 1)
			{
				$mainframe->enqueueMessage('Error on metadata delete',"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
				exit();
			}
			*/
			
			// Si une version suit, corriger son parent_id avec celui de la version qui va �tre supprim�e
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
					$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
				}
			}
			
			// Supprimer l'historique d'assignement
			$query = 'DELETE FROM #__sdi_history_assign
					  WHERE objectversion_id=' . $objectversion->id;
			$database->setQuery($query);
			/*if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}*/
			
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
					$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
				}
			}
			
			if (!$objectversion->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
			}
			
			if (!$metadata->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
	}
	
	function createVersion($rowObject, $object_id, $metadata_guid, $new_metadata_guid, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO(); 
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		//$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix,':',a.name) as attribute_isocode, at.isocode as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, CONCAT(atns.prefix, ':', at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		
		// Récupérer la classe racine
		$root = array();
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery($query);
		$root = array_merge( $root, $database->loadObjectList() );
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$metadata_guid;
		
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		
		// Si la métadonnée du lien enfant n'existe plus, sauter le reste des étapes
		$doc = new DOMDocument();
		if ($cswResults->getElementsByTagName($root[0]->name)->length == 0)
		{
			$mainframe->enqueueMessage(JText::_('CATALOG_METADATA_SAVE_RETRIEVEPROBLEM_MSG'),"ERROR");
		}
		else
		{
			$xmlContent = $doc->importNode($cswResults->getElementsByTagName($root[0]->name)->item(0), true);
			$doc->appendChild($xmlContent);				 
			
			/* Remplacer la valeur du noeud fileIdentifier par la valeur metadata_guid*/
			$nodeList = &$doc->getElementsByTagName($idrow[0]->name);
				
	        foreach ($nodeList as $node)
	        {
	        	// Remplacer la valeur de fileIdentifier par celle de metadata_id pour que
	        	// la m�tadonn�e import�e prenne son nouvel id 
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
			
			//$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
			$result = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase, $xmlstr);
			
			$insertResults = DOMDocument::loadXML($result);
			$xpathInsert = new DOMXPath($insertResults);
			$xpathInsert->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
			$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
			
			if ($inserted <> 1)
			{
				$mainframe->enqueueMessage(JText::_('CATALOG_METADATA_SAVE_INSERTPROBLEM_MSG'),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
				exit();
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
		$object_id = JRequest::getVar('object_id',0);
		
		// Check the attribute in if checked out
		$rowObject = new objectversion( $database );
		$rowObject->load($object_id);
		$rowObject->checkin();
	}
	
	function archiveObjectVersion($cid ,$option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		$object_id = JRequest::getVar('object_id',0);
		
		if (!is_array( $cid ) or count( $cid ) < 1 or $cid[0] == 0) {
			$msg = JText::_('CATALOG_OBJECTVERSION_ARCHIVE_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id", $msg);
			exit;
		}		
		
		foreach( $cid as $id )
		{
			$objectversion = new objectversion( $database );
			$objectversion->load( $id );

			$metadata = new metadata($database);
			$metadata->load( $objectversion->metadata_id );
			
			if ($metadata->metadatastate_id <> 1) // Impossible d'archiver si le statut n'est pas "PUBLISHED"
			{
				if ($metadata->metadatastate_id == 2) // Message particulier si on est d�j� dans l'�tat ARCHIVED
					$msg = JText::sprintf('CATALOG_OBJECTVERSION_ARCHIVEMETADATA_ARCHIVED_MSG', $object->name);
				else
					$msg = JText::sprintf('CATALOG_OBJECTVERSION_ARCHIVEMETADATA_MSG', $object->name);
				$mainframe->enqueueMessage($msg, "error");
				continue;
			}
			
			$metadata->metadatastate_id=2;
			
			if (!$metadata->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
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
		
		// R�cup�rer les �tats du listing des objets, pour �viter que les �tats des versions soient utilis�s
		// alors qu'on change de contexte
		JRequest::setVar('filter_order', $mainframe->getUserState($option."listObject.filter_order"));
		JRequest::setVar('filter_order_Dir', $mainframe->getUserState($option."listObject.filter_order_Dir"));
		
		// Check the attribute in if checked out
		$rowObject = new objectversion( $database );
		$rowObject->load($object_id);
		$rowObject->checkin();
	}
	
	function viewObjectVersionLink($objectversion_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		
		if ($objectversion_id == 0 and !JRequest::getVar('objectversion_id'))
		{
			$msg = JText::_('CATALOG_OBJECT_VIEWOBJECTVERSIONLINK_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id", $msg);
			exit;
		}
		
		$object_id = JRequest::getVar('object_id',0);
		
		// get list of parents for this object
		$parent_objectlinks=array();
		$query = "SELECT parent.id as value, CONCAT(o.name, ' ', parent.title)  as name , o.code AS code , t.label as objecttype, ms.label as status
				  FROM #__sdi_objectversionlink l
				  INNER JOIN #__sdi_objectversion parent ON parent.id=l.parent_id
				  INNER JOIN #__sdi_object o ON o.id=parent.object_id
				  INNER JOIN #__sdi_metadata m ON m.id = parent.metadata_id
				  INNER JOIN #__sdi_list_metadatastate ms ON ms.id = m.metadatastate_id
				  INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id
				  INNER JOIN #__sdi_translation t ON ot.guid = t.element_guid
				  INNER JOIN #__sdi_language lg ON t.language_id=lg.id
				  INNER JOIN #__sdi_list_codelang cl ON lg.codelang_id=cl.id
				  WHERE l.child_id=" . $objectversion_id."
				  AND cl.code='".$language->_lang."'
				  ORDER BY parent.name";
		$database->setQuery($query);
		$parent_objectlinks = array_merge($parent_objectlinks, $database->loadObjectList());
		foreach ($parent_objectlinks as $parent_objectlink)
			$parent_objectlink->status = JText::_($parent_objectlink->status);
		
		// get list of childs for this object
		$child_objectlinks=array();
		$query = "SELECT child.id as value, CONCAT(o.name,' ', child.title) as name , o.code AS code , t.label as objecttype, ms.label as status
				  FROM #__sdi_objectversionlink l
				  INNER JOIN #__sdi_objectversion child ON child.id=l.child_id
				  INNER JOIN #__sdi_object o ON o.id=child.object_id
				  INNER JOIN #__sdi_metadata m ON m.id = child.metadata_id
				  INNER JOIN #__sdi_list_metadatastate ms ON ms.id = m.metadatastate_id
				  INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id
				  INNER JOIN #__sdi_translation t ON ot.guid = t.element_guid
				  INNER JOIN #__sdi_language lg ON t.language_id=lg.id
				  INNER JOIN #__sdi_list_codelang cl ON lg.codelang_id=cl.id
				  WHERE l.parent_id=" . $objectversion_id."
				  AND cl.code='".$language->_lang."'
				  ORDER BY child.name";
		$database->setQuery($query);
		$child_objectlinks = array_merge($child_objectlinks, $database->loadObjectList());
		foreach ($child_objectlinks as $child_objectlink)
			$child_objectlink->status = JText::_($child_objectlink->status);
		
		HTML_objectversion::viewObjectVersionLink($parent_objectlinks, $child_objectlinks, $objectversion_id, $object_id, $option);
	}
	
	function manageObjectVersionLink($objectversion_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		
		$object_id = JRequest::getVar('object_id',0);
		
		if ($objectversion_id == 0 and !JRequest::getVar('objectversion_id'))
		{
			$msg = JText::_('CATALOG_OBJECT_MANAGEOBJECTVERSIONLINK_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id", $msg);
			exit;
		}
		
		$rowObject = new object($database);
		$rowObject->load($object_id);
		
		// get list of childs for this object
		$selected_objectlinks=array();
		$query = "SELECT child.id as value, o.objecttype_id as objecttype_id, CONCAT(o.name, ' ', child.title) as name, t.label as objecttype, ms.label as status 
				  FROM #__sdi_objectversionlink l
				  INNER JOIN #__sdi_objectversion child ON child.id=l.child_id
				  INNER JOIN #__sdi_object o ON o.id=child.object_id
				  INNER JOIN #__sdi_metadata m ON m.id = child.metadata_id
				  INNER JOIN #__sdi_list_metadatastate ms ON ms.id = m.metadatastate_id
				  INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id
				  INNER JOIN #__sdi_translation t ON ot.guid = t.element_guid
				  INNER JOIN #__sdi_language lg ON t.language_id=lg.id
				  INNER JOIN #__sdi_list_codelang cl ON lg.codelang_id=cl.id 
				  WHERE l.parent_id=" . $objectversion_id."
				  AND cl.code='".$language->_lang."'
				  ORDER BY child.name";
		$database->setQuery($query);
		$selected_objectlinks = array_merge($selected_objectlinks, $database->loadObjectList());
		foreach ($selected_objectlinks as $selected_objectlink)
			$selected_objectlink->status = JText::_($selected_objectlink->status);
		
		// get list of versions which are not childs
		$unselected_objectlinks=array();
		$temp_objectlinks=array();
		$objectlinks=array();
// 		$query = 'SELECT ov.id as value, o.objecttype_id as objecttype_id, CONCAT(o.name, " ", ov.title) as name' .
// 				' FROM #__sdi_objectversion ov
// 				INNER JOIN #__sdi_object o ON o.id=ov.object_id' .
// 				' WHERE ov.id<>' . $objectversion_id.
// 				' ORDER BY name';
		$query = "SELECT ov.id as value, o.objecttype_id as objecttype_id, CONCAT(o.name, ' ' , ov.title) as name, t.label as objecttype, ms.label as status 
				  FROM #__sdi_objectversion ov
				  INNER JOIN #__sdi_object o ON o.id=ov.object_id
				  INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id
				  INNER JOIN #__sdi_list_metadatastate ms ON ms.id = m.metadatastate_id
				  INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id
				  INNER JOIN #__sdi_translation t ON ot.guid = t.element_guid
				  INNER JOIN #__sdi_language lg ON t.language_id=lg.id
				  INNER JOIN #__sdi_list_codelang cl ON lg.codelang_id=cl.id 
				  WHERE ov.id<>" . $objectversion_id."
				  AND cl.code='".$language->_lang."'
				  ORDER BY o.name";
		$database->setQuery($query);
		$unselected_objectlinks = array_merge($unselected_objectlinks, $database->loadObjectList());
		
		$temp_objectlinks = helper_easysdi::array_obj_diff($unselected_objectlinks, $selected_objectlinks);
		foreach ($temp_objectlinks as $temp_objectlink)
			$temp_objectlink->status = JText::_($temp_objectlink->status);
		
		// Recréer le tableau afin d'avoir des clés qui se suivent
		foreach ($temp_objectlinks as $object)
			$objectlinks[] = $object;
		
		$objecttypes = array();
		$listObjecttypes = array();
		$database->setQuery( "SELECT ot.id AS value, t.label as text 
				 FROM #__sdi_objecttype ot 
				 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
				 INNER JOIN #__sdi_language l ON t.language_id=l.id
				 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
				 WHERE ot.predefined=false 
				 	   AND cl.code='".$language->_lang."'
				 ORDER BY t.label");
		
		$objecttypes= array_merge( $objecttypes, $database->loadObjectList() );
		foreach($objecttypes as $ot)
		{
			$listObjecttypes[$ot->value] = $ot->text;
		}
		$listObjecttypes = HTML_metadata::array2extjs($listObjecttypes, true);
		
		$status = array();
		$listStatus = array();
		$database->setQuery( "SELECT id as value, label as text FROM #__sdi_list_metadatastate ORDER BY label" );
		$status= array_merge( $status, $database->loadObjectList() );
		foreach($status as $s)
		{
			$listStatus[$s->value] = $s->text;
		}
		helper_easysdi::arrayTranslate($listStatus);
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

		$objecttypelink = array();
		$query = 'SELECT child_ot.id as objecttype_id, otl.childbound_upper as childbound_upper' .
				' FROM #__sdi_objecttypelink otl
				  INNER JOIN #__sdi_objecttype child_ot ON child_ot.id=otl.child_id
				  WHERE otl.parent_id =' . $rowObject->objecttype_id;
		$database->setQuery($query);
		$objecttypelink = array_merge( $objecttypelink, $database->loadObjectList() );

		
		HTML_objectversion::manageObjectVersionLink($objectlinks, $selected_objectlinks, $listObjecttypes, $listStatus, $listManagers, $listEditors, $objectversion_id, $object_id, $objecttypelink, $option);
	}

	function getObjectVersionForLink($option)
	{
		global  $mainframe;
		$database 			=& JFactory::getDBO(); 
		$language 			=& JFactory::getLanguage();
		
		$dir 				= $_POST['dir'];
		$sort 				= $_POST['sort'];
		$object_id			= $_POST['object_id'];
		$objectversion_id	= $_POST['objectversion_id'];
		$selectedObjects 	= $_POST['selectedObjects'];
		
		$objecttype_id 		=null;
		$id					=null;
		$name				=null;
		$status				=null;
		$version			=null;
		$editor				=null;
		$manager			=null;
		$fromDate			=null;
		$toDate				=null;
		
		if (array_key_exists('objecttype_id', $_POST))
			$objecttype_id = $_POST['objecttype_id'];
		if (array_key_exists('id', $_POST))
			$id = $_POST['id'];
		if (array_key_exists('name', $_POST))
			$name = $_POST['name'];
		if (array_key_exists('status', $_POST))
			$status = $_POST['status'];
		if (array_key_exists('version', $_POST))
			$version = $_POST['version'];
		if (array_key_exists('editor', $_POST))
			$editor = $_POST['editor'];
		if (array_key_exists('manager', $_POST))
			$manager = $_POST['manager'];
		if (array_key_exists('fromDate', $_POST))
			$fromDate = $_POST['fromDate'];
		if (array_key_exists('toDate', $_POST))
			$toDate = $_POST['toDate'];
				
		if ($fromDate <> "")
			$fromDate = date('Y-m-d', strtotime($fromDate))." 00:00:00";
		if ($toDate <> "")
			$toDate = date('Y-m-d', strtotime($toDate))." 23:59:59";

		
		$rowParentObject = new object($database);
		$rowParentObject->load($object_id);

		// Récupérer tous les objets du type d'objet sélectionné,
		// qui ne sont ni l'objet courant, ni dans la liste des objets sélectionnés,
		// et pour lesquels il existe une relation parent/enfant entre les types d'objets
		if($version == 'Last' )
		{
			$query =
			"SELECT ov.id as value, o.objecttype_id as objecttype_id, o.name as object_name, CONCAT(o.name, ' ', ov.title) as name, otl.parentbound_upper, link.* , t.label as objecttype, ms.label as status
			FROM #__sdi_objectversion ov
			INNER JOIN #__sdi_object o ON ov.object_id=o.id
			INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id
			INNER JOIN
			(
				SELECT ssm.id, ssm.metadatastate_id,ssm.guid,ssm.published,ssm.archived,max( ssm.published ), ssov.object_id
				FROM jos_sdi_metadata ssm
				INNER JOIN jos_sdi_list_metadatastate ssms ON ssms.id = ssm.metadatastate_id
				INNER JOIN jos_sdi_objectversion ssov ON ssov.metadata_id = ssm.id
				WHERE 
				(
					(ssms.code='published' AND ssm.published <=NOW())
					OR
					(ssms.code='archived' AND ssm.archived >NOW())
				) 
				GROUP BY ssov.object_id
				ORDER BY ssov.object_id
			) 
			 m ON ov.metadata_id=m.id
			INNER JOIN #__sdi_list_metadatastate ms ON ms.id = m.metadatastate_id
			INNER JOIN #__sdi_translation t ON ot.guid = t.element_guid
			INNER JOIN #__sdi_language lg ON t.language_id=lg.id
			INNER JOIN #__sdi_list_codelang cl ON lg.codelang_id=cl.id
			LEFT OUTER JOIN #__sdi_manager_object ma ON o.id = ma.object_id
			LEFT OUTER JOIN #__sdi_editor_object e ON o.id = e.object_id
			LEFT OUTER JOIN #__sdi_objecttypelink otl ON otl.child_id=o.objecttype_id
			LEFT OUTER JOIN
			(SELECT count(*) as linkCount, ovl.parent_id, parent_o.objecttype_id as parentobjecttype_id, ovl.child_id
			FROM #__sdi_objectversionlink ovl
			INNER JOIN #__sdi_objectversion parent_ov ON ovl.parent_id=parent_ov.id
			INNER JOIN #__sdi_object parent_o ON parent_ov.object_id=parent_o.id
			WHERE parent_o.objecttype_id=".$rowParentObject->objecttype_id."
			GROUP BY ovl.child_id
			) AS link ON link.child_id=ov.id
			WHERE otl.child_id IS NOT NULL
			AND otl.parent_id=".$rowParentObject->objecttype_id."
			AND ov.id<>".$objectversion_id."
			AND cl.code='".$language->_lang."'
			AND
			(link.child_id IS NULL
			OR
			(link.linkCount < otl.parentbound_upper)
			)
			";
		}
		else 
		{
			$query = 
			"SELECT ov.id as value, o.objecttype_id as objecttype_id, o.name as object_name, CONCAT(o.name, ' ', ov.title) as name, otl.parentbound_upper, link.* , t.label as objecttype, ms.label as status
			FROM #__sdi_objectversion ov 
			INNER JOIN #__sdi_object o ON ov.object_id=o.id
			INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id 
			INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
			INNER JOIN #__sdi_list_metadatastate ms ON ms.id = m.metadatastate_id
			INNER JOIN #__sdi_translation t ON ot.guid = t.element_guid
			INNER JOIN #__sdi_language lg ON t.language_id=lg.id
			INNER JOIN #__sdi_list_codelang cl ON lg.codelang_id=cl.id 
			LEFT OUTER JOIN #__sdi_manager_object ma ON o.id = ma.object_id
			LEFT OUTER JOIN #__sdi_editor_object e ON o.id = e.object_id 
			LEFT OUTER JOIN #__sdi_objecttypelink otl ON otl.child_id=o.objecttype_id
			LEFT OUTER JOIN 
							(SELECT count(*) as linkCount, ovl.parent_id, parent_o.objecttype_id as parentobjecttype_id, ovl.child_id 
							 FROM #__sdi_objectversionlink ovl 
							 INNER JOIN #__sdi_objectversion parent_ov ON ovl.parent_id=parent_ov.id
							 INNER JOIN #__sdi_object parent_o ON parent_ov.object_id=parent_o.id
							 WHERE parent_o.objecttype_id=".$rowParentObject->objecttype_id."
							 GROUP BY ovl.child_id
							) AS link ON link.child_id=ov.id
			WHERE otl.child_id IS NOT NULL 
				  AND otl.parent_id=".$rowParentObject->objecttype_id."
				  AND ov.id<>".$objectversion_id."
				  AND cl.code='".$language->_lang."'
				  AND 
				  (link.child_id IS NULL
				   OR
				   (link.linkCount < otl.parentbound_upper)
				  )
			";
		}
		// Ajout des filtres
		if ($objecttype_id)
			$query .= " AND ot.id=".$objecttype_id;
		if ($id)
			$query .= " AND m.guid LIKE '%".$id."%'";
		if ($name)
			$query .= " AND (o.name LIKE '%".$name."%' OR ov.name LIKE '%".$name."%')";
		if ($status)
			$query .= " AND m.metadatastate_id=".$status;
		if ($editor)
			$query .= " AND e.account_id=".$editor;
		if ($manager)
			$query .= " AND ma.account_id=".$manager;
		if ($fromDate)
			$query .= " AND ov.updated >= '".$fromDate."'";
		if ($toDate)
			$query .= " AND ov.updated <= '".$toDate."'";

		
		// Suppression des entrées déjà sélectionn�es
		if (strlen($selectedObjects) > 0)
			$query .= " AND ov.id NOT IN (".$selectedObjects.")";
			
		$query .= "	ORDER BY $sort $dir";

		$database->setQuery($query);
		$results= $database->loadObjectList();
		
		foreach ($results as $result)
			$result->status = JText::_($result->status);
		
		// Construire le tableau de r�sultats
		$return = array ("total"=>count($results), "links"=>$results);
		
		print_r(HTML_metadata::array2json($return));
		die();
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
					$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id" );
					exit();
				}
			}
		}
	}
	
	function historyAssignMetadata($id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$object_id = $_POST['object_id'];
		
		if ($id == 0 and !JRequest::getVar('objectversion_id'))
		{
			$msg = JText::_('CATALOG_OBJECT_HISTORYASSIGN_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=$object_id", $msg);
			exit;
		}
		
		if (JRequest::getVar('objectversion_id'))
			$id = JRequest::getVar('objectversion_id');
		
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		$rowObjectVersion = new objectversion($database);
		$rowObjectVersion->load($id);
		
		$query = "SELECT COUNT(*) FROM #__sdi_history_assign h
				  WHERE h.objectversion_id=".$rowObjectVersion->id;					
		$database->setQuery($query);
		$total = $database->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		
		$query = "SELECT h.assigned as date, aa.username as assignedby, bb.username as assignedto, o.name as object_name, ov.name as objectversion_name 
                  FROM #__sdi_history_assign h
					INNER JOIN #__sdi_account a ON h.assignedby=a.id
					INNER JOIN #__users aa ON a.user_id=aa.id
					INNER JOIN #__sdi_account b ON h.account_id=a.id
					INNER JOIN #__users bb ON b.user_id=bb.id
					INNER JOIN #__sdi_objectversion ov ON h.objectversion_id=ov.id
					INNER JOIN #__sdi_object o ON o.id=ov.object_id
				  WHERE h.objectversion_id=".$rowObjectVersion->id." ORDER BY date DESC";
		$database->setQuery( $query, $pagination->limitstart, $pagination->limit);
		$rowHistory = $database->loadObjectList();
		
		HTML_objectversion::historyAssignMetadata($rowHistory, $pagination, $id, $object_id, $option);
	}
}
?>