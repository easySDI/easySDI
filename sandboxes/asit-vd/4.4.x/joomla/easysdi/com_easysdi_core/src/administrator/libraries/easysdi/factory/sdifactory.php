<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/sdiVersion.php';

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

    /**
     * get easySDI version 
     * @return string (eg: 4.4.0)
     * @see sdiVersion
     */
    public static function getSdiVersion() {
        $v = new sdiVersion();
        return $v->getSdiVersion();
    }

    /**
     * get easySDI Full version : [version]-[revision]
     * @return string (eg: 4.4.0-9458)
     * @see sdiVersion
     */
    public static function getSdiFullVersion() {
        $v = new sdiVersion();
        return $v->getSdiFullVersion();
    }

}

?>
