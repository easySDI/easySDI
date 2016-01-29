<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_core
 * @copyright	
 * @license		
 * @author		
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Versionrelations list controller class.
 */
class Easysdi_coreControllerVersionrelations extends Easysdi_coreController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Versionrelations', $prefix = 'Easysdi_coreModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}