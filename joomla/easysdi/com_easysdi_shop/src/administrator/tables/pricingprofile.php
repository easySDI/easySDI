<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_shop
 * @copyright	
 * @license		
 * @author		
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * order Table class
 */
class Easysdi_shopTablePricingProfile extends sdiTable {
    
    
    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_pricing_profile', 'id', $db);
    }
}