<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_contact model.
 */
class Easysdi_contactModeluser extends JModelAdmin {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_EASYSDI_CONTACT';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'User', $prefix = 'Easysdi_contactTable', $config = array()) {
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

            if (!empty($record->catid)) {
                return $user->authorise('core.delete', 'com_easysdi_contact.category.' . (int) $record->catid);
            }
            // Default to component settings if category not known.
            else {
                return parent::canDelete($record);
            }
        }
    }

    /**
     * Method to delete one or more records.
     *
     * @param   array  &$pks  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     *
     * @since   12.2
     */
    public function delete(&$pks) {
        $pks = (array) $pks;

        $result = true;
        // Iterate the items to delete each one.
        foreach ($pks as $i => $pk) {
            $sdiuser = sdiFactory::getSdiUser($pk);
            try {

                //If the user is the only resource manager of a resource, it can't be deleted
                $list = $this->getResourceManagedByMe($pk);
                if (count($list) > 0):
                    $errorMessage = sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ERROR_RESOURCES'), $sdiuser->name);
                    foreach ($list as $item):
                        $errorMessage .= '<br> - ' . $item->rname;
                    endforeach;
                    JFactory::getApplication()->enqueueMessage($errorMessage, 'error');
                    $result = false;
                endif;

                //If the user has order history, it can't be deleted
                if ($this->getOrdersCount($pk) > 0):
                    JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ERROR_ORDER'), $sdiuser->name), 'error');
                    $result = false;
                endif;

                //if the user has order processing history, it can't be deleted
                if ($this->getProcessOrdersCount($pk) > 0):
                    JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ERROR_PROCESSING'), $sdiuser->name), 'error');
                    $result = false;
                endif;
            } catch (Exception $ex) {
                JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_EXCEPTION'), $sdiuser->name), 'error');
                $result = false;
            }
        }

        if ($result):
            $userjoomlaaction = JFactory::getApplication()->input->post->get('userjoomlaaction', '', 'string');
            foreach ($pks as $i => $pk) {
                $sdiuser = sdiFactory::getSdiUser($pk);
                $username = $sdiuser->name;
                $juser = $sdiuser->juser;
                if (parent::delete($pk)):
                    switch ($userjoomlaaction) {
                    case "btn_keep":
                        JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_OK_KEEP_JUSER'), $username), 'message');
                        break;
                        case "btn_disable":
                            //Disable Joomla user
                            $juser->block = 1;
                            if ($juser->save(true)):
                                JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_OK_DISABLE_JUSER'), $username), 'message');
                            else:
                                JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ERROR_DISABLE_JUSER'), $username), 'warning');
                            endif;
                            break;
                        case "btn_delete":
                            //Delete Joomla user
                            if ($juser->delete()) :
                                JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_OK_DELETE_JUSER'), $username), 'message');
                            else:
                                JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_EASYSDI_CONTACT_USER_DELETE_ERROR_DELETE_JUSER'), $username), 'warning');
                            endif;
                            break;
                    }
                    return true;
                else:
                    return false;
                endif;
            }
            return false;
        else:
            return false;
        endif;
    }

    /**
     * Method to test whether a record can have its state edited.
     *
     * @param	object	$record	A record object.
     *
     * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
     * @since	1.6
     */
    protected function canEditState($record) {
        $user = JFactory::getUser();

        // Check against the category.
        if (!empty($record->catid)) {
            return $user->authorise('core.edit.state', 'com_easysdi_contact.category.' . (int) $record->catid);
        }
        // Default to component settings if category not known.
        else {
            return parent::canEditState($record);
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
        $form = $this->loadForm('com_easysdi_contact.user', 'user', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_contact.edit.user.data', array());

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
            $role = JTable::getInstance('role', 'Easysdi_contactTable');
            $item->organismsRM = $role->loadByUserID($item->id, 2);
            $item->organismsMR = $role->loadByUserID($item->id, 3);
            $item->organismsME = $role->loadByUserID($item->id, 4);
            $item->organismsDM = $role->loadByUserID($item->id, 5);
            $item->organismsVM = $role->loadByUserID($item->id, 6);
            $item->organismsER = $role->loadByUserID($item->id, 7);
            /* $item->organismsOE = $role->loadByUserID($item->id, 8); // role removed */
            $item->organismsPM = $role->loadByUserID($item->id, 9);
            $item->organismsTM = $role->loadByUserID($item->id, 10);
            $item->organismsManager = $role->loadByUserID($item->id, 11);
            $item->organismsMember = $role->loadByUserID($item->id, 1);
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

        if (empty($table->id)) {

            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('MAX(ordering)');
                $query->from('#__sdi_user');

                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
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
        $fieldRole = array(
            2 => 'organismsRM',
            3 => 'organismsMR',
            4 => 'organismsME',
            5 => 'organismsDM',
            6 => 'organismsVM',
            7 => 'organismsER',
            /* 8   => 'organismsOE', // role removed */
            9 => 'organismsPM',
            10 => 'organismsTM',
            11 => 'organismsManager'
        );

        // Trigger the onEasysdiUserBeforeDeleteRoleAttribution event.
        JPluginHelper::importPlugin('user');
        $dispatcher = JEventDispatcher::getInstance();

        $canContinue = $dispatcher->trigger('onEasysdiUserBeforeDeleteRoleAttribution', array(array(
                'user_id' => $data['id'],
                'organisms_ids' => isset($data['organismsRM']) ? $data['organismsRM'] : array()))
        );

        // Fire the onEasysdiUserBeforeDeleteRoleAttribution event.
        if ($canContinue[0] !== true) {
            $this->setError($canContinue[0]);
            return false;
        }

        if (parent::save($data)) {
            $item = parent::getItem($data['id']);
            $data['id'] = $item->id;

            //Delete existing role attribution for this user
            $role = JTable::getInstance('role', 'Easysdi_contactTable');
            $role->deleteByUserId($data['id']);

            //Insert new role attribution  
            foreach ($fieldRole as $role_id => $fieldName)
                isset($data[$fieldName]) ? $this->saveRoleAttribution($data[$fieldName], $role_id) : $this->deleteRoleAttribution($role_id);


            $array = array();
            $array['user_id'] = $this->getItem()->get('id');
            $array['role_id'] = 1;
            $array['organism_id'] = $data['organismsMember'];
            $role = JTable::getInstance('role', 'Easysdi_contactTable');
            $role->save($array);

            //Instantiate an address JTable
            $addresstable = & JTable::getInstance('address', 'Easysdi_contactTable');

            //Save addresses
            $data['user_id'] = $this->getItem()->get('id');
            $data['organism_id'] = null;
            if (!$addresstable->saveByType($data, 'contact')) {
                return false;
            }

            if (!$addresstable->saveByType($data, 'billing')) {
                return false;
            }

            if (!$addresstable->saveByType($data, 'delivry')) {
                return false;
            }
            return true;
        }
        return false;
    }

    function saveRoleAttribution($organisms, $role_id) {
        if (!is_array($organisms))
            $organisms = array($organisms);

        foreach ($organisms as $organism) {
            $role = JTable::getInstance('role', 'Easysdi_contactTable');
            $array = array(
                'user_id' => $this->getItem()->get('id'),
                'role_id' => $role_id,
                'organism_id' => $organism
            );
            $role->save($array);
        }

        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
                ->select('id')
                ->from('#__sdi_resource')
                ->where('organism_id IN (' . implode(',', $organisms) . ')');
        $db->setQuery($query);
        $resources = $db->loadColumn();

        $this->deleteRoleAttribution($role_id, $resources);
    }

    function deleteRoleAttribution($role_id, $resources = false) {
        if (sizeof($resources) > 0 || $resources === false) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                    ->delete('#__sdi_user_role_resource')
                    ->where('user_id=' . (int) $this->getItem()->get('id'))
                    ->where('role_id=' . (int) $role_id);
            if ($resources == true && sizeof($resources) > 0)
                $query->where('resource_id NOT IN (' . implode(',', $resources) . ')');

            $db->setQuery($query);
            $db->execute();
        }
    }

    private function getOrdersCount($user_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('count(id)')
                ->from('#__sdi_order')
                ->where('user_id = ' . (int) $user_id)
        ;
        $db->setQuery($query);
        return $db->loadResult();
    }

    private function getProcessOrdersCount($user_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('count(id)')
                ->from('#__sdi_processing_order')
                ->where('user_id = ' . (int) $user_id)
        ;
        $db->setQuery($query);
        return $db->loadResult();
    }

    private function getResourceManagedByMe($user_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('urr.*, r.name as rname')
                ->from('#__sdi_user_role_resource urr')
                ->innerJoin('#__sdi_resource r ON urr.resource_id = r.id ')
                ->where('urr.role_id = 2')
                ->where('urr.user_id = ' . (int) $user_id)
        ;
        $db->setQuery($query);
        $urrs = $db->loadObjectList();
        $result = array();
        foreach ($urrs as $urr):
            $query = $db->getQuery(true)
                    ->select('count(urr.id)')
                    ->from('#__sdi_user_role_resource urr')
                    ->where('urr.role_id = 2')
                    ->where('urr.resource_id = ' . (int) $urr->resource_id)
            ;
            $db->setQuery($query);
            $count = $db->loadResult();
            if ($count = 1):
                array_push($result, $urr);
            endif;
        endforeach;

        return $result;
    }

}
