<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
$items = $displayData['items'];
$displayTitle = isset($displayData['displayTitle']) ? $displayData['displayTitle'] : false;
?>

<table class="table table-striped">
    <?php if ($displayTitle): ?>
        <thead>
            <tr>
                <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    <?php endif; ?>

    <tbody>
        <?php
        foreach ($items as $item) :
            $basket = new sdiBasket();
            $basket->loadOrder($item->id);
            ?>
            <tr class="order-line order-line-new <?php echo('sdi-orderstate-' . preg_replace('/\s+/', '', $item->orderstate) . ' ' . 'sdi-ordertype-' . preg_replace('/\s+/', '', $item->ordertype) ); ?>">
                <td class="ordercreated">
                    <span class="hasTip" title="<?php echo JHtml::date($item->sent, JText::_('DATE_FORMAT_LC2')); ?>">
                        <?php echo Easysdi_shopHelper::getRelativeTimeString(JFactory::getDate($item->sent)); ?>
                    </span>
                </td>
                <td class="ordername">
                    <strong>
                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.edit&id=' . $item->id); ?>"><?php echo $item->name; ?></a>
                    </strong> - <?php echo $item->id; ?>
                    <?php
                    //only show order type if estimate
                    if ($item->ordertype_id == Easysdi_shopHelper::ORDERTYPE_ESTIMATE):
                        $classicontype = 'icon-lamp';
                        ?>
                        <i class="<?php echo $classicontype; ?>"></i> <?php echo JText::_($item->ordertype); ?>
                        <?php
                    endif;
                    ?>

                </td>
                <td class="orderstate">
                    <?php echo Easysdi_shopHelper::getOrderStatusLabel($item, $basket); ?>
                </td>
                <td>
                    <div class="pull-right">
                        <?php if ($item->ordertype_id == Easysdi_shopHelper::ORDERTYPE_ORDER && ($item->orderstate_id == Easysdi_shopHelper::ORDERSTATE_PROGRESS || $item->orderstate_id == Easysdi_shopHelper::ORDERSTATE_FINISH)): ?>                                    
                            <?php
                            $first = true;
                            $basket = new sdiBasket();
                            $basket->loadOrder($item->id);
                            foreach ($basket->extractions as $extraction) {
                                if ($extraction->productstate_id == Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE):
                                    if ($first)://Create the dropdown menu to hold the download links
                                        ?>
                                        <div class="btn-group sdi-btn-download-file-from-list">
                                            <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                                <i class="icon-flag-2"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <?php
                                                $first = false;
                                    endif;
                                    if ($extraction->otp == 1):
                                        echo '<li><a id="' . $item->id .'_' . $extraction->id . '_otpdownload" style="cursor: pointer;">' . $extraction->name . '</a></li>';
                                    else : 
                                        echo '<li><a target="RAW" href="index.php?option=com_easysdi_shop&task=order.download&id=' . $extraction->id . '&order=' . $item->id . '">' . $extraction->name . '</a></li>';
                                    endif;                                   
                                endif;
                            }
                            if (!$first):
                            ?>
                                            </ul>
                                        </div>
                            <?php
                            endif;
                            ?>                                
                        <?php endif; ?>
                        <div class="btn-group">
                            <a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                <?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_ACTIONS'); ?>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.edit&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_OPEN'); ?></a>
                                </li>
                                <li>
                                    <?php if ($item->ordertype_id == Easysdi_shopHelper::ORDERTYPE_ORDER || $item->ordertype_id == Easysdi_shopHelper::ORDERTYPE_ESTIMATE): ?>
                                        <a onclick="acturl = '<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=basket.copy&id='); ?><?php echo $item->id; ?>';
                                                getBasketContent('addOrderToBasket');"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_COPY_ORDER_INTO_BASKET'); ?></a>
                                       <?php else : ?>
                                        <a onclick="acturl = '<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=basket.load&id='); ?><?php echo $item->id; ?>';
                                                getBasketContent('addOrderToBasket');"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_LOAD_DRAFT_INTO_BASKET'); ?></a>
                                       <?php endif; ?>
                                </li>
                                <?php if ($item->ordertype_id == Easysdi_shopHelper::ORDERTYPE_DRAFT): ?>
                                    <li>
                                        <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.remove&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_REMOVE_DRAFT'); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($item->archived == 0 && ($item->orderstate_id == Easysdi_shopHelper::ORDERSTATE_FINISH || $item->orderstate_id == Easysdi_shopHelper::ORDERSTATE_REJECTED || $item->orderstate_id == Easysdi_shopHelper::ORDERSTATE_REJECTED_SUPPLIER)): ?>
                                    <li>
                                        <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.archive&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_ARCHIVE_ORDER'); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>                                    
                        </div>
                    </div>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
