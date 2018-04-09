<?php
/**
 * @version     4.4.5
 * @package     plg_easysdi_getordersbutton
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

class PlgEasysdi_admin_buttonGetordersbutton extends JPlugin {

    protected $autoloadLanguage = true;

      /**
     * Trigger event quickButton
     * This function is called on the trigger
     * It gets the button and return it in an array
     */
    public function quickButton($context) {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Count unactivated users
        $query->select('COUNT(*)');
        $query->from('#__sdi_order');
        $query->where('orderstate_id < 7 ');
        $query->where('orderstate_id > 3 ');
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        //Convert the stdClass object in an array
        $values = get_object_vars($rows[0]);
        $values = $values['COUNT(*)'];
        $state = 'important';
        $badgetooltip = null;
        
        if($values > 0){
            $badgetooltip = JText::plural('PLG_EASYSDI_ADMIN_BUTTON_GETORDERSBUTTON_ORDERS_INCOMPLETES', $values);
            
        }

        //Create the return array with all the infos
        return array(
                'info' => $values,
                'state' => $state,
                'link' => 'index.php?option=com_easysdi_shop&view=orders',
                'text' => JText::_('PLG_EASYSDI_ADMIN_BUTTON_GETORDERSBUTTON_ORDERS'),
                'badgetooltip' => $badgetooltip
        );
    }

}
