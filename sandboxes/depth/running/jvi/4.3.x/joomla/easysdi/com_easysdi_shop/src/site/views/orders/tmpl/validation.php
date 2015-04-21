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
JHtml::_('formbehavior.chosen', 'select.chosen');
?>
<style type="text/css">
    .label-important{
        border-color: #b94a48;
    }
</style>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#tporganism').chosen({
            allow_single_deselect: true
        }).change(function(){jQuery('#criterias').submit()});
        
        jQuery('input[name^=filter_status]').on('click', function () {
            jQuery('#criterias').submit();
        });

        jQuery(document).on('click', 'a.reject_lnk', function () {
            jQuery('#modal-dialog-reject input[name=id]').val(jQuery(this).attr('rel'));
            jQuery('#modal-dialog-reject').modal('show');
            return false;
        });

        jQuery('textarea#reason').on('input propertychange', function () {
            jQuery('#order-reject button[type=submit]').prop('disabled', !(this.value.length > 20));
        });

        jQuery(document).on('click', '#order-reject button[type=submit]', function () {
            jQuery('#order-reject').submit();
        });
    });
</script>

<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERSVALIDATION'); ?></h1>
    <div class="well sdi-searchcriteria">
        <div class="row-fluid">
            <form id='criterias' class="form-search" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orders'); ?>" method="post">
                <div class="control-group">
                    <label><?php echo JText::_('COM_EASYSDI_SHOP_ORDERSVALIDATION_CHOOSE_ORGANISM'); ?></label>
                    <select id='tporganism' name='filter_organism' data-placeholder='<?php echo JText::_('COM_EASYSDI_SHOP_ORDERSVALIDATION_CHOOSE_ORGANISM_PLACEHOLDER'); ?>'>
                        <option></option>
                        <?php foreach($this->organisms as $organism):?>
                        <option value="<?php echo $organism->id;?>"<?php if($this->state->get('filter.organism')==$organism->id):?> selected='selected'<?php endif;?>><?php echo $organism->name;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="control-group">
                    <label><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERSVALIDATION_STATUS'); ?></label>
                    <fieldset class="radio btn-group btn-group-yesno">
                        <input type="radio" id="state0" name="filter_status" value="0"<?php if($this->state->get('filter.status')==0):?> checked='checked'<?php endif;?>>
                        <label for="state0" class="btn"><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERSVALIDATION_STATUS_DONE'); ?></label>
                        <input type="radio" id="state1" name="filter_status" value="1"<?php if($this->state->get('filter.status')==1):?> checked='checked'<?php endif;?>>
                        <label for="state1" class="btn"><?php echo JText::_('COM_EASYSDI_SHOP_TITLE_ORDERSVALIDATION_STATUS_TODO'); ?></label>
                    </fieldset>
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
                        <th colspan="2"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                        <tr class="order-line order-line-new">
                            <td class="ordercreatedby"><?php echo $item->created_by_name; ?></td>
                            <td><a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=validation&id=' . $item->id); ?>"><?php echo $item->name; ?></a></td>
                            <td class="ordercreated"><?php echo $item->created; ?></td>
                            <?php if($item->orderstate_id == 8 && $this->user->isOrganismManager($item->thirdparty_id)):?>
                                <td><a class="btn btn-success btn-small" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.validate&id=' . $item->id); ?>"><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_VALIDATE_ORDER'); ?></a></td>
                                <td><a class="reject_lnk btn btn-danger btn-small" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.reject&id=' . $item->id); ?>" rel='<?php echo $item->id ?>'><?php echo JText::_('COM_EASYSDI_SHOP_ORDERS_REJECT_ORDER'); ?></a></td>
                            <?php else:?>
                                <td colspan="2">&nbsp;</td>
                            <?php endif;?>
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
