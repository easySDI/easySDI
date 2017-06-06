<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/version.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables/metadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/diffusion.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_map/tables/layer.php';

/**
 * Easysdi_map model.
 */
class Easysdi_mapModelPreview extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $db = JFactory::getDbo();

        // Load state from the passed variable 
        if (JFactory::getApplication()->input->get('versionid')) {
            $versionid = JFactory::getApplication()->input->get('versionid');
            $query = $db->getQuery(true)
                            ->select('p.id')
                            ->from('#__sdi_visualization p')
                            ->innerJoin('#__sdi_version v ON p.version_id = v.id')
                            ->where('v.id IN ( ' . (int) $versionid) . ')';
            $db->setQuery($query);
            $id = implode($db->loadColumn(), ',');
        } else if (JFactory::getApplication()->input->get('diffusionid')) {
            $diffusionid = JFactory::getApplication()->input->get('diffusionid');
            $query = $db->getQuery(true)
                            ->select('p.id')
                            ->from('#__sdi_visualization p')
                            ->innerJoin('#__sdi_version v ON p.version_id = v.id')
                            ->innerJoin('#__sdi_diffusion d ON d.version_id = v.id')
                            ->where('d.id IN ( ' . (int) $diffusionid) . ')';
            $db->setQuery($query);
            $id = implode($db->loadColumn(), ',');
        } else if (JFactory::getApplication()->input->get('metadataid')) {
            $metadataid = JFactory::getApplication()->input->get('metadataid');
            $query = $db->getQuery(true)
                            ->select('p.id')
                            ->from('#__sdi_visualization p')
                            ->innerJoin('#__sdi_version v ON p.version_id = v.id')
                            ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                            ->where('m.id IN (' . (int) $metadataid) . ')';
            $db->setQuery($query);
            $id = implode($db->loadColumn(), ',');
        } else {
            $id = JFactory::getApplication()->input->get('id', null, 'safehtml');
        }
        $this->setState('preview.id', $id, ',');

        // Load the parameters.
        $app = JFactory::getApplication('com_easysdi_map');
        $params = $app->getParams();
        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('preview.id');
            }

            // Attempt to load the row.
            $ids = array();
            if (strpos($id, ',') !== false) {
                $ids = explode(',', $id);
            } else {
                $ids[] = $id;
            }
            $this->_item = array();
            foreach ($ids as $id):
                // Get a level row instance.
                $table = $this->getTable();
                if ($table->load($id)) {

                    if ($table->state != 1)
                        continue;

                    // Convert the JTable to a clean JObject.
                    $properties = $table->getProperties(1);
                    $item = JArrayHelper::toObject($properties, 'JObject');

                    if (!empty($item->maplayer_id)):
                        //Load layer object
                        $maplayer = JTable::getInstance('layer', 'Easysdi_mapTable');
                        $maplayer->load($item->maplayer_id);
                        $maplayerproperties = $maplayer->getProperties(1);
                        $maplayer = JArrayHelper::toObject($maplayerproperties, 'JObject');                        
                        $item->maplayer = $maplayer;
                        
                        //Load Layer source
                        //TODO : Do we have to check the service access scope?
                        if ($maplayer->servicetype == 'physical') {
                            $service = JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
                        } else {
                            $service = JTable::getInstance('virtualservice', 'Easysdi_serviceTable');
                        }
                        $service->load($maplayer->service_id);
                        $serviceproperties = $service->getProperties(1);
                        $service = JArrayHelper::toObject($serviceproperties, 'JObject');
                        $item->service = $service;
                        
                        //Load server type : used for indoor navigation
                        if ($maplayer->servicetype == 'physical') {
                             $item->service->server = $service->server_id;                            
                        }else{                            
                            $db = JFactory::getDbo();
                            $query = $db->getQuery(true);
                            $query->select('p.server_id');
                            $query->from('#__sdi_virtualservice AS v');
                            $query->join('LEFT', '#__sdi_virtual_physical AS vp ON vp.virtualservice_id=v.id');
                            $query->join('LEFT', '#__sdi_physicalservice AS p ON vp.physicalservice_id=p.id');
                            $query->group('p.server_id');
                            $query->where('v.id = ' .  (int) $maplayer->service_id);
                            $db->setQuery($query);
                            $services = $db->loadColumn();
                            if (count($services) > 1) {
                                //virtual service aggregates more than one kind of physical services
                                 $item->service->server = 3;
                            } else {
                                 $item->service->server = $services[0];
                            }
                        }
                    endif;

                    //Load related version 
                    $version = JTable::getInstance('version', 'Easysdi_coreTable');
                    $version->load($item->version_id);
                    $versionproperties = $version->getProperties(1);
                    $item->version = JArrayHelper::toObject($versionproperties, 'JObject');

                    //Load related metadata 
                    $metadata = JTable::getInstance('metadata', 'Easysdi_catalogTable');
                    $keys = array('version_id' => $item->version_id);
                    $metadata->load($keys);
                    $metadataproperties = $metadata->getProperties(1);
                    $item->metadata = JArrayHelper::toObject($metadataproperties, 'JObject');

                    //Load related diffusion 
                    $diffusion = JTable::getInstance('diffusion', 'Easysdi_shopTable');
                    $keys = array('version_id' => $item->version_id);
                    $diffusion->load($keys);
                    $diffusionproperties = $diffusion->getProperties(1);
                    $item->diffusion = JArrayHelper::toObject($diffusionproperties, 'JObject');

                    $this->_item[] = $item;
                } elseif ($error = $table->getError()) {
                    $this->setError($error);
                }
            endforeach;
        }

        return $this->_item;
    }

    /**
     * 
     * @param type $type
     * @param type $prefix
     * @param type $config
     * @return type
     */
    public function getTable($type = 'Visualization', $prefix = 'Easysdi_mapTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_easysdi_map.map', 'map', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

}