<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Easysdi.getUsers
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Return users from the EasySDI DB
 */
class PlgEasysdi_admin_infoGetusers extends JPlugin {

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Trigger event onGetAdminInfos
     * This function is called on the trigger
     * It gets the users and return them in an array
     */
    public function onGetAdminInfos($context) {

        // Get a db connection.
        $db = JFactory::getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        $query->select('COUNT(*)');
        $query->from('#__sdi_user');
        // Reset the query using our newly populated query object.
        $db->setQuery($query);

        // Load the results as a list of stdClass objects.
        $rows = $db->loadObjectList();
        //Convert the stdClass object in an array
        $values = get_object_vars($rows[0]);
        $values = $values['COUNT(*)'];
        //Create the return array with all the infos
        return array(
            'info' => $values,
            'text' => JText::plural('PLG_EASYSDI_ADMIN_INFO_GETUSERS_USERS',$values)
        );
    }

}
