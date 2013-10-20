<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiDao.php';

/**
 * Description of SdiNamespaceDao
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class SdiNamespaceDao extends SdiDao{
    
    /**
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
    
}

?>
