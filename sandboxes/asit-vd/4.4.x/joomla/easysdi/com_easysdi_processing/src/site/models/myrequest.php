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
class Easysdi_processingModelmyrequest extends Easysdi_processingModelorder {

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_processing');
        
        $id = JFactory::getApplication()->getUserState('com_easysdi_processing.edit.myrequest.id');
        
        $this->setState('myrequest.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
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
        JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
        // Get the form.
        $form = $this->loadForm('com_easysdi_processing.myrequest', 'myrequest', array('control' => 'jform', 'load_data' => $loadData));
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

        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('myrequest.id');
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

            //Get resourcetype from GET
            $jinput = JFactory::getApplication()->input;
            if (!isset($this->_item->processing_id)) {
                $this->_item->processing_id = $app->getUserState('processing.id');
            }else{
                JFactory::getApplication()->setUserState('com_easysdi_processing.edit.myrequest.processing.id', $this->_item->processing_id);
            }  
            //$user = sdiFactory::getSdiUser();
            //$this->_item->user_id=$user->id;
        }

        return $this->_item;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = JFactory::getApplication()->getUserState('com_easysdi_processing.edit.myrequest.data', array());

        if (empty($data)) {
            $data = $this->getData();
        }

        return $data;
    }

   
    
}
