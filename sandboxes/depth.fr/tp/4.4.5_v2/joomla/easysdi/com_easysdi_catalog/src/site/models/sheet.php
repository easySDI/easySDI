<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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
        $id = JFactory::getApplication()->input->get('guid');
        if(empty($id)):
            $item = JFactory::getApplication()->input->get('id');
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('guid')
                    ->from('#__sdi_metadata')
                    ->where('id = '.(int) $item ) ;
            $db->setQuery($query);
            $id = $db->loadResult();
        endif;
        JFactory::getApplication()->setUserState('com_easysdi_catalog.edit.sheet.id', $id);

        $this->setState('sheet.id', $id);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if(!empty($_GET['guid'])){
            $session = JFactory::getSession();
            $this->_item = $session->get($_GET['guid']);
        }
        
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('sheet.id');
            }

            //Load CSW metadata
            $metadata = new cswmetadata($id);
            $metadata->load('complete');

            $jinput = JFactory::getApplication()->input;
            $langtag = $jinput->get('lang', '', 'STRING');
            if (empty($langtag)):
                //Current language
                $lang = JFactory::getLanguage();
                $langtag = $lang->getTag();
            endif;

            //Is the call from joomla
            $callfromjoomla = $jinput->get('callfromjoomla', true, 'BOOLEAN');
            
            //Current catalog context
            $catalog = $jinput->get('catalog', '', 'STRING');
            /* Current type view. Possible value :
             * - result (value used by the code to display the search results in a catalog view)
             * - any other values wanted by the administrator and used in the XSL files...
             */
            $type = $jinput->get('type', '', 'STRING');
            /* Current preview. Possible value :
             * - editor
             * - public
             * - map
             * A preview corresponds to an association of a catalog and a type :
             * preview = catalog + type
             * If a preview is provided, its value is used to load the XSL file.
             * If no preview is provided, catalog and type values are used to load the XSL file
             */
            $preview = $jinput->get('preview', '', 'STRING');

            //Build extended metadata
            $metadata->extend($catalog, $type, $preview, $callfromjoomla, $langtag);

            //Apply XSL transformation 
            $this->_item = $metadata->applyXSL(array ('catalog' => $catalog, 'type' => $type, 'preview' => $preview));
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