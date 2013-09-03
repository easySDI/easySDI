<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

class sdiPerimeter {

    var $id;
    var $name;
    var $buffer;

    function __construct($session_perimeter) {
        if (empty($session_perimeter))
            return;

        $this->id = $session_perimeter->perimeter_id;
        $this->buffer = $session_perimeter->buffer;
        $this->loadData();

    }

    private function loadData() {
        try {
            $lang = JFactory::getLanguage();
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__sdi_perimeter p')
                    ->where('p.id = ' . $this->id)
                    ;

            $db->setQuery($query);
            $item = $db->loadObject();
            
            $params = get_object_vars($item);
            foreach ($params as $key => $value){
                $this->$key = $value;
            }
            
        } catch (JDatabaseException $e) {
            
        }
    }

}

?>
