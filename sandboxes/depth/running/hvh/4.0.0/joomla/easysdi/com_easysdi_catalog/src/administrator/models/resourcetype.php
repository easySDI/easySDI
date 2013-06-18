<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_core model.
 */
class Easysdi_catalogModelresourcetype extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EASYSDI_CATALOG';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Resourcetype', $prefix = 'Easysdi_catalogTable', $config = array())
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
		$form = $this->loadForm('com_easysdi_catalog.resourcetype', 'resourcetype', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.resourcetype.data', array());

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
                    //Load translations
                    $translationtable = $this->getTable('Translation', 'Easysdi_catalogTable', array());
                    $rows = $translationtable->loadAll($item->guid);
                    if(is_array ($rows)){
                        $item->label = $rows['label'];
                    }
                    
                    // Get the access scope
                    $item->organisms 		= $this->getAccessScopeOrganism($item->id);
                    $item->users 		= $this->getAccessScopeUser($item->id);
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
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_resourcetype');
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
	public function save($data)
	{
          
            if(parent::save($data)){
                //Get the element guid
               $item = parent::getItem($data['id']);
               $data['guid'] = $item->guid;
              
                //Save translations
                $translationtable = $this->getTable('Translation', 'Easysdi_catalogTable', array());
                if(!$translationtable->saveAll($data)){
                    $this->setError($translationtable->getError());
                    return false;
                }
                
                //Access Scope
                if (!$this->saveAccessScope($data)) {
                        $this->setError('Failed to save access scope.');
                        return false;
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
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
            $item = parent::getItem($pks[0]);
            $guid = $item->guid;
           
            if(parent::delete($pks)){
                //Delete translation
                $translationtable = $this->getTable('Translation', 'Easysdi_catalogTable', array());
                if(!$translationtable->deleteAll($guid)){
                    $this->setError($translationtable->getError());
                    return false;
                }
                
                //Delete accessscope
                $db = JFactory::getDbo();
		$db->setQuery('DELETE FROM #__sdi_resourcetype_organism WHERE resourcetype_id = '.$pks[0]);
		$db->query();
		$db->setQuery('DELETE FROM #__sdi_resourcetype_user WHERE resourcetype_id = '.$pks[0]);
		$db->query();
                
                return true;
            }
            return false;
        }


        /**
	 * Method to save the organisms and users allowed by the access scope
	 *
	 * @param array 	$data	data posted from the form
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.3.0
	 */
	public function saveAccessScope ($data)
	{
		//Delete previously saved access
		$db = JFactory::getDbo();
		$db->setQuery('DELETE FROM #__sdi_resourcetype_organism WHERE resourcetype_id = '.$data['id']);
		$db->query();
		$db->setQuery('DELETE FROM #__sdi_resourcetype_user WHERE resourcetype_id = '.$data['id']);
		$db->query();
	
		$pks = $data['organisms'];
		foreach ($pks as $pk)
		{
			try {
				$db->setQuery(
						'INSERT INTO #__sdi_resourcetype_organism (resourcetype_id, organism_id) ' .
						' VALUES ('.$data['id'].','.$pk.')'
				);
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		
		$pks = $data['users'];
		foreach ($pks as $pk)
		{
			try {
				$db->setQuery(
						'INSERT INTO #__sdi_resourcetype_user (resourcetype_id, user_id) ' .
						' VALUES ('.$data['id'].','.$pk.')'
				);
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
        
        /**
	 * Method to get the organisms authorized to access this resourcetype
	 *
	 * @param int		$id		primary key of the current resourcetype to get.
	 *
	 * @return boolean 	Object list on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function getAccessScopeOrganism  ( $id=null)
	{
		if(!isset($id))
			return null;
	
		try {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('p.organism_id as id');
			$query->from('#__sdi_resourcetype_organism p');
			$query->where('p.resourcetype_id = ' . (int) $id);
			$db->setQuery($query);
	
			$scope = $db->loadColumn();
			return $scope;
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}
	
	/**
	 * Method to get the users authorized to access this resourcetype
	 *
	 * @param int		$id		primary key of the current resourcetype to get.
	 *
	 * @return boolean 	Object list on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function getAccessScopeUser  ( $id=null)
	{
		if(!isset($id))
			return null;
	
		try {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('p.user_id as id');
			$query->from('#__sdi_resourcetype_user p');
			$query->where('p.resourcetype_id = ' . (int) $id);
			$db->setQuery($query);
	
			$scope = $db->loadColumn();
			return $scope;
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}
}