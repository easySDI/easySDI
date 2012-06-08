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
            
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
	            
				<li><?php echo $this->form->getLabel('guid'); ?>
				<?php echo $this->form->getInput('guid'); ?></li>
				
				<li><?php echo $this->form->getLabel('catid'); ?>
					<?php echo $this->form->getInput('catid'); ?></li>
	            
				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>
	           
				<li><?php echo $this->form->getLabel('state'); ?>
	            <?php echo $this->form->getInput('state'); ?></li>
	            
				<li><?php echo $this->form->getLabel('user_id'); ?>
				<?php echo $this->form->getInput('user_id'); ?></li>
	            
	            <li><?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?></li>
				
				<li><?php echo $this->form->getLabel('acronym'); ?>
				<?php echo $this->form->getInput('acronym'); ?></li>
	            
				<li><?php echo $this->form->getLabel('logo'); ?>
				<?php echo $this->form->getInput('logo'); ?></li>
	            
				<li><?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?></li>
	            
				<li><?php echo $this->form->getLabel('website'); ?>
				<?php echo $this->form->getInput('website'); ?></li>
	            
				<li><?php echo $this->form->getLabel('notificationrequesttreatment'); ?>
				<?php echo $this->form->getInput('notificationrequesttreatment'); ?></li>
				
				 <li><?php echo $this->form->getLabel('checked_out'); ?>
	            <?php echo $this->form->getInput('checked_out'); ?></li>
	            
	            <li><?php echo $this->form->getLabel('checked_out_time'); ?>
	            <?php echo $this->form->getInput('checked_out_time'); ?></li>
 			</ul>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'user-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_FIELDSET_CONTACTADDRESS'), 'contactaddress-details'); ?>
			<fieldset class="adminform">
				<ul class="adminformlist">
				<li><?php echo $this->contactaddressform->getLabel('id'); ?>
			<?php echo $this->contactaddressform->getInput('id'); ?></li>
            
			<li><?php echo $this->contactaddressform->getLabel('guid'); ?>
			<?php echo $this->contactaddressform->getInput('guid'); ?></li>
					
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'user-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_FIELDSET_BILLINGADDRESS'), 'billingaddress-details'); ?>
			<fieldset class="adminform">
				<ul class="adminformlist">
				<li><?php echo $this->billingaddressform->getLabel('id'); ?>
			<?php echo $this->billingaddressform->getInput('id'); ?></li>
            
			<li><?php echo $this->billingaddressform->getLabel('guid'); ?>
			<?php echo $this->billingaddressform->getInput('guid'); ?></li>
					
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'user-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_FIELDSET_DELIVRYADDRESS'), 'delivryaddress-details'); ?>
			<fieldset class="adminform">
				<ul class="adminformlist">
				<li><?php echo $this->delivryaddressform->getLabel('id'); ?>
			<?php echo $this->delivryaddressform->getInput('id'); ?></li>
            
			<li><?php echo $this->delivryaddressform->getLabel('guid'); ?>
			<?php echo $this->delivryaddressform->getInput('guid'); ?></li>
					
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