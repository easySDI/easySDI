<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables/translation.php';

/**
 * Easysdi_catalog model.
 */
abstract class sdiModel extends JModelAdmin {

    /**
     * Method to get a single record.
     *
     * @param	integer	The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     * @since	1.6
     */
    public function getItem($pk = null) {
        $item = parent::getItem($pk);
        if ($item && $item->guid) {
            //Load translations
            $translationtable = $this->getTable('Translation', 'Easysdi_catalogTable', array());
            $rows = $translationtable->loadAll($item->guid);
            if (is_array($rows)) {
                if (isset($rows['text1']))
                    $item->text1 = $rows['text1'];
                if (isset($rows['text2']))
                    $item->text2 = $rows['text2'];
                if (isset($rows['text3']))
                    $item->text3 = $rows['text3'];
            }

            // Get the access scope
            $item->organisms = sdiModel::getAccessScopeOrganism($item->guid);
            $item->users = sdiModel::getAccessScopeUser($item->guid);
            $item->categories = sdiModel::getAccessScopeCategory($item->guid);
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
     * @since   12.2
     */
    public function save($data) {
        
        if (parent::save($data)) {

            //Get the element guid
            $item = parent::getItem($data['id']);
            $data['guid'] = $item->guid;

            //Save translations
            $translationtable = $this->getTable('Translation', 'Easysdi_catalogTable', array());
            if (!$translationtable->saveAll($data)) {
                $this->setError($translationtable->getError());
                return false;
            }

            //Access Scope
            if (!sdiModel::saveAccessScope($data)) {
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
    public function delete(&$pks) {
        $item = parent::getItem($pks[0]);
        $guid = $item->guid;

        if (parent::delete($pks)) {
            //Delete translation
            $translationtable = $this->getTable('Translation', 'Easysdi_catalogTable', array());
            if (!$translationtable->deleteAll($guid)) {
                $this->setError($translationtable->getError());
                return false;
            }

            //Delete Access scope
            if (!sdiModel::deleteAccessScope($guid)) {
                return false;
            }

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
    public static function saveAccessScope($data) {
        //Delete previously saved access
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__sdi_accessscope');
        $query->where('entity_guid = '. $query->quote($data['guid']));
        
        $db->setQuery($query);
        $db->query();

        if (isset($data['organisms'])) {
            $pks = $data['organisms'];
            foreach ($pks as $pk) {
                try {
                    $columns = array('entity_guid', 'organism_id');
                    $values = array($query->quote($data['guid']), $query->quote($pk));
                    $query = $db->getQuery(true);
                    $query->insert('#__sdi_accessscope');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));
                    
                    $db->setQuery($query);
                    $db->execute();
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }
            }
        }
        if (isset($data['users'])) {
            $pks = $data['users'];
            foreach ($pks as $pk) {
                try {
                    $columns = array('entity_guid', 'user_id');
                    $values = array($query->quote($data['guid']), $query->quote($pk));
                    
                    $query = $db->getQuery(true);
                    $query->insert('#__sdi_accessscope');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));
                    $db->setQuery($query);
                    $db->execute();
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }
            }
        }
        if (isset($data['categories'])) {
            $pks = $data['categories'];
            foreach ($pks as $pk) {
                try {
                    $columns = array('entity_guid', 'category_id');
                    $values = array($query->quote($data['guid']), $query->quote($pk));
                    
                    $query = $db->getQuery(true);
                    $query->insert('#__sdi_accessscope');
                    $query->columns($query->quoteName($columns));
                    $query->values(implode(',', $values));
                    $db->setQuery($query);
                    $db->execute();
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }
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
    public static function getAccessScopeOrganism($guid) {
        if (!isset($guid))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.organism_id as id');
            $query->from('#__sdi_accessscope p');
            $query->where('p.entity_guid = ' . $query->quote($guid) );
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
    public static function getAccessScopeUser($guid) {
        if (!isset($guid))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.user_id as id');
            $query->from('#__sdi_accessscope p');
            $query->where('p.entity_guid = ' . $query->quote($guid) );
            $db->setQuery($query);

            $scope = $db->loadColumn();
            return $scope;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
    
    
    /**
     * Method to get the organism categories authorized to access this resourcetype
     *
     * @param int		$id		primary key of the current resourcetype to get.
     *
     * @return boolean 	Object list on success, False on error
     *
     * @since EasySDI 3.0.0
     */
    public static function getAccessScopeCategory($guid) {
        if (!isset($guid))
            return null;

        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('p.category_id as id');
            $query->from('#__sdi_accessscope p');
            $query->where('p.entity_guid = ' . $query->quote($guid) );
            $db->setQuery($query);

            $scope = $db->loadColumn();
            return $scope;
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }    

    public static function deleteAccessScope($guid) {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->quoteName('#__sdi_accessscope'));
        $query->where('entity_guid = ' . $query->quote($guid) );
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
    
    public static function checkAccessScope($guid, $accessscope, $sdiUser) {
        //check if the user has access on the objet identify by the guid
        switch ($accessscope) {
            case 2: //accesscope by organism's category
                $categories = sdiModel::getAccessScopeCategory($guid);
                if (count($categories) == 0)
                    return false;
                if (!$sdiUser->isPartOfCategories($categories))
                    return false;
                return true;                
            case 3: //accessscope by organism
                $organisms = sdiModel::getAccessScopeOrganism($guid);
                $organism = $sdiUser->getMemberOrganisms();
                if (!in_array($organism[0]->id, $organisms))
                    return false;
                return true;
                
            case 4: //accessscope by user
                $users = sdiModel::getAccessScopeUser($guid);
                if (!in_array($sdiUser->id, $users))
                    return $false;
                return true;
                
            case 1: //accessscope: public
            default:
                return true;
                
        }
        return false;
    }

}