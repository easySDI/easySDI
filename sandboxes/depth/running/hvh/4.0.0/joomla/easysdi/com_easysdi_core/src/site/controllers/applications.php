<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Applications list controller class.
 */
class Easysdi_coreControllerApplications extends Easysdi_coreController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Applications', $prefix = 'Easysdi_coreModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
        
        function cancel() {
            // Clear the resource id from the session.
            JFactory::getApplication()->setUserState('com_easysdi_core.edit.applicationresource.id', null);
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }
}