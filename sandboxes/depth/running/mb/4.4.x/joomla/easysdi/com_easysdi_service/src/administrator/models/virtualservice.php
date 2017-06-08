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
class Easysdi_serviceModelvirtualservice extends JModelAdmin {

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
    public function getTable($type = 'Virtualservice', $prefix = 'Easysdi_serviceTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_service.virtualservice', 'virtualservice', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_service.edit.virtualservice.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            //Support for multiple or not foreign key field: proxytype_id
            $array = array();
            foreach ((array) $data->proxytype_id as $value):
                if (!is_array($value)):
                    $array[] = $value;
                endif;
            endforeach;
            $data->proxytype_id = implode(',', $array);

            //Support for multiple or not foreign key field: exceptionlevel_id
            $array = array();
            foreach ((array) $data->exceptionlevel_id as $value):
                if (!is_array($value)):
                    $array[] = $value;
                endif;
            endforeach;
            $data->exceptionlevel_id = implode(',', $array);

            //Support for multiple or not foreign key field: loglevel_id
            $array = array();
            foreach ((array) $data->loglevel_id as $value):
                if (!is_array($value)):
                    $array[] = $value;
                endif;
            endforeach;
            $data->loglevel_id = implode(',', $array);

            //Support for multiple or not foreign key field: logroll_id
            $array = array();
            foreach ((array) $data->logroll_id as $value):
                if (!is_array($value)):
                    $array[] = $value;
                endif;
            endforeach;
            $data->logroll_id = implode(',', $array);
        }

        return $data;
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
        if (isset($this->alias)) {//Get Item by alias
            $item = $this->getTable();
            $return = $item->loadByAlias($this->alias);
            if ($return === false && $item->getError()) {
                $this->setError($item->getError());
                return false;
            }
        } else {//Get item by Id
            $item = parent::getItem($pk);
            if (!$item) {
                return false;
            }
        }

        //inserting virtualmetadata content in virtualservice for display of edit form
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables');
        $metadata = JTable::getInstance('virtualmetadata', 'Easysdi_serviceTable');
        $metadata->loadByVirtualServiceID(JRequest::getVar('id', null));
        //Merging metadata object fields into virtualservice object 
        $item_fields = Array();
        foreach ($item as $key => $value) {
            $item_fields[] = $key;
        }
        foreach ($metadata->getFields() as $field) {
            if (!in_array($field->Field, $item_fields)) {
                $item->{$field->Field} = $metadata->{$field->Field};
            }
        }

        //Get service compliance
        $compliances = $this->getServiceCompliance($item->id);
        $compliance_ids = array();
        $compliance_values = array();
        if (isset($compliances)) {
            foreach ($compliances as $compliance) {
                $compliance_ids[] = $compliance->id;
                $compliance_values[] = $compliance->value;
            }
        }
        if (count($compliance_ids) > 0)
            $item->compliance = json_encode($compliance_values);
        else
            $item->compliance = '';
        $item->supportedversions = json_encode($compliance_values);

        $item->physicalservice_id = $this->getPhysicalServiceAggregation($item->id);

        // Get the service scope
        $item->organisms = $this->getServiceScopeOrganism($item->id);

        //SetLayout : layout is the connector type
        if (!$item->serviceconnector_id) {
            $item->serviceconnector_id = JRequest::getVar('connector');
        }
        $serviceconnector = JTable::getInstance('serviceconnector', 'Easysdi_serviceTable');
        $serviceconnector->load($item->serviceconnector_id);
        $item->serviceconnector = $serviceconnector->value;

        $item->layout = ($serviceconnector->value == "WMSC") ? "WMS" : $serviceconnector->value;

        return $item;
    }

    /**
     * Method to get a single record.
     *
     * @param	$alias 			string		Alias of the service.
     *
     * @return	mixed	Object on success, false on failure.
     * @since	EasySDI 3.0.0
     */
    public function getItemByServiceAlias($alias) {
        $this->alias = $alias;
        $item = $this->getItem(null);

        return $item;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @since	1.6
     */
    protected function prepareTable($table) {
        jimport('joomla.filter.output');
        $jform = JRequest::getVar('jform');

        //Service id is set to default value '0' in case of creation.
        //So this section of code is never executed.
        //Ordering is set in sdiTable->check() function.
        //However, We keep this section in case of default id was not set to '0' anymore (changes in form xml)
        if (empty($table->id)) {

            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('MAX(ordering)');
                $query->from('#__sdi_virtualservice');

                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
        if (!isset($jform['reflectedmetadata'])) { // see if the checkbox has been submitted
            $table->reflectedmetadata = 0; // if it has not been submitted, mark the field unchecked
        } else {
            $table->reflectedmetadata = 1; //else mark the field checked
        }

        //Alias is a mandatory field, if not set, use the name
        if (empty($table->alias)) {
            $table->alias = $table->name;
        }

        //Load component parameters
        $params = JComponentHelper::getParams('com_easysdi_service');
        $table->url = $params->get('proxyurl') . $table->alias;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   11.1
     */
    public function save($data) {
        if (parent::save($data)) {
            $data['id'] = $this->getItem()->get('id');
            //Instantiate an address JTable
            $virtualmetadata = JTable::getInstance('virtualmetadata', 'Easysdi_serviceTable');
            $virtualmetadata->loadByVirtualServiceID($data['id']);
            //If reflectedmetadata option is checked, delete existing metadata
            if (isset($data['reflectedmetadata'])) {
                if (isset($virtualmetadata->id))
                    $virtualmetadata->delete($virtualmetadata->id);
            }else {
                //Call the overloaded save function to store the input data
                if (!$virtualmetadata->save($data)) {
                    return false;
                }
            }

            if (isset($data['physicalservice_id'])) {
                if (!$this->savePhysicalServiceAggregation($data, $this->getState($this->getName() . '.id'))) {
                    return false;
                }
            }

            $physicalservicepolicy = JTable::getInstance('physicalservice_policy', 'Easysdi_serviceTable');
            if (!$physicalservicepolicy->saveAll($data['id'])) {
                return false;
            }

            if (isset($data['compliance'])) {
                if (!$this->saveServiceCompliance($data['compliance'], $data['serviceconnector_id'], $this->getState($this->getName() . '.id'))) {
                    return false;
                }
            }

            if (!$this->saveServiceScopeOrganism($data['organisms'], $this->getState($this->getName() . '.id'))) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 
     */
    protected function savePhysicalServiceAggregation($data, $id) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_virtual_physical');
        $query->where('virtualservice_id = ' . (int) $id);

        $db->setQuery($query);
        $db->query();

        $arr_pks = $data['physicalservice_id'];
        foreach ($arr_pks as $pk) {
            try {
                $query = $db->getQuery(true);
                $columns = array('virtualservice_id', 'physicalservice_id');
                $values = array($id, $pk);
                $query->insert('#__sdi_virtual_physical');
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
     * 
     */
    public function getPhysicalServiceAggregation($id = null) {
        if (!isset($id))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('physicalservice_id');
            $query->from('#__sdi_virtual_physical');
            $query->where('virtualservice_id =' . (int) $id);

            $db->setQuery($query);
            return $db->loadColumn();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Method to save the service compliance deducted from the agregation process
     *
     * @param array 	$pks	array of the #__sdi_sys_servicecompliance ids to link with the current service
     * @param int		$id		primary key of the current service to save.
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    public function saveServiceCompliance($pks, $connector, $id) {
        //Delete previously saved compliance
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_virtualservice_servicecompliance');
        $query->where('service_id = ' . (int) $id);

        $db->setQuery($query);
        $db->query();

        $arr_pks = json_decode($pks);
        foreach ($arr_pks as $pk) {
            try {
                $queryvalue = $db->getQuery(true);
                $queryvalue->select('c.id');
                $queryvalue->from('#__sdi_sys_servicecompliance c');
                $queryvalue->innerJoin('#__sdi_sys_serviceversion v ON c.serviceversion_id = v.id');
                $queryvalue->where('c.serviceconnector_id = ' . (int) $connector);
                $queryvalue->where('v.value=' . $queryvalue->quote($pk));

                $db->setQuery($queryvalue);
                $servicecompliance = $db->loadObject();

                $query = $db->getQuery(true);
                $columns = array('service_id', 'servicecompliance_id');
                $values = array($id, $servicecompliance->id);
                $query->insert('#__sdi_virtualservice_servicecompliance');
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
     * Method to get the service compliance deducted from the agregation process and saved into database
     *
     * @param int		$id		primary key of the current service to get.
     *
     * @return boolean 	Object list on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    public function getServiceCompliance($id = null) {
        if (!isset($id))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('sv.value as value, sc.id as id');
            $query->from('#__sdi_virtualservice_servicecompliance ssc');
            $query->join('INNER', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id ');
            $query->join('INNER', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id ');
            $query->where('ssc.service_id = ' . (int) $id);
            $db->setQuery($query);

            $compliance = $db->loadObjectList();
            return $compliance;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Method to save the organisms allowed by the service scope
     *
     * @param array 	$pks	array of the #__sdi_organism ids to link with the current service
     * @param int		$id		primary key of the current service to save.
     *
     * @return boolean 	True on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    public function saveServiceScopeOrganism($pks, $id) {
        //Delete previously saved compliance
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_virtualservice_organism');
        $query->where('virtualservice_id = ' . (int) $id);

        $db->setQuery($query);
        $db->query();

        if(!is_array($pks)){
            return true;
        }
        foreach ($pks as $pk) {
            try {
                $query = $db->getQuery(true);
                $columns = array('virtualservice_id', 'organism_id');
                $values = array($id, $pk);
                $query->insert('#__sdi_virtualservice_organism');
                $query->columns($query->quote($columns));
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
     * Method to get the organisms authorized to access this service
     *
     * @param int		$id		primary key of the current service to get.
     *
     * @return boolean 	Object list on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    public function getServiceScopeOrganism($id = null) {
        if (!isset($id))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('so.organism_id as id');
            $query->from('#__sdi_virtualservice_organism so');
            $query->where('so.virtualservice_id = ' . (int) $id);
            $db->setQuery($query);

            $scope = $db->loadColumn();
            return $scope;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

}
