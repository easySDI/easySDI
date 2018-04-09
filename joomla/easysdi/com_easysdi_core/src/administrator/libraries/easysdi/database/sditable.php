<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('JPATH_PLATFORM') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';

/**
 * Abstract sdiTable class
 *
 * Parent class to all EasySDI tables.
 *
 * @package     com_easysdi_core
 * @since       EasySDI 3.0.0
 */
abstract class sdiTable extends JTable {

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
    public function publish($pks = null, $state = 1, $userId = 0) {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        $query = $this->_db->getQuery(true);
        $query->update($this->_tbl);
        $query->set('state = '. (int)$state);
        
        
        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        $query->where($where);
        
        // Determine if there is checkin support for the table.
        if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time')) {
            $query->where('(checked_out = 0 OR checked_out = ' . (int) $userId . ')');
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery($query);
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin the rows.
            foreach ($pks as $pk) {
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
    public function bind($array, $ignore = '') {
        if (!isset($array['created_by']) || $array['created_by'] == 0) {
            $array['created_by'] = JFactory::getUser()->id;
        }
        
        if (!isset($array['created'])) {
            $array['created'] = JFactory::getDate()->toSql();
        }
        
        if (!isset($array['state'])) {
            $array['state'] = 1;
        }
        
        if (!isset($array['checked_out'])) {
            $array['checked_out'] = 0;
        }
        
        if (!isset($array['checked_out_time'])) {
            $array['checked_out_time'] = JFactory::getDate()->toSql();
        }
        
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
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
    protected function JAccessRulestoArray($jaccessrules) {
        $rules = array();
        foreach ($jaccessrules as $action => $jaccess) {
            $actions = array();
            foreach ($jaccess->getData() as $group => $allow) {
                $actions[$group] = ((bool) $allow);
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
    public function store($updateNulls = true) {
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        if ($this->id) {
            // Existing item
            $this->modified = $date->toSql();
            $this->modified_by = $user->get('id');
        } else {
            $this->created = $date->toSql();
            $this->created_by = $user->get('id');
        }
        if (empty($this->guid)) {
            $this->guid = Easysdi_coreHelper::uuid();
        }


        return $this->storeOverwrite($updateNulls);
    }

    /**
     * EASYSDI : this method is an overwritten version of JTable::store($updateNulls).
     * This method is overwritten because using it with $updateNulls = true
     * leads to a problem when coming to : $asset->store($updateNulls).
     * Asset can't be store with the param set to true (bug).
     * As we need to update null values often in EasySDI solution, this method 
     * was modify at only one line : if (!$asset->check() || !$asset->store(false)).
     * 
     * 
     * Method to store a row in the database from the JTable instance properties.
     * If a primary key value is set the row with that primary key value will be
     * updated with the instance property values.  If no primary key value is set
     * a new row will be inserted into the database with the properties from the
     * JTable instance.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTable/store
     * @since   11.1
     */
    public function storeOverwrite($updateNulls = false) {
        $k = $this->_tbl_key;
        $currentAssetId = null;
        if (!empty($this->asset_id)) {
            $currentAssetId = $this->asset_id;
        }

        if (0 == $this->$k) {
            $this->$k = null;
        }

        // The asset id field is managed privately by this class.
        if ($this->_trackAssets) {
            unset($this->asset_id);
        }

        // If a primary key exists update the object, otherwise insert it.
        if ($this->$k) {
            $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
        } else {
            $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
        }

        // If the table is not set to track assets return true.
        if (!$this->_trackAssets) {
            return true;
        }

        if ($this->_locked) {
            $this->_unlock();
        }

        /*
         * Asset Tracking
         */

        $parentId = $this->_getAssetParentId();
        $name = $this->_getAssetName();
        $title = $this->_getAssetTitle();

        $asset = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
        $asset->loadByName($name);

        // Re-inject the asset id.
        $this->asset_id = $asset->id;

        // Check for an error.
        $error = $asset->getError();
        if ($error) {
            $this->setError($error);
            return false;
        }

        // Specify how a new or moved node asset is inserted into the tree.
        if (empty($this->asset_id) || $asset->parent_id != $parentId) {
            $asset->setLocation($parentId, 'last-child');
        }

        // Prepare the asset to be stored.
        $asset->parent_id = $parentId;
        $asset->name = $name;
        $asset->title = $title;

        if ($this->_rules instanceof JAccessRules) {
            $asset->rules = (string) $this->_rules;
        }else{
            $asset->rules = '{}';
        }

        if (!$asset->check() || !$asset->store(false)) {
            $this->setError($asset->getError());
            return false;
        }

        // Create an asset_id or heal one that is corrupted.
        if (empty($this->asset_id) || ($currentAssetId != $this->asset_id && !empty($this->asset_id))) {
            // Update the asset_id field in this table.
            $this->asset_id = (int) $asset->id;

            $query = $this->_db->getQuery(true)
                    ->update($this->_db->quoteName($this->_tbl))
                    ->set('asset_id = ' . (int) $this->asset_id)
                    ->where($this->_db->quoteName($k) . ' = ' . (int) $this->$k);
            $this->_db->setQuery($query);

            $this->_db->execute();
        }

        return true;
    }

    private function getUniqueAlias($alias) {
        $query = $this->_db->getQuery(true);
        $query->select('count(*)');
        $query->from( $query->quoteName($this->_tbl));
        $query->where('alias = ' . $query->quote($alias) );
        if ($this->id)
            $query->where('id <> ' . (int)$this->id);
        $this->_db->setQuery($query);

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return "";
        }

        if ($this->_db->loadResult() > 0) {
            //alias is already used
            $pos = strrpos($alias, "_");
            if ($pos === false) {
                return $this::getUniqueAlias($alias . "_1");
            } else {
                //Increment alias
                if (is_numeric(substr($alias, $pos + 1))) {
                    $i = (int) substr($alias, $pos + 1);
                    $i++;
                    $alias = substr($alias, 0, $pos);
                    return $this::getUniqueAlias($alias . "_" . $i);
                } else {
                    return $this::getUniqueAlias($alias . "_1");
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
        //Alias can be used in SEF URL, check that this field is filled and url safe
        jimport('joomla.filter.output');
        $fields = $this->getFields();
        if (array_key_exists('alias', $fields)) {
            if (empty($this->alias)) {
                $this->alias = $this->name;
            }
            $this->alias = JApplication::stringURLSafe($this->alias);
            $this->alias = $this::getUniqueAlias($this->alias);
            
        }
        //If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0) {
            $this->ordering = $this->getNextOrder();
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
    protected function _getAssetParentId(JTable $table = null, $id = null) {
        // Initialise variables.
        $assetId = null;

        // This is a article under a category.
        if ($this->catid) {
            // Build the query to get the asset id for the parent category.
            $query = $this->_db->getQuery(true);
            $query->select($this->_db->quoteName('asset_id'));
            $query->from($this->_db->quoteName('#__categories'));
            $query->where($this->_db->quoteName('id') . ' = ' . (int) $this->catid);


            // Get the asset id from the database.
            $this->_db->setQuery($query);
            if ($result = $this->_db->loadResult()) {
                $assetId = (int) $result;
            }
        }

        // Return the asset id.
        if ($assetId) {
            return $assetId;
        } else {
            return parent::_getAssetParentId($table, $id);
        }
    }

}
