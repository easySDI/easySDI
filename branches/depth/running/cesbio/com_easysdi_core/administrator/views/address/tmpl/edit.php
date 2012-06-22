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
		if (task == 'address.cancel' || document.formvalidator.isValid(document.id('address-form'))) {
			Joomla.submitform(task, document.getElementById('address-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="address-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYSDI_CORE_LEGEND_ADDRESS'); ?></legend>
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

            
			<li><?php echo $this->form->getLabel('organismcomplement'); ?>
			<?php echo $this->form->getInput('organismcomplement'); ?></li>

            
			<li><?php echo $this->form->getLabel('organism'); ?>
			<?php echo $this->form->getInput('organism'); ?></li>

            
			<li><?php echo $this->form->getLabel('civility'); ?>
			<?php echo $this->form->getInput('civility'); ?></li>

            
			<li><?php echo $this->form->getLabel('firstname'); ?>
			<?php echo $this->form->getInput('firstname'); ?></li>

            
			<li><?php echo $this->form->getLabel('lastname'); ?>
			<?php echo $this->form->getInput('lastname'); ?></li>

            
			<li><?php echo $this->form->getLabel('function'); ?>
			<?php echo $this->form->getInput('function'); ?></li>

            
			<li><?php echo $this->form->getLabel('address'); ?>
			<?php echo $this->form->getInput('address'); ?></li>

            
			<li><?php echo $this->form->getLabel('addresscomplement'); ?>
			<?php echo $this->form->getInput('addresscomplement'); ?></li>

            
			<li><?php echo $this->form->getLabel('postalcode'); ?>
			<?php echo $this->form->getInput('postalcode'); ?></li>

            
			<li><?php echo $this->form->getLabel('postalbox'); ?>
			<?php echo $this->form->getInput('postalbox'); ?></li>

            
			<li><?php echo $this->form->getLabel('locality'); ?>
			<?php echo $this->form->getInput('locality'); ?></li>

            
			<li><?php echo $this->form->getLabel('country'); ?>
			<?php echo $this->form->getInput('country'); ?></li>

            
			<li><?php echo $this->form->getLabel('phone'); ?>
			<?php echo $this->form->getInput('phone'); ?></li>

            
			<li><?php echo $this->form->getLabel('mobile'); ?>
			<?php echo $this->form->getInput('mobile'); ?></li>

            
			<li><?php echo $this->form->getLabel('fax'); ?>
			<?php echo $this->form->getInput('fax'); ?></li>

            
			<li><?php echo $this->form->getLabel('email'); ?>
			<?php echo $this->form->getInput('email'); ?></li>

            
			<li><?php echo $this->form->getLabel('sameascontact'); ?>
			<?php echo $this->form->getInput('sameascontact'); ?></li>

            

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