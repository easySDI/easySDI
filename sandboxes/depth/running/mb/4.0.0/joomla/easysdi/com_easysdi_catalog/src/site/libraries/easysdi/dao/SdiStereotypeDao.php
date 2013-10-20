<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiDao.php';

/**
 * Description of SdiStereotypeDao
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class SdiStereotypeDao extends SdiDao{
    
    public function getAll() {
        $query = $this->db->getQuery(true);
        
        $query->select('*');
        $query->from('#__sdi_sys_stereotype');
        $query->order('ordering ASC');

        $this->db->setQuery($query);
        $result = $this->db->loadObjectList('id');
        
        return $result;
    }    
}

?>
