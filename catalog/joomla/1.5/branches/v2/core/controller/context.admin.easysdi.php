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

class ADMIN_context {
	
	function listContext($option) {
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

	function editContext($id, $option)
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
	
	function saveContext($option)
	{	
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$objectversion_id = $_POST['objectversion_id'];
		$object_id = $_POST['object_id'];
		$metadata_guid = $_POST['metadata_guid'];
		
		if ($objectversion_id == 0) // Nouvelle version
		{
			// Récupérer toutes les versions de l'objet, ordonnées de la plus récente à la plus ancienne
			$listVersions = array();
			$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY created DESC" );
			$listVersions = array_merge( $listVersions, $database->loadObjectList() );
			
			// Récupérer la métadonnée de la dernière version de l'objet
			$rowLastMetadata = new metadata( $database );
			$rowLastObjectVersion = new objectversion( $database );
			if (count($listVersions) > 0)
			{
				$rowLastMetadata->load($listVersions[0]->metadata_id);
				$rowLastObjectVersion->load($listVersions[0]->id);
			}
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
			/*if (count($listVersions) > 0)
				$rowMetadata->visibility_id = $rowLastMetadata->visibility_id;*/
			
			
			// S'il y a une version précédente, la copier, sinon créer un nouveau contenu
			if (count($listVersions) > 0)
			{
				ADMIN_objectversion::createVersion($rowObject, $object_id, $rowLastMetadata->guid, $_POST['metadata_guid'], $option);
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
			
			// Générer un guid
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
			 * Si l'objet en question à des liens enfants, les étudier afin de voir s'il faut créer de nouvelles versions des enfants également
			*/
			// Récupérer tous les liens enfants de la dernière version
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
								
							// Le lien indique qu'il faut répercuter la création de nouvelles versions sur les liens enfants
							if ($otl->flowdown_versioning)
							{
								/*
								 * FLOWDOWN_VERSIONING
								 * Faire une nouvelle version de la version liée
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
	
								// Indiquer l'objet, la métadonnée et la description de la nouvelle version enfant
								$childObjectVersion->object_id=$rowChildObject->id;
								$childObjectVersion->metadata_id=$childMetadata->id;
								$childObjectVersion->description=$_POST['description'];
								$childObjectVersion->title=$_POST['title'];
								
								// Indiquer le parent de cette nouvelle version enfant
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
							
								/*
								 * NOUVEAUX LIENS
								 * Lier la nouvelle version de l'enfant à la nouvelle version du parent
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
							else // Sinon lier ces enfants à la nouvelle version
							{
								/*
								 * NOUVEAUX LIENS
								 * Lier la version de l'enfant à la nouvelle version du parent
								 */
								$newLink = new objectversionlink($database);
								$newLink->parent_id=$rowObjectVersion->id;
								$newLink->child_id=$childLastObjectVersion->id;
								
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
		else // Mettre à jour la version
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
		
		// Au cas où on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyObjectVersion' :
				// Reprendre en édition l'objet
				TOOLBAR_objectversion::_EDIT();
				ADMIN_objectversion::editObjectVersion($rowObjectVersion->id,$option);
				break;

			case 'saveObjectVersion' :
			default :
				break;
		}
	}
	
	function deleteContext($cid, $option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage("Sï¿½lectionnez un enregistrement ï¿½ supprimer","error");
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
				$mainframe->enqueueMessage("CATALOG_OBJECTVERSION_DELETE_STATE_MSG","error");
				//$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$object_id );
				exit;
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
	
	/**
	* Cancels an edit operation
	*/
	function cancelContext($option)
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