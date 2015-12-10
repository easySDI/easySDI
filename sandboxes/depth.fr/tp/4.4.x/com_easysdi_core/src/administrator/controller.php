<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// No direct access
defined('_JEXEC') or die;

class Easysdi_coreController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/easysdi_core.php';
		//Check if others EasySDI components are installed and saved results in UserState
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_contact'));
                
		$db->setQuery($query);
		$app->setUserState( 'com_easysdi_contact-installed' ,$db->loadResult() == 0 ? false : true);
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_catalog'));
                
                $db->setQuery($query);
		$app->setUserState( 'com_easysdi_catalog-installed' ,$db->loadResult() == 0 ? false : true);
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_processing'));
                
                $db->setQuery($query);
		$app->setUserState( 'com_easysdi_processing-installed' ,$db->loadResult() == 0 ? false : true);
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_shop'));
                
		$db->setQuery($query);
		$app->setUserState( 'com_easysdi_shop-installed' ,$db->loadResult() == 0 ? false : true);
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_service'));
                
		$db->setQuery($query);
		$app->setUserState( 'com_easysdi_service-installed' ,$db->loadResult() == 0 ? false : true);
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_map'));
                
		$db->setQuery($query);
		$app->setUserState( 'com_easysdi_map-installed' ,$db->loadResult() == 0 ? false : true);
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_monitor'));
                
		$db->setQuery($query);
		$app->setUserState( 'com_easysdi_monitor-installed' ,$db->loadResult() == 0 ? false : true);
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_shop'));
                
                $db->setQuery($query);
		$app->setUserState( 'com_easysdi_shop-installed' ,$db->loadResult() == 0 ? false : true);
                
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_dashboard'));
                
                $db->setQuery('SELECT COUNT(*) FROM #__extensions WHERE name = '.$db->quote('com_easysdi_dashboard'));
		$app->setUserState( 'com_easysdi_dashboard-installed' ,$db->loadResult() == 0 ? false : true);
		
		$view		= JFactory::getApplication()->input->getCmd('view', 'easysdi');
		$layout		= JFactory::getApplication()->input->getCmd('layout', 'edit');
		$id		= JFactory::getApplication()->input->getInt('id');
                
                JFactory::getApplication()->input->set('view', $view);
	
		parent::display($cachable, $urlparams);

		return $this;
	}
}
