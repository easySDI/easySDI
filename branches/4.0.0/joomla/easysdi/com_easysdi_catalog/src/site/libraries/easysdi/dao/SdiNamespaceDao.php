<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiDao.php';

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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
