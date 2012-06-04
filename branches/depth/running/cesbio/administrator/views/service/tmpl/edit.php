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
		if (task == 'service.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYSDI_CORE_LEGEND_SERVICE'); ?></legend>
			<ul class="adminformlist">

            
			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>

            
			<li><?php echo $this->form->getLabel('guid'); ?>
			<?php echo $this->form->getInput('guid'); ?></li>

            
			<li><?php echo $this->form->getLabel('alias'); ?>
			<?php echo $this->form->getInput('alias'); ?></li>

            
			<li><?php echo $this->form->getLabel('created_by'); ?>
			<?php echo $this->form->getInput('created_by'); ?></li>

            
			<li><?php echo $this->form->getLabel('created'); ?>
			<?php echo $this->form->getInput('created'); ?></li>

            
			<li><?php echo $this->form->getLabel('modified_by'); ?>
			<?php echo $this->form->getInput('modified_by'); ?></li>

            
			<li><?php echo $this->form->getLabel('modified'); ?>
			<?php echo $this->form->getInput('modified'); ?></li>

            
			<li><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>

            
			<li><?php echo $this->form->getLabel('serviceconnector_id'); ?>
			<?php echo $this->form->getInput('serviceconnector_id'); ?></li>

            
			<li><?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?></li>

            
			<li><?php echo $this->form->getLabel('resourceauthentication_id'); ?>
			<?php echo $this->form->getInput('resourceauthentication_id'); ?></li>

            
			<li><?php echo $this->form->getLabel('resourceurl'); ?>
			<?php echo $this->form->getInput('resourceurl'); ?></li>

            
			<li><?php echo $this->form->getLabel('resourceusername'); ?>
			<?php echo $this->form->getInput('resourceusername'); ?></li>

            
			<li><?php echo $this->form->getLabel('resourcepassword'); ?>
			<?php echo $this->form->getInput('resourcepassword'); ?></li>

            
			<li><?php echo $this->form->getLabel('serviceauthentication_id'); ?>
			<?php echo $this->form->getInput('serviceauthentication_id'); ?></li>

            
			<li><?php echo $this->form->getLabel('serviceurl'); ?>
			<?php echo $this->form->getInput('serviceurl'); ?></li>

            
			<li><?php echo $this->form->getLabel('serviceusername'); ?>
			<?php echo $this->form->getInput('serviceusername'); ?></li>

            
			<li><?php echo $this->form->getLabel('servicepassword'); ?>
			<?php echo $this->form->getInput('servicepassword'); ?></li>

            
			<li><?php echo $this->form->getLabel('catid'); ?>
			<?php echo $this->form->getInput('catid'); ?></li>

            

            <li><?php echo $this->form->getLabel('state'); ?>
                    <?php echo $this->form->getInput('state'); ?></li><li><?php echo $this->form->getLabel('checked_out'); ?>
                    <?php echo $this->form->getInput('checked_out'); ?></li><li><?php echo $this->form->getLabel('checked_out_time'); ?>
                    <?php echo $this->form->getInput('checked_out_time'); ?></li>

            </ul>
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