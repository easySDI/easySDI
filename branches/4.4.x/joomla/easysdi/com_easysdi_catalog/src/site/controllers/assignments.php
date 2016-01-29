<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_catalog
 * @copyright	
 * @license		
 * @author		
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Assignments controller class.
 */
class Easysdi_catalogControllerAssignments extends Easysdi_catalogController{
    
    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function &getModel($name = 'Resources', $prefix = 'Easysdi_coreModel')
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
}