<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SdiDao
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
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
