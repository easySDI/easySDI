<?php
/**
 * @version		4.4.0
 * @package     mod_easysdi_basket
 * @copyright	
 * @license		
 * @author		
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