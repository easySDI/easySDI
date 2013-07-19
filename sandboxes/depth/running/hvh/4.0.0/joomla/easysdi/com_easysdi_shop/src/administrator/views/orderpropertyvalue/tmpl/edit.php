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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_shop/assets/css/easysdi_shop.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function(){
        
	js('input:hidden.orderdiffusion_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('orderdiffusion_idhidden')){
			js('#jform_orderdiffusion_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_orderdiffusion_id").trigger("liszt:updated");
	js('input:hidden.property_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('property_idhidden')){
			js('#jform_property_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_property_id").trigger("liszt:updated");
	js('input:hidden.propertyvalue_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('propertyvalue_idhidden')){
			js('#jform_propertyvalue_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_propertyvalue_id").trigger("liszt:updated");
    });
    
    Joomla.submitbutton = function(task)
    {
        if(task == 'orderpropertyvalue.cancel'){
            Joomla.submitform(task, document.getElementById('orderpropertyvalue-form'));
        }
        else{
            
            if (task != 'orderpropertyvalue.cancel' && document.formvalidator.isValid(document.id('orderpropertyvalue-form'))) {
                
                Joomla.submitform(task, document.getElementById('orderpropertyvalue-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="orderpropertyvalue-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">

                			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('orderdiffusion_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('orderdiffusion_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->orderdiffusion_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="orderdiffusion_id" name="jform[orderdiffusion_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('property_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('property_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->property_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="property_id" name="jform[property_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('propertyvalue_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('propertyvalue_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->propertyvalue_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="propertyvalue_id" name="jform[propertyvalue_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('propertyvalue'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('propertyvalue'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>


            </fieldset>
        </div>

        

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>