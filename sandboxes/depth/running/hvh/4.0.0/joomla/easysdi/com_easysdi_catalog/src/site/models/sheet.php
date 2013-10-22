<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';

/**
 * Easysdi_catalog model.
 */
class Easysdi_catalogModelSheet extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_catalog');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.sheet.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_easysdi_catalog.edit.sheet.id', $id);
        }
        $this->setState('sheet.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('sheet.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('sheet.id');
            }

            //Load CSW metadata
            $metadata = new cswmetadata($id);
            $metadata->load();
            
            $jinput = JFactory::getApplication()->input; 
            $langtag = $jinput->get('lang', '', 'STRING');
            if(empty($langtag)):
                 //Current language
                $lang = JFactory::getLanguage();  
                $langtag = $lang->getTag();
            endif;
                       
            //Is the call from joomla
            $callfromjoomla = true;
            //Current catalog context
            $catalog = $jinput->get('catalog', '', 'STRING');
            /* Current type view. Possible value :
             * - result
             * - complete
             * - abstract
             * - diffusion
             */
            $type = $jinput->get('type', 'abstract', 'STRING');
                        
            //Build extended metadata
            $metadata->extend($catalog, $type, $callfromjoomla, $langtag);
            
            
            //Apply XSL transformation and complete with shop order fields
            if($callfromjoomla)
                $this->_item = $metadata->getShopExtenstion(). $metadata->applyXSL($catalog, $type);
            else
                $this->_item = $metadata->applyXSL($catalog, $type);
            
        }

        return $this->_item;
    }
   

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_easysdi_catalog.sheet', 'sheet', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = $this->getData();
        return $data;
    }
}