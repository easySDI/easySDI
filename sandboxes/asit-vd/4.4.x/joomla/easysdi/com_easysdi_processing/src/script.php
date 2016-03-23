<?php
/*------------------------------------------------------------------------
# script.php - Easysdi_processing Component
# ------------------------------------------------------------------------
# author    Thomas Portier
# copyright Copyright (C) 2014. All Rights Reserved
# license   Depth France
# website   www.depth.fr
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of Processing component
 */
class com_easysdi_processingInstallerScript
{
	/**
	 * method to install the component
	 *
	 *
	 * @return void
	 */
	function install($parent)
	{

	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{

	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{

	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
            //Check if com_easysdi_core is installed
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('COUNT(*)');
            $query->from('#__extensions');
            $query->where('name = '.$db->quote('com_easysdi_core'));
            $db->setQuery($query);
            $install = $db->loadResult();

            if($install == 0){
                    JError::raiseWarning(null, JText::_('COM_EASYSDI_MAP_INSTALL_SCRIPT_CORE_ERROR'));
                    return false;
            }

	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
            //remove general admin menu
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->delete('#__menu');
            $query->where('title = '.$db->quote('com_easysdi_processing'));
            $db->setQuery($query);
            $db->query();

	}
}
?>