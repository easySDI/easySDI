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
		if (task == 'maplayer.cancel' || document.formvalidator.isValid(document.id('maplayer-form'))) {
			Joomla.submitform(task, document.getElementById('maplayer-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_easysdi_map&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="maplayer-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_EASYSDI_MAP_LEGEND_MAPLAYER'); ?>
			</legend>
			<ul class="adminformlist">


				<li><?php echo $this->form->getLabel('id'); ?> <?php echo $this->form->getInput('id'); ?>
				</li>


				<li><?php echo $this->form->getLabel('created_by'); ?> <?php echo $this->form->getInput('created_by'); ?>
				</li>


				<li><?php echo $this->form->getLabel('created'); ?> <?php echo $this->form->getInput('created'); ?>
				</li>


				<li><?php echo $this->form->getLabel('modified_by'); ?> <?php echo $this->form->getInput('modified_by'); ?>
				</li>


				<li><?php echo $this->form->getLabel('modified'); ?> <?php echo $this->form->getInput('modified'); ?>
				</li>


				<li><?php echo $this->form->getLabel('name'); ?> <?php echo $this->form->getInput('name'); ?>
				</li>


				<li><?php echo $this->form->getLabel('maplayergroup_id'); ?> <?php echo $this->form->getInput('maplayergroup_id'); ?>
				</li>


				<li><?php echo $this->form->getLabel('physicalservice_id'); ?> <?php echo $this->form->getInput('physicalservice_id'); ?>
				</li>


				<li><?php echo $this->form->getLabel('virtualservice_id'); ?> <?php echo $this->form->getInput('virtualservice_id'); ?>
				</li>


				<li><?php echo $this->form->getLabel('layername'); ?> <?php echo $this->form->getInput('layername'); ?>
				</li>


				<li><?php echo $this->form->getLabel('ispublished'); ?> <?php echo $this->form->getInput('ispublished'); ?>
				</li>


				<li><?php echo $this->form->getLabel('istiled'); ?> <?php echo $this->form->getInput('istiled'); ?>
				</li>


				<li><?php echo $this->form->getLabel('isdefaultvisible'); ?> <?php echo $this->form->getInput('isdefaultvisible'); ?>
				</li>


				<li><?php echo $this->form->getLabel('opacity'); ?> <?php echo $this->form->getInput('opacity'); ?>
				</li>


				<li><?php echo $this->form->getLabel('metadatalink'); ?>
					<div class="clr"></div> <?php echo $this->form->getInput('metadatalink'); ?>
				</li>



				<li><?php echo $this->form->getLabel('state'); ?> <?php echo $this->form->getInput('state'); ?>
				</li>
				<li><?php echo $this->form->getLabel('checked_out'); ?> <?php echo $this->form->getInput('checked_out'); ?>
				</li>
				<li><?php echo $this->form->getLabel('checked_out_time'); ?> <?php echo $this->form->getInput('checked_out_time'); ?>
				</li>

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
