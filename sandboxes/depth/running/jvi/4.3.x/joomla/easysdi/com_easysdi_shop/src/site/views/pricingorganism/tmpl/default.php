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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');


$document = JFactory::getDocument();
$document->addScript('components/com_easysdi_shop/helpers/helper.js');

?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        Joomla.submitbutton = function(task){
            taskArray = task.split('.');
            jQuery('input[name=action]').val(taskArray[1]);
            Joomla.submitform(task, document.getElementById('adminForm'));
        };
    });
</script>

<div class="shop front-end-edit">
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_PRICINGORGANISM_TITLE'); ?> : <?php echo $this->item->name;?></h1>
    
    <div class="well">
    
        <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

            <h4><?php echo JText::_('COM_EASYSDI_SHOP_FORM_PRICINGORGANISM_FIELDSET_GLOBAL');?></h4>
            <table>
                <?php foreach($this->form->getFieldset('global') as $field): ?>
                <tr>
                    <td><?php echo $field->label; ?></td>
                    <td><?php echo $field->input; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <h4><?php echo JText::_('COM_EASYSDI_SHOP_FORM_PRICINGORGANISM_FIELDSET_CATEGORIES_REBATE');?></h4>
            <?php if(count($this->item->categories)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_TH_CATEGORIES');?></th>
                        <th><?php echo JText::_('COM_EASYSDI_SHOP_TH_PERCENT');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->item->categories as $category): ?>
                    <tr>
                        <td><label><?php echo $category->name; ?></label></td>
                        <td><input type="text" name="jform[categories][<?php echo $category->id;?>]" value="<?php echo $category->rebate;?>"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <?php foreach($this->form->getFieldset('hidden') as $field): ?>
                <?php echo $field->input; ?>
            <?php endforeach; ?>
            <input type="hidden" name="task" id="task" value="" />
            <input type="hidden" name="id" value="<?php echo $this->item->id;?>" />

            <?php echo $this->getToolbar(); ?>
        </form>
    </div>
    
    <div class="well">
        <h4><?php echo JText::_('COM_EASYSDI_SHOP_FORM_PRICINGORGANISM_FIELDSET_PRICING_PROFILE');?></h4>
        
        <div class="btn-group">
            <a class="btn btn-success" data-toggle="dropdown" href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=pricingprofile.edit&id=0&organism=' . $this->item->id); ?>">
                <i class="icon-white icon-plus-sign"></i> <?php echo JText::_('COM_EASYSDI_CORE_PRICING_PROFILE_NEW'); ?>
            </a>
        </div>
        
        <?php if(count($this->item->profiles)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_NAME');?></th>
                    <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_FIXED_PRICE');?></th>
                    <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_SURFACE_PRICE');?></th>
                    <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_MIN_PRICE');?></th>
                    <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_MAX_PRICE');?></th>
                    <th><?php echo JText::_('COM_EASYSDI_SHOP_FOR_LBL_PRICING_PROFILE_CATEGORIES_FREE');?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($this->item->profiles as $profile): ?>
                <tr>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=pricingprofile.edit&id='.$profile->id.'&organism=' . $this->item->id); ?>"><?php echo $profile->name;?></a>
                    </td>
                    <td><?php echo $profile->fixed_fee;?></td>
                    <td><?php echo $profile->surface_rate;?></td>
                    <td><?php echo $profile->min_fee;?></td>
                    <td><?php echo $profile->max_fee;?></td>
                    <td><?php echo (bool)$profile->free_category ? JText::_('YES') : JText::_('NO');?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php endif;?>
    </div>
</div>


