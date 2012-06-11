<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// Import CSS
$document = &JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'user.cancel' || document.formvalidator.isValid(document.id('user-form'))) {
			Joomla.submitform(task, document.getElementById('user-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="user-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYSDI_CORE_LEGEND_USER'); ?></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('details') as $field): ?>
					<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'user-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_FIELDSET_CONTACTADDRESS'), 'contactaddress-details'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('contactaddress') as $field): ?>
					<?php $property = substr($field->id, 14);
					if($property == 'addresstype_id')
						$defaultvalue = '1';
					else if($property == 'user_id')
						$defaultvalue = $this->item->id;
					else
						$defaultvalue = $this->contactitem->$property;
					?>
					<li><?php echo $field->label; ?>
					<?php echo $this->form->getInput(substr($field->id, 6), null,$defaultvalue); ?></li>
					</li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_FIELDSET_BILLINGADDRESS'), 'billingaddress-details'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('billingaddress') as $field): ?>
					<?php $property = substr($field->id, 14);
					if($property == 'addresstype_id')
						$defaultvalue = '2';
					else if($property == 'user_id')
						$defaultvalue = $this->item->id;
					else
						$defaultvalue = $this->contactitem->$property;
					?>
					<li><?php echo $field->label; ?>
					<?php echo $this->form->getInput(substr($field->id, 6), null,$defaultvalue); ?></li>
					</li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_FIELDSET_DELIVRYADDRESS'), 'delivryaddress-details'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('delivryaddress') as $field): ?>
					<?php $property = substr($field->id, 14);
					if($property == 'addresstype_id')
						$defaultvalue = '3';
					else if($property == 'user_id')
						$defaultvalue = $this->item->id;
					else
						$defaultvalue = $this->contactitem->$property;
					?>
					<li><?php echo $field->label; ?>
					<?php echo $this->form->getInput(substr($field->id, 6), null,$defaultvalue); ?></li>
					</li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('created_by'); ?>
					<?php echo $this->form->getInput('created_by'); ?></li>
		            
					<li><?php echo $this->form->getLabel('created'); ?>
					<?php echo $this->form->getInput('created'); ?></li>
		
		            <?php if ($this->item->modified_by) : ?>
						<li><?php echo $this->form->getLabel('modified_by'); ?>
						<?php echo $this->form->getInput('modified_by'); ?></li>
			            
						<li><?php echo $this->form->getLabel('modified'); ?>
						<?php echo $this->form->getInput('modified'); ?></li>
					<?php endif; ?>
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	
	<div class="clr"></div>
	
		<div class="width-100 fltlft">
			<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

				<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_FIELDSET_RULES'), 'access-rules'); ?>
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>

			<?php echo JHtml::_('sliders.end'); ?>
		</div>
	
    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>