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

JHtml::_('behavior.tooltip');

JFactory::getDocument()->addScript('components/com_easysdi_shop/helpers/helper.js?v=' . sdiFactory::getSdiFullVersion());
?>

<div>
    <div class="titlewithlink clearfix">
        <h2><?php echo JText::_('MOD_EASYSDI_LASTORDERS_TITLE_MY_LAST_ORDERS'); ?></h2>
        <a title="<?php echo JText::plural('MOD_EASYSDI_LASTORDERS_LINK_ALL_ORDERS', $totalOrders); ?>" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orders' . $myOrdersItemString); ?>" class="link"><?php echo JText::plural('MOD_EASYSDI_LASTORDERS_LINK_ALL_ORDERS', $totalOrders); ?></a>
    </div>

    <?php if ($orders): ?>

        <?php
        $ordersListLayout = new JLayoutFile('com_easysdi_shop.orders', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_shop'));
        echo $ordersListLayout->render(array(
            'items' => $orders,
            'displayTitle' => false
        ));
        ?>

    <?php endif; ?>
</div> 

<?php echo Easysdi_shopHelper::getAddToBasketModal(); ?>