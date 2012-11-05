<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_map/assets/css/easysdi_map.css');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'context.cancel' || document.formvalidator.isValid(document.id('context-form'))) {
			Joomla.submitform(task, document.getElementById('context-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_map&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="context-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYSDI_MAP_LEGEND_CONTEXT'); ?></legend>
			<ul class="adminformlist">

            
			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>

            
			<li><?php echo $this->form->getLabel('created'); ?>
			<?php echo $this->form->getInput('created'); ?></li>

            
			<li><?php echo $this->form->getLabel('created_by'); ?>
			<?php echo $this->form->getInput('created_by'); ?></li>

            
			<li><?php echo $this->form->getLabel('modified_by'); ?>
			<?php echo $this->form->getInput('modified_by'); ?></li>

            
			<li><?php echo $this->form->getLabel('modified'); ?>
			<?php echo $this->form->getInput('modified'); ?></li>

            
			<li><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>

            
			<li><?php echo $this->form->getLabel('srs'); ?>
			<?php echo $this->form->getInput('srs'); ?></li>

            
			<li><?php echo $this->form->getLabel('unit_id'); ?>
			<?php echo $this->form->getInput('unit_id'); ?></li>

            
			<li><?php echo $this->form->getLabel('centercoordinates'); ?>
			<?php echo $this->form->getInput('centercoordinates'); ?></li>

            
			<li><?php echo $this->form->getLabel('maxresolution'); ?>
			<?php echo $this->form->getInput('maxresolution'); ?></li>

            
			<li><?php echo $this->form->getLabel('maxextent'); ?>
			<?php echo $this->form->getInput('maxextent'); ?></li>

            
			<li><?php echo $this->form->getLabel('abstract'); ?>
			<div class="clr"></div><?php echo $this->form->getInput('abstract'); ?></li>

            
			<li><?php echo $this->form->getLabel('access'); ?>
			<?php echo $this->form->getInput('access'); ?></li>

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

	<div class="width-100 fltlft">
		<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_MAP_FIELDSET_RULES'), 'access-rules'); ?>
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