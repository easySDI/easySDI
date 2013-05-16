<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');


?>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=virtualservice&id='.JRequest::getVar('id',null)); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_CONNECTOR_CHOICE' );?></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('connector_type') as $field): ?>
					
					<li><?php echo $field->input;?></li>
				<?php endforeach; ?>
				<script>
					var obj = document.getElementById('jform_sys_serviceconnector_id');
					var servicetype = '<?php echo JRequest::getVar('layout', null); ?>';
					for (var i = 0; i < obj.options.length; i++) {
						if (servicetype == obj.options[i].text) {
							obj.selectedIndex = i;
							break;
						}
					}
					obj.onchange = function () {
						window.location = "<?php echo html_entity_decode(JRoute::_('index.php?option=com_easysdi_service&view=virtualservice&id='.JRequest::getVar('id',null).'&layout=')); ?>" + obj.options[obj.selectedIndex].text;
					};
				</script>
			</ul>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_SERVICE_LIST' );?><span style="color:red;"> *</span></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('physical_service') as $field): ?>
					
					<li><?php echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_INFOS' );?></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('csw') as $field): ?>
					
					<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_METADATA' );?></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('metadata') as $field): ?>
					
					<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
			<br /><br />
			<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_METADATA_CONTACT' );?></legend>
				<ul class="adminformlist">
					<?php foreach($this->form->getFieldset('contact') as $field): ?>
						<li><?php
							if ('jform[city]' != $field->name)
								echo $field->label;
							echo $field->input;
						?></li>
					<?php endforeach; ?>
					<?php foreach($this->form->getFieldset('contact_csw') as $field): ?>
						<li><?php 
								echo $field->label;
								echo $field->input;
						?></li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_EXCEPTION_MANAGEMENT' );?><span style="color:red;"> *</span></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('exceptionlevel_id') as $field): ?>
					
					<li><?php echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_FORM_LBL_VIRTUALSERVICE_XSLTFILENAME' );?></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('xsltfilename') as $field): ?>
					
					<li><?php echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_LOG_CONFIGURATION' );?></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('log_config') as $field): ?>
					
					<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
	<div class="width-100 fltlft">
		<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_SERVICE_FIELDSET_RULES'), 'access-rules'); ?>
			<fieldset class="panelform">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<input type="hidden" name="layout" id="layout" value="wms" />
	<input type="hidden" name="task" value="<?php echo JRequest::getCmd('task');?>" />
	<input type="hidden" name="previoustask" value="<?php echo JRequest::getCmd('task');?>" />

	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>

	<style type="text/css">
		/* Temporary fix for drifting editor fields */
		.adminformlist li {
		clear: both;
		}
	</style>
</form>