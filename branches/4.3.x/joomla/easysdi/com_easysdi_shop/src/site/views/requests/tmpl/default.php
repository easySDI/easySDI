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
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_REQUESTS'); ?></h1>
    <div class="well sdi-searchcriteria">
        <div class="row-fluid">
            <form class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=requests'); ?>" method="post">
                <div class="btn-group pull-right">
                    <?php if(count($this->organisms)>1):?>
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
                    <?php endif;?>
                    
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
                    <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>
                    <th></th>
                    <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>


                </tr>
            </thead>

            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr class="order-line order-line-new">
                        <td><i><a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=request.edit&id=' . $item->id); ?>"><?php echo $item->name; ?></a></i></td>
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
