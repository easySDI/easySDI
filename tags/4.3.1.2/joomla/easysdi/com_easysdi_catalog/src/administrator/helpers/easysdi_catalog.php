<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_catalog helper.
 */
class Easysdi_catalogHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
            JHtmlSidebar::addEntry(
                '<i class="icon-home"></i> '.JText::_('COM_EASYSDI_CATALOG_TITLE_HOME'),
                'index.php?option=com_easysdi_core&view=easysdi',
                $vName == 'easysdi'
	    );
            
            if($vName == 'attributevalues'){
                JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_ATTRIBUTES'),
			'index.php?option=com_easysdi_catalog&view=attributes',
			$vName == 'attributes'
		);
                
                return;
            };
            
            if($vName == 'searchcriterias'){
                JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_CATALOGS'),
			'index.php?option=com_easysdi_catalog&view=catalogs',
			$vName == 'catalogs'
		);
                return;
            }
                
                
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_CATALOGS'),
			'index.php?option=com_easysdi_catalog&view=catalogs',
			$vName == 'catalogs'
		);
                
                JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_RESOURCESTYPE'),
			'index.php?option=com_easysdi_catalog&view=resourcestype',
			$vName == 'resourcestype'
		);
                JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_RESOURCETYPELINKS'),
			'index.php?option=com_easysdi_catalog&view=resourcetypelinks',
			$vName == 'resourcetypelinks'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_CLASSES'),
			'index.php?option=com_easysdi_catalog&view=classes',
			$vName == 'classes'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_ATTRIBUTES'),
			'index.php?option=com_easysdi_catalog&view=attributes',
			$vName == 'attributes'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_RELATIONS'),
			'index.php?option=com_easysdi_catalog&view=relations',
			$vName == 'relations'
		);
		
                JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_PROFILES'),
			'index.php?option=com_easysdi_catalog&view=profiles',
			$vName == 'profiles'
		);
                
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_IMPORTREFS'),
			'index.php?option=com_easysdi_catalog&view=importrefs',
			$vName == 'importrefs'
		);
                
                JHtmlSidebar::addEntry(
                        JText::_('COM_EASYSDI_CATALOG_TITLE_NAMESPACES'),
                        "index.php?option=com_easysdi_catalog&view=namespaces",
                        $vName == 'namespaces'
                );
                
                JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_BOUNDARIESCATEGORY'),
			'index.php?option=com_easysdi_catalog&view=boundariescategory',
			$vName == 'boundariescategory'
		);
                JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_CATALOG_TITLE_BOUNDARIES'),
			'index.php?option=com_easysdi_catalog&view=boundaries',
			$vName == 'boundaries'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_easysdi_catalog';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
