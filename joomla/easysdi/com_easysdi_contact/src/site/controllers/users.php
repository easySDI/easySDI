<?php
/**
*** @version		4.4.0
* @package     com_easysdi_contact
 * @copyright	
 * @license		
 * @author		
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Users list controller class.
 */
class Easysdi_coreControllerUsers extends JController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Users', $prefix = 'Easysdi_coreModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}