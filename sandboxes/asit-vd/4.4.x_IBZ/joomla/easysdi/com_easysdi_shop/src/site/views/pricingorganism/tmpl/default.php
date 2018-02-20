<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');


$document = JFactory::getDocument();
$document->addScript(Juri::root(true) . '/components/com_easysdi_shop/helpers/helper.js?v=' . sdiFactory::getSdiFullVersion());
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        Joomla.submitbutton = function (task) {
            taskArray = task.split('.');
            jQuery('input[name=action]').val(taskArray[1]);
            Joomla.submitform(task, document.getElementById('adminForm'));
        };
        jQuery('input[type=radio]:disabled').each(function () {
            jQuery('label[for="' + jQuery(this).attr('id') + '"]').attr('disabled', 'disabled');
        });
    });
</script>

<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_PRICINGORGANISM_TITLE'); ?> : <?php echo $this->item->name; ?></h1>

    <div class="well">
        <h2><?php echo JText::_('COM_EASYSDI_SHOP_FORM_PRICINGORGANISM_FIELDSET_PRICING_PROFILE'); ?></h2>
        <?php if ($this->isPricingManager): ?>
            <div class="btn-group">
                <a class="btn btn-success" data-toggle="dropdown" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=pricingprofile.edit&id=0&organism=' . $this->item->id); ?>">
                    <i class="icon-white icon-plus-sign"></i> <?php echo JText::_('COM_EASYSDI_CORE_PRICING_PROFILE_NEW'); ?>
                </a>
            </div>
        <?php endif; ?>
        <?php if (count($this->item->profiles)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_NAME'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_FIXED_PRICE'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_SURFACE_PRICE'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_MIN_PRICE'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_MAX_PRICE'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_CATEGORIES_FREE'); ?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_COUNT_DIFFUSIONS'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->item->profiles as $profile): ?>
                        <tr>
                            <td>
                                <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=pricingprofile.edit&id=' . $profile->id . '&organism=' . $this->item->id); ?>"><?php echo $profile->name; ?></a>
                            </td>
                            <td><?php echo $profile->fixed_fee; ?></td>
                            <td><?php echo $profile->surface_rate; ?></td>
                            <td><?php echo $profile->min_fee; ?></td>
                            <td><?php echo $profile->max_fee; ?></td>
                            <td><?php echo (bool) $profile->free_category ? JText::_('JYES') : JText::_('JNO'); ?></td>
                            <td><?php echo $profile->count_diffusions; ?></td>
                            <td><?php if ($this->isPricingManager): ?><button type="button" class="btn btn-danger <?php echo $profile->count_diffusions > 0 ? 'disabled' : 'delete'; ?>" data-id="<?php echo $profile->id; ?>"><?php echo JText::_('COM_EASYSDI_SHOP_DELETE_ITEM'); ?></button><?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="well">
        <form class="form-horizontal form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

            <h2><?php echo JText::_('COM_EASYSDI_SHOP_FORM_PRICINGORGANISM_FIELDSET_FIXED_FEES'); ?></h2>
            <table>
                <?php foreach ($this->form->getFieldset('fixed_fees') as $field): ?>
                    <div class="control-group" id="<?php echo $field->fieldname; ?>">
                        <div class="control-label"><?php echo $field->label; ?></div>
                        <div class="controls"><?php echo $field->input; ?></div>
                    </div>
                <?php endforeach; ?>
            </table>

            <h2><?php echo JText::_('COM_EASYSDI_SHOP_FORM_PRICINGORGANISM_FIELDSET_INTERNAL_ORDERS'); ?></h2>
            <table>
                <?php foreach ($this->form->getFieldset('internal_orders') as $field): ?>
                    <div class="control-group" id="<?php echo $field->fieldname; ?>">
                        <div class="control-label"><?php echo $field->label; ?></div>
                        <div class="controls"><?php echo $field->input; ?></div>
                    </div>
                <?php endforeach; ?>
            </table>            

            <h2><?php echo JText::_('COM_EASYSDI_SHOP_FORM_PRICINGORGANISM_FIELDSET_CATEGORIES_REBATE'); ?></h2>
            <?php if (count($this->item->categories)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo JText::_('COM_EASYSDI_SHOP_TH_CATEGORIES'); ?></th>
                            <th><?php echo JText::_('COM_EASYSDI_SHOP_TH_PERCENT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->item->categories as $category): ?>
                            <tr>
                                <td><label><?php echo $category->name; ?></label></td>
                                <td><input type="text" name="jform[categories][<?php echo $category->id; ?>]" value="<?php echo $category->rebate; ?>"
                                           <?php if (!$this->isPricingManager): ?>readonly="readonly"<?php endif; ?>></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php foreach ($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>
            <input type="hidden" name="task" id="task" value="" />
            <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />

            <?php echo $this->getToolbar(); ?>
        </form>
    </div>
</div>

<!-- Delete modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo JText::_('COM_EASYSDI_CORE_DELETE_ITEM'); ?></h4>
            </div>
            <div id="deleteModalBody" class="modal-body">
                <?php echo JText::_('COM_EASYSDI_SHOP_FORM_MSG_PRICINGORGANISM_CONFIRM_DELETE_PROFILE'); ?>
                <span id="deleteModalChildrenList"></span>
            </div>
            <div class="modal-footer">
                <a href="#" id="btn_delete"><button type="button" class="btn btn-danger"><?php echo JText::_('COM_EASYSDI_CORE_DELETE_ITEM'); ?></button></a>
                <button type="button" class="btn btn-success" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>

