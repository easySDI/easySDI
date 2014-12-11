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
        border-color: #b94a48;
    }
</style>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('filter_status').on('change', function(){
            jQuery('#criterias').submit();
        });
        
        jQuery('#reset-btn').on('click', function(){
            jQuery('#filter_search').val('');
            jQuery('#criterias').submit();
        });
        
        jQuery(document).on('click', 'a.reject_lnk', function(){
            jQuery('#modal-dialog-reject input[name=id]').val(jQuery(this).attr('rel'));
            jQuery('#modal-dialog-reject').modal('show');
            return false;
        });
        
        jQuery('textarea#reason').on('input propertychange', function(){
            jQuery('#order-reject button[type=submit]').prop('disabled', !(this.value.length>20));
        });
        
        jQuery(document).on('click', '#order-reject button[type=submit]', function(){
            jQuery('#order-reject').submit();
        });
    });
</script>

<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'); ?> validation</h1>
    <div class="well">
        <div class="row-fluid">
            <form id='criterias' class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orders'); ?>" method="post">
                <div class="btn-toolbar">
                    <div class="btn-group pull-left">
                        <select id="filter_status" name="filter_status" class="inputbox">
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
                            <button class="btn hasTooltip" type="button" title="Effacer" id='reset-btn'><i class="icon-remove"></i></button>
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
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED_BY') ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_NAME') ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED') ?></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
    <?php foreach ($this->items as $item) : ?>
                        <tr class="order-line order-line-new">
                            <td class="ordercreatedby"><?php echo $item->created_by_name; ?></td>
                            <td><i><?php echo $item->name; ?></i></td>
                            <td class="ordercreated"><?php echo $item->created; ?></td>
                            <td class="orderstate"><?php if($item->ordertype_id != 3):?>
                                <span class="label <?php
                                    switch($item->orderstate_id){
                                        case 3: echo 'label-success';break;
                                        case 4: echo 'label-warning';break;
                                        case 5: echo 'label-info';break;
                                        case 6: echo 'label-inverse';break;
                                        case 8: echo 'label-warning';break;
                                        case 9: echo 'label-important';break;
                                    }?>"><?php echo JText::_($item->orderstate); ?></span>
                            <?php endif; ?></td>
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
                                                <a  href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.reject&id=' . $item->id); ?>" rel='<?php echo $item->id ?>' class='reject_lnk'><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_REJECT_ORDER'); ?></a>
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
    
    
    <!-- MODAL -->
    <div id="modal-dialog-reject" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form id='order-reject' action='<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.reject'); ?>'>
            <input type="hidden" name="task" value="order.reject" />
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="option" value="com_easysdi_shop" />
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo JText::_("COM_EASYSDI_SHOP_ORDER_REJECT_DIALOG_HEADER") ?></h3>
            </div>
            <div class="modal-body">
                <p><?php echo JText::_("COM_EASYSDI_SHOP_ORDER_REJECT_DIALOG_BODY") ?></p>
                <p>
                    <label for="reason"><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_REMARK'); ?>:</label>
                    <textarea id="reason" name="reason"></textarea>
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("COM_EASYSDI_SHOP_ORDER_REJECT_MODAL_BTN_CANCEL") ?></button>
                <button type='submit' class="btn btn-primary" data-dismiss="modal" aria-hidden="true" disabled="disabled"><?php echo JText::_("COM_EASYSDI_SHOP_ORDER_REJECT_MODAL_BTN_REJECT") ?></button>
            </div>
        </form>
    </div>
</div>
