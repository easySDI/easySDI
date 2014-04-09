<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_map model.
 */
class Easysdi_mapModelmap extends JModelAdmin {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_EASYSDI_MAP';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Map', $prefix = 'Easysdi_mapTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param	object	$record	A record object.
     *
     * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
     * @since	1.6
     */
    protected function canDelete($record) {
        $user = JFactory::getUser();

        if (!empty($record->id)) {
            if ($record->state != -2) {
                return;
            }
            return parent::canDelete($record);
        }
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
        $form = $this->loadForm('com_easysdi_map.map', 'map', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_map.edit.map.data', array());

        if (empty($data)) {
            $data = $this->getItem();
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
        if ($item = parent::getItem($pk)) {

            $item->url = JURI::root() . 'index.php?option=com_easysdi_map&view=map&id=' . $item->id;

            if ($item->id) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('group_id');
                $query->from('#__sdi_map_layergroup');
                $query->where('isbackground = 0');
                $query->where('map_id = ' . (int)$item->id);
                
                $db->setQuery($query);
                $item->groups = $db->loadColumn();

                $query = $db->getQuery(true);
                $query->select('group_id');
                $query->from('#__sdi_map_layergroup');
                $query->where('isbackground = 1');
                $query->where('map_id = ' . (int)$item->id);
                $db->setQuery($query);
                $item->background = $db->loadResult();

                $query = $db->getQuery(true);
                $query->select('group_id');
                $query->from('#__sdi_map_layergroup');
                $query->where('isdefault = 1');
                $query->where('map_id = ' . (int)$item->id);
                
                $db->setQuery($query);
                $item->default = $db->loadResult();

                //Tools activation
                $query = $db->getQuery(true);
                $query->select('tool_id, params');
                $query->from('#__sdi_map_tool');
                $query->where('map_id = ' . $item->id);
                
                $db->setQuery($query);
                $tools = $db->loadObjectList();
                foreach ($tools as $tool):
                    $n = 'tool' . $tool->tool_id;
                    (empty($tool->params))?$item->$n = "1" : $item->$n = $tool->params;
                endforeach;
                
                //Search Catalog
                $query = $db->getQuery(true);
                $query->select('params');
                $query->from('#__sdi_map_tool');
                $query->where('tool_id=17');
                $query->where('map_id = ' . (int)$item->id);
                
                $db->setQuery($query);
                $catalogsearch = $db->loadResult();
                if(!empty($catalogsearch)){
                    $item->tool17 = "1";
                    $item->catalog_id = $catalogsearch;
                }

                //Scale line parameters
                $query = $db->getQuery(true);
                $query->select('params');
                $query->from('#__sdi_map_tool');
                $query->where('tool_id=14');
                $query->where('map_id = ' . (int)$item->id);
                
                $db->setQuery($query);
                $scalelineparams = $db->loadResult();
                if(!empty($scalelineparams)){
                    $params = json_decode($scalelineparams);
                    foreach ($params as $key => $value) {
                        $item->$key = $value;
                    } 
                }
                
                //Wfs locator
                $query = $db->getQuery(true);
                $query->select('params');
                $query->from('#__sdi_map_tool');
                $query->where('tool_id=16');
                $query->where('map_id = ' . $item->id);
                
                $db->setQuery($query);
                $wfslocator = $db->loadResult();
                if(!empty($wfslocator)){
                    $params = json_decode($wfslocator);
                    foreach ($params as $key => $value) {
                        $item->$key = $value;
                    }                    
                }
                
                $query = $db->getQuery(true);
                $query->select($query->concatenate(array('"physical_"','physicalservice_id')));
                $query->from('#__sdi_map_physicalservice');
                $query->where('map_id = ' . $item->id);
                
                $query2 = $db->getQuery(true);
                $query2->select($query->concatenate(array('"physical_"','physicalservice_id')));
                $query2->from('#__sdi_map_virtualservice');
                $query2->where('map_id = ' . $item->id);
                $query->union($query2);
                
                
                $db->setQuery($query);
                $item->services = $db->loadColumn();
            }
        }

        return $item;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @since	1.6
     */
    protected function prepareTable($table) {
        jimport('joomla.filter.output');

        //Map id is set to default value '0' in case of creation.
        //So this section of code is never executed.
        //Ordering is set in sdiTable->check() function.
        //However, We keep this section in case of default id was not set to '0' anymore (changes in form xml)
        if (empty($table->id)) {

            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('MAX(ordering)');
                $query->from('#__sdi_map');
                
                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
        if (empty($table->alias)) {
            $table->alias = $table->name;
        }
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
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_tool');
            $query->where('map_id = ' . (int)$this->getItem()->get('id'));
            
            $db->setQuery($query);
            $db->query();
            
            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_physicalservice');
            $query->where('map_id = ' . (int)$this->getItem()->get('id'));
            $db->setQuery($query);
            $db->query();
            
            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_virtualservice');
            $query->where('map_id = ' . (int)$this->getItem()->get('id'));
            $db->setQuery($query);
            $db->query();

            //Tools
            foreach ($data as $key => $value) {
                if (substr($key, 0, 4) == "tool") {
                    if ($value == 1) {
                        $tool = substr($key, 4);
                        ($tool == '17')? $params= $data['catalog_id']: $params = 'NULL';   
                        
                        $columns = array('map_id', 'tool_id', 'params');
                        $values = array($this->getItem()->get('id'),$query->quote($tool), $query->quote($params));
                        $query = $db->getQuery(true);
                        $query->insert('#__sdi_map_tool');
                        $query->columns($query->quoteName($columns));
                        $query->values(implode(',', $values));
                        
                        $db->setQuery($query);
                        if (!$db->query()) {
                            $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_TOOL_ERROR"));
                            return false;
                        }
                    } else if ($value == "html" || $value == "grid") {
                        $tool = substr($key, 4);
                        
                        $columns = array('map_id', 'tool_id', 'params');
                        $values = array($this->getItem()->get('id'),$query->quote($tool),$query->quote($value));
                        $query = $db->getQuery(true);
                        $query->insert('#__sdi_map_tool');
                        $query->columns($query->quoteName($columns));
                        $query->values(implode(',', $values));
                        
                        $db->setQuery($query);
                        if (!$db->query()) {
                            $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_TOOL_ERROR"));
                            return false;
                        }                        
                    }
                }
            }

            //Scale line params
            $scaleparamsparams = '{\"bottomInUnits\" :\"'.$data["bottomInUnits"].'\",\"bottomOutUnits\" :\"'.$data["bottomOutUnits"].'\",\"topInUnits\" :\"'.$data["topInUnits"].'\",\"topOutUnits\" :\"'.$data["topOutUnits"].'\"}';
            
            $columns = array('map_id', 'tool_id', 'params');
            $values = array($this->getItem()->get('id'), 14, $query->quote($scaleparamsparams));
            $query = $db->getQuery(true);
            $query->insert('#__sdi_map_tool');
            $query->columns($query->quoteName($columns));
            $query->values(implode(',', $values));
            
            $db->setQuery($query);
                        if (!$db->query()) {
                            $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_TOOL_ERROR"));
                            return false;
                        }
                        
            //Wfs locator
            $locatorparamsparams = '{\"urlwfslocator\" :\"'.$data["urlwfslocator"].'\",\"fieldname\" :\"'.$data["fieldname"].'\",\"featuretype\" :\"'.$data["featuretype"].'\",\"featureprefix\" :\"'.$data["featureprefix"].'\",\"geometryname\" :\"'.$data["geometryname"].'\"}';
            
            $columns = array('map_id', 'tool_id', 'params');
            $values = array($this->getItem()->get('id'), 16, $query->quote($locatorparamsparams));
            $query = $db->getQuery(true);
            $query->insert('#__sdi_map_tool');
            $query->columns($query->quoteName($columns));
            $query->values(implode(',', $values));
            
            
            $db->setQuery($query);
                        if (!$db->query()) {
                            $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_TOOL_ERROR"));
                            return false;
                        }
                        
            //Service
            $services = $data['services'];
            foreach ($services as $service) {
                $pos = strstr($service, 'physical_');
                if ($pos) {
                    $service_id = substr($service, strrpos($service, '_') + 1);
                    
                    $columns = array('map_id', 'physicalservice_id');
                    $values = array($this->getItem()->get('id'), $service_id);
                    $query = $db->getQuery(true);
                    $query->insert('#__sdi_map_physicalservice');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));
                    
                    $db->setQuery($query);
                } else {
                    $service_id = substr($service, strrpos($service, '_') + 1);
                    
                    $columns = array('map_id', 'virtualservice_id');
                    $values = array($this->getItem()->get('id'), $service_id);
                    $query = $db->getQuery(true);
                    $query->insert('#__sdi_map_virtualservice');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));
                    
                    $db->setQuery($query);
                }
                if (!$db->query()) {
                    $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_SERVICE_ERROR"));
                    return false;
                }
            }

            //Default adding  group
            $default = $data['default'];

            //Background group
            $background = $data['background'];

            //Overlay groups and default adding group
            $groups = $data['groups'];
            if (!empty($default) && array_search($default, $groups) === false)
                $groups[] = $default;
            if (!empty($background) && array_search($background, $groups) === false)
                $groups[] = $background;



            //Get existing relations for the current map
            try {
                $query = $db->getQuery(true);
                $query
                        ->select('group_id')
                        ->from('#__sdi_map_layergroup ')
                        ->where('map_id= ' . (int)$this->getItem()->get('id'));
                $db->setQuery($query);
                $pks = $db->loadColumn();
            } catch (Exception $e) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_GROUP_ERROR"));
                return false;
            }

            //Clean up the database from groups no more selected
            foreach ($pks as $pk) {
                if (in_array($pk, $groups)) {//Existing group
                    //Remove this layer from the selected list, because it doesn't have to be changed in the database
                    if (($key = array_search($pk, $groups)) !== false) {
                        unset($groups[$key]);
                    }
                } else { //Group is no more selected, delete the relation
                    $query = $db->getQuery(true);
                    $query
                            ->delete('#__sdi_map_layergroup')
                            ->where('map_id= ' . (int)$this->getItem()->get('id'))
                            ->where('group_id =' . (int)$pk);
                    $db->setQuery($query);
                    try {
                        // Execute the query in Joomla 3.0.
                        $result = $db->execute();
                    } catch (Exception $e) {
                        $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_GROUP_ERROR"));
                        return false;
                    }
                }
            }

            //Select max ordering for the groups of the current map
            try {
                $query = $db->getQuery(true);
                $query
                        ->select('MAX(ordering)')
                        ->from('#__sdi_map_layergroup ')
                        ->where('map_id= ' . (int)$this->getItem()->get('id'));
                $db->setQuery($query);
                $ordering = $db->loadResult();
            } catch (Exception $e) {
                // catch any database errors.
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_MAXORDERING_FAIL_GROUP_ERROR"));
                return false;
            }
            if (!$ordering)
                $ordering = 0;

            //Insert the new relation
            foreach ($groups as $group) {
                if (empty($group))
                    continue;
                $ordering++;
                //Store map-group relation
                $columns = array('map_id', 'group_id', 'isbackground', 'isdefault', 'ordering');
                $values = array($this->getItem()->get('id'), $group, '0', '0', $ordering);
                $query = $db->getQuery(true);
                $query
                        ->insert($db->quoteName('#__sdi_map_layergroup'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));
                $db->setQuery($query);
                try {
                    $result = $db->execute();
                } catch (Exception $e) {
                    $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_INSERT_FAIL_GROUP_ERROR"));
                    return false;
                }
            }

            //Clean up isBackground boolean state
            $query = $db->getQuery(true);
            $query
                    ->update($db->quoteName('#__sdi_map_layergroup'))
                    ->set('isbackground=0')
                    ->where('map_id= ' . (int)$this->getItem()->get('id'));
            $db->setQuery($query);
            try {
                $result = $db->execute();
            } catch (Exception $e) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELBACKGROUND_FAIL_GROUP_ERROR"));
                return false;
            }

            //Clean up isdefault boolean state
            $query = $db->getQuery(true);
            $query
                    ->update($db->quoteName('#__sdi_map_layergroup'))
                    ->set('isdefault=0')
                    ->where('map_id= ' . (int)$this->getItem()->get('id'));
            $db->setQuery($query);
            try {
                $result = $db->execute();
            } catch (Exception $e) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELDEFAULT_FAIL_GROUP_ERROR"));
                return false;
            }


            //Set background group if needed
            if (!empty($background)) {
                $query = $db->getQuery(true);
                $query
                        ->update($db->quoteName('#__sdi_map_layergroup'))
                        ->set('isbackground=1')
                        ->where('map_id= ' . (int)$this->getItem()->get('id'))
                        ->where('group_id= ' . (int)$background);
                $db->setQuery($query);
                try {
                    $result = $db->execute();
                } catch (Exception $e) {
                    $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SETBACKGROUND_FAIL_GROUP_ERROR"));
                    return false;
                }
            }

            //Set default adding group if needed
            if (!empty($default)) {
                $query = $db->getQuery(true);
                $query
                        ->update($db->quoteName('#__sdi_map_layergroup'))
                        ->set('isdefault=1')
                        ->where('map_id= ' . (int)$this->getItem()->get('id'))
                        ->where('group_id= ' . (int)$default);
                $db->setQuery($query);
                try {
                    $result = $db->execute();
                } catch (Exception $e) {
                    $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SETDEFAULT_FAIL_GROUP_ERROR"));
                    return false;
                }
            }


            return true;
        }
        return false;
    }

    /**
     * Method to delete one or more records.
     *
     * @param   array  &$pks  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     *
     * @since   11.1
     */
    public function delete(&$pks) {
        // Iterate the items to delete each one.
        foreach ($pks as $i => $pk) {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_tool');
            $query->where('map_id = ' . (int)$pk);
            
            $db->setQuery($query);
            if (!$db->query()) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_TOOL_ERROR"));
                return false;
            }
            
            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_layergroup');
            $query->where('map_id = ' . (int)$pk);
            
            $db->setQuery($query);
            if (!$db->query()) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_GROUP_ERROR"));
                return false;
            }
            
            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_physicalservice');
            $query->where('map_id = ' . (int)$pk);
            
            $db->setQuery($query);
            if (!$db->query()) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_SERVICE_ERROR"));
                return false;
            }
            
            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_virtualservice');
            $query->where('map_id = ' . (int)$pk);
            
            $db->setQuery($query);
            if (!$db->query()) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_SERVICE_ERROR"));
                return false;
            }
        }

        return parent::delete($pks);
    }

}