<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_map
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

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_map/assets/css/easysdi_map.css');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'layer.cancel' || document.formvalidator.isValid(document.id('layer-form'))) {
			Joomla.submitform(task, document.getElementById('layer-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_map&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="layer-form" class="form-validate">
	<div class="row-fluid">
	<div class="span10 form-horizontal">
            <ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_MAP_TAB_NEW_GROUP') : JText::sprintf('COM_EASYSDI_MAP_TAB_EDIT_GROUP', $this->item->id); ?></a></li>
				<li><a href="#asOL" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_ASOL');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_PUBLISHING');?></a></li>
				<?php if ($this->canDo->get('core.admin')): ?>
				<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_MAP_TAB_RULES');?></a></li>
			<?php endif ?>
			</ul>
			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="details">
					<?php foreach($this->form->getFieldset('details') as $field): ?>
						<?php
						if($field->name=="jform[service_id]"){
							?>
							<div class="control-group">
								<div class="control-label"><?php  echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
							<fieldset class="adminform">
									<div class="control-group">
										<div class="control-label"><?php echo $this->form->getLabel('user'); ?></div>
										<div class="controls"><?php echo $this->form->getInput('user'); ?></div>
									</div>
									<div class="control-group">
										<div class="control-label"><?php echo $this->form->getLabel('password'); ?></div>
										<div class="controls"><?php echo $this->form->getInput('password'); ?></div>
									</div>
								
							</fieldset>
							<?php 
							continue;
						}
						if($field->name=="jform[layername]"){
							?>
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input;  echo $this->form->getField('getlayers')->input;?></div>
							</div>
							<?php 
							continue;
						}
						?>
						<div class="control-group">
							<div class="control-label"><?php echo $field->label; ?></div>
							<div class="controls"><?php echo $field->input; ?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="tab-pane" id="asOL">
						<div  class="control-group" id="WMTS-info">
							<?php echo JText::_("COM_EASYSDI_MAP_FORM_LBL_LAYER_WMTS_ASOL");?>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('asOL'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('asOL'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('asOLstyle'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('asOLstyle'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('asOLmatrixset'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('asOLmatrixset'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('asOLoptions'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('asOLoptions'); ?></div>
						</div>
				</div>
				<div class="tab-pane" id="publishing">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
					</div>
					<?php if ($this->item->modified_by) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
					</div>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
					</div>
					<?php endif; ?>
				</div>
				<?php if ($this->canDo->get('core.admin')): ?>
				<div class="tab-pane" id="permissions">
					<fieldset>
						<?php echo $this->form->getInput('rules'); ?>
					</fieldset>
				</div>
				<?php endif; ?>
			</div>
            <!-- End Tabs -->
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	
	<!-- Begin Sidebar -->
		<div class="span2">
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr />
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="control-group">
						<div class="controls">
							<?php echo $this->form->getValue('group'); ?>
						</div>
					</div>
					<?php
					if($this->canDo->get('core.edit.state'))
					{
						?>
						<div class="control-label">
							<?php echo $this->form->getLabel('state'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('state'); ?>
						</div>
						<?php 
					}
					?>
				</div>
	
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('access'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('access'); ?>
					</div>
				</div>
			</fieldset>
		</div>
		<!-- End Sidebar -->
	</div>
	<div id="progress">
		<img id="progress_image"  src="components/com_easysdi_service/assets/images/loader.gif" alt="">
	</div>
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_EASYSDI_MAP_LEGEND_LAYER'); ?>
			</legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('details') as $field): ?>
				<?php
				if($field->name=="jform[state]"){
					if($this->canDo->get('core.edit.state'))
					{
						?>
				<li><?php echo $field->label;echo $field->input;?></li>
				<?php
					}
					continue;
					} ?>
				<?php
				if($field->name=="jform[service_id]"){
					?>
					<li><?php echo $field->label;echo $field->input;  ?>
					<div class="width-50 fltrt" style="margin: -18px 0 0;">					
					<?php echo JHtml::_('sliders.start', 'au-sliders-'.$this->item->id,array('useCookie'=>0, 'startOffset'=>-1, 'startTransition'=>1)); ?>
							<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_MAP_LAYER_FIELDSET_AUTHENTICATION'), 'authentication'); ?>
							<fieldset class="adminform">
								<ul class="adminformlist">
									<li><?php echo $this->form->getLabel('user'); ?> <?php echo $this->form->getInput('user'); ?>
									</li>
					
									<li><?php echo $this->form->getLabel('password'); ?> <?php echo $this->form->getInput('password'); ?>
									</li>
								</ul>
							</fieldset>
							<?php echo JHtml::_('sliders.end'); ?>
					</div>
					</li>
					
					<?php 
					continue;
				}
				if($field->name=="jform[layername]"){
					?>
					<li><?php echo $field->label;echo $field->input;  echo $this->form->getField('getlayers')->input;?>
					</li>
					<?php 
					continue;
				}
				?>
				<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
				
				
			</ul>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'user-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_MAP_LAYER_FIELDSET_OPENLAYERS'), 'openlayers-options'); ?>
		<fieldset class="adminform">

		<div  id="WMTS-info">
			<?php echo JText::_("COM_EASYSDI_MAP_FORM_LBL_LAYER_WMTS_ASOL");?>
		</div>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('asOL'); ?> <?php echo $this->form->getInput('asOL'); ?>
				</li>
				<li><?php echo $this->form->getLabel('asOLstyle'); ?> <?php echo $this->form->getInput('asOLstyle'); ?>
				</li>
				<li><?php echo $this->form->getLabel('asOLmatrixset'); ?> <?php echo $this->form->getInput('asOLmatrixset'); ?>
				</li>
				<li><?php echo $this->form->getLabel('asOLoptions'); ?> <?php echo $this->form->getInput('asOLoptions'); ?>
				</li>
			</ul>
		</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'user-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
		<fieldset class="adminform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('created_by'); ?> <?php echo $this->form->getInput('created_by'); ?>
				</li>

				<li><?php echo $this->form->getLabel('created'); ?> <?php echo $this->form->getInput('created'); ?>
				</li>

				<?php if ($this->item->modified_by) : ?>
				<li><?php echo $this->form->getLabel('modified_by'); ?> <?php echo $this->form->getInput('modified_by'); ?>
				</li>

				<li><?php echo $this->form->getLabel('modified'); ?> <?php echo $this->form->getInput('modified'); ?>
				</li>
				<?php endif; ?>
			</ul>
		</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<div class="width-100 fltlft">
		<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

		<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_MAP_FIELDSET_RULES'), 'access-rules'); ?>
		<fieldset class="panelform">
			<?php echo $this->form->getLabel('rules'); ?>
			<?php echo $this->form->getInput('rules'); ?>
		</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
