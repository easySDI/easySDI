<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/order.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/orderdiffusion.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/models/order.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

jimport('joomla.application.component.controllerform');

/**
 * Order controller class.
 */
class Easysdi_shopControllerOrder extends JControllerForm {

    function __construct() {
        $this->view_list = 'orders';
        parent::__construct();
    }

    /**
     * check rights and download an order file
     */
    function download() {
        $diffusion_id = JFactory::getApplication()->input->getInt('id', null, 'int');
        $order_id = JFactory::getApplication()->input->getInt('order', null, 'int');

        if (empty($diffusion_id)):
            $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_ORDER_ERROR_EMPTY_ID');
            echo json_encode($return);
            die();
        endif;

        $order = JTable::getInstance('order', 'Easysdi_shopTable');
        $order->load($order_id);

        /////////// Check user right on this order
        $downloadAllowed = false;

        //the user is shop admin
        if (JFactory::getUser()->authorise('core.manage', 'com_easysdi_shop')):
            $downloadAllowed = true;
        endif;

        if (!$downloadAllowed) {
            $return['ERROR'] = JText::_('JERROR_ALERTNOAUTHOR');
            echo json_encode($return);
            die();
        }

        //Load order response
        $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
        $keys = array();
        $keys['order_id'] = $order_id;
        $keys['diffusion_id'] = $diffusion_id;
        $orderdiffusion->load($keys);

        return Easysdi_shopHelper::downloadOrderFile($orderdiffusion);
    }

}
