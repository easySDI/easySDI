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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';


/**
 * Easysdi_shop model.
 */
class Easysdi_processingModelprocessing extends sdiModel
{
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
	public function getTable($type = 'Processing', $prefix = 'Easysdi_processingTable', $config = array())
	{
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
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_easysdi_processing.processing', 'processing', array('control' => 'jform', 'load_data' => $loadData));
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
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_easysdi_processing.edit.processing.data', array());

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
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

                    //Do any procesing on fields here if needed
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query->select('sdi_user_id');
                    $query->from('#__sdi_processing_obs');
                    $query->where('processing_id = ' . (int) $item->id);

                    $db->setQuery($query);
                    $item->observers=$db->loadColumn();
		}

		return $item;
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
                
        // Trigger the onEasysdiUserBeforeDeleteRoleAttribution event.
        JPluginHelper::importPlugin('user');
        $dispatcher = JEventDispatcher::getInstance();      
        
        
        
        if (parent::save($data)) {
            $item = parent::getItem($data['id']);
            $data['id'] = $item->id;
            
            //Delete existing oberservers for this processing
            $this->deleteObs($data['id']);

            //Insert new observers 
            isset($data['observers']) ? $this->saveObs($data['id'],$data['observers']) : $this->deleteObs ($data['id']);
           
            return true;
        }
        return false;
    }
    
    function saveObs($processing_id, $observers) {
            if(!is_array($observers))
                $observers = array($observers);

            foreach($observers as $observer){
                try {
                    $db = JFactory::getDbo();
                    $columns = array('processing_id', 'sdi_user_id');
                    $values = array($processing_id, $observer);
                    $query = $db->getQuery(true);
                    $query->insert('#__sdi_processing_obs');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));

                    $db->setQuery($query);
                    $db->execute();
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }
            }
        
        return true;
    }

    function deleteObs($processing_id) {
        if ($processing_id > 0){
            $db = JFactory::getDbo();
            
            $query = $db->getQuery(true)
                ->delete('#__sdi_processing_obs')
                ->where('processing_id='.(int)$processing_id);
            
            $db->setQuery($query);
            $db->execute();
        }
    }

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select('MAX(ordering)');
                                $query->from('#__sdi_processing');
                                
				$db->setQuery($query);
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
                
                if (empty($table->alias)) {
                    $table->alias = $table->name;
                }
	}

}