<?php
/**
 * @version     4.0.0
 * @package     mod_easysdi_basket
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Include the syndicate functions only once
require_once( dirname(__FILE__).'/helper.php' );

$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
 
$basketcontent = modEasysdiBasketHelper::getBasketContent( $params );
require( JModuleHelper::getLayoutPath( 'mod_easysdi_basket' ) );
?>