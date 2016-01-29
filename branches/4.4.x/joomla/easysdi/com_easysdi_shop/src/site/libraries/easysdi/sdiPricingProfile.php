<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_shop
 * @copyright	
 * @license		
 * @author		
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
