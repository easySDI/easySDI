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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';

/**
 * Easysdi_map model.
 */
class Easysdi_mapModellayer extends sdiModel {

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
    public function getTable($type = 'Layer', $prefix = 'Easysdi_mapTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_map.layer', 'layer', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_map.edit.layer.data', array());

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
            if (isset($item->service_id))
                $item->service_id = $item->servicetype . '_' . $item->service_id;
            if (isset($item->layername))
                $item->onloadlayername = $item->layername;

            $groupTable = JTable::getInstance('group', 'easysdi_mapTable');
            $item->groups = $groupTable->loadItemsByLayer($item->id);
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

        $jform = JRequest::getVar('jform');

        if (!isset($jform['isdefaultvisible'])) { // see if the checkbox has been submitted
            $table->isdefaultvisible = 0; // if it has not been submitted, mark the field unchecked
        }
        if (!isset($jform['istiled'])) { // see if the checkbox has been submitted
            $table->istiled = 0; // if it has not been submitted, mark the field unchecked
        }
        if (!isset($jform['asOL'])) { // see if the checkbox has been submitted
            $table->asOL = 0; // if it has not been submitted, mark the field unchecked
            $table->asOLoptions = "";
            $table->asOLstyle = "";
            $table->asOLmatrixset = "";
        }
        if (!isset($jform['isindoor'])) { // see if the checkbox has been submitted
            $table->isindoor = 0;
            $table->levelfield = ""; // if it has not been submitted, mark the field unchecked
        }
        $service_id = $jform['service_id'];
        $pos = strstr($service_id, 'physical_');
        if ($pos) {
            $table->service_id = substr($service_id, strrpos($service_id, '_') + 1);
            $table->servicetype = 'physical';
        } else {
            $table->service_id = substr($service_id, strrpos($service_id, '_') + 1);
            $table->servicetype = 'virtual';
        }

        //Layer id is set to default value '0' in case of creation.
        //So this section of code is never executed.
        //Ordering is set in sdiTable->check() function.
        //However, We keep this section in case of default id was not set to '0' anymore (changes in form xml) 
        if (empty($table->id)) {
            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('MAX(ordering)');
                $query->from('#__sdi_maplayer');

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
     * Saves the manually set order of records.
     *
     * @param   array    $pks    An array of primary key ids.
     * @param   integer  $order  +1 or -1
     *
     * @return  mixed
     *
     * @since   12.2
     */
    public function saveorder($pks = null, $order = null) {
        if (empty($pks)) {
            return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
        }

        //Get if a filter on group was set.
        $app = JFactory::getApplication('administrator');
        $group = $app->getUserStateFromRequest('com_easysdi_map.layers.filter.group', 'filter_group', null, 'int');
        //No filter : standard saving process
        if (empty($group)) {
            parent::saveorder($pks, $order);
        }

        $table = $this->getTable();
        $conditions = array();

        //A filter on group is set : Update ordering inside this group
        foreach ($pks as $i => $pk) {
            $table->load((int) $pk);

            // Access checks.
            if (!$this->canEditState($table)) {
                // Prune items that you can't change.
                unset($pks[$i]);
                JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
            } else {
                $order = $i + 1;
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query
                        ->update($db->quoteName('#__sdi_layer_layergroup'))
                        ->set('ordering=' . $order)
                        ->where('layer_id= ' . (int) $pks[$i])
                        ->where('group_id= ' . (int) $group);
                $db->setQuery($query);
                try {
                    $result = $db->execute();
                } catch (Exception $e) {
                    $this->setError(JText::_("COM_EASYSDI_MAP_FORM_MAP_DELBACKGROUND_FAIL_GROUP_ERROR"));
                    return false;
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond) {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

}
