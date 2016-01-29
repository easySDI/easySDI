<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiDao.php';

/**
 * @version		4.4.0
 * @package     com_easysdi_catalog
 * @copyright	
 * @license		
 * @author		
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
