<?php
/**
 * @version     3.0.0
* @package     com_easysdi_service
* @copyright   Copyright (C) 2012. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_service model.
 */
class Easysdi_serviceModelvirtualservice extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EASYSDI_SERVICE';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'VirtualService', $prefix = 'Easysdi_serviceTable', $config = array())
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
		return null;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		return null;
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
		//Get Item by User Id
		if(isset($this->alias)){
			$table = $this->getTable();
				
			$return = $table->loadByAlias($this->alias);
		
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
				
			return $table;
		}else{//Get item by Id
			if ($item = parent::getItem($pk)) {
				//Do any procesing on fields here if needed
			}
		}
		
		return $item;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param	$alias 			string		Alias of the service.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	EasySDI 3.0.0
	 */
	public function getItemByServiceAlias($alias)
	{
		$this->alias = $alias;
		$item = $this->getItem(null);
	
		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_virtualservice');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
	
	/**
	 * Method to get the service compliance deducted from the agregation process and saved into database
	 *
	 * @param int		$id		primary key of the current service to get.
	 *
	 * @return boolean 	Object list on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function getServiceCompliance ( $id=null)
	{
		if(!isset($id))
			return null;
	
		try {
			$db = JFactory::getDbo();
			$db->setQuery(
					'SELECT sv.value as value, sc.id as id FROM #__sdi_service_servicecompliance ssc ' .
					' INNER JOIN #__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id '.
					' INNER JOIN #__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id'.
					' WHERE ssc.service_id ='.$id.
					' AND ssc.servicetype = "virtual"'
	
			);
			$compliance = $db->loadObjectList();
			return $compliance;
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}

}