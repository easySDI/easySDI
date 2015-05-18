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
<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'); ?></h1>
    <div class="well">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orders'); ?>" method="post">
                <div class="btn-toolbar">
                    <div class="btn-group pull-left">
                        <select id="filter_type" name="filter_type" onchange="this.form.submit();" class="inputbox">
                            <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_ORDERS_TYPE_FILTER'); ?></option>
                            <?php foreach ($this->ordertype as $type): ?>
                                <option value="<?php echo $type->id; ?>" <?php
                                if ($this->state->get('filter.type') == $type->id) : echo 'selected="selected"';
                                endif;
                                ?> >
                                <?php echo JText::_($type->value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
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
                        <th></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
    <?php foreach ($this->items as $item) : ?>
                        <tr class="order-line order-line-new">
                            <td><i><a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.edit&id=' . $item->id); ?>"><?php echo $item->name; ?></a></i></td>
                            <td class="ordertype">
                                <?php
                                if ($item->ordertype_id == 1):
                                    $classicontype = 'icon-cart';
                                elseif ($item->ordertype_id == 2):
                                    $classicontype = 'icon-lamp';
                                else:
                                    $classicontype = 'icon-edit2';
                                endif;
                                ?>
                                <i class="<?php echo $classicontype; ?>"></i> <?php echo JText::_($item->ordertype); ?>
                            </td>
                            <td class="ordercreated"><?php echo $item->created; ?></td>
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
                                    endif;
                                    ?>
                                    <span class="label <?php echo $classlabel; ?> "><?php echo JText::_($item->orderstate); ?></span>
        <?php endif; ?>
                            </td>
                            <td >
                                <?php if ($item->orderstate_id == 5 || $item->orderstate_id == 3): ?>
                                    <div class="btn-group">
                                    <a class="btn btn-info btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="icon-flag-2"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                    <?php
                                        $basket = new sdiBasket();
                                        $basket->loadOrder($item->id);
                                        foreach ($basket->extractions as $extraction) {
                                           echo '<li><a target="RAW" href="index.php?option=com_easysdi_shop&task=order.download&id='.$extraction->id.'&order='.$item->id.'">'.$extraction->name.'</a></li>';
                                        }
                                    ?>                                        
                                    </ul>
                                    </div>                                   
        <?php endif; ?>
                            </td>
                            <td>
        <?php if ($item->ordertype_id == 3 || $item->orderstate_id == 3): ?>
                                    <div class="btn-group pull-right">
                                        <a class="btn btn-success btn-small dropdown-toggle" data-toggle="dropdown" href="#">
            <?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_ACTIONS'); ?>
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
            <?php if ($item->ordertype_id == 3): ?>
                                                <li>
                                                    <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=basket.load&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_LOAD_DRAFT_INTO_BASKET'); ?></a>
                                                </li>
                                                <li>
                                                    <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.remove&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_REMOVE_DRAFT'); ?></a>
                                                </li>
                                            <?php endif; ?>
            <?php if ($item->orderstate_id == 3): ?>
                                                <li>
                                                    <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.archive&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_ARCHIVE_ORDER'); ?></a>
                                                </li>
            <?php endif; ?>
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
