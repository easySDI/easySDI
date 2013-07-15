<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * metadata Table class
 */
class Easysdi_catalogTablemetadata extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_metadata', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param	array		Named array
     * @return	null|string	null is operation was satisfactory, otherwise returns an error
     * @see		JTable:bind
     * @since	1.5
     */
    public function bind($array, $ignore = '') {


        $task = JRequest::getVar('task');
        if ($task == 'apply' || $task == 'save') {
            $array['modified'] = date("Y-m-d H:i:s");
        }


        if (!JFactory::getUser()->authorise('core.admin', 'com_easysdi_catalog.metadata.' . $array['id'])) {
            $actions = JFactory::getACL()->getActions('com_easysdi_catalog', 'metadata');
            $default_actions = JFactory::getACL()->getAssetRules('com_easysdi_catalog.metadata.' . $array['id'])->getData();
            $array_jaccess = array();
            foreach ($actions as $action) {
                $array_jaccess[$action->name] = $default_actions[$action->name];
            }
            $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
        }
        //Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $this->setRules($array['rules']);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Define a namespaced asset name for inclusion in the #__assets table
     * @return string The asset name 
     *
     * @see JTable::_getAssetName 
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_easysdi_catalog.metadata.' . (int) $this->$k;
    }

    /**
     * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
     *
     * @see JTable::_getAssetParentId 
     */
    protected function _getAssetParentId($table = null, $id = null) {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_easysdi_catalog');
        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }
        return $assetParentId;
    }

    /**
     * Overriden JTable::store to set modified data and user id.
     *
     * @param	boolean	True to update fields even if they are null.
     * @return	boolean	True on success.
     * @since	1.6
     */
    public function store($updateNulls = true) {
        (empty($this->id) ) ? $new = true : $new = false;

        if (parent::store($updateNulls)) {
            if ($new) {
                //Insert new Metadata into CSW catalog
                try {
                    //Get from the metadata structure, the attribute to store the metadata ID
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query = "SELECT a.name as name, ns.prefix as ns, 
                        CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, 
                        CONCAT(atns.prefix, ':', at.isocode) as type_isocode 
                        FROM #__sdi_profile p
                            INNER JOIN #__sdi_resourcetype rt on p.id=rt.profile_id 
                            INNER JOIN #__sdi_attribute a on a.id=p.metadataidentifier 
                            INNER JOIN #__sdi_relation rel on rel.attributechild_id=a.id
                            INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.stereotype_id 
                            LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id 
                            LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id 
                        WHERE rt.id=" . $table->resourcetype_id;
                    $db->setQuery($query);
                    $attributeIdentifier = $db->loadObject();

                    //Get from the metadata structure the root classe
                    $query = $db->getQuery(true);
                    $query = "SELECT CONCAT(ns.prefix,':',c.isocode) as isocode 
                                FROM #__sdi_profile p 
                                INNER JOIN #__sdi_resourcetype rt ON p.id=rt.profile_id
                                INNER JOIN #__sdi_class c ON c.id=p.class_id  
                                LEFT OUTER JOIN #__sdi_namespace as ns ON c.namespace_id=ns.id 
                                WHERE rt.id=" . $table->resourcetype_id;
                    $db->setQuery($query);
                    $rootclass = $db->loadObject();
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }

                /*
                 * <sdi:platform guid ="" harvested="false">
                  <rsdi:esource guid="" alias="" name="" type="" organism="" scope="">
                  <sdi:organisms>
                  <sdi:organism guid=""/>
                  </sdi:organisms>
                  <sdi:users>
                  <sdi:user guid=""/>
                  </sdi:users>
                  <sdi:metadata lastVersion="true" guid="" created="" published="" state="">
                  <sdi:diffusion isFree="false" osDownloadable="false" isOrderable="true"/>
                  </sdi:metadata>
                  </sdi:resource>
                  </sdi:platform>
                 */
                //Create metadata XML
                $xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<csw:Transaction service=\"CSW\"
			version=\"2.0.2\"
			xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" >
				<csw:Insert>
					<" . $rootclass->isocode . "
						xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" 
						xmlns:gco=\"http://www.isotc211.org/2005/gco\" 
						xmlns:xlink=\"http://www.w3.org/1999/xlink\" 
						xmlns:gml=\"http://www.opengis.net/gml\" 
						xmlns:gts=\"http://www.isotc211.org/2005/gts\" 
						xmlns:srv=\"http://www.isotc211.org/2005/srv\"
						xmlns:ext=\"http://www.depth.ch/2008/ext\">
						
						<" . $attributeIdentifier->attribute_isocode . ">
							<" . $attributeIdentifier->type_isocode . ">" . $metadata->guid . "</" . $attributeIdentifier->type_isocode . ">
						</" . $attributeIdentifier->attribute_isocode . ">
					</" . $rootclass->isocode . ">
				</csw:Insert>
			</csw:Transaction>";


                require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_easysdi_core' . DS . 'common' . DS . 'easysdi.config.php');
                $catalogUrlBase = config_easysdi::getValue("catalog_url");

                $result = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase, $xmlstr);

                $insertResults = DOMDocument::loadXML($result);

                $xpathInsert = new DOMXPath($insertResults);
                $xpathInsert->registerNamespace('csw', 'http://www.opengis.net/cat/csw/2.0.2');
                $inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;

                if ($inserted <> 1) {
                    //$mainframe->enqueueMessage(htmlspecialchars($xmlstr),"INFO");
                    //$mainframe->enqueueMessage($catalogUrlBase,"INFO");
                    //$mainframe->enqueueMessage(uniqid(),"INFO");
                    //$mainframe->enqueueMessage(htmlspecialchars($result),"INFO");
                    //$mainframe->enqueueMessage('Error on metadata insert',"ERROR");
                    $mainframe->redirect("index.php?option=$option&task=listObject");
                    exit();
                }
            }else{
                $this->update();
            }
            return true;
        }
        return false;
    }

    public function update(){
        
    }
}
