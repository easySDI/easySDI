<?php

/**
 * @version     4.4.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2016. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';

/**
 * EasySDI
 *
 * @since    4.0.0
 */
abstract class sdiFactory {

    /**
     * Get an sdiuser object, with an optional sdiUser id.
     * Returns the {@link sdiUser} object, without id the current sdiUser is returned.
     * @param   integer  $id  The sdiUser id to load.
     * @return  sdiUser object
     * @see     sdiUser
     * @since   4.0.0
     */
    public static function getSdiUser($sdiId = null) {
        return new sdiUser($sdiId);
    }

    /**
     * Get an sdiuser object by it's joomla user id.
     * Returns the {@link sdiUser} object
     * @param   integer  $id  The joomla user id to load.
     * @return  sdiUser object
     * @see     sdiUser
     * @since   4.3.2
     */
    public static function getSdiUserByJoomlaId($juserId) {
        return new sdiUser($juserId, true);
    }

}

?>
