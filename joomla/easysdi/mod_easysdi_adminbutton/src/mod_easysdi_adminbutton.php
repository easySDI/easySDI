<?php
/**
 * @version     4.4.5
 * @package     mod_easysdi_adminbutton
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

// Include dependancies.
require_once __DIR__ . '/helper.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';

$document = JFactory::getDocument();

$usersButton = ModEasysdi_adminbuttonHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_easysdi_adminbutton', $params->get('layout', 'default'));
