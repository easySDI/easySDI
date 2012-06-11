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
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_FIELDSET_BILLINGADDRESS'), 'billingaddress-details'); ?>
			<fieldset class="adminform">
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('billing_id'); ?>
					<?php echo $this->form->getInput('billing_id', null,$this->billingitem->id); ?></li>
            
					<li><?php echo $this->form->getLabel('billing_guid'); ?>
					<?php echo $this->form->getInput('billing_guid', null,$this->billingitem->guid); ?></li>
					
					<li><?php echo $this->form->getLabel('billing_addresstype_id'); ?>
					<?php echo $this->form->getInput('billing_addresstype_id', null,2); ?></li>
					
					<li><?php echo $this->form->getLabel('billing_user_id'); ?>
					<?php echo $this->form->getInput('billing_user_id', null,$this->item->id); ?></li>
							
					<li><?php echo $this->form->getLabel('billing_organismcomplement'); ?>
					<?php echo $this->form->getInput('billing_organismcomplement', null,$this->billingitem->organismcomplement); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_organism'); ?>
					<?php  echo $this->form->getInput('billing_organism', null,$this->billingitem->organism); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_civility'); ?>
					<?php echo $this->form->getInput('billing_civility', null,$this->billingitem->civility); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_firstname'); ?>
					<?php echo $this->form->getInput('billing_firstname', null,$this->billingitem->firstname); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_lastname'); ?>
					<?php echo $this->form->getInput('billing_lastname', null,$this->billingitem->lastname); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_function'); ?>
					<?php echo $this->form->getInput('billing_function', null,$this->billingitem->function); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_address'); ?>
					<?php echo $this->form->getInput('billing_address', null,$this->billingitem->address); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_addresscomplement'); ?>
					<?php echo $this->form->getInput('billing_addresscomplement', null,$this->billingitem->addresscomplement); ?></li>
		
					<li><?php echo $this->form->getLabel('billing_postalcode'); ?>
					<?php echo $this->form->getInput('billing_postalcode', null,$this->billingitem->postalcode); ?></li>
		
					<li><?php echo $this->form->getLabel('billing_postalbox'); ?>
					<?php echo $this->form->getInput('billing_postalbox', null,$this->billingitem->postalbox); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_locality'); ?>
					<?php echo $this->form->getInput('billing_locality', null,$this->billingitem->locality); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_country'); ?>
					<?php echo $this->form->getInput('billing_country', null,$this->billingitem->country); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_phone'); ?>
					<?php echo $this->form->getInput('billing_phone', null,$this->billingitem->phone); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_mobile'); ?>
					<?php echo $this->form->getInput('billing_mobile', null,$this->billingitem->mobile); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_fax'); ?>
					<?php echo $this->form->getInput('billing_fax', null,$this->billingitem->fax); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_email'); ?>
					<?php echo $this->form->getInput('billing_email', null,$this->billingitem->email); ?></li>
		            
					<li><?php echo $this->form->getLabel('billing_sameascontact'); ?>
					<?php echo $this->form->getInput('billing_sameascontact', null,$this->billingitem->sameascontact); ?></li>
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	


	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'user-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
			<fieldset class="adminform">
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