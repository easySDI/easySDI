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

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiBasket.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelOrderPerimeter extends JModelLegacy {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $content = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        $this->setState('orderperimeter.content', $content);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($content = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($content)) {
                $content = $this->getState('orderperimeter.content');
            }
            
            $this->_item = new sdiBasket($content);
        }

        return $this->_item;
    }

}