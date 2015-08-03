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

JFactory::getDocument()->addScript('components/com_easysdi_shop/helpers/helper.js');
?>
<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'); ?></h1>
    <div class="well sdi-searchcriteria">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orders'); ?>" method="post">
                <div class="btn-toolbar">
                    <div class="btn-group pull-right">
                        <?php if (count($this->organisms) > 1): ?>
                            <div id="filterorganism" >
                                <select id="filter_organism" name="filter_organism" onchange="this.form.submit();" class="inputbox">
                                    <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_ORDERS_ORGANISM_FILTER'); ?></option>
                                    <?php foreach ($this->organisms as $organism): ?>
                                        <option value="<?php echo $organism->id; ?>" <?php
                                        $filterName = (!empty($this->parent)) ? 'filter.userorganism.children' : 'filter.userorganism';
                                        if ($this->state->get('filter.organism') == $organism->id) : echo 'selected="selected"';
                                        endif;
                                        ?> ><?php echo $organism->name; ?></option>
                                            <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div id="filtertype">
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

                        <div id="filterstatus">
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

                        <div id="filtersearch">
                            <label for="filter_search" class="element-invisible">Rechercher</label>
                            <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_ORDERS_SEARCH_FILTER'); ?>" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" />
                            <button class="btn hasTooltip" type="submit" title="Rechercher"><i class="icon-search"></i></button>
                            <button class="btn hasTooltip" type="button" title="Effacer" onclick="document.id('filter_search').value = '';
                                    this.form.submit();"><i class="icon-remove"></i></button>
                        </div>
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
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    foreach ($this->items as $item) :
                        $basket = new sdiBasket();
                        $basket->loadOrder($item->id);
                        ?>
                        <tr class="order-line order-line-new sdi-orderstate-<?php echo (preg_replace('/\s+/', '', $item->orderstate)); ?>">
                            <td class="ordercreated">
                                <span class="hasTip" title="<?php echo JHtml::date($item->created, JText::_('DATE_FORMAT_LC2')); ?>">
                                    <?php echo Easysdi_shopHelper::getRelativeTimeString(JFactory::getDate($item->created)); ?>
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
                            <td class="orderstate">
                                <?php
                                if ($item->ordertype_id != 3):

                                    $progressCount = 0;
                                    $statusCompl = '';
                                    //count finished products
                                    foreach ($basket->extractions as $extraction) {
                                        if ($extraction->productstate_id == 1)
                                            $progressCount++;
                                    }
                                    ?>
                                    <span class="label <?php
                                    switch ($item->orderstate_id) {
                                        case 3:
                                            echo 'label-success';
                                            if (count($basket->extractions) > 1) {
                                                $statusCompl = ' (' . $progressCount . '/' . count($basket->extractions) . ')';
                                            }
                                            break;
                                        case 4:
                                            echo 'label-warning';
                                            break;
                                        case 5:
                                            echo 'label-info';
                                            if (count($basket->extractions) > 1) {
                                                $statusCompl = ' (' . $progressCount . '/' . count($basket->extractions) . ')';
                                            }
                                            break;
                                        case 6:
                                            echo 'label-inverse';
                                            break;
                                    }
                                    ?>">
                                        <?php echo JText::_($item->orderstate); ?> <?php echo $statusCompl; ?></span>

                                <?php else: ?>
                                    <span class="label">
                                        <?php echo JText::_($item->ordertype); ?>
                                    </span>
                                <?php
                                endif;
                                ?>
                            </td>
                            <td>
                                <div class="pull-right">
                                    <?php if ($item->orderstate_id == 5 || $item->orderstate_id == 3): ?>                                    
                                        <?php
                                        $first = true;
                                        $basket = new sdiBasket();
                                        $basket->loadOrder($item->id);
                                        foreach ($basket->extractions as $extraction) {
                                            if ($extraction->productstate_id == 1):
                                                if ($first)://Create the dropdown menu to hold the download links
                                                    ?>
                                                    <div class="btn-group">
                                                        <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                                            <i class="icon-flag-2"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <?php
                                                            $first = false;
                                                        endif;
                                                        echo '<li><a target="RAW" href="index.php?option=com_easysdi_shop&task=order.download&id=' . $extraction->id . '&order=' . $item->id . '">' . $extraction->name . '</a></li>';
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
                                                <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=request.edit&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_OPEN'); ?></a>
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
                                            <?php if ($item->orderstate_id == Easysdi_shopHelper::ORDERSTATE_FINISH): ?>
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
        </div>
    </div>

    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
</div>

<?php echo Easysdi_shopHelper::getAddToBasketModal(); ?>