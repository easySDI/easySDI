<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_map
 * @copyright	
 * @license		
 * @author		
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Map controller class.
 */
class Easysdi_mapControllerPreview extends Easysdi_mapController {

    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {
       
        parent::display();

        return $this;
    }

}