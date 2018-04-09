<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_service model.
 */
class Easysdi_serviceModelpolicy extends JModelAdmin {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_EASYSDI_SERVICE';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Policy', $prefix = 'Easysdi_serviceTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Initialise variables.
        $app = JFactory::getApplication();

        // Get the form.
        $form = $this->loadForm('com_easysdi_service.policy', 'policy', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_easysdi_service.edit.policy.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param	object	A record object.
     *
     * @return	array	An array of conditions to add to add to ordering queries.
     * @since	1.6
     */
    protected function getReorderConditions($table) {
        $condition = array();
        $condition[] = 'virtualservice_id = ' . (int) $table->virtualservice_id;
        return $condition;
    }

    /**
     * Method to get a single record.
     *
     * @param	integer	The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     * @since	1.6
     */
    public function getItem($pk = null) {
        if ($item = parent::getItem($pk)) {
            //Do any procesing on fields here if needed
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables');

            $pk = $item->id;

            if (!isset($item->virtualservice_id)) {
                $item->virtualservice_id = (int) JRequest::getVar('virtualservice_id', null);
            }

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                    ->select('sc.value, vs.name')
                    ->from(' #__sdi_virtualservice AS vs ')
                    ->join('LEFT', '#__sdi_sys_serviceconnector AS sc ON sc.id = vs.serviceconnector_id')
                    ->where('vs.id = ' . (int) $item->virtualservice_id);
            $db->setQuery($query);
            $result = $db->loadObject();

            $item->layout = ($result->value == "WMSC") ? "WMS" : $result->value;
            $item->virtualservice = $result->name;

            if (method_exists('Easysdi_serviceModelpolicy', '_getItem' . $item->layout)) {
                $item->physicalService = $this->{'_getItem' . $item->layout}($pk, $item->virtualservice_id);
            }
            $item->{'allowedoperation_' . strtolower($item->layout)} = $this->loadAllowedOperation($pk);

            if (strtolower($item->layout) == 'csw') {
                $item->csw_state = $this->loadAllowedMetadatastate($pk);
                $item->csw_organisms = $this->loadAllowedVisibility($pk, 'organism');
                $item->csw_users = $this->loadAllowedVisibility($pk, 'user');
                $item->csw_resourcetype = $this->loadAllowedResourcetype($pk);
                if ($item->csw_spatialpolicy_id) {
                    $spatialpolicy = $this->loadCSWSpatialPolicy($item->csw_spatialpolicy_id);
                    $item->srssource = $spatialpolicy->srssource;
                    $item->maxx = (float) $spatialpolicy->maxx;
                    $item->maxy = (float) $spatialpolicy->maxy;
                    $item->minx = (float) $spatialpolicy->minx;
                    $item->miny = (float) $spatialpolicy->miny;
                }
            }
        }

        // Get the access scope
        $item->organisms = $this->getAccessScopeOrganism($item->id);
        $item->users = $this->getAccessScopeUser($item->id);
        $item->categories = $this->getAccessScopeCategory($item->id);

        return $item;
    }

    /**
     *
     * Get item with WMS service connector
     *
     */
    private function _getItemWMS($pk, $virtualservice_id) {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/WmsPhysicalService.php');
        $tab_physicalService = JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
        $db = JFactory::getDbo();
        $ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
        $wmsObjList = Array();
        foreach ($ps_list as $ps) {
            $layerList = Array();
            $data = Array();
            //check layers that have settings
            if (!empty($pk)) {
                $query = $db->getQuery(true);
                $query->select('wlp.name, wlp.enabled, wlp.spatialpolicy_id');
                $query->from('#__sdi_policy p');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON p.id = psp.policy_id');
                $query->innerJoin('#__sdi_wmslayer_policy wlp ON psp.id = wlp.physicalservicepolicy_id');
                $query->where('p.id = ' . $pk);
                $query->where('psp.physicalservice_id = ' . $ps->id);
                $query->where('(wlp.spatialpolicy_id IS NOT NULL OR wlp.enabled = 1)');


                $db->setQuery($query);

                try {
                    $db->execute();
                    $resultset = $db->loadObjectList();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                foreach ($resultset as $row) {
                    if (!is_null($row->spatialpolicy_id)) {
                        $layerList[] = $row->name;
                    }
                    $data[$row->name] = Array(
                        'enabled' => $row->enabled,
                    );
                }
            }

            $wmsObj = new WmsPhysicalService($ps->id, $ps->resourceurl, $ps->resourceusername, $ps->resourcepassword);
            $wmsObj->getCapabilities();
            $wmsObj->populate();
            $wmsObj->loadData($data);
            $wmsObj->sortLists();
            $wmsObj->setLayerAsConfigured($layerList);
            $wmsObjList[] = $wmsObj;

            $this->cacheXMLCapabilities($wmsObj->getRawXml(), $ps->id, $virtualservice_id);
        }

        return $wmsObjList;
    }

    /**
     *
     * Get item with WFS service connector
     *
     */
    private function _getItemWFS($pk, $virtualservice_id) {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/WfsPhysicalService.php');
        $tab_physicalService = JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
        $db = JFactory::getDbo();

        $ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
        $wfsObjList = Array();
        foreach ($ps_list as $ps) {
            $layerList = Array();
            $data = Array();
            //check layers that have settings
            if (!empty($pk)) {
                $query = $db->getQuery(true);
                $query->select('wlp.name, wlp.enabled, wlp.spatialpolicy_id');
                $query->from('#__sdi_policy p');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON p.id = psp.policy_id');
                $query->innerJoin('#__sdi_featuretype_policy wlp ON psp.id = wlp.physicalservicepolicy_id');
                $query->where('p.id = ' . (int) $pk);
                $query->where('psp.physicalservice_id = ' . (int) $ps->id);

                $db->setQuery($query);

                try {
                    $db->execute();
                    $resultset = $db->loadObjectList();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                foreach ($resultset as $row) {
                    if (!is_null($row->spatialpolicy_id)) {
                        $layerList[] = $row->name;
                    }
                    $data[$row->name] = Array(
                        'enabled' => $row->enabled,
                    );
                }
            }

            $wfsObj = new WfsPhysicalService($ps->id, $ps->resourceurl, $ps->resourceusername, $ps->resourcepassword);
            $wfsObj->getCapabilities();
            $wfsObj->populate();
            $wfsObj->loadData($data);
            $wfsObj->sortLists();
            $wfsObj->setLayerAsConfigured($layerList);
            $wfsObjList[] = $wfsObj;

            $this->cacheXMLCapabilities($wfsObj->getRawXml(), $ps->id, $virtualservice_id);
        }

        return $wfsObjList;
    }

    /**
     *
     * Get item with WMTS service connector
     *
     */
    private function _getItemWMTS($pk, $virtualservice_id) {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/WmtsPhysicalService.php');
        set_time_limit(60);
        $tab_physicalService = JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
        $db = JFactory::getDbo();

        $ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
        $wmtsObjList = Array();
        foreach ($ps_list as $ps) {
            $layerList = Array();
            $data = Array();
            //check layers that have settings and that are enabled
            if (!empty($pk)) {
                $query = $db->getQuery(true);
                $query->select('wlp.identifier, wlp.enabled, wlp.spatialpolicy_id, tmsp.identifier AS tmsp_id, tmp.identifier AS tmp_id');
                $query->from('#__sdi_policy p');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON p.id = psp.policy_id');
                $query->innerJoin('#__sdi_wmtslayer_policy wlp ON psp.id = wlp.physicalservicepolicy_id');
                $query->leftJoin('#__sdi_tilematrixset_policy tmsp ON tmsp.wmtslayerpolicy_id = wlp.id');
                $query->leftJoin('#__sdi_tilematrix_policy tmp ON tmp.tilematrixsetpolicy_id = tmsp.id');
                $query->where('p.id = ' . $pk);
                $query->where('psp.physicalservice_id = ' . $ps->id);
              //  $query->group('wlp.identifier');

                $db->setQuery($query);

                try {
                    $db->execute();
                    $resultset = $db->loadObjectList();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                foreach ($resultset as $row) {
                    //if ((!is_null($row->spatialpolicy_id)) || (!is_null($row->tmsp_id)) || (!is_null($row->tmp_id))) {
                    if ((!is_null($row->spatialpolicy_id))) {
                        $layerList[] = $row->identifier;
                    }
                    $data[$row->identifier] = Array(
                        'enabled' => $row->enabled,
                    );
                }
            }

            $wmtsObj = new WmtsPhysicalService($ps->id, $ps->resourceurl, $ps->resourceusername, $ps->resourcepassword);
            $wmtsObj->getCapabilities();
            $wmtsObj->populate();
            $wmtsObj->loadData($data);
            $wmtsObj->sortLists();
            $wmtsObj->setLayerAsConfigured($layerList);
            $wmtsObj->compileAllSRS();
            $wmtsObjList[] = $wmtsObj;

            $this->cacheXMLCapabilities($wmtsObj->getRawXml(), $ps->id, $virtualservice_id);
        }

        return $wmtsObjList;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @since	1.6
     */
    protected function prepareTable($table) {
        jimport('joomla.filter.output');
        if (empty($table->id)) {
            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('MAX(ordering)');
                $query->from('#__sdi_policy');

                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
        //Fill alias with name as default value
        if (empty($table->alias)) {
            $table->alias = $table->name;
        }
    }

    public function save($data) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('sc.value');
        $query->from('#__sdi_virtualservice vs');
        $query->innerJoin('#__sdi_sys_serviceconnector sc ON sc.id = vs.serviceconnector_id');
        $query->where('vs.id = ' . (int) $data['virtualservice_id']);

        $db->setQuery($query);

        try {
            $db->execute();
            $serviceconnector_name = $db->loadResult();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        $isNew = (0 == $data['id']) ? true : false;

        if (parent::save($data)) {

            $data['id'] = $this->getItem()->get('id');
            if ('WMS' == $serviceconnector_name || 'WFS' == $serviceconnector_name || 'WMTS' == $serviceconnector_name) {
                $physicalservicepolicy = JTable::getInstance('physicalservice_policy', 'Easysdi_serviceTable');
                if (!$physicalservicepolicy->saveAll($data['virtualservice_id'], $data['id'])) {
                    $this->setError('Failed to save physicalservice_policy.');
                    return false;
                }
            }

            if ('WMS' == $serviceconnector_name) {
                require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/WmsWebservice.php');
                if (!WmsWebservice::saveAllLayers($data['virtualservice_id'], $data['id'])) {
                    $this->setError('Failed to save all WMS layers.');
                    return false;
                }
            }
            
            if ('WFS' == $serviceconnector_name) {
                require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/WfsWebservice.php');
                if (!WfsWebservice::saveAllFeatureTypes($data['virtualservice_id'], $data['id'])) {
                    $this->setError('Failed to save all WFS layers.');
                    return false;
                }
            }
            
            

            if ('CSW' == $serviceconnector_name) {
                //Save specific restrictions

                if (!$this->saveCSWState($data)) {
                    $this->setError('Failed to save state.');
                    return false;
                }

                if (!$this->saveCSWResourcetype($data)) {
                    $this->setError('Failed to save resource type.');
                    return false;
                }

                if (!$this->saveCSWVisibility($data)) {
                    $this->setError('Failed to save csw visibility restrictions.');
                    return false;
                }

                if (!$this->saveCSWResourcetype($data)) {
                    $this->setError('Failed to save csw resource type restrictions.');
                    return false;
                }

                if (!$this->saveExcludedAttributes($data)) {
                    $this->setError('Failed to save excluded attributes.');
                    return false;
                }

                if (!$this->saveCSWSpatialPolicy($data)) {
                    $this->setError('Failed to save CSW spatial policy.');
                    return false;
                }
            }

            if (!$isNew) {
                switch ($serviceconnector_name) {
                    case 'WMTS':
                        if (!$this->saveWMTSInheritance($data)) {
                            $this->setError('Failed to save inheritance.');
                            return false;
                        }

                        if (!$this->saveWMTSEnabledLayers($data)) {
                            $this->setError('Failed to save enabled layers.');
                            return false;
                        }

                        if (!$this->calculateAllTiles($data)) {
                            $this->setError('Failed to calculate authorized tiles.');
                            return false;
                        }
                        break;
                    case 'WMS':
                        if (!$this->saveWMSInheritance($data)) {
                            $this->setError('Failed to save inheritance.');
                            return false;
                        }

                        if (!$this->saveWMSEnabledLayers($data)) {
                            $this->setError('Failed to save enabled layers.');
                            return false;
                        }
                        break;
                    case 'WFS':
                        if (!$this->saveWFSInheritance($data)) {
                            $this->setError('Failed to save inheritance.');
                            return false;
                        }

                        if (!$this->saveWFSEnabledFeatureType($data)) {
                            $this->setError('Failed to save enabled layers.');
                            return false;
                        }
                        break;
                    case 'CSW':
//						if (!$this->saveCSWState($data)) {
//							$this->setError('Failed to save state.');
//							return false;
//						}
//						if (!$this->saveExcludedAttributes($data)) {
//							$this->setError('Failed to save excluded attributes.');
//							return false;
//						}
//						
//						if(!$this->saveCSWSpatialPolicy($data)){
//							$this->setError('Failed to save CSW spatial policy.');
//							return false;
//						}
                        break;
                }
            }

            //Allowed operations
            if (!$this->saveAllowedOperation($data)) {
                $this->setError('Failed to save allowed operations.');
                return false;
            }

            //Access Scope
            if (!$this->saveAccessScope($data)) {
                $this->setError('Failed to save access scope.');
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Method to save the inherited settings on layers
     *
     * @param array 	$data	data posted from the form
     * 
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.3.0
     */
    private function saveWMTSInheritance($data) {
        $db = JFactory::getDbo();

        //Save the policy-wide inheritance
        $spatialPolicy = current($_POST['inherit_policy']);
        $spatialPolicyID = key($_POST['inherit_policy']);

        $spatialPolicy['spatialoperatorid'] = ('' != $spatialPolicy['spatialoperatorid']) ? $spatialPolicy['spatialoperatorid'] : 1;
        $spatialPolicy['eastBoundLongitude'] = ('' != $spatialPolicy['eastBoundLongitude']) ? $spatialPolicy['eastBoundLongitude'] : 'null';
        $spatialPolicy['westBoundLongitude'] = ('' != $spatialPolicy['westBoundLongitude']) ? $spatialPolicy['westBoundLongitude'] : 'null';
        $spatialPolicy['northBoundLatitude'] = ('' != $spatialPolicy['northBoundLatitude']) ? $spatialPolicy['northBoundLatitude'] : 'null';
        $spatialPolicy['southBoundLatitude'] = ('' != $spatialPolicy['southBoundLatitude']) ? $spatialPolicy['southBoundLatitude'] : 'null';

        $policyUpdates = Array(
            'anyservice = ' . ((isset($spatialPolicy['anyservice'])) ? 1 : 0),
        );

        //test whether that policy already have a spatialPolicy or not
        if ('null' != $spatialPolicy['northBoundLatitude']) {
            if (-1 == $spatialPolicyID) {
                //we create the spatial policy
                $query = $db->getQuery(true);
                $columns = array('northboundlatitude', 'westboundlongitude', 'eastboundlongitude', 'southboundlatitude', 'spatialoperator_id');
                $values = array($spatialPolicy['northBoundLatitude'], $spatialPolicy['westBoundLongitude'], $spatialPolicy['eastBoundLongitude'], $spatialPolicy['southBoundLatitude'], $spatialPolicy['spatialoperatorid']);
                $query->insert('#__sdi_wmts_spatialpolicy')
                        ->columns($query->quoteName($columns))
                        ->values(implode(',', $values));

                try {
                    $db->setQuery($query);
                    $db->execute();
                    $spatialPolicyID = $db->insertid();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                //we update the spatial policy foreign key in policy
                $policyUpdates[] = 'wmts_spatialpolicy_id = ' . $spatialPolicyID;
            } else {
                //we update the spatial policy
                $query = $db->getQuery(true);
                $query->update('#__sdi_wmts_spatialpolicy')->set(Array(
                    'northboundlatitude = ' . $spatialPolicy['northBoundLatitude'],
                    'westboundlongitude = ' . $spatialPolicy['westBoundLongitude'],
                    'eastboundlongitude = ' . $spatialPolicy['eastBoundLongitude'],
                    'southboundlatitude = ' . $spatialPolicy['southBoundLatitude'],
                    'spatialoperator_id = ' . $spatialPolicy['spatialoperatorid'],
                ))->where('id = ' . (int) $spatialPolicyID);

                try {
                    $db->setQuery($query);
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }

        //If spatial policy was cleared
        if ($spatialPolicyID != -1 && 'null' == $spatialPolicy['northBoundLatitude']) {
            //we update the spatial policy foreign key in policy
            $policyUpdates[] = 'wmts_spatialpolicy_id = NULL';
        }

        //we update the anyservice switch
        $query = $db->getQuery(true);
        $query->update('#__sdi_policy')->set($policyUpdates)->where('id = ' . (int) $data['id']);

        try {
            $db->setQuery($query);
            $db->execute();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        //If spatial policy was cleared
        if ($spatialPolicyID != NULL && 'null' == $spatialPolicy['northBoundLatitude']) {
            //delete the no more used spatial policy
            $query = $db->getQuery(true);
            $query->delete('#__sdi_wmts_spatialpolicy')->where('id = ' . (int) $spatialPolicyID);
            try {
                $db->setQuery($query);
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }

        //Save the server-wide inheritance
        $spatialPolicy = null;
        foreach ($_POST['inherit_server'] as $physicalServiceID => $spatialPolicy) {
            $physicalServicePolicyUpdates = Array(
                'anyitem = ' . ((isset($spatialPolicy['anyitem'])) ? 1 : 0),
            );
            //check if a spatial policy exists for that physicalservice_policy
            $query = $db->getQuery(true);
            $query->select('wmts_spatialpolicy_id, id');
            $query->from('#__sdi_physicalservice_policy');
            $query->where('physicalservice_id = ' . (int) $physicalServiceID);
            $query->where('policy_id = ' . (int) $data['id']);

            try {
                $db->setQuery($query);
                $db->execute();
                $resultSet = $db->loadObject();
                $spatialPolicyID = null;
                if (!empty($resultSet)) {
                    $spatialPolicyID = $resultSet->wmts_spatialpolicy_id;
                    $physicalServicePolicyID = $resultSet->id;
                }
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            $spatialPolicy['spatialoperatorid'] = ('' != $spatialPolicy['spatialoperatorid']) ? $spatialPolicy['spatialoperatorid'] : 1;
            $spatialPolicy['eastBoundLongitude'] = ('' != $spatialPolicy['eastBoundLongitude']) ? $spatialPolicy['eastBoundLongitude'] : 'null';
            $spatialPolicy['westBoundLongitude'] = ('' != $spatialPolicy['westBoundLongitude']) ? $spatialPolicy['westBoundLongitude'] : 'null';
            $spatialPolicy['northBoundLatitude'] = ('' != $spatialPolicy['northBoundLatitude']) ? $spatialPolicy['northBoundLatitude'] : 'null';
            $spatialPolicy['southBoundLatitude'] = ('' != $spatialPolicy['southBoundLatitude']) ? $spatialPolicy['southBoundLatitude'] : 'null';

            //test whether that physicalservice_policy already have a spatialPolicy or not
            if ('null' != $spatialPolicy['northBoundLatitude']) {
                if (empty($spatialPolicyID)) {
                    //create a spatial policy
                    $query = $db->getQuery(true);
                    $columns = array('northboundlatitude', 'westboundlongitude', 'eastboundlongitude', 'southboundlatitude', 'spatialoperator_id');
                    $values = array($spatialPolicy['northBoundLatitude'], $spatialPolicy['westBoundLongitude'], $spatialPolicy['eastBoundLongitude'], $spatialPolicy['southBoundLatitude'], $spatialPolicy['spatialoperatorid']);
                    $query->insert('#__sdi_wmts_spatialpolicy')
                            ->columns($query->quoteName($columns))
                            ->values(implode(',', $values));

                    try {
                        $db->setQuery($query);
                        $db->execute();
                        $spatialPolicyID = $db->insertid();
                    } catch (JDatabaseException $e) {
                        $je = new JException($e->getMessage());
                        $this->setError($je);
                        return false;
                    }

                    //update the spatial foreign key in physicalservice_policy
                    $physicalServicePolicyUpdates[] = 'wmts_spatialpolicy_id = ' . $spatialPolicyID;
                    //And set the inheritedspatialpolicy boolean value accordingly
                    $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 0';
                } else {
                    //update the spatial policy
                    $query = $db->getQuery(true);
                    $query->update('#__sdi_wmts_spatialpolicy')->set(Array(
                        'northboundlatitude = ' . $spatialPolicy['northBoundLatitude'],
                        'westboundlongitude = ' . $spatialPolicy['westBoundLongitude'],
                        'eastboundlongitude = ' . $spatialPolicy['eastBoundLongitude'],
                        'southboundlatitude = ' . $spatialPolicy['southBoundLatitude'],
                        'spatialoperator_id = ' . $spatialPolicy['spatialoperatorid'],
                    ))->where('id = ' . (int) $spatialPolicyID);

                    try {
                        $db->setQuery($query);
                        $db->execute();
                    } catch (JDatabaseException $e) {
                        $je = new JException($e->getMessage());
                        $this->setError($je);
                        return false;
                    }
                    //Set the inheritedspatialpolicy boolean value accordingly
                    $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 0';
                }
            }

            //update the anyitem switch
            $query = $db->getQuery(true);
            $query->update('#__sdi_physicalservice_policy')->set($physicalServicePolicyUpdates)->where('id = ' . (int) $physicalServicePolicyID);

            try {
                $db->setQuery($query);
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            //If spatial policy was cleared
            if ($spatialPolicyID != NULL && 'null' == $spatialPolicy['northBoundLatitude']) {
                //update the spatial foreign key in physicalservice_policy
                $physicalServicePolicyUpdates[] = 'wmts_spatialpolicy_id = NULL';
                //And set the inheritedspatialpolicy boolean value accordingly
                $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 1';

                //update the anyitem switch
                $query = $db->getQuery(true);
                $query->update('#__sdi_physicalservice_policy')->set($physicalServicePolicyUpdates)->where('id = ' . (int) $physicalServicePolicyID);

                try {
                    $db->setQuery($query);
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                //delete the no more used spatial policy
                $query = $db->getQuery(true);
                $query->delete('#__sdi_wmts_spatialpolicy')->where('id = ' . (int) $spatialPolicyID);
                try {
                    $db->setQuery($query);
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to save the inherited settings on layers
     *
     * @param array 	$data	data posted from the form
     * 
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.3.0
     */
    private function saveWMSInheritance($data) {
        $db = JFactory::getDbo();

        //Save the policy-wide inheritance
        $spatialPolicy = current($_POST['inherit_policy']);
        $spatialPolicyID = key($_POST['inherit_policy']);

        $spatialPolicy['maxx'] = ('' != $spatialPolicy['maxx']) ? $spatialPolicy['maxx'] : 'null';
        $spatialPolicy['maxy'] = ('' != $spatialPolicy['maxy']) ? $spatialPolicy['maxy'] : 'null';
        $spatialPolicy['minx'] = ('' != $spatialPolicy['minx']) ? $spatialPolicy['minx'] : 'null';
        $spatialPolicy['miny'] = ('' != $spatialPolicy['miny']) ? $spatialPolicy['miny'] : 'null';
        $spatialPolicy['minimumscale'] = ('' != $spatialPolicy['minimumscale']) ? $spatialPolicy['minimumscale'] : 'null';
        $spatialPolicy['maximumscale'] = ('' != $spatialPolicy['maximumscale']) ? $spatialPolicy['maximumscale'] : 'null';

        $policyUpdates = Array(
            'anyservice = ' . ((isset($spatialPolicy['anyservice'])) ? 1 : 0),
        );

        if ('null' != $spatialPolicy['minimumscale'] || 'null' != $spatialPolicy['maximumscale'] || !empty($spatialPolicy['geographicfilter'])) {
            //test whether that policy already have a spatialPolicy or not
            if (-1 == $spatialPolicyID) {
                //we create the spatial policy
                $query = $db->getQuery(true);
                $columns = array('maxx', 'maxy', 'minx', 'miny', 'geographicfilter', 'maximumscale', 'minimumscale', 'srssource');
                $values = array($spatialPolicy['maxx'], $spatialPolicy['maxy'], $spatialPolicy['minx'], $spatialPolicy['miny'], ((!empty($spatialPolicy['geographicfilter'])) ? $query->quote($spatialPolicy['geographicfilter']) : NULL), $spatialPolicy['maximumscale'], $spatialPolicy['minimumscale'], $query->quote($spatialPolicy['srssource']));
                $query->insert('#__sdi_wms_spatialpolicy')
                        ->columns($columns)
                        ->values(implode(',', $values));

                try {
                    $db->setQuery($query);
                    $db->execute();
                    $spatialPolicyID = $db->insertid();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                //we update the spatial policy foreign key in policy
                $policyUpdates[] = 'wms_spatialpolicy_id = ' . $spatialPolicyID;
            } else {
                //we update the spatial policy
                $query = $db->getQuery(true);
                $query->update('#__sdi_wms_spatialpolicy')->set(Array(
                    'maxx = ' . $spatialPolicy['maxx'],
                    'maxy = ' . $spatialPolicy['maxy'],
                    'minx = ' . $spatialPolicy['minx'],
                    'miny = ' . $spatialPolicy['miny'],
                    'geographicfilter = ' . ((!empty($spatialPolicy['geographicfilter'])) ? $query->quote($spatialPolicy['geographicfilter']) : 'null'),
                    'maximumscale = ' . $spatialPolicy['maximumscale'],
                    'minimumscale = ' . $spatialPolicy['minimumscale'],
                    'srssource = ' . $query->quote($spatialPolicy['srssource']),
                ))->where('id = ' . (int) $spatialPolicyID);

                try {
                    $db->setQuery($query);
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }

        //If spatial policy was cleared
        if ($spatialPolicyID != -1 && 'null' == $spatialPolicy['minimumscale'] && 'null' == $spatialPolicy['maximumscale'] && empty($spatialPolicy['geographicfilter'])) {
            //we update the spatial policy foreign key in policy
            $policyUpdates[] = 'wms_spatialpolicy_id = NULL';
        }

        //we update the anyservice switch
        $query = $db->getQuery(true);
        $query->update('#__sdi_policy')->set($policyUpdates)->where('id = ' . (int) $data['id']);

        try {
            $db->setQuery($query);
            $db->execute();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        //If spatial policy was cleared
        if ($spatialPolicyID != NULL && 'null' == $spatialPolicy['minimumscale'] && 'null' == $spatialPolicy['maximumscale'] && empty($spatialPolicy['geographicfilter'])) {
            //delete the no more used spatial policy
            $query = $db->getQuery(true);
            $query->delete('#__sdi_wms_spatialpolicy')->where('id = ' . (int) $spatialPolicyID);
            try {
                $db->setQuery($query);
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }

        //Save the server-wide inheritance
        $spatialPolicy = null;
        foreach ($_POST['inherit_server'] as $physicalServiceID => $spatialPolicy) {
            $physicalServicePolicyUpdates = Array(
                'anyitem = ' . ((isset($spatialPolicy['anyitem'])) ? 1 : 0),
            );

            $spatialPolicy['maxx'] = ('' != $spatialPolicy['maxx']) ? $spatialPolicy['maxx'] : 'null';
            $spatialPolicy['maxy'] = ('' != $spatialPolicy['maxy']) ? $spatialPolicy['maxy'] : 'null';
            $spatialPolicy['minx'] = ('' != $spatialPolicy['minx']) ? $spatialPolicy['minx'] : 'null';
            $spatialPolicy['miny'] = ('' != $spatialPolicy['miny']) ? $spatialPolicy['miny'] : 'null';
            $spatialPolicy['minimumscale'] = ('' != $spatialPolicy['minimumscale']) ? $spatialPolicy['minimumscale'] : 'null';
            $spatialPolicy['maximumscale'] = ('' != $spatialPolicy['maximumscale']) ? $spatialPolicy['maximumscale'] : 'null';

            //check if a spatial policy exists for that physicalservice_policy
            $query = $db->getQuery(true);
            $query->select('wms_spatialpolicy_id, id');
            $query->from('#__sdi_physicalservice_policy');
            $query->where('physicalservice_id = ' . (int) $physicalServiceID);
            $query->where('policy_id = ' . (int) $data['id']);

            try {
                $db->setQuery($query);
                $db->execute();
                $resultSet = $db->loadObject();
                $spatialPolicyID = null;
                if (!empty($resultSet)) {
                    $spatialPolicyID = $resultSet->wms_spatialpolicy_id;
                    $physicalServicePolicyID = $resultSet->id;
                }
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            if ('null' != $spatialPolicy['minimumscale'] || 'null' != $spatialPolicy['maximumscale'] || !empty($spatialPolicy['geographicfilter'])) {
                //test whether that physicalservice_policy already have a spatialPolicy or not
                if (empty($spatialPolicyID)) {
                    //create a spatial policy
                    $query = $db->getQuery(true);
                    $columns = array('maxx', 'maxy', 'minx', 'miny', 'geographicfilter', 'maximumscale', 'minimumscale', 'srssource');
                    $values = array($spatialPolicy['maxx'], $spatialPolicy['maxy'], $spatialPolicy['minx'], $spatialPolicy['miny'], ((!empty($spatialPolicy['geographicfilter'])) ? $query->quote($spatialPolicy['geographicfilter']) : NULL), $spatialPolicy['maximumscale'], $spatialPolicy['minimumscale'], $query->quote($spatialPolicy['srssource']));
                    $query->insert('#__sdi_wms_spatialpolicy')
                            ->columns($query->quoteName($columns))
                            ->values(implode(',', $values));

                    try {
                        $db->setQuery($query);
                        $db->execute();
                        $spatialPolicyID = $db->insertid();
                    } catch (JDatabaseException $e) {
                        $je = new JException($e->getMessage());
                        $this->setError($je);
                        return false;
                    }

                    //update the spatial foreign key in physicalservice_policy
                    $physicalServicePolicyUpdates[] = 'wms_spatialpolicy_id = ' . $spatialPolicyID;
                    //And set the inheritedspatialpolicy boolean value accordingly
                    $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 0';
                } else {
                    //update the spatial policy
                    $query = $db->getQuery(true);
                    $query->update('#__sdi_wms_spatialpolicy')->set(Array(
                        'maxx = ' . $spatialPolicy['maxx'],
                        'maxy = ' . $spatialPolicy['maxy'],
                        'minx = ' . $spatialPolicy['minx'],
                        'miny = ' . $spatialPolicy['miny'],
                        'geographicfilter = ' . ((!empty($spatialPolicy['geographicfilter'])) ? $query->quote($spatialPolicy['geographicfilter']) : 'null'),
                        'maximumscale = ' . $spatialPolicy['maximumscale'],
                        'minimumscale = ' . $spatialPolicy['minimumscale'],
                        'srssource = ' . $query->quote($spatialPolicy['srssource']),
                    ))->where('id = ' . (int) $spatialPolicyID);

                    try {
                        $db->setQuery($query);
                        $db->execute();
                    } catch (JDatabaseException $e) {
                        $je = new JException($e->getMessage());
                        $this->setError($je);
                        return false;
                    }
                    //Set the inheritedspatialpolicy boolean value accordingly
                    $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 0';
                }
            }

            //update the anyitem switch
            $query = $db->getQuery(true);
            $query->update('#__sdi_physicalservice_policy')->set($physicalServicePolicyUpdates)->where('id = ' . (int) $physicalServicePolicyID);

            try {
                $db->setQuery($query);
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            //If spatial policy was cleared
            if ($spatialPolicyID != NULL && 'null' == $spatialPolicy['minimumscale'] && 'null' == $spatialPolicy['maximumscale'] && empty($spatialPolicy['geographicfilter'])) {
                //update the spatial foreign key in physicalservice_policy
                $physicalServicePolicyUpdates[] = 'wms_spatialpolicy_id = NULL';
                //And set the inheritedspatialpolicy boolean value accordingly
                $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 1';

                //update the anyitem switch
                $query = $db->getQuery(true);
                $query->update('#__sdi_physicalservice_policy')->set($physicalServicePolicyUpdates)->where('id = ' . (int) $physicalServicePolicyID);

                try {
                    $db->setQuery($query);
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                //delete the no more used spatial policy
                $query = $db->getQuery(true);
                $query->delete('#__sdi_wms_spatialpolicy')->where('id = ' . (int) $spatialPolicyID);
                try {
                    $db->setQuery($query);
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to save the inherited settings on layers
     *
     * @param array 	$data	data posted from the form
     * 
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.3.0
     */
    private function saveWFSInheritance($data) {
        $db = JFactory::getDbo();

        //Save the policy-wide inheritance
        $spatialPolicy = current($_POST['inherit_policy']);
        $spatialPolicyID = key($_POST['inherit_policy']);

        $policyUpdates = Array(
            'anyservice = ' . ((isset($spatialPolicy['anyservice'])) ? 1 : 0),
        );

        if (!empty($spatialPolicy['localgeographicfilter']) || !empty($spatialPolicy['remotegeographicfilter'])) {
            //test whether that policy already have a spatialPolicy or not
            if (-1 == $spatialPolicyID) {
                //we create the spatial policy
                $query = $db->getQuery(true);
                $columns = array('localgeographicfilter', 'remotegeographicfilter');
                $values = array(((!empty($spatialPolicy['localgeographicfilter'])) ? $query->quote($spatialPolicy['localgeographicfilter']) : null), ((!empty($spatialPolicy['remotegeographicfilter'])) ? $query->quote($spatialPolicy['remotegeographicfilter']) : null));
                $query->insert('#__sdi_wfs_spatialpolicy')
                        ->columns($query->quoteName($columns))
                        ->values(implode(',', $values));

                try {
                    $db->setQuery($query);
                    $db->execute();
                    $spatialPolicyID = $db->insertid();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                //we update the spatial policy foreign key in policy
                $policyUpdates[] = 'wfs_spatialpolicy_id = ' . $spatialPolicyID;
            } else {
                //we update the spatial policy
                $query = $db->getQuery(true);
                $query->update('#__sdi_wfs_spatialpolicy')->set(Array(
                    'localgeographicfilter = ' . ((!empty($spatialPolicy['localgeographicfilter'])) ? $query->quote($spatialPolicy['localgeographicfilter']) : 'null'),
                    'remotegeographicfilter = ' . ((!empty($spatialPolicy['remotegeographicfilter'])) ? $query->quote($spatialPolicy['remotegeographicfilter']) : 'null'),
                ))->where('id = ' . (int) $spatialPolicyID);

                try {
                    $db->setQuery($query);
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }
        //If spatial policy was cleared
        if (empty($spatialPolicy['localgeographicfilter']) && empty($spatialPolicy['remotegeographicfilter'])) {
            //we update the spatial policy foreign key in policy
            $policyUpdates[] = 'wfs_spatialpolicy_id = NULL';
        }

        //we update the anyservice switch
        $query = $db->getQuery(true);
        $query->update('#__sdi_policy')->set($policyUpdates)->where('id = ' . (int) $data['id']);

        try {
            $db->setQuery($query);
            $db->execute();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        //If spatial policy was cleared
        if (empty($spatialPolicy['localgeographicfilter']) && empty($spatialPolicy['remotegeographicfilter'])) {
            //delete the no more used spatial policy
            $query = $db->getQuery(true);
            $query->delete('#__sdi_wfs_spatialpolicy')->where('id = ' . (int) $spatialPolicyID);
            try {
                $db->setQuery($query);
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }

        //Save the server-wide inheritance
        $spatialPolicy = null;
        foreach ($_POST['inherit_server'] as $physicalServiceID => $spatialPolicy) {
            $physicalServicePolicyUpdates = Array(
                'anyitem = ' . ((isset($spatialPolicy['anyitem'])) ? 1 : 0),
                'prefix = ' . ((isset($spatialPolicy['prefix'])) ? '\'' . $spatialPolicy['prefix'] . '\'' : NULL),
                'namespace = ' . ((isset($spatialPolicy['namespace'])) ? '\'' . $spatialPolicy['namespace'] . '\'' : NULL),
            );

            //check if a spatial policy exists for that physicalservice_policy
            $query = $db->getQuery(true);
            $query->select('wfs_spatialpolicy_id, id');
            $query->from('#__sdi_physicalservice_policy');
            $query->where('physicalservice_id = ' . (int) $physicalServiceID);
            $query->where('policy_id = ' . (int) $data['id']);

            try {
                $db->setQuery($query);
                $db->execute();
                $resultSet = $db->loadObject();
                $spatialPolicyID = null;
                if (!empty($resultSet)) {
                    $spatialPolicyID = $resultSet->wmts_spatialpolicy_id;
                    $physicalServicePolicyID = $resultSet->id;
                }
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            if ('' != $spatialPolicy['localgeographicfilter'] || '' != $spatialPolicy['remotegeographicfilter']) {

                //test whether that physicalservice_policy already have a spatialPolicy or not
                if (empty($spatialPolicyID)) {
                    //create a spatial policy
                    $query = $db->getQuery(true);
                    $columns = array('localgeographicfilter', 'remotegeographicfilter');
                    $values = array(((!empty($spatialPolicy['localgeographicfilter'])) ? $query->quote($spatialPolicy['localgeographicfilter']) : null), ((!empty($spatialPolicy['remotegeographicfilter'])) ? $query->quote($spatialPolicy['remotegeographicfilter']) : null));
                    $query->insert('#__sdi_wfs_spatialpolicy')
                            ->columns($query->quoteName($columns))
                            ->values(implode(',', $values));

                    try {
                        $db->setQuery($query);
                        $db->execute();
                        $spatialPolicyID = $db->insertid();
                    } catch (JDatabaseException $e) {
                        $je = new JException($e->getMessage());
                        $this->setError($je);
                        return false;
                    }

                    //update the spatial foreign key in physicalservice_policy
                    $physicalServicePolicyUpdates[] = 'wfs_spatialpolicy_id = ' . $spatialPolicyID;
                    //And set the inheritedspatialpolicy boolean value accordingly
                    $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 0';
                } else {
                    //update the spatial policy
                    $query = $db->getQuery(true);
                    $query->update('#__sdi_wms_spatialpolicy')->set(Array(
                        'localgeographicfilter = ' . ((!empty($spatialPolicy['localgeographicfilter'])) ? $query->quote($spatialPolicy['localgeographicfilter']) : 'null'),
                        'remotegeographicfilter = ' . ((!empty($spatialPolicy['remotegeographicfilter'])) ? $query->quote($spatialPolicy['remotegeographicfilter']) : 'null'),
                    ))->where('id = ' . (int) $spatialPolicyID);

                    try {
                        $db->setQuery($query);
                        $db->execute();
                    } catch (JDatabaseException $e) {
                        $je = new JException($e->getMessage());
                        $this->setError($je);
                        return false;
                    }
                    //Set the inheritedspatialpolicy boolean value accordingly
                    $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 0';
                }
            }

            //If spatial policy was cleared
            if (empty($spatialPolicy['localgeographicfilter']) && empty($spatialPolicy['remotegeographicfilter'])) {
                //we update the spatial policy foreign key in policy
                $physicalServicePolicyUpdates[] = 'wfs_spatialpolicy_id = NULL';
                //And set the inheritedspatialpolicy boolean value accordingly
                $physicalServicePolicyUpdates[] = 'inheritedspatialpolicy = 1';
            }

            //update the anyitem switch
            $query = $db->getQuery(true);
            $query->update('#__sdi_physicalservice_policy')->set($physicalServicePolicyUpdates)->where('id = ' . (int) $physicalServicePolicyID);

            try {
                $db->setQuery($query);
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            //If spatial policy was cleared
            if (empty($spatialPolicy['localgeographicfilter']) && empty($spatialPolicy['remotegeographicfilter']) && !empty($spatialPolicyID)) {
                //delete the no more used spatial policy
                $query = $db->getQuery(true);
                $query->delete('#__sdi_wfs_spatialpolicy')->where('id = ' . (int) $spatialPolicyID);
                try {
                    $db->setQuery($query);
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to save the organisms and users allowed by the access scope
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.3.0
     */
    public function saveAccessScope($data) {
        //Delete previously saved access
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_policy_organism');
        $query->where('policy_id = ' . (int) $data['id']);

        $db->setQuery($query);
        $db->query();

        $query = $db->getQuery(true);
        $query->delete('#__sdi_policy_user');
        $query->where('policy_id = ' . (int) $data['id']);

        $db->setQuery($query);
        $db->query();

        $query = $db->getQuery(true);
        $query->delete('#__sdi_policy_category');
        $query->where('policy_id = ' . (int) $data['id']);

        $db->setQuery($query);
        $db->query();

        $pks = $data['organisms'];
        if(is_array($pks)){
            foreach ($pks as $pk) {
                try {
                    $query = $db->getQuery(true);
                    $columns = array('policy_id', 'organism_id');
                    $values = array($data['id'], $pk);
                    $query->insert('#__sdi_policy_organism');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));

                    $db->setQuery($query);
                    if (!$db->query()) {
                        throw new Exception($db->getErrorMsg());
                    }
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }
            }
        }

        $pks = $data['users'];
        if(is_array($pks)){
            foreach ($pks as $pk) {
                try {
                    $query = $db->getQuery(true);
                    $columns = array('policy_id', 'user_id');
                    $values = array($data['id'], $pk);
                    $query->insert('#__sdi_policy_user');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));

                    $db->setQuery($query);
                    if (!$db->query()) {
                        throw new Exception($db->getErrorMsg());
                    }
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }
            }
        }

        $pks = $data['categories'];
        if(is_array($pks)){
            foreach ($pks as $pk) {
                try {
                    $query = $db->getQuery(true);
                    $columns = array('policy_id', 'category_id');
                    $values = array($data['id'], $pk);
                    $query->insert('#__sdi_policy_category');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));

                    $db->setQuery($query);
                    if (!$db->query()) {
                        throw new Exception($db->getErrorMsg());
                    }
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Method to get the organisms authorized to access this policy
     *
     * @param int		$id		primary key of the current policy to get.
     *
     * @return boolean 	Object list on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    public function getAccessScopeOrganism($id = null) {
        if (!isset($id))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.organism_id as id');
            $query->from('#__sdi_policy_organism p');
            $query->where('p.policy_id = ' . (int) $id);
            $db->setQuery($query);

            $scope = $db->loadColumn();
            return $scope;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Method to get the users authorized to access this policy
     *
     * @param int		$id		primary key of the current policy to get.
     *
     * @return boolean 	Object list on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    public function getAccessScopeUser($id = null) {
        if (!isset($id))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.user_id as id');
            $query->from('#__sdi_policy_user p');
            $query->where('p.policy_id = ' . (int) $id);
            $db->setQuery($query);

            $scope = $db->loadColumn();
            return $scope;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Method to get the categories authorized to access this policy
     *
     * @param int		$id		primary key of the current policy to get.
     *
     * @return boolean 	Object list on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    public function getAccessScopeCategory($id = null) {
        if (!isset($id))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.category_id as id');
            $query->from('#__sdi_policy_category p');
            $query->where('p.policy_id = ' . (int) $id);
            $db->setQuery($query);

            $scope = $db->loadColumn();
            return $scope;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Method to save allowed operations on that policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveAllowedOperation($data) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_allowedoperation');
        $query->where('policy_id = ' . (int) $data['id']);

        $db->setQuery($query);
        $db->query();

        $arr_pks = $data['allowedoperation_' . strtolower($data['layout'])];
        if(!is_array($arr_pks)){
            return true;
        }
        
        foreach ($arr_pks as $pk) {
            try {
                $query = $db->getQuery(true);
                $columns = array('policy_id', 'serviceoperation_id');
                $values = array($data['id'], $pk);
                $query->insert('#__sdi_allowedoperation');
                $query->columns($query->quoteName($columns));
                $query->values(implode(',', $values));

                $db->setQuery($query);
                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }
            } catch (Exception $e) {
                $this->setError($e->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * Load allowed operation
     *
     * @param int $pk the primary key of the current policy
     *
     * @return object 	Array of results on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function loadAllowedOperation($pk) {
        if (empty($pk)) {
            return Array();
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('serviceoperation_id');
        $query->from('#__sdi_allowedoperation');
        $query->where('policy_id =' . (int) $pk);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return $db->loadColumn();
    }

    /**
     * Load allowed metadata state
     *
     * @param int $pk the primary key of the current policy
     *
     * @return object 	Array of results on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function loadAllowedMetadatastate($pk) {
        if (empty($pk)) {
            return Array();
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('metadatastate_id');
        $query->from('#__sdi_policy_metadatastate');
        $query->where('policy_id =' . (int) $pk);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return $db->loadColumn();
    }

    /**
     * Load allowed resourcetype
     *
     * @param int $pk the primary key of the current policy
     *
     * @return object 	Array of results on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function loadAllowedResourcetype($pk) {
        if (empty($pk)) {
            return Array();
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('resourcetype_id');
        $query->from('#__sdi_policy_resourcetype');
        $query->where('policy_id =' . (int) $pk);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return $db->loadColumn();
    }

    /**
     * Load allowed visibility
     *
     * @param int $pk the primary key of the current policy
     *
     * @return object 	Array of results on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function loadAllowedVisibility($pk, $target) {
        if (empty($pk)) {
            return Array();
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($query->quoteName($target . '_id'));
        $query->from('#__sdi_policy_visibility');
        $query->where('policy_id =' . $pk);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return $db->loadColumn();
    }

    /**
     * Method to save CSW spatial policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveCSWSpatialPolicy($data) {
        try {
            $db = JFactory::getDbo();

            //Get the existing spatial policy id
            $query = $db->getQuery(true);
            $query->select('csw_spatialpolicy_id')
                    ->from('#__sdi_policy')
                    ->where('id = ' . $data['id']);
            $db->setQuery($query);
            $spatialpolicy_id = $db->loadResult();

            //Delete existing spatial policy
            if ($spatialpolicy_id && ($data['srssource'] == null || $data['srssource'] == '')) {
                $query = $db->getQuery(true);
                $query->update('#__sdi_policy');
                $query->set('csw_spatialpolicy_id = NULL');
                $query->where('id = ' . (int) $data['id']);

                $db->setQuery($query);
                $db->execute();

                $spatial = JTable::getInstance('cswspatialpolicy', 'Easysdi_serviceTable');
                $spatial->delete($spatialpolicy_id);
                parent::save($data);
                return true;
            }

            if (!empty($data['srssource'])):
                $spatialtable = JTable::getInstance('cswspatialpolicy', 'Easysdi_serviceTable');
                $spatial = array();
                $spatial['eastboundlongitude'] = $data['eastboundlongitude'];
                $spatial['westboundlongitude'] = $data['westboundlongitude'];
                $spatial['northboundlatitude'] = $data['northboundlatitude'];
                $spatial['southboundlatitude'] = $data['southboundlatitude'];
                $spatial['maxx'] = $data['maxx'];
                $spatial['maxy'] = $data['maxy'];
                $spatial['minx'] = $data['minx'];
                $spatial['miny'] = $data['miny'];
                $spatial['srssource'] = $data['srssource'];

                if ($spatialpolicy_id) {
                    $spatial['id'] = $spatialpolicy_id;

                    if (!$spatialtable->bind($spatial))
                        throw new Exception('Cannot bind cswspatialpolicy');
                    if (!$spatialtable->store())
                        throw new Exception('Cannot save cswspatialpolicy');
                }else {
                    if (!$spatialtable->bind($spatial))
                        throw new Exception('Cannot bind cswspatialpolicy');
                    if (!$spatialtable->store())
                        throw new Exception('Cannot save cswspatialpolicy');

                    $spatialpolicy_id = $spatialtable->id;
                    $data['csw_spatialpolicy_id'] = $spatialpolicy_id;
                    parent::save($data);
                }
            endif;

            return true;
        } catch (Exception $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }
    }

    /**
     * Load CSW spatial policy
     *
     * @param int $pk the primary key of the current policy
     *
     * @return object 	a CSW spatial policy object on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function loadCSWSpatialPolicy($pk) {
        if (empty($pk)) {
            return null;
        }

        $spatial = JTable::getInstance('cswspatialpolicy', 'Easysdi_serviceTable');
        $spatial->load($pk);

        return $spatial;
    }

    /**
     * Method to save excluded attributes on a csw policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveExcludedAttributes($data) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_excludedattribute');
        $query->where('policy_id = ' . (int) $data['id']);

        $db->setQuery($query);
        $db->execute();
        if (isset($_POST['excluded_attribute'])):
            $arr_ex = $_POST['excluded_attribute'];
            foreach ($arr_ex as $value) {
                if (!empty($value)) {
                    $query = $db->getQuery(true);
                    $columns = array('policy_id', 'path');
                    $values = array($data['id'], $query->quote($value));
                    $query->insert('#__sdi_excludedattribute');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));

                    $db->setQuery($query);
                    try {
                        $db->execute();
                    } catch (JDatabaseException $e) {
                        $je = new JException($e->getMessage());
                        $this->setError($je);
                        return false;
                    }
                }
            }
        endif;
        return true;
    }

    /**
     * Method to save the state of a csw policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveCSWState($data) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_policy_metadatastate');
        $query->where('policy_id = ' . (int) $data['id']);

        $db->setQuery($query);
        $db->execute();

        $arr_pks = $data['csw_state'];
        $version_id = $data['csw_version_id'];
		
		if(!is_array($arr_pks)){
            return true;
        }
		
        foreach ($arr_pks as $pk) {
            $query = $db->getQuery(true);
            $columns = array('policy_id', 'metadatastate_id', 'metadataversion_id');
            $values = array($data['id'], $pk, $version_id);
            $query->insert('#__sdi_policy_metadatastate');
            $query->columns($query->quoteName($columns));
            $query->values(implode(',', $values));

            $db->setQuery($query);
            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }
        return true;
    }

    /**
     * Method to save the authorized visibility (scope) of a csw policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveCSWVisibility($data) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_policy_visibility');
        $query->where('policy_id = ' . (int) $data['id']);
        $db->setQuery($query);
        $db->execute();

        $arr_pks = $data['csw_organisms'];
		if(!is_array($arr_pks)){
            return true;
        }
        foreach ($arr_pks as $pk) {
            $query = $db->getQuery(true);
            $columns = array(policy_id, organism_id);
            $values = array($data['id'], $pk);
            $query->insert($pk);
            $query->columns($query->quoteName($columns));
            $query->values(implode(',', $values));

            $db->setQuery($query);
            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }
        $arr_pks = $data['csw_users'];
        foreach ($arr_pks as $pk) {
            $query = $db->getQuery(true);
            $columns = array('policy_id', 'user_id');
            $values = array($data['id'], $pk);
            $query->insert('#__sdi_policy_visibility');
            $query->columns($query->quoteName($columns));
            $query->values(implode(',', $values));

            $db->setQuery($query);
            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }
        return true;
    }

    /**
     * Method to save the authorized resource type of a csw policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveCSWResourcetype($data) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_policy_resourcetype');
        $query->where('policy_id = ' . (int) $data['id']);

        $db->setQuery($query);
        $db->execute();

        $arr_pks = $data['csw_resourcetype'];
		if(!is_array($arr_pks)){
            return true;
        }
        foreach ($arr_pks as $pk) {
            $query = $db->getQuery(true);
            $columns = array('policy_id', 'resourcetype_id');
            $values = array($data['id'], $pk);
            $query->insert('#__sdi_policy_resourcetype');
            $query->columns($query->quoteName($columns));
            $query->values(implode(',', $values));

            $db->setQuery($query);
            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }
        return true;
    }

    /**
     * Method to save the enabled layers of a wmts policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveWMTSEnabledLayers($data) {
        $arrEnabled = (isset($_POST['enabled'])) ? $_POST['enabled'] : Array();
        $policyID = $data['id'];
        $db = $this->getDbo();

        foreach ($arrEnabled as $physicalServiceID => $arrValues) {
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__sdi_physicalservice_policy');
            $query->where('physicalservice_id = ' . (int) $physicalServiceID);
            $query->where('policy_id = ' . (int) $policyID);

            $db->setQuery($query);

            try {
                $db->execute();
                $physicalservice_policy_id = $db->loadResult();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            //disable all layers (only checked layer will be added)
            $query = $db->getQuery(true);
            $query->update('#__sdi_wmtslayer_policy')->set('enabled = 0')->where('physicalservicepolicy_id = ' . (int) $physicalservice_policy_id);
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
            foreach ($arrValues as $layerID => $value) {
                $query = $db->getQuery(true);
                $query->select('p.id');
                $query->from('#__sdi_wmtslayer_policy p');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = p.physicalservicepolicy_id');
                $query->where('psp.physicalservice_id = ' . $physicalServiceID);
                $query->where('psp.policy_id = ' . $policyID);
                $query->where('p.identifier = ' . $query->quote($layerID));

                $db->setQuery($query);

                try {
                    $db->execute();
                    $num_result = $db->getNumRows();
                    $wmtslayerpolicy_id = $db->loadResult();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                if (0 == $num_result) {
                    //create Wmts Layer Policy if don't exist
                    $query = $db->getQuery(true);
                    $columns = array('identifier', 'enabled', 'physicalservicepolicy_id');
                    $values = array($query->quote($layerID), 1, $physicalservice_policy_id);
                    $query->insert('#__sdi_wmtslayer_policy')
                            ->columns($query->quoteName($columns))
                            ->values(implode(',', $values));
                } else {
                    $query = $db->getQuery(true);
                    $query->update('#__sdi_wmtslayer_policy')
                            ->set('enabled = 1')
                            ->where(Array('id = ' . (int) $wmtslayerpolicy_id,
                    ));
                }

                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Method to save the enabled layers of a wms policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveWMSEnabledLayers($data) {
        $arrEnabled = $_POST['enabled'];
        $policyID = $data['id'];
        $db = $this->getDbo();

        foreach ($arrEnabled as $physicalServiceID => $arrValues) {
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__sdi_physicalservice_policy');
            $query->where('physicalservice_id = ' . (int) $physicalServiceID);
            $query->where('policy_id = ' . (int) $policyID);

            $db->setQuery($query);

            try {
                $db->execute();
                $physicalservice_policy_id = $db->loadResult();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            //disable all layers (only checked layer will be added)
            $query = $db->getQuery(true);
            $query->update('#__sdi_wmslayer_policy')->set('enabled = 0')->where('physicalservicepolicy_id = ' . (int) $physicalservice_policy_id);
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
            if(is_array($arrValues)){
            foreach ($arrValues as $layerID => $value) {
                $query = $db->getQuery(true);
                $query->select('p.id');
                $query->from('#__sdi_wmslayer_policy p');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = p.physicalservicepolicy_id');
                $query->where('psp.physicalservice_id = ' . $physicalServiceID);
                $query->where('psp.policy_id = ' . $policyID);
                $query->where('p.name = ' . $query->quote($layerID));


                $db->setQuery($query);

                try {
                    $db->execute();
                    $num_result = $db->getNumRows();
                    $wmslayerpolicy_id = $db->loadResult();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                if (0 == $num_result) {
                    //create Wmts Layer Policy if don't exist
                    $query = $db->getQuery(true);
                    $columns = array('name', 'enabled', 'physicalservicepolicy_id');
                    $values = array($db->quote($layerID), 1, $physicalservice_policy_id);
                    $query->insert('#__sdi_wmslayer_policy')
                            ->columns($query->quoteName($columns))
                            ->values(implode(',', $values));
                } else {
                    $query = $db->getQuery(true);
                    $query->update('#__sdi_wmslayer_policy')->set(
                            'enabled = 1'
                    )->where(Array(
                        'id = ' . (int) $wmslayerpolicy_id,
                    ));
                }

                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
            }
        }
        return true;
    }

    /**
     * Method to save the enabled feature type of a wfs policy
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function saveWFSEnabledFeatureType($data) {
        $arrEnabled = $_POST['enabled'];
        $policyID = $data['id'];
        $db = $this->getDbo();

        foreach ($arrEnabled as $physicalServiceID => $arrValues) {
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__sdi_physicalservice_policy');
            $query->where('physicalservice_id = ' . (int) $physicalServiceID);
            $query->where('policy_id = ' . (int) $policyID);

            $db->setQuery($query);

            try {
                $db->execute();
                $physicalservice_policy_id = $db->loadResult();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            //disable all layers (only checked layer will be added)
            $query = $db->getQuery(true);
            $query->update('#__sdi_featuretype_policy')->set('enabled = 0')->where('physicalservicepolicy_id = ' . (int) $physicalservice_policy_id);
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
            foreach ($arrValues as $layerID => $value) {
                $query = $db->getQuery(true);
                $query->select('p.id');
                $query->from('#__sdi_featuretype_policy p');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = p.physicalservicepolicy_id');
                $query->where('psp.physicalservice_id = ' . $physicalServiceID);
                $query->where('psp.policy_id = ' . $policyID);
                $query->where('p.name = ' . $query->quote($layerID));

                $db->setQuery($query);

                try {
                    $db->execute();
                    $num_result = $db->getNumRows();
                    $wmslayerpolicy_id = $db->loadResult();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }

                if (0 == $num_result) {
                    //create Wmts Layer Policy if don't exist
                    $query = $db->getQuery(true);
                    $columns = array('name', 'enabled', 'physicalservicepolicy_id');
                    $values = array($db->quote($layerID), 1, $physicalservice_policy_id);
                    $query->insert('#__sdi_featuretype_policy')
                            ->columns($query->quoteName($columns))
                            ->values(implode(',', $values));
                } else {
                    $query = $db->getQuery(true);
                    $query->update('#__sdi_featuretype_policy')
                            ->set('enabled = 1')
                            ->where(Array('id = ' . (int) $wmslayerpolicy_id,
                    ));
                }

                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Cache capabilities xml in db for later uses
     *
     * @param string $xml	xmlstring to store in DB
     *
     * @param int $physicalServiceID a physical service id
     *
     * @param int $virutalServiceID a virtual service id
     *
     * @return boolean 	True on success, False on error
     *
     */
    private function cacheXMLCapabilities($xml, $physicalServiceID, $virtualServiceID) {
        $xml = trim(preg_replace('/\n/', ' ', $xml));

        $db = $this->getDbo();

        $query = $db->getQuery(true);
        $query->select('pssc.id');
        $query->from('#__sdi_virtualservice vs');
        $query->innerJoin('#__sdi_virtual_physical vp ON vs.id = vp.virtualservice_id');
        $query->innerJoin('#__sdi_physicalservice ps ON ps.id = vp.physicalservice_id');
        $query->innerJoin('#__sdi_physicalservice_servicecompliance pssc ON ps.id = pssc.service_id');
        $query->innerJoin('#__sdi_virtualservice_servicecompliance vssc ON vs.id = vssc.service_id');
        $query->innerJoin('#__sdi_sys_servicecompliance sc ON sc.id = vssc.servicecompliance_id');
        $query->innerJoin('#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
        $query->where('ps.id = ' . $physicalServiceID);
        $query->where('vs.id = ' . $virtualServiceID);
        $query->order('sv.ordering DESC');

        $db->setQuery($query, 0, 1);
        try {
            $db->execute();
            $serviceComplianceID = $db->loadResult();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        $query = $db->getQuery(true);
        $query->update('#__sdi_physicalservice_servicecompliance')
                ->set('capabilities = ' . $query->quote($query->escape($xml)))
                ->where('id = ' . (int) $serviceComplianceID);
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }
    }

    /**
     * Calculate the authorized Tiles based on the inherited bboxes
     *
     * @param array 	$data	data posted from the form
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    private function calculateAllTiles($data) {
        set_time_limit(300);
        $db = JFactory::getDbo();
        $policy_id = $data['id'];
        $precalculatedData = json_decode($_POST['precalculatedData']);

        $query = $db->getQuery(true);
        $query->select('ps.id, ps.resourceurl AS url, psp.id AS psp_id');
        $query->from('#__sdi_physicalservice_policy psp');
        $query->innerJoin('#__sdi_physicalservice ps ON ps.id = psp.physicalservice_id');
        $query->where('psp.policy_id = ' . (int) $policy_id);

        try {
            $db->setQuery($query);
            $db->execute();
            $arr_ps = $db->loadObjectList();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        $arr_wmtsObj = Array();
        foreach ($arr_ps as $ps) {
            $query = $db->getQuery(true);
            $query->select('wp.*, wsp.*, wp.id AS wmtslayerpolicy_id, tms.identifier AS tms_identifier, tm.identifier AS tm_identifier');
            $query->from('#__sdi_wmtslayer_policy wp');
            $query->innerJoin('#__sdi_physicalservice_policy pp ON wp.physicalservicepolicy_id = pp.id');
            $query->innerJoin('#__sdi_wmts_spatialpolicy wsp ON wp.spatialpolicy_id = wsp.id');
            $query->innerJoin('#__sdi_tilematrixset_policy tms ON wp.id = tms.wmtslayerpolicy_id');
            $query->leftJoin('#__sdi_tilematrix_policy tm ON tms.id = tm.tilematrixsetpolicy_id');
            $query->where('pp.id = ' . (int) $ps->psp_id);

            $db->setQuery($query);

            try {
                $db->execute();
                $resultset = $db->loadObjectList();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            //preparing the object to be returned
            $data = Array();
            $tms_arr = Array();
            foreach ($resultset as $tileMatrixSet) {
                if (empty($data[$tileMatrixSet->identifier])) {
                    $data[$tileMatrixSet->identifier] = Array(
                        'enabled' => $tileMatrixSet->enabled,
                        'spatialOperator' => $tileMatrixSet->spatialoperator_id,
                        'westBoundLongitude' => $tileMatrixSet->westboundlongitude,
                        'eastBoundLongitude' => $tileMatrixSet->eastboundlongitude,
                        'northBoundLatitude' => $tileMatrixSet->northboundlatitude,
                        'southBoundLatitude' => $tileMatrixSet->southboundlatitude,
                        'tileMatrixSetList' => Array(),
                    );
                }
                $data[$tileMatrixSet->identifier]['tileMatrixSetList'][$tileMatrixSet->tms_identifier] = Array('maxTileMatrix' => $tileMatrixSet->tm_identifier);
            }

            $wmtsObj = new WmtsPhysicalService($ps->id, $ps->url);
            $wmtsObj->getCapabilities();
            $wmtsObj->populate();
            $wmtsObj->sortLists();
            $wmtsObj->loadData($data);

            //we insert the inherited bbox set for the server
            $wmtsObj->setAllBoundingBoxes(Array(
                'north' => $precalculatedData->inherit_server->{$ps->id}->northBoundLatitude,
                'east' => $precalculatedData->inherit_server->{$ps->id}->eastBoundLongitude,
                'south' => $precalculatedData->inherit_server->{$ps->id}->southBoundLatitude,
                'west' => $precalculatedData->inherit_server->{$ps->id}->westBoundLongitude,
            ));
            //we insert the inherited bbox set for the all policy
            $wmtsObj->setAllBoundingBoxes(Array(
                'north' => $precalculatedData->inherit_policy->northBoundLatitude,
                'east' => $precalculatedData->inherit_policy->eastBoundLongitude,
                'south' => $precalculatedData->inherit_policy->southBoundLatitude,
                'west' => $precalculatedData->inherit_policy->westBoundLongitude,
            ));
            //we insert the srsUnits
            $wmtsObj->setAllSRSUnit($precalculatedData->srs_units);

            //insert projected bboxes in each tms
            foreach ($wmtsObj->getLayerList() as $layerObj) {
                foreach ($layerObj->getTileMatrixSetList() as $tmsObj) {
                    if (isset($precalculatedData->inherit_server->{$ps->id}->recalculated->{$tmsObj->srs})) {
                        $bbox = $precalculatedData->inherit_server->{$ps->id}->recalculated->{$tmsObj->srs};
                        $tmsObj->minX = $bbox->minX;
                        $tmsObj->maxX = $bbox->maxX;
                        $tmsObj->minY = $bbox->minY;
                        $tmsObj->maxY = $bbox->maxY;
                    }
                }
            }
            $wmtsObj->calculateAuthorizedTiles();
            $arr_wmtsObj[] = $wmtsObj;
        }

        foreach ($arr_wmtsObj as $wmtsObj) {
            foreach ($wmtsObj->getLayerList() as $layerObj) {
                foreach ($layerObj->getTileMatrixSetList() as $tmsObj) {
                    foreach ($tmsObj->getTileMatrixList() as $tmObj) {
                        $query = '
							SELECT psp.id AS pspID, tm.id AS tmID, tms.id AS tmsID, wp.id AS wpID
							FROM #__sdi_policy p
							JOIN #__sdi_physicalservice_policy psp
							ON p.id = psp.policy_id
							LEFT JOIN #__sdi_wmtslayer_policy wp
							ON (
								psp.id = wp.physicalservicepolicy_id
								AND (
									wp.identifier = ' . $db->quote($layerObj->name) . '
									OR wp.identifier IS NULL
								)
							)
							LEFT JOIN #__sdi_tilematrixset_policy tms
							ON (
								wp.id = tms.wmtslayerpolicy_id
								AND (
									tms.identifier = ' . $db->quote($tmsObj->identifier) . '
									OR tms.identifier IS NULL
								)
							)
							LEFT JOIN #__sdi_tilematrix_policy tm
							ON (
								tms.id = tm.tilematrixsetpolicy_id
								AND (
									tm.identifier = ' . $db->quote($tmObj->identifier) . '
									OR tm.identifier IS NULL
								)
							)
							WHERE p.id = ' . $policy_id . '
							AND psp.physicalservice_id = ' . $wmtsObj->id . ';
						';

                        $db->setQuery($query);
                        try {
                            $db->execute();
                            $result = $db->loadObject();
                            $pspID = $result->pspID;
                            $wpID = $result->wpID;
                            $tmsID = $result->tmsID;
                            $tmID = $result->tmID;
                        } catch (JDatabaseException $e) {
                            $je = new JException($e->getMessage());
                            $this->setError($je);
                            return false;
                        }

                        //if the layer doesn't exist yet, we create it
                        if (is_null($wpID)) {
                            $query = $db->getQuery(true);
                            $columns = Array('identifier', 'physicalservicepolicy_id');
                            $values = array($query->quote($layerObj->name), $pspID);
                            $query->insert('#__sdi_wmtslayer_policy')
                                    ->columns($query->quoteName($columns))
                                    ->values(implode(',', $values));
                            $db->setQuery($query);
                            try {
                                $db->execute();
                                $wpID = $db->insertid();
                            } catch (JDatabaseException $e) {
                                $je = new JException($e->getMessage());
                                $this->setError($je);
                                return false;
                            }
                        }

                        //if the tilematrixset doesn't exist yet, we create it
                        if (is_null($tmsID)) {
                            $query = $db->getQuery(true);
                            $columns = array('identifier', 'wmtslayerpolicy_id', 'srssource');
                            $values = array($query->quote($tmsObj->identifier), $wpID, $query->quote($tmsObj->srs));
                            $query->insert('#__sdi_tilematrixset_policy')
                                    ->columns($query->quoteName($columns))
                                    ->values(implode(',', $values));
                            $db->setQuery($query);
                            try {
                                $db->execute();
                                $tmsID = $db->insertid();
                            } catch (JDatabaseException $e) {
                                $je = new JException($e->getMessage());
                                $this->setError($je);
                                return false;
                            }
                        }

                        $query = $db->getQuery(true);
                        if (isset($tmID) && is_numeric($tmID)) {
                            $query->update('#__sdi_tilematrix_policy')
                                    ->set('tileminrow = ' . ((isset($tmObj->minTileRow)) ? $tmObj->minTileRow : 'null'))
                                    ->set('tilemaxrow = ' . ((isset($tmObj->maxTileRow)) ? $tmObj->maxTileRow : 'null'))
                                    ->set('tilemincol = ' . ((isset($tmObj->minTileCol)) ? $tmObj->minTileCol : 'null'))
                                    ->set('tilemaxcol = ' . ((isset($tmObj->maxTileCol)) ? $tmObj->maxTileCol : 'null'))
                                    ->where('id = ' . (int) $tmID);
                        } else {
                            $columns = array('tilematrixsetpolicy_id', 'identifier', 'tileminrow', 'tilemaxrow', 'tilemincol', 'tilemaxcol');
                            $values = array($tmsID, $query->quote($tmObj->identifier), ((isset($tmObj->minTileRow)) ? $tmObj->minTileRow : 'null'), ((isset($tmObj->maxTileRow)) ? $tmObj->maxTileRow : 'null'), ((isset($tmObj->minTileCol)) ? $tmObj->minTileCol : 'null'), ((isset($tmObj->maxTileCol)) ? $tmObj->maxTileCol : 'null'));
                            $query->insert('#__sdi_tilematrix_policy')
                                    ->columns($query->quoteName($columns))
                                    ->values(implode(',', $values));
                        }
                        $db->setQuery($query);
                        try {
                            $db->execute();
                        } catch (JDatabaseException $e) {
                            $je = new JException($e->getMessage());
                            $this->setError($je);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

}
