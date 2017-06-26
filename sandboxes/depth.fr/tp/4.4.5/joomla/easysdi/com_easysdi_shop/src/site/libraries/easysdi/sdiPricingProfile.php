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


class sdiPricingProfile {

    public $id;
    public $name;
    public $organism_id;
    public $fixed_price;
    public $surface_price;
    public $min_price;
    public $max_price;
    
    function __construct($id) {
        if (empty($id))
            return;

        $this->id = $id;
        $this->loadData();
    }

    private function loadData() {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('pp.*')
                    ->from('#__sdi_pricing_profile pp')
                    ->where('pp.id = ' . (int)$this->id);
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
