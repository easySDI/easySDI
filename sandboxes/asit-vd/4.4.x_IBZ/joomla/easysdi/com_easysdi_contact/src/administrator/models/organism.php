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
class Easysdi_contactModelorganism extends JModelAdmin {

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
    public function getTable($type = 'Organism', $prefix = 'Easysdi_contactTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_contact.organism', 'organism', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_contact.edit.organism.data', array());

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
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                        ->select($db->quoteName('oc.category_id'))
                        ->from($db->quoteName('#__sdi_organism_category').' as oc')
                        ->where('oc.organism_id='.(int)$item->id);
            $db->setQuery($query);
            $item->categories = $db->loadColumn();
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
                $query->from('#__sdi_organism');
                
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

        if (!empty($data['username'])) {
            if (!empty($data['clearpassword'])) {
                if ($data['clearpassword'] == $data['confirmpassword']) {
                    // Encrypte password
                    $salt = JUserHelper::genRandomPassword(32);
                    $crypt = JUserHelper::getCryptedPassword($data['clearpassword'], $salt);
                    $cryptPassword = $crypt . ':' . $salt;
                    $data['password'] = $cryptPassword;
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CONTACT_FORM_ERROR_COMPAREPASSWORD'), 'error');
                    JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_contact&view=organism&layout=edit&id=' . $data['id'], false));
                    return false;
                }
            } else {
                if (empty($data['password'])) {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CONTACT_FORM_ERROR_EMPTYPASSWORD'), 'error');
                    JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_contact&view=organism&layout=edit&id=' . $data['id'], false));
                    return false;
                }
            }
        }  else {
            $data['password'] = "";
        }

        if (parent::save($data)) {
            $db = JFactory::getDbo();
            
            //Get the last inserted id if new object
            if($data['id'] == 0) $data['id'] = (int) $this->getState($this->getName() . '.id');
            
            //Manage organism's categories
            $query = $db->getQuery(true)
                        ->delete($db->quoteName('#__sdi_organism_category'))
                        ->where('organism_id = '. $data['id']);
            $db->setQuery($query);
            $delete = $db->execute();
                
            if($data['categories'] && is_array($data['categories'])){
                $query = $db->getQuery(true)
                            ->insert($db->quoteName('#__sdi_organism_category'))
                            ->columns($db->quoteName(array('organism_id', 'category_id')));
                foreach($data['categories'] as $category)
                    $query->values(implode (',', array($data['id'], $category)));
                $db->setQuery($query);
                $insert = $db->execute();
            }
            
            //Instantiate an address JTable
            $addresstable = & JTable::getInstance('address', 'Easysdi_contactTable');

            //Call the overloaded save function to store the input data
            //$data['id'] 			= $this->getItem()->get('id');
            $data['organism_id'] = $this->getItem()->get('id');
            $data['user_id'] = null;


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

}
