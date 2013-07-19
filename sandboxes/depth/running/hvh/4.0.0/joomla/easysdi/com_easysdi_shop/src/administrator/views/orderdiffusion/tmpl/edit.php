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
        
	js('input:hidden.order_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('order_idhidden')){
			js('#jform_order_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_order_id").trigger("liszt:updated");
	js('input:hidden.diffusion_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('diffusion_idhidden')){
			js('#jform_diffusion_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_diffusion_id").trigger("liszt:updated");
    });
    
    Joomla.submitbutton = function(task)
    {
        if(task == 'orderdiffusion.cancel'){
            Joomla.submitform(task, document.getElementById('orderdiffusion-form'));
        }
        else{
            
            if (task != 'orderdiffusion.cancel' && document.formvalidator.isValid(document.id('orderdiffusion-form'))) {
                
                Joomla.submitform(task, document.getElementById('orderdiffusion-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="orderdiffusion-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">

                			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('order_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('order_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->order_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="order_id" name="jform[order_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('diffusion_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('diffusion_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->diffusion_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="diffusion_id" name="jform[diffusion_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('productstate_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('productstate_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('remark'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('remark'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('fee'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('fee'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('completed'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('completed'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('file'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('file'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('size'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('size'); ?></div>
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