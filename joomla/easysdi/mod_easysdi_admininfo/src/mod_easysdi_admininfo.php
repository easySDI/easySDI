<?php
/**
 * @version     4.3.2
 * @package     mod_easysdi_admininfo
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

// Include dependancies.
require_once __DIR__ . '/helper.php';

$infos = Modeasysdi_admininfoHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_easysdi_admininfo', $params->get('layout', 'default'));
