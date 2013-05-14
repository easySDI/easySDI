<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Maps list controller class.
 */
class Easysdi_mapControllerMaps extends Easysdi_mapController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Maps', $prefix = 'Easysdi_mapModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}