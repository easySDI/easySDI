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
    <h1><?php echo JText::_('COM_EASYSDI_SHOP_PRICINGORGANISM_TITLE'); ?> :</h1>
    
    <div class="well">
    
        <form class="form-inline form-validate" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
            
            <?php foreach($this->form->getFieldsets() as $fieldset): ?>
                <?php if(isset($fieldset->label)):?><h4><?php echo JText::_($fieldset->label);?></h4><?php endif;?>
                <table>
                    <?php if($fieldset->name == 'free_categories'):?>
                        <thead>
                            <tr>
                                <th><?php echo JText::_('COM_EASYSDI_SHOP_PRICINGPROFILE_TH_CATEGORIES');?></th>
                                <th><?php echo JText::_('COM_EASYSDI_SHOP_PRICINGPROFILE_TH_IS_FREE');?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($this->item->categories as $category):?>
                            <tr>
                                <td><?php echo $category->name; ?></td>
                                <td>
                                    <input type="radio" name="jform[categories][<?php echo $category->id;?>]" value="1" <?php if($category->isFree>0):?>checked<?php endif;?>><label><?php echo JText::_('YES'); ?></label>
                                    <input type="radio" name="jform[categories][<?php echo $category->id;?>]" value="0" <?php if($category->isFree==0):?>checked<?php endif;?>><label><?php echo JText::_('NO'); ?></label>
                                </td>
                            </tr>

                        <?php endforeach;?>
                        </tbody></table>
                    <?php break; endif;?>
                    
                    <?php foreach($this->form->getFieldset($fieldset->name) as $field): ?>
                    <tr>
                        <td><?php echo $field->label; ?></td>
                        <td><?php echo $field->input; ?> <?php echo $field->name=="jform[name]" ? "" : ($field->name=="jform[surface_rate]" ? "km2" : $this->paramsarray["currency"]); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>
                
            <input type="hidden" name="task" id="task" value="" />
            <input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
            <input type="hidden" name="organism_id" value="<?php echo $this->state->get('pricingprofile.organism_id');?>" />

            <?php echo $this->getToolbar(); ?>
        </form>
    </div>
</div>


