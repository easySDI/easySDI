<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'physicalservice.cancel' || document.formvalidator.isValid(document.id('physicalservice-form'))) {
			Joomla.submitform(task, document.getElementById('physicalservice-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="physicalservice-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYSDI_SERVICE_LEGEND_PHYSICALSERVICE'); ?></legend>
			<ul class="adminformlist">

            XXX_LOOP_FORM_FIELDS_START_XXX
			<li><?php echo $this->form->getLabel('XXX_LOOP_FORM_FIELD_XXX'); ?>
			XXX_LOOP_FORM_FIELD_TEXT_XXX<?php echo $this->form->getInput('XXX_LOOP_FORM_FIELD_XXX'); ?></li>

            XXX_LOOP_FORM_FIELDS_END_XXX

            XXX_CORE_FIELDS_XXX

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