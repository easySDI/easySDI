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
    });
</script>

<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_PRICINGPROFILE_ITEM_TITLE') . ' : ' . $this->item->name; ?></h1>

    <div class="well">

        <form class="form-horizontal form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
            <?php foreach ($this->form->getFieldsets() as $fieldset): ?>
                <?php if (isset($fieldset->label) && $fieldset->label != ''): ?><h2><?php echo JText::_($fieldset->label); ?></h2><?php endif; ?>

                <?php if ($fieldset->name == 'free_categories'): ?>
                    <table class="table table-striped shop-free-categories-table">
                        <thead>
                            <tr>
                                <th class="shop-pricing-category-name-col"><?php echo JText::_('COM_EASYSDI_SHOP_PRICINGPROFILE_TH_CATEGORIES'); ?></th>
                                <th class="shop-pricing-category-free-toggle"><?php echo JText::_('COM_EASYSDI_SHOP_PRICINGPROFILE_TH_IS_FREE'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($this->item): ?>
                            <?php foreach ($this->item->categories as $category): ?>
                                <tr>
                                    <td class="shop-pricing-category-name-col"><?php echo $category->name; ?></td>
                                    <td class="shop-pricing-category-free-toggle">
                                        <fieldset id="jform_categories_<?php echo $category->id; ?>_free" class="radio btn-group btn-group-yesno">
                                            <input type="radio" id="jform_categories_<?php echo $category->id; ?>_free0" name="jform[categories][<?php echo $category->id; ?>]" value="0" <?php if ($category->isFree == 0): ?>checked="checked"<?php endif; ?> <?php if (!$this->isPricingManager): ?>disabled="disabled"<?php endif; ?>>
                                            <label for="jform_categories_<?php echo $category->id; ?>_free0" <?php if (!$this->isPricingManager): ?>disabled="disabled"<?php endif; ?>><?php echo JText::_('JNO'); ?></label>                                            
                                            <input type="radio" id="jform_categories_<?php echo $category->id; ?>_free1" name="jform[categories][<?php echo $category->id; ?>]" value="1" <?php if ($category->isFree > 0): ?>checked="checked"<?php endif; ?> <?php if (!$this->isPricingManager): ?>disabled="disabled"<?php endif; ?>>
                                            <label for="jform_categories_<?php echo $category->id; ?>_free1" <?php if (!$this->isPricingManager): ?>disabled="disabled"<?php endif; ?>><?php echo JText::_('JYES'); ?></label>
                                        </fieldset>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                                <?php endif ?>
                        </tbody>
                    </table>
                    <?php
                    break;
                endif;
                ?>

                <?php foreach ($this->form->getFieldset($fieldset->name) as $field): ?>
                    <div class="control-group" id="<?php echo $field->fieldname; ?>">
                        <div class="control-label"><?php echo $field->label; ?></div>
                        <div class="controls"><?php echo $field->input; ?>
                            <?php echo $field->name == "jform[name]" || $field->name == "jform[apply_vat]" ? "" : ($field->name == "jform[surface_rate]" ? " km2" : " ".$this->paramsarray["currency"]); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <input type="hidden" name="task" id="task" value="" />
            <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
            <input type="hidden" name="organism_id" value="<?php echo $this->state->get('pricingprofile.organism_id'); ?>" />

            <?php echo $this->getToolbar(); ?>
        </form>
    </div>
</div>


