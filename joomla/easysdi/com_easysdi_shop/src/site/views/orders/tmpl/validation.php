<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHTML::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
?>

<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERSVALIDATION'); ?></h1>
    <div class="well sdi-searchcriteria">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orders&layout=validation'); ?>" method="post">
                <div class="control-group pull-right">
                    <div id="filterstatus">
                        <fieldset class="radio btn-group btn-group-yesno">
                            <input type="radio" id="state0" name="filter_status" value="0"<?php if ($this->state->get('filter.status') == 0): ?> checked='checked'<?php endif; ?> onClick="this.form.submit();">
                            <label for="state0" class="btn"><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERSVALIDATION_STATUS_DONE'); ?></label>
                            <input type="radio" id="state1" name="filter_status" value="1"<?php if ($this->state->get('filter.status') == 1): ?> checked='checked'<?php endif; ?> onClick="this.form.submit();">
                            <label for="state1" class="btn"><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERSVALIDATION_STATUS_TODO'); ?></label>
                        </fieldset>
                    </div>
                    <div id="filterorganism" >
                        <select id="filter_organism" name="filter_organism" onchange="this.form.submit();" class="inputbox">
                            <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_ORDERS_ORGANISM_FILTER'); ?></option>
                            <?php foreach ($this->organisms as $organism): ?>
                                <option value="<?php echo $organism->id; ?>" <?php
                                if ($this->state->get('filter.organism') == $organism->id) : echo 'selected="selected"';
                                endif;
                                ?> ><?php echo $organism->name; ?></option>
                                    <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="items">
        <div class="well">                      
            <table class="table table-striped">

                <thead>
                    <tr>
                        <th class="ordercreated"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                        <th class="orderid"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_ORDER_NO') ?></th>
                        <th class="ordername"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>                        
                        <th class="orderclient"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CLIENT') ?></th>
                        <th class="ordermandate"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_MANDATE') ?></th>
                        <th class="orderstate"></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    foreach ($this->items as $item) :
                        $basket = new sdiBasket();
                        $basket->loadOrder($item->id);
                        ?>
                        <tr class="order-line order-line-new <?php echo('sdi-orderstate-' . preg_replace('/\s+/', '', $item->orderstate) . ' ' . 'sdi-ordertype-' . preg_replace('/\s+/', '', $item->ordertype) ); ?>">
                            <td class="ordercreated">
                                <span class="hasTip" title="<?php echo JHtml::date($item->created, JText::_('DATE_FORMAT_LC2')); ?>">
                                    <?php echo Easysdi_shopHelper::getRelativeTimeString(JFactory::getDate($item->created)); ?>
                                </span>
                            </td>
                            <td class="orderid">
                                <span title="<?php echo $item->name; ?>" class="hasTip" >
                                    <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=validation&id=' . $item->id); ?>">
                                        <?php echo($item->id); ?>
                                    </a>
                                </span>
                            </td>
                            <td class="ordername">
                                <span title="<?php echo $item->id; ?>" class="hasTip" >
                                    <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=validation&id=' . $item->id); ?>">
                                        <?php echo $item->name; ?>
                                    </a>
                                </span>
                            </td>
                            <td class="orderclient">
                                <span  class="hasTip" title="<?php echo($item->clientname); ?>">
                                    <?php echo($item->organismname); ?>
                                </span>
                            </td>

                            <td class="ordermandate">
                                <?php echo Easysdi_shopHelper::getShortenedString($item->mandate_ref, 80); ?>
                            </td>
                            <td class="orderstate">
                                <?php echo Easysdi_shopHelper::getOrderStatusLabel($item, $basket); ?>
                            </td>
                            <td>
                                <a class="btn btn-primary btn-small pull-right" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=validation&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_OPEN'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>

</div>
