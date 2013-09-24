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

require_once JPATH_SITE. '/components/com_easysdi_shop/libraries/easysdi/sdiProperty.php';

class sdiExtraction {

    var $id;
    var $name;
    var $organism;
    var $properties;
    var $resource;
    var $restrictedperimeter;
    var $perimeters;
    
    function __construct($session_extraction) {
        if (empty($session_extraction))
            return;

        $this->id = $session_extraction->id;
        $this->loadData();
        
        if (!isset($this->properties))
            $this->properties = array();

        foreach ($session_extraction->properties as $property):
            $this->properties[] = new sdiProperty($property);
        endforeach;
    }

    private function loadData() {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('r.id as resource, r.name as name, o.name as organism, d.restrictedperimeter, d.surfacemin, d.surfacemax, d.pricing_id as pricing')
                    ->from('#__sdi_resource r')
                    ->innerJoin('#__sdi_version v ON v.resource_id = r.id')
                    ->innerJoin('#__sdi_diffusion d ON d.version_id = v.id')
                    ->innerJoin('#__sdi_organism o ON r.organism_id = o.id')
                    ->where('d.id = ' . $this->id);
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
