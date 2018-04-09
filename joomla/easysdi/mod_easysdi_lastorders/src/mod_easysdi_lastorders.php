<?php

/**
 * @version     4.4.5
 * @package     mod_easysdi_lastorders
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the helper
require_once( dirname(__FILE__) . '/helper.php' );

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiBasket.php';

$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);

$nbOrders = (int) $params->get('nb_orders', 5);
$myOrdersItemId = (int) $params->get('myorders_itemid', 0);
$myOrdersItemString = $myOrdersItemId != 0 ? '&Itemid=' . $myOrdersItemId : '';

require_once JPATH_SITE . '/components/com_easysdi_shop/models/orders.php';
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_easysdi_shop/models');
$ordersModel = JModelLegacy::getInstance('Orders', 'Easysdi_shopModel');
//reset filters, without saving them, to get global count
foreach (get_object_vars($ordersModel->getState()) as $key => $value) {
    if (strpos($key, "filter.") === 0) {
        $ordersModel->setState($key, "");
    }
}
$ordersModel->setState("filter.archived", 1);
$totalOrders = $ordersModel->getTotal();
$ordersModel->setState('list.limit', $nbOrders);
$ordersModel->setState('list.start', 0);
$orders = $ordersModel->getItems();

require( JModuleHelper::getLayoutPath('mod_easysdi_lastorders') );
?>