<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_shop model.
 */
class Easysdi_processingModelorder extends JModelAdmin {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_EASYSDI_PROCESSING';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Order', $prefix = 'Easysdi_processingTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_processing.order', 'order', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_processing.edit.order.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    protected static $json_fields = array('parameters','plugins','info');

    protected static function loadJson(&$item) {
        foreach (self::$json_fields as $field) {
            $t=$item->$field;
            if (isset($t)) {
                $n_field=$field.'_obj';
                $item->$n_field=json_decode($t);
            }
        }
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
            $query = $db->getQuery(true);
            $query
                ->select('p.name AS processing, p.parameters AS processing_parameters, p.auto AS processing_auto, p.command AS processing_command, p.contact_id AS processing_contact_id, p.map_id AS processing_map_id')
                ->from(' #__sdi_processing AS p ')
                ->where('p.id = ' . (int) $item->processing_id);
            $db->setQuery($query);
            $result = $db->loadObject();

            $item->processing_label = $result->processing;
            $item->processing_parameters = $result->processing_parameters;
            $item->processing_command = $result->processing_command;
            $item->processing_auto = $result->processing_auto;
            $item->processing_contact_id= $result->processing_contact_id;
            $item->processing_map_id=$result->processing_map_id;
            
            $query = $db->getQuery(true);
            $query  ->select($db->quoteName('users2.name', 'user'))
                    ->from(' #__sdi_user AS sdi_user ')
                    ->join('LEFT', '#__users AS users2 ON users2.id=sdi_user.user_id')
                     ->where('sdi_user.id = ' . (int) $result->processing_contact_id);
           $db->setQuery($query);
           $result = $db->loadObject();
           
           $item->processing_contact_label= $result->user;

            $query = $db->getQuery(true);
            $query  ->select($db->quoteName('users.name', 'user'))
                    ->from(' #__users AS users ')
                     ->where('users.id = ' . (int) $item->created_by);
           $db->setQuery($query);
           $result = $db->loadObject();

           $item->user_label = $result->user;

           self::loadJson($item);

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
                $query->from('FROM #__sdi_processing_order');

                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

}
