<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_shop
 * @copyright	
 * @license		
 * @author		
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Orders list controller class.
 */
class Easysdi_shopControllerRequests extends Easysdi_shopController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Requests', $prefix = 'Easysdi_shopModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}