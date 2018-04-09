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
$filterStatus = isset($displayData['filterStatus']) ? $displayData['filterStatus'] : false;
?>

<table class="table table-striped">
    <?php if ($displayTitle): ?>
        <thead>
            <tr>
                <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>
                <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CLIENT') ?></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    <?php endif; ?>

    <tbody>
        <?php foreach ($items as $item) : ?>
            <tr class="order-line order-line-new <?php echo('sdi-orderstate-' . preg_replace('/\s+/', '', $item->orderstate) . ' ' . 'sdi-ordertype-' . preg_replace('/\s+/', '', $item->ordertype) ); ?>">
                <td class="ordercreated">
                    <span class="hasTip" title="<?php echo JHtml::date($item->sent, JText::_('DATE_FORMAT_LC2')); ?>">
                        <?php echo Easysdi_shopHelper::getRelativeTimeString(JFactory::getDate($item->sent)); ?>
                    </span>
                </td>
                <td class="ordername">
                    <strong>
                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=request.edit&id=' . $item->id); ?>"><?php echo $item->name; ?></a>
                    </strong> - <?php echo $item->id; ?>
                    <?php
                    //only show order type if estimate
                    if ($item->ordertype_id == 2):
                        $classicontype = 'icon-lamp';
                        ?>
                        <i class="<?php echo $classicontype; ?>"></i> <?php echo JText::_($item->ordertype); ?>
                        <?php
                    endif;
                    ?>

                </td>

                <td class="orderclient">
                    <span  class="hasTip" title="<?php echo($item->clientname); ?>">
                        <?php echo($item->organismname); ?>
                    </span>
                </td>
                <td class="orderproductcount">
                    <span  class="label" >
                        <?php echo( JText::plural('COM_EASYSDI_SHOP_REQUESTS_N_PRODUCTS_TO_PROCESS', $item->productcount)); ?>
                    </span>
                </td>                            
                <td class="orderactions">
                    <a class="btn btn-primary btn-small pull-right" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=request.edit&id=' . $item->id); ?>"><?php echo $filterStatus == 0 ? JText::_('COM_EASYSDI_SHOP_REQUESTS_OPEN') : JText::_('COM_EASYSDI_SHOP_REQUESTS_REPLY') ?></a>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
