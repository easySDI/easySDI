<?php
/**
* @version		4.4.0
* @package     com_easysdi_processing
* @copyright	
* @license		
* @author		
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Cadastre Controller
 */
class Easysdi_processingControllerprocessing extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	2.5
	 */
	public function getModel($name = 'search', $prefix = 'Easysdi_processingModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
?>