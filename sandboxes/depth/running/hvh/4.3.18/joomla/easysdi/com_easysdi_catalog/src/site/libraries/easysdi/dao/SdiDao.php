<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
abstract class SdiDao {
    
    /**
     *
     * @var JDatabaseDriver 
     */
    protected $db;
    
    function __construct() {
        $this->db = JFactory::getDbo();
    }

    abstract public function getAll();
    
}

?>
