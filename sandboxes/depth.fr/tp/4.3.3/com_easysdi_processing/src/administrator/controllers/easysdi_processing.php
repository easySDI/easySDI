<?php
/*------------------------------------------------------------------------
# easysdi_processing.php - easysdi_processing Component
# ------------------------------------------------------------------------
# author    Thomas Portier
# copyright Copyright (C) 2015. All Rights Reserved
# license   Depth France
# website   www.depth.fr
-------------------------------------------------------------------------*/

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