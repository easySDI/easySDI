<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';

/**
 * EasySDI
 *
 * @since    4.0.0
 */
abstract class sdiFactory 
{
    /**
	 * Get an sdiuser object.
	 *
	 * Returns the {@link sdiUser} object
	 *
	 * @param   integer  $id  The juser id to load.
	 *
	 * @return  sdiUser object
	 *
	 * @see     sdiUser
	 * @since   4.0.0
	 */
	public static function getSdiUser($sdiId = null)
	{
		return new sdiUser($sdiId);
	}
        
        
        
}
?>
