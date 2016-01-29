<?php
/**
 * @version		4.4.0
 * @package     mod_easysdi_adminbutton
 * @copyright	
 * @license		
 * @author		
 */

defined('_JEXEC') or die;

// Include dependancies.
require_once __DIR__ . '/helper.php';

$document = JFactory::getDocument();

$usersButton = ModEasysdi_adminbuttonHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_easysdi_adminbutton', $params->get('layout', 'default'));
