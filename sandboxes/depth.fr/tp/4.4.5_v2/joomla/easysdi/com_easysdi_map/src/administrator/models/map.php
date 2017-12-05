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
                $query->where('map_id = ' . (int) $item->id);

                $db->setQuery($query);
                $item->groups = $db->loadColumn();

                $query = $db->getQuery(true);
                $query->select('group_id');
                $query->from('#__sdi_map_layergroup');
                $query->where('isbackground = 1');
                $query->where('map_id = ' . (int) $item->id);
                $db->setQuery($query);
                $item->background = $db->loadResult();

                $query = $db->getQuery(true);
                $query->select('group_id');
                $query->from('#__sdi_map_layergroup');
                $query->where('isdefault = 1');
                $query->where('map_id = ' . (int) $item->id);

                $db->setQuery($query);
                $item->default = $db->loadResult();

                //Tools activation
                $query = $db->getQuery(true);
                $query->select('tool_id, params, activated');
                $query->from('#__sdi_map_tool');
                $query->where('map_id = ' . $item->id);                

                $db->setQuery($query);
                $tools = $db->loadObjectList();
                foreach ($tools as $tool):
                    $n = 'tool' . $tool->tool_id;                    
                    ($tool->tool_id == 12) ? $item->$n = $tool->params : $item->$n = $tool->activated;
                endforeach;

                //Search Catalog
                $query = $db->getQuery(true);
                $query->select('params');
                $query->from('#__sdi_map_tool');
                $query->where('tool_id=17');
                $query->where('map_id = ' . (int) $item->id);

                $db->setQuery($query);
                $catalogsearch = $db->loadResult();
                if (!empty($catalogsearch)) {
                    $item->tool17 = "1";
                    $item->catalog_id = $catalogsearch;
                }

                //Scale line parameters
                $query = $db->getQuery(true);
                $query->select('tool_id, params');
                $query->from('#__sdi_map_tool');
                $query->where('tool_id IN (14,16,21)');
                $query->where('map_id = ' . (int) $item->id);
                $db->setQuery($query);
                $results = $db->loadObjectList();
                foreach ($results as $result){
                    if (!empty($result->params)) {           
                        $param = stripslashes($result->params);
                        if($result->tool_id == 21){                            
                            $item->level = stripslashes($param);
                        }else{
                            $params = json_decode(stripslashes($param));
                            foreach ($params as $key => $value) {
                                $item->$key = $value;
                            }
                        }
                    }
                }                    

                $item->services = array();
                $query = $db->getQuery(true);
                $query->select('ps.physicalservice_id');
                $query->from('#__sdi_map_physicalservice ps');
                $query->where('map_id = ' . $item->id);
                $db->setQuery($query);
                $physicalservices = $db->loadColumn();
                foreach ($physicalservices as $physicalservice) {
                    array_push($item->services, "physical_" . $physicalservice);
                }

                $query2 = $db->getQuery(true);
                $query2->select('vs.virtualservice_id');
                $query2->from('#__sdi_map_virtualservice vs');
                $query2->where('map_id = ' . $item->id);
                $db->setQuery($query2);
                $virtualservices = $db->loadColumn();
                foreach ($virtualservices as $virtualservice) {
                    array_push($item->services, "virtual_" . $virtualservice);
                }
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
        
        if ($this->checkMapType($data)){
            if (parent::save($data)) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->delete('#__sdi_map_tool');
                $query->where('map_id = ' . (int) $this->getItem()->get('id'));

                $db->setQuery($query);
                $db->query();

                $query = $db->getQuery(true);
                $query->delete('#__sdi_map_physicalservice');
                $query->where('map_id = ' . (int) $this->getItem()->get('id'));
                $db->setQuery($query);
                $db->query();

                $query = $db->getQuery(true);
                $query->delete('#__sdi_map_virtualservice');
                $query->where('map_id = ' . (int) $this->getItem()->get('id'));
                $db->setQuery($query);
                $db->query();

                //Tools
                foreach ($data as $key => $value) {
                    if (substr($key, 0, 4) == "tool") {
                        switch ($value) {
                            case '1':
                            case '0':
                                $tool = substr($key, 4);
                                $columns = array('map_id', 'tool_id', 'params', 'activated');

                                switch ($tool) {
                                    case '17':
                                        //Search catalog
                                        $params = $data['catalog_id'];
                                        $values = array($this->getItem()->get('id'), $query->quote($tool), $query->quote($params), $value);
                                        break;
                                    case '14':
                                        //Scale line params
                                        $scaleparamsparams = '{\"bottomInUnits\" :\"' . $data["bottomInUnits"] . '\",\"bottomOutUnits\" :\"' . $data["bottomOutUnits"] . '\",\"topInUnits\" :\"' . $data["topInUnits"] . '\",\"topOutUnits\" :\"' . $data["topOutUnits"] . '\"}';
                                        $values = array($this->getItem()->get('id'), 14, $query->quote($scaleparamsparams), $value);
                                        break;
                                    case '16':
                                        //Wfs locator
                                        $locatorparamsparams = '{\"urlwfslocator\" :\"' . $data["urlwfslocator"] . '\",\"fieldname\" :\"' . $data["fieldname"] . '\",\"featuretype\" :\"' . $data["featuretype"] . '\",\"featureprefix\" :\"' . $data["featureprefix"] . '\",\"geometryname\" :\"' . $data["geometryname"] . '\"}';
                                        $values = array($this->getItem()->get('id'), 16, $query->quote($locatorparamsparams), $value);
                                        break;
                                    case '21':
                                        //Indoor navigation
                                        $i = 1;
                                        $indoornavigation = '';
                                        $codes = $_POST['jform']["code"];
                                        if (isset($codes)) {
                                            foreach ($codes as $key => $v) {
                                                $indoornavigation = (strlen($indoornavigation) > 0 ) ? $indoornavigation . ',' : $indoornavigation;
                                                $indoornavigation .= '{\"code\": \"' . $v . '\", \"label\":\"' . $_POST['jform']["label"][$key] . '\",\"defaultlevel\":\"' . $_POST['jform']["defaultlevel"][$key] . '\"}';
                                            }
                                        }
                                        $indoornavigation = (strlen($indoornavigation) > 0 ) ? '[' . $indoornavigation . ']' : '';
                                        $values = array($this->getItem()->get('id'), 21, $query->quote($indoornavigation), $value);
                                        break;
                                    default :
                                        $values = array($this->getItem()->get('id'), $query->quote($tool), 'NULL', $value);
                                        break;
                                }

                                $query = $db->getQuery(true);
                                $query->insert('#__sdi_map_tool');
                                $query->columns($query->quoteName($columns));
                                $query->values(implode(',', $values));

                                $db->setQuery($query);
                                if (!$db->query()) {
                                    $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_TOOL_ERROR"));
                                    return false;
                                }
                                break;
                            case 'html':
                            case 'grid':
                                $tool = substr($key, 4);
                                $columns = array('map_id', 'tool_id', 'params', 'activated');
                                $values = array($this->getItem()->get('id'), $query->quote($tool), $query->quote($value), 1);
                                $query = $db->getQuery(true);
                                $query->insert('#__sdi_map_tool');
                                $query->columns($query->quoteName($columns));
                                $query->values(implode(',', $values));

                                $db->setQuery($query);
                                if (!$db->query()) {
                                    $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_TOOL_ERROR"));
                                    return false;
                                }
                                break;
                        }
                    }
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
                
                $query = $db->getQuery(true);
                $query
                        ->update($db->quoteName('#__sdi_map'))
                        ->set('default_backgroud_layer='.$data['default_backgroud_layer_new'])
                        ->where('id = ' . (int) $this->getItem()->get('id'));
                $db->setQuery($query);
                try {
                    $result = $db->execute();
                } catch (Exception $e) {
                    var_dump($e);
                    die();
                    $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELBACKGROUND_FAIL_GROUP_ERROR"));
                    return false;
                }


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
                            ->where('map_id= ' . (int) $this->getItem()->get('id'));
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
                                ->where('map_id= ' . (int) $this->getItem()->get('id'))
                                ->where('group_id =' . (int) $pk);
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
                            ->where('map_id= ' . (int) $this->getItem()->get('id'));
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
                        ->where('map_id= ' . (int) $this->getItem()->get('id'));
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
                        ->where('map_id= ' . (int) $this->getItem()->get('id'));
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
                            ->where('map_id= ' . (int) $this->getItem()->get('id'))
                            ->where('group_id= ' . (int) $background);
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
                            ->where('map_id= ' . (int) $this->getItem()->get('id'))
                            ->where('group_id= ' . (int) $default);
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
        }
        return false;
    }

    /*Verify if the map type is leaflet and context not associated to sh*/
    /**
     * Method to check if the map is used in the shop, the catalog or the preview.
     *
     * @return  boolean  True if successful, false if the map is used.
     *
     * @since   11.1
     */
    public function checkMapType($data) {
        $errmap=true;
        if ($data['type']=='leaflet'){
            if (JComponentHelper::getParams('com_easysdi_catalog')->get('catalogmap')==$data['id']){
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SETLEAFLETTYPECAT_FAIL_ERROR"));
                $errmap=false;
            }

            if (JComponentHelper::getParams('com_easysdi_shop')->get('ordermap')==$data['id']){
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SETLEAFLETTYPESHOP_FAIL_ERROR"));
                $errmap=false;
            }

            if (JComponentHelper::getParams('com_easysdi_shop')->get('previewmap')==$data['id']){
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_SETLEAFLETTYPEMAP_FAIL_ERROR"));
                $errmap=false;
            }
        }
        return $errmap;
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
            $query->where('map_id = ' . (int) $pk);

            $db->setQuery($query);
            if (!$db->query()) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_TOOL_ERROR"));
                return false;
            }

            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_layergroup');
            $query->where('map_id = ' . (int) $pk);

            $db->setQuery($query);
            if (!$db->query()) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_GROUP_ERROR"));
                return false;
            }

            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_physicalservice');
            $query->where('map_id = ' . (int) $pk);

            $db->setQuery($query);
            if (!$db->query()) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_SERVICE_ERROR"));
                return false;
            }

            $query = $db->getQuery(true);
            $query->delete('#__sdi_map_virtualservice');
            $query->where('map_id = ' . (int) $pk);

            $db->setQuery($query);
            if (!$db->query()) {
                $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELETE_FAIL_SERVICE_ERROR"));
                return false;
            }
        }

        return parent::delete($pks);
    }

}
