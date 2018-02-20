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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelperimeter extends sdiModel {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_EASYSDI_SHOP';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Perimeter', $prefix = 'Easysdi_shopTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_shop.perimeter', 'perimeter', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.perimeter.data', array());

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
           ($item->wfsservicetype_id == 1) ? $item->wfsservice_id = 'physical_'.$item->wfsservice_id : $item->wfsservice_id = 'virtual_'.$item->wfsservice_id;
//           ($item->wmsservicetype_id == 1) ? $item->wmsservice_id = 'physical_'.$item->wmsservice_id : $item->wmsservice_id = 'virtual_'.$item->wmsservice_id;
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
                $query->from('#__sdi_perimeter');
                
                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
//        $poswms = strstr($_REQUEST['jform']['wmsservice_id'], 'physical_');
//        $table->wmsservice_id = substr($_REQUEST['jform']['wmsservice_id'], strrpos($_REQUEST['jform']['wmsservice_id'], '_') + 1);
//        if ($poswms) : $table->wmsservicetype_id = 1; else :$table->wmsservicetype_id = 2; endif;
        
        $poswfs = strstr($_REQUEST['jform']['wfsservice_id'], 'physical_');
        $table->wfsservice_id = substr($_REQUEST['jform']['wfsservice_id'], strrpos($_REQUEST['jform']['wfsservice_id'], '_') + 1);
        if ($poswfs) : $table->wfsservicetype_id = 1; else :$table->wfsservicetype_id = 2; endif;
        
    }
}