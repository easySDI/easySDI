<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_shop')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_easysdi_core/models/fields');

$controller	= JControllerLegacy::getInstance('Easysdi_shop');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
