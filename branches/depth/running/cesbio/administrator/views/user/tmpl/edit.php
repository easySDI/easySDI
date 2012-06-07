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
                    <?php echo $this->form->getInput('state'); ?></li><li><?php echo $this->form->getLabel('checked_out'); ?>
                    <?php echo $this->form->getInput('checked_out'); ?></li><li><?php echo $this->form->getLabel('checked_out_time'); ?>
                    <?php echo $this->form->getInput('checked_out_time'); ?></li>

            </ul>

            
			<li><?php echo $this->form->getLabel('user_id'); ?>
			<?php echo $this->form->getInput('user_id'); ?></li>

            
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

            
			
            
            <li><?php echo $this->form->getLabel('created_by'); ?>
			<?php echo $this->form->getInput('created_by'); ?></li>

            
			<li><?php echo $this->form->getLabel('created'); ?>
			<?php echo $this->form->getInput('created'); ?></li>

            
			<li><?php echo $this->form->getLabel('modified_by'); ?>
			<?php echo $this->form->getInput('modified_by'); ?></li>

            
			<li><?php echo $this->form->getLabel('modified'); ?>
			<?php echo $this->form->getInput('modified'); ?></li>
		</fieldset>
	</div>


	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>

    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>