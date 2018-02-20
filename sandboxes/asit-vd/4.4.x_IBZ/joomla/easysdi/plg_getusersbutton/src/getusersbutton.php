<?php
/**
 * @version     4.4.5
 * @package     plg_easysdi_getusersbutton
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

class PlgEasysdi_admin_buttonGetusersbutton extends JPlugin {

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
        $query->select('COUNT(*) as nbre');
        $query->from('#__sdi_user');
        $query->where('state != 1');
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        //Convert the stdClass object in an array
        $values = get_object_vars($rows[0]);
        $values = $values['nbre'];
        $state = 'important';
        $badgetooltip = null;
        
        if($values > 0){
            $badgetooltip = JText::plural('PLG_EASYSDI_ADMIN_BUTTON_GETUSERSBUTTON_DISABLED_USERS', $values);
            
        }

        //Create the return array with all the infos
        return array(
                'info' => $values,
                'state' => $state,
                'link' => 'index.php?option=com_easysdi_contact',
                'text' => JText::_('PLG_EASYSDI_ADMIN_BUTTON_GETUSERSBUTTON_USERS'),
                'badgetooltip' => $badgetooltip
        );
    }

}
