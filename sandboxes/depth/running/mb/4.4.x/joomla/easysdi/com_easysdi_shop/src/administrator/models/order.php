<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelorder extends JModelAdmin {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_EASYSDI_SHOP';
    
    public $item;

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Order', $prefix = 'Easysdi_shopTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_shop.order', 'order', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.order.data', array());

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
    public function getItem($id = null) {
        if ($this->item === null) {
            $this->item = false;

            if (empty($id)) {
                $id = $this->getState('order.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->item = JArrayHelper::toObject($properties, 'JObject');

                //Get constante value (to display)
                $this->item->orderstate = constant('Easysdi_shopTableorder::orderstate_' . $this->item->orderstate_id);
                $this->item->ordertype = constant('Easysdi_shopTableorder::ordertype_' . $this->item->ordertype_id);
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
            
            //if a validator is set, loat it
            if(isset($this->item->validated_by)){
                $validator = new sdiUser($this->item->validated_by);
                $this->item->validator = $validator->name;
            }

            $basket = new sdiBasket();
            $basket->loadOrder($id);

            $this->item->basket = $basket;
        }

        return $this->item;
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
                $query->from('FROM #__sdi_order');

                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

}
