<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
JHTML::_('script','system/multiselect.js',false,true);

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');
?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=virtualservice'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_SERVICE_TAB_NEW_SERVICE') : JText::sprintf('COM_EASYSDI_SERVICE_TAB_EDIT_SERVICE', $this->item->id); ?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_TAB_PUBLISHING');?></a></li>
				<?php if ($this->canDo->get('core.admin')): ?>
					<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_TAB_RULES');?></a></li>
				<?php endif ?>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="details">
					<?php foreach($this->form->getFieldset('connector_type') as $field): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label; ?></div>
							<div class="controls"><?php echo $field->input; ?></div>
						</div>
					<?php endforeach; ?>
					<script>
						var obj = document.getElementById('jform_sys_serviceconnector_id');
						obj.onchange = function () {
							window.location = "<?php echo html_entity_decode(JRoute::_('index.php?option=com_easysdi_service&view=virtualservice&layout=')); ?>" + obj.options[obj.selectedIndex].text;
						};
					</script>
				</div>
			</div>
		</div>
	</div>
	
	<input type="hidden" name="task" value="virtualservice.add" />
	<?php echo JHtml::_('form.token'); ?>
	</form>