<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHTML::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
?>
<style type="text/css">
    .label-important{
        color: #b94a48;
        border-color: #b94a48;
    }
</style>
<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'); ?> validation</h1>
    <div class="well">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orders'); ?>" method="post">
                <div class="btn-toolbar">
                    <div class="btn-group pull-left">
                        <select id="filter_status" name="filter_status" onchange="this.form.submit();" class="inputbox">
                            <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_ORDERS_STATE_FILTER'); ?></option>
                            <?php foreach ($this->orderstate as $status): ?>
                                <option value="<?php echo $status->id; ?>" <?php
                                if ($this->state->get('filter.status') == $status->id) : echo 'selected="selected"';
                                endif;
                                ?> >
                                <?php echo JText::_($status->value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="btn-group pull-left">
                            <label for="filter_search" class="element-invisible">Rechercher</label>
                            <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_ORDERS_SEARCH_FILTER'); ?>" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" />
                            <button class="btn hasTooltip" type="submit" title="Rechercher"><i class="icon-search"></i></button>
                            <button class="btn hasTooltip" type="button" title="Effacer" onclick="document.id('filter_search').value = '';
                                    this.form.submit();"><i class="icon-remove"></i></button>
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
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED_BY') ?></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
    <?php foreach ($this->items as $item) : ?>
                        <tr class="order-line order-line-new">
                            <td><i><?php echo $item->name; ?></i></td>
                            <td class="ordercreated"><?php echo $item->created; ?></td>
                            <td class="ordercreatedby"><?php echo $item->created_by; ?></td>
                            <td class="orderstate">
                                <?php if ($item->ordertype_id != 3): ?>
                                    <?php
                                    if ($item->orderstate_id == 1):
                                        $classlabel = '';
                                    elseif ($item->orderstate_id == 2):
                                        $classlabel = '';
                                    elseif ($item->orderstate_id == 3):
                                        $classlabel = 'label-success';
                                    elseif ($item->orderstate_id == 4):
                                        $classlabel = 'label-warning';
                                    elseif ($item->orderstate_id == 5):
                                        $classlabel = 'label-info';
                                    elseif ($item->orderstate_id == 6):
                                        $classlabel = 'label-inverse';
                                    elseif ($item->orderstate_id == 8):
                                        $classlabel = 'label-warning';
                                    elseif ($item->orderstate_id == 9):
                                        $classlabel = 'label-important';
                                    endif;
                                    ?>
                                    <span class="label <?php echo $classlabel; ?> "><?php echo JText::_($item->orderstate); ?></span>
        <?php endif; ?>
                            </td>
                            <td>
        <?php if ($item->orderstate_id == 8): ?>
                                    <div class="btn-group pull-right">
                                        <a class="btn btn-success btn-small dropdown-toggle" data-toggle="dropdown" href="#">
            <?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_ACTIONS'); ?>
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=validation&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_VALIDATION_ORDER'); ?></a>
                                            </li>
                                            <li>
                                                <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.validate&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_VALIDATE_ORDER'); ?></a>
                                            </li>
                                            <li>
                                                <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.reject&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_REJECT_ORDER'); ?></a>
                                            </li>
                                        </ul>                                    
                                    </div>
        <?php endif; ?>
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
