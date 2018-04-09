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

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';

class Easysdi_shopController extends JControllerLegacy {

    public function addToBasket() {
        $item = JFactory::getApplication()->input->getString('item', null);
        //'{"id":5,"properties":[{"id": 1, "values" :[{"id" : 4, "value" : "foo"}]},{"id": 1, "values" :[{"id" : 5, "value" : "bar"}]}]}'
        Easysdi_shopHelper::addToBasket($item);
    }

    public function removeFromBasket() {
        $id = JFactory::getApplication()->input->getInt('id', null);
        Easysdi_shopHelper::removeFromBasket($id);
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket', false));
    }

    public function abortAdd() {
        Easysdi_shopHelper::abortAdd();
    }

    public function confirmAdd() {
        Easysdi_shopHelper::addToBasket(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.suspend'), true);
    }

    public function addExtentToBasket() {
        $item = JFactory::getApplication()->input->getString('item', null);
        Easysdi_shopHelper::addExtentToBasket($item);
    }

}