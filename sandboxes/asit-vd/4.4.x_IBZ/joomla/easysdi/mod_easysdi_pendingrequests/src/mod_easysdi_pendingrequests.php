<?php
/**
 * @version     4.4.5
 * @package     mod_easysdi_pendingrequests
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
require_once JPATH_SITE . '/components/com_asitvd_users/helpers/asitvd_users.php';


$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);

$nbRequests = (int)$params->get('nb_requests',5);
$myRequestsItemId = (int)$params->get('myrequests_itemid',0);
$myRequestsItemString = $myRequestsItemId != 0 ? '&Itemid='.$myRequestsItemId : '';

require_once JPATH_SITE . '/components/com_easysdi_shop/models/requests.php';
$requestsModel = JModelLegacy::getInstance('Requests', 'Easysdi_shopModel');
//reset filters, without saving them, to get global count
foreach (get_object_vars($requestsModel->getState()) as $key => $value) {
    if (strpos($key, "filter.") === 0) {
        $requestsModel->setState($key, "");
    }
}
$totalRequests = $requestsModel->getTotal();
$requestsModel->setState('list.limit', $nbRequests);
$requestsModel->setState('list.start', 0);
$requests = $requestsModel->getItems();

$sdiUser = sdiFactory::getSdiUser();

if(Asitvd_usersHelper::isOrganismProvider(array_merge((array) $sdiUser->getOrganismManagerOrganisms(),(array) $sdiUser->getExtractionResponsibleOrganisms()))){
    require( JModuleHelper::getLayoutPath('mod_easysdi_pendingrequests') );
}

?>