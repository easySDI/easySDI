<?php
/**
 * @version     4.4.0
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2016. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');

 
class Easysdi_monitorViewproxy extends JViewLegacy
{
    function display($tpl = null)
    {
	  parent::display($tpl);
    }
}