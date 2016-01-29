<?php
/**
 * @version		4.4.0
 * @package     mod_easysdi_admininfo
 * @copyright	
 * @license		
 * @author		
 */

defined('_JEXEC') or die;

// Include dependancies.
require_once __DIR__ . '/helper.php';

$infos = Modeasysdi_admininfoHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_easysdi_admininfo', $params->get('layout', 'default'));
