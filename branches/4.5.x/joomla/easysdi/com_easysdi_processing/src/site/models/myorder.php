<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/models/order.php';

/**
 * Methods supporting a list of Easysdi_processing records.
 */
class Easysdi_processingModelmyorder extends Easysdi_processingModelorder {

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
        JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms');
        JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');
        // Get the form.
        $form = $this->loadForm('com_easysdi_processing.order', 'order', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }
    
     /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();

        /*if ($this->_item === null) {
            $this->_item = false;*/

            if (empty($id)) {
                $id = $this->getState('myorder.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }

            //Get processing_id from GET
            $jinput = JFactory::getApplication()->input;
            if (!isset($this->_item->processing_id)) {
                $this->_item->processing_id = $app->getUserState('processing.id');
            }else{
                JFactory::getApplication()->setUserState('com_easysdi_processing.edit.myorder.processing.id', $this->_item->processing_id);
            }  
            //$user = sdiFactory::getSdiUser();
            //$this->_item->user_id=$user->id;
        //}

        return $this->_item;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = JFactory::getApplication()->getUserState('com_easysdi_processing.edit.myorder.data', array());

        if (empty($data)) {
            $data = $this->getData();
        }

        return $data;
    }

   
    
}
