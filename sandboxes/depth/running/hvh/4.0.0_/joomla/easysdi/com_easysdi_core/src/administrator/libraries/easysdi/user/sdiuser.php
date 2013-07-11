<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class sdiUser {

    /**
     * Unique id
     *
     * @var    integer
     */
    public $id = null;

    /**
     * Unique juser
     *
     * @var    integer
     */
    public $juser = null;
    
    /**
     * Unique role
     *
     * @var    array
     */
    public $role = null;

    function __construct($juser = 0) {

        $this->juser = $juser;
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('u.*')
                ->from('#__sdi_user AS u')
                ->where('u.user_id = ' . $juser)
                ;
        $db->setQuery($query);
        $user = $db->loadObject();
        
        $this->id = $user->id;
        
        $query = $db->getQuery(true)
                ->select('o.*')
                ->from('#__sdi_user_role_organism  o')
                ->where('o.user_id = ' . $this->id)
                ;
        $db->setQuery($query);
        $roles = $db->loadObjectList();
        
        $this->role = array ();
        foreach ($roles as $role){
             if (!isset ($this->role[$role->role_id]))
                 $this->role[$role->role_id] = array ();
             
             array_push($this->role[$role->role_id], $role->organism_id);
        }
    }
    
    public function  isResourceManager(){
        if (isset($this->role[2]) ){
            return true;
        }
        return false;
    }

}

?>
