<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_map model.
 */
class Easysdi_mapModelcontext extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EASYSDI_MAP';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Context', $prefix = 'Easysdi_mapTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
	
		if (!empty($record->id)) {
			if ($record->state != -2) {
				return ;
			}
			return parent::canDelete($record);
		}
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
		$form = $this->loadForm('com_easysdi_map.context', 'context', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_map.edit.context.data', array());

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

			$item->url = JURI::root().'index.php?option=com_easysdi_map&tmpl=component&view=context&id='.$item->id;
			
			$db = JFactory::getDbo();
			$db->setQuery('SELECT group_id FROM #__sdi_map_context_group WHERE isbackground = 0  AND context_id = '.$item->id);
			$item->groups = $db->loadResultArray();
			
			$db->setQuery('SELECT group_id FROM #__sdi_map_context_group WHERE isbackground = 1 AND context_id = '.$item->id);
			$item->background = $db->loadResult();
			
			$db->setQuery('SELECT group_id FROM #__sdi_map_context_group WHERE isdefault = 1 AND context_id = '.$item->id);
			$item->default = $db->loadResult();
			
			$db->setQuery('SELECT tool_id FROM #__sdi_map_context_tool WHERE context_id = '.$item->id);
			$item->tools = $db->loadResultArray();
			
			$db->setQuery('SELECT CONCAT ("physical_",physicalservice_id) FROM #__sdi_map_context_physicalservice WHERE context_id = '.$item->id.' 
							UNION SELECT CONCAT ("virtual_",virtualservice_id) FROM #__sdi_map_context_virtualservice WHERE context_id = '.$item->id);
			$item->services = $db->loadResultArray();
		}

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
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_map_context');
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
	 * @since   11.1
	 */
	public function save($data)
	{
		if(parent::save($data))
		{
			$db = JFactory::getDbo();
			$db->setQuery('DELETE FROM #__sdi_map_context_tool WHERE context_id = '.$this->getItem()->get('id'));
			$db->query();
			$db->setQuery('DELETE FROM #__sdi_map_context_group WHERE context_id = '.$this->getItem()->get('id'));
			$db->query();
			$db->setQuery('DELETE FROM #__sdi_map_context_physicalservice WHERE context_id = '.$this->getItem()->get('id'));
			$db->query();
			$db->setQuery('DELETE FROM #__sdi_map_context_virtualservice WHERE context_id = '.$this->getItem()->get('id'));
			$db->query();
			//Tools
			$tools = $data['tools'];
			foreach ($tools as $tool)
			{
				$db->setQuery('INSERT INTO #__sdi_map_context_tool (context_id, tool_id) VALUES ('.$this->getItem()->get('id').', '.$tool.')');
				if(!$db->query())
				{
					$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_CONTEXT_SAVE_FAIL_TOOL_ERROR" ) );
					return false;
				}
					
			}
			//Service
			$services = $data['services'];
			foreach ($services as $service)
			{
				$pos 			= strstr ($service, 'physical_');
				if($pos){
					$service_id 	= substr ($service, strrpos ($service, '_')+1);
					$db->setQuery('INSERT INTO #__sdi_map_context_physicalservice (context_id, physicalservice_id) VALUES ('.$this->getItem()->get('id').', '.$service_id.')');
				}
				else {
					$service_id 		= substr ($service, strrpos ($service, '_')+1);
					$db->setQuery('INSERT INTO #__sdi_map_context_virtualservice (context_id, virtualservice_id) VALUES ('.$this->getItem()->get('id').', '.$service_id.')');
					
				}
				if(!$db->query())
				{
					$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_CONTEXT_SAVE_FAIL_SERVICE_ERROR" ) );
					return false;
				}
					
			}
			//Groups
			
			$background = $data['background'];
			if(!empty($background))
			{
				$db->setQuery('INSERT INTO #__sdi_map_context_group (context_id, group_id, isbackground, isdefault) VALUES ('.$this->getItem()->get('id').', '.$background.',1 ,0)');
				if(!$db->query())
				{
					$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_CONTEXT_SAVE_FAIL_GROUP_ERROR" ) );
					return false;
				}
			}
			
			$default = $data['default'];
			$groups = $data['groups'];
			foreach ($groups as $group)
			{
				if($group == $background)
					continue;
				
				$isdefault = 0;
				if($group == $default)
					$isdefault = 1;
				$db->setQuery('INSERT INTO #__sdi_map_context_group (context_id, group_id, isbackground, isdefault) VALUES ('.$this->getItem()->get('id').', '.$group.',0 ,'.$isdefault.')');
				if(!$db->query())
				{
					$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_CONTEXT_SAVE_FAIL_GROUP_ERROR" ) );
					return false;
				}
					
			}
			
			return true;
		}
		return false;
	}
	
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   11.1
	 */
	public function delete(&$pks)
	{
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
	
			$db = JFactory::getDbo();
			$db->setQuery('DELETE FROM #__sdi_map_context_tool WHERE context_id = '.$pk);
			if(!$db->query())
			{
				$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_CONTEXT_DELETE_FAIL_TOOL_ERROR" ) );
				return false;
			}
			$db->setQuery('DELETE FROM #__sdi_map_context_group WHERE context_id = '.$pk);
			if(!$db->query())
			{
				$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_CONTEXT_DELETE_FAIL_GROUP_ERROR" ) );
				return false;
			}
			$db->setQuery('DELETE FROM #__sdi_map_context_physicalservice WHERE context_id = '.$pk);
			if(!$db->query())
			{
				$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_CONTEXT_DELETE_FAIL_SERVICE_ERROR" ) );
				return false;
			}
			$db->setQuery('DELETE FROM #__sdi_map_context_virtualservice WHERE context_id = '.$pk);
			if(!$db->query())
			{
				$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_CONTEXT_DELETE_FAIL_SERVICE_ERROR" ) );
				return false;
			}
		}
		
		return parent::delete($pks);
	}

}