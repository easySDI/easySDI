<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
defined('_JEXEC') or die('Restricted access');
// Require helper file
//require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/helpers/easysdi_processing.php';
//require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/helpers/easysdi_processing_params.php';
//require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/helpers/easysdi_processing_status.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/views/orders/view.html.php';

class Easysdi_processingViewMyOrders extends Easysdi_processingViewOrders
{

        protected $items;
	protected $pagination;
	protected $state;
        protected $params;
	
        function display($tpl = null)
	{
		$app                = JFactory::getApplication();
		$this->state	    = $this->get('State');
		$this->items	    = $this->get('Items');
		$this->pagination   = $this->get('Pagination');
                $this->params       = $app->getParams('com_easysdi_processing');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

                $this->_prepareDocument();
		parent::display($tpl);
	}
        
        protected function addToolbar()
	{
        
        }
        
        /**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		$this->user_processes=Easysdi_processingHelper::getUserProcesses();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('com_easysdi_core_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}



	function form(){
		JHTML::_('behavior.modal','a.modal');
		return parent::form();
	}

	
}
?>
