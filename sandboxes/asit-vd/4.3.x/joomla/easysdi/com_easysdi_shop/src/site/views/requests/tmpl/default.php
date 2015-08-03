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
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_REQUESTS'); ?></h1>
    <div class="well sdi-searchcriteria">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=requests'); ?>" method="post">
                <div class="btn-group pull-right">
                    <?php if (count($this->organisms) > 1): ?>
                        <div id="filterorganism" >
                            <select id="filter_userorganism" name="filter_userorganism" onchange="this.form.submit();" class="inputbox">
                                <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_REQUESTS_ORGANISM_FILTER'); ?></option>
                                <?php foreach ($this->organisms as $organism): ?>
                                    <option value="<?php echo $organism->id; ?>" <?php
                            if ($this->state->get('filter.organism') == $organism->id) : echo 'selected="selected"';
                            endif;
                                    ?> ><?php echo $organism->name; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div id="filterstatus">                        
                        <select id="filter_status" name="filter_type" onchange="this.form.submit();" class="inputbox">
                            <option value="" ><?php echo JText::_('COM_EASYSDI_CORE_REQUESTS_TYPE_FILTER'); ?></option>
                            <?php foreach ($this->ordertype as $ordertype): ?>
                                <option value="<?php echo $ordertype->id; ?>" <?php
                            if ($this->state->get('filter.type') == $ordertype->id) : echo 'selected="selected"';
                            endif;
                                ?> >
                                        <?php echo JText::_($ordertype->value); ?></option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="filtersearch">
                        <label for="filter_search" class="element-invisible">Rechercher</label>
                        <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EASYSDI_CORE_REQUESTS_SEARCH_FILTER'); ?>" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_EASYSDI_CORE_RESOURCES_SEARCH_FILTER'); ?>" />
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

                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CLIENT') ?></th>
                        <th></th>
                        <th></th>



                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                        <tr class="order-line order-line-new">
                            <td class="ordercreated">
                                <span class="hasTip" title="<?php echo JHtml::date($item->created, JText::_('DATE_FORMAT_LC2')); ?>">
                                    <?php echo Easysdi_shopHelper::getRelativeTimeString(new Datetime($item->created)); ?>
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
                                <a class="btn btn-primary pull-right" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=request.edit&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_REQUESTS_REPLY') ?></a>
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
