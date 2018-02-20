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
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/models/propertyvalue.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/property.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelproperty extends sdiModel
{
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
	public function getTable($type = 'Property', $prefix = 'Easysdi_shopTable', $config = array())
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
		$form = $this->loadForm('com_easysdi_shop.property', 'property', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.property.data', array());

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

		}

		return $item;
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
                                $query->from('#__sdi_property');
                                
				$db->setQuery($query);
				$max = $db->loadResult();
				$table->ordering = $max+1;
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
     * @since   12.2
     */
    public function save($data) {

        $new = false;
        if(empty($data['id']) || $data['id'] == 0){
            $new = true;
        }
        if (parent::save($data)) {
            if($new){
                $item = parent::getItem($data['id']);
                if($item->propertytype_id == 4 || $item->propertytype_id == 5){
                    //Create the default and unique property value for property text and text area
                    $propertyvalue = JModelLegacy::getInstance( 'propertyvalue', 'Easysdi_shopModel' );
                    $languages = JComponentHelper::getParams('com_easysdi_catalog')->get('languages', array());
                    array_unshift($languages, JComponentHelper::getParams('com_easysdi_catalog')->get('defaultlanguage'));
                    $text1 = array();
                    foreach ($languages as $language) :
                        $text1[$language] = '';
                    endforeach;
                    $data = array(
                        'name' => 'default',
                        'alias' => 'default'.$item->id,
                        'id' => 0,
                        'modified' => '',
                        'description' => 'default'.$item->id,
                        'access' => 1,
                        'state' => 1,
                        'property_id' => $item->id,
                        'guid' => '',
                        'text1' => $text1
                    );
                    $propertyvalue->save($data);
                }
            }

            return true;
        }

        return false;
    }

}