<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
 */
// No direct access
defined('_JEXEC') or die;


/**
 * service Table class
 */
class Easysdi_serviceTablecswspatialpolicy extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_csw_spatialpolicy', 'id', $db);
    }
    
    
}
