<?php

require_once JPATH_SITE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiDao.php';

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class SdiNamespaceDao extends SdiDao{
    
    /**
     * Get all namespace
     * 
     * @return array
     */
    public function getAll(){
        $query = $this->db->getQuery(true);
        
        $query->select('*');
        $query->from('#__sdi_namespace');
        $query->order('ordering ASC');

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        
        return $result;
    }
    
    /**
     * Get a namspace object by prefix
     * 
     * @param string $prefix
     * @return stdClass
     */
    public function getByPrefix($prefix){
        $query = $this->db->getQuery(true);
        
        $query->select('*');
        $query->from('#__sdi_namespace');
        $query->where('prefix = '.$query->quote($prefix));

        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        
        return $result;
    }
    
}

?>
