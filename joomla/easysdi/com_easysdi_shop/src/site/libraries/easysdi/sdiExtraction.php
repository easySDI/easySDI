<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiProperty.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/diffusionperimeter.php';

class sdiExtraction {

    public $id;
    public $name;
    public $organism;
    public $properties;
    public $resource;
    public $restrictedperimeter;
    public $otp;
    public $perimeters;
    public $visualization;

    /**
     * 
     * @param type $session_extraction : json {"id":5,"properties":[{"id": 1, "values" :[{"id" : 4, "value" : "foo"}]},{"id": 1, "values" :[{"id" : 5, "value" : "bar"}]}]}
     * @return type
     */
    function __construct($session_extraction) {
        if (empty($session_extraction))
            return;

        $this->id = $session_extraction->id;
        $this->loadData();

        $diffusionperimeter = JTable::getInstance('diffusionperimeter', 'Easysdi_shopTable');
        $perimeters = $diffusionperimeter->loadBydiffusionID($this->id);
        if ($perimeters):
            foreach ($perimeters as $perimeter):
                $new_perimeter = new sdiPerimeter($perimeter);
                $this->perimeters[] = $new_perimeter;
            endforeach;
        endif;

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
                    ->select('r.id as resource, 
                        r.name as name, 
                        m.guid as metadataguid,
                        o.name as organism, 
                        o.id as organism_id,
                        d.restrictedperimeter, 
                        d.otp, 
                        d.surfacemin, 
                        d.surfacemax, 
                        d.pricing_id as pricing, 
                        d.pricing_profile_id as pricing_profile,
                        z.id as visualization')
                    ->from('#__sdi_resource r')
                    ->innerJoin('#__sdi_version v ON v.resource_id = r.id')
                    ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                    ->innerJoin('#__sdi_diffusion d ON d.version_id = v.id')
                    ->innerJoin('#__sdi_organism o ON r.organism_id = o.id')
                    ->leftJoin('#__sdi_visualization z ON z.version_id = v.id')
                    ->where('d.id = ' . (int)$this->id);
            $db->setQuery($query);
            $item = $db->loadObject();
            $params = get_object_vars($item);
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }
        } catch (JDatabaseException $e) {
            
        }
    }

}

?>
