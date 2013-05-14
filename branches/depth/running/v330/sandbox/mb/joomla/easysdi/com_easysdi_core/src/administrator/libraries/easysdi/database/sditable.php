<?php
/**
 \* @version     3.3.0
* @package     com_easysdi_core
* @copyright   Copyright (C) 2012. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

defined('JPATH_PLATFORM') or die;

require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/helpers/easysdi_core.php';

/**
 * Abstract sdiTable class
 *
 * Parent class to all EasySDI tables.
 *
  * @package     com_easysdi_user
 * @since       EasySDI 3.0.0
 */
abstract class sdiTable extends JTable
{

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param    mixed    An optional array of primary key values to update.  If not
	 *                    set the instance property value is used.
	 * @param    integer The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param    integer The user id of the user performing the operation.
	 * @return    boolean    True on success.
	 * @since    1.0.4
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
	
		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;
	
		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}
	
		// Build the WHERE clause for the primary keys.
		$where = $k.'='.implode(' OR '.$k.'=', $pks);
	
		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
			$checkin = '';
		}
		else {
			$checkin = ' AND (checked_out = 0 OR checked_out = '.(int) $userId.')';
		}
	
		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
				'UPDATE `'.$this->_tbl.'`' .
				' SET `state` = '.(int) $state .
				' WHERE ('.$where.')' .
				$checkin
		);
		$this->_db->query();
	
		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	
		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach($pks as $pk)
			{
				$this->checkin($pk);
			}
		}
	
		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->state = $state;
		}
	
		$this->setError('');
		return true;
	}
	
	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see		JTable:bind
	 * @since	1.5
	 */
	public function bind($array, $ignore = '')
	{
		if(isset($array['created_by']) || $array['created_by'] == 0){
			$array['created_by'] = JFactory::getUser()->id;
		}
		
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}
		
		if (isset($array['metadata']) && is_array($array['metadata'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string)$registry;
		}
		
	
		//Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules'])) {
			$this->setRules($array['rules']);
		}
	
		return parent::bind($array, $ignore);
	}
	
	/**
	 * This function convert an array of JAccessRule objects into an rules array.
	 * @param type $jaccessrules an arrao of JAccessRule objects.
	 */
	protected function JAccessRulestoArray($jaccessrules){
		$rules = array();
		foreach($jaccessrules as $action => $jaccess){
			$actions = array();
			foreach($jaccess->getData() as $group => $allow){
				$actions[$group] = ((bool)$allow);
			}
			$rules[$action] = $actions;
		}
		return $rules;
	}
	
	/**
	 * Overriden JTable::store to set modified data and user id.
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->id) {
			// Existing item
			$this->modified		= $date->toSql();
			$this->modified_by	= $user->get('id');
		} else {
			$this->created = $date->toSql();
			$this->created_by = $user->get('id');
		}
		if(empty ($this->guid)){
			$this->guid = Easysdi_coreHelper::uuid();
		}
		if(!empty ($this->alias))
		{
				$this->alias 	= preg_replace('/\s+/', '-', $this->alias);
				$this->alias 	= str_replace( array('Ã ','Ã¡','Ã¢','Ã£','Ã¤', 'Ã§', 'Ã¨','Ã©','Ãª','Ã«', 'Ã¬','Ã­','Ã®','Ã¯', 'Ã±', 'Ã²','Ã³','Ã´','Ãµ','Ã¶', 'Ã¹','Ãº','Ã»','Ã¼', 'Ã½','Ã¿', 'Ã€','Ã�','Ã‚','Ãƒ','Ã„', 'Ã‡', 'Ãˆ','Ã‰','ÃŠ','Ã‹', 'ÃŒ','Ã�','ÃŽ','Ã�', 'Ã‘', 'Ã’','Ã“','Ã”','Ã•','Ã–', 'Ã™','Ãš','Ã›','Ãœ', 'Ã�'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $this->alias);
				$this->alias	= str_replace("'", "_",$this->alias);
				$this->alias 	= strtolower($this->alias);
				$this->alias 	= $this::getUniqueAlias($this->alias);
		}

		return parent::store($updateNulls);
	}
	
	
	private function getUniqueAlias ($alias)
	{
		$query = $this->_db->getQuery(true);
		$query->select('count(*)');
		$query->from('`'.$this->_tbl.'`');
		$query->where('alias = "'.$alias.'"');
		if($this->id)
			$query->where('id <> '.$this->id);
		$this->_db->setQuery($query);
		
		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return "";
		}
		
		if( $this->_db->loadResult() == 1)
		{
			//alias is already used
			$pos = strrpos ($alias ,"_");	
			if($pos === false)
			{
				return $this::getUniqueAlias($alias."_1");
			}
			else
			{
				//Increment alias
				if(is_numeric(substr ($alias, $pos+1)))
				{
					$i = (int) substr ($alias, $pos+1);
					$i ++;
					$alias = substr ($alias, 0, $pos);
					return $this::getUniqueAlias($alias."_".$i);
				}
				else
				{
					return $this::getUniqueAlias($alias."_1");
				}
				
			}
		}
		else 
			return $alias;
	}
	
	/**
	 * Overloaded check function
	 */
	public function check() {
		//If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0) {
			$this->ordering = self::getNextOrder();
		}
	
		return parent::check();
	}
	
	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
	
		// This is a article under a category.
		if ($this->catid)
		{
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true);
			$query->select($this->_db->quoteName('asset_id'));
			$query->from($this->_db->quoteName('#__categories'));
			$query->where($this->_db->quoteName('id') . ' = ' . (int) $this->catid);
	
			
			// Get the asset id from the database.
			$this->_db->setQuery($query);
			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}
	
		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
}
