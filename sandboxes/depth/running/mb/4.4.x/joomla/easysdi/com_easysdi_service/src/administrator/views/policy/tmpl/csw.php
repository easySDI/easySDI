<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(JURI::root(true) .'/components/com_easysdi_core/libraries/easysdi/view/view.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_service/views/policy/tmpl/policy.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_service/views/policy/tmpl/csw.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript(JURI::root(true) .'/components/com_easysdi_core/libraries/OpenLayers-2.13.1/OpenLayers.js' );
$document->addScript(JURI::root(true) .'/components/com_easysdi_core/libraries/proj4js-1.1.0/lib/proj4js-combined.js' );
JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
JText::script('COM_EASYSDI_SERVICE_POLICY_CSW_BTN_DELETE_EXCLUDED_ATTRIBUTE');

?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=policy&layout=csw&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="policy-form" class="form-validate">
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_SERVICE_TAB_NEW_POLICY') : JText::sprintf('COM_EASYSDI_SERVICE_TAB_EDIT_POLICY', $this->item->id); ?></a></li>
				<li><a href="#restrictions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_TAB_FILTER');?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_TAB_PUBLISHING');?></a></li>
				<?php if ($this->canDo->get('core.admin')): ?>
					<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_TAB_RULES');?></a></li>
				<?php endif; ?>
			</ul>
			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="details">
					<fieldset>
					<legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_DETAILS' );?></legend>
						<?php foreach($this->form->getFieldset('policy') as $field): 
						?> 
							<div class="control-group" id="<?php echo $field->fieldname;?>">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
						<?php endforeach; ?>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('allowedoperation_csw'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('allowedoperation_csw'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
						</div>
					</fieldset>
					<fieldset>
					<legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_AUTORISATIONS' );?></legend>
						<?php foreach($this->form->getFieldset('csw_policy') as $field): 
						?> 
							<div class="control-group" id="<?php echo $field->fieldname;?>">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
						<?php endforeach; ?>
					</fieldset>
					
					<div class="control-group">
					<?php 
					foreach($this->form->getFieldset('hidden') as $field):
					?> 
						<div class="controls"><?php echo $field->input; ?></div>
					<?php endforeach; ?>
					</div>
				</div>
				
				<div class="tab-pane" id="restrictions">
					<fieldset>
						<legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_GEOGRAPHIC_FILTER' );?></legend>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('srssource'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('srssource'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo JText::_( 'COM_EASYSDI_SERVICE_FORM_LBL_POLICY_BBOX' ); ?></div>
							<div class="controls">
								<div class="row-fluid">
									<div class="controls span3 offset3"><?php echo $this->form->getInput('maxy'); ?></div>
								</div>
								<div class="row-fluid">
									<div class="controls span3"><?php echo $this->form->getInput('minx'); ?></div>
									<div class="controls span3 offset3"><?php echo $this->form->getInput('maxx'); ?></div>
								</div>
								<div class="row-fluid">
									<div class="controls span3 offset3"><?php echo $this->form->getInput('miny'); ?></div>
								</div>
							</div>
						</div>
						
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_EXCLUDED_ATTRIBUTE' );?></legend>
						<div id="div_excluded_attributes">
							<?php
								$policy_id = (!empty($this->item->id))?$this->item->id:-1;
								$db = JFactory::getDbo();
                                                                $query = $db->getQuery(true);
                                                                $query->select('path');
                                                                $query->from('#__sdi_excludedattribute');
                                                                $query->where('policy_id = ' . (int)$policy_id);
                                                                
								$db->setQuery($query);
								$db->execute();
								$paths = $db->loadColumn();
								$path_count = 0;
								foreach ($paths as $path) {
									echo '<div class="div_ea_' . $path_count . ' input-xxlarge">
											<input type="text" name="excluded_attribute[' . $path_count . ']" class="span10" value="'.$path.'" />
											<span class="btn btn-danger btn-small btn_ea_delete" onClick="onDeleteExcludedAttribute(' .$path_count. ');return false;"><i class="icon-white icon-remove"></i></span>
											<br /><br />
										</div>';
									$path_count++;
								}
							?>
						</div>
						<button class="btn " data-count="<?php echo $path_count; ?>" id="btn_add_excluded_attribute" onClick="onAddExcludedAttribute();return false;">
							<i class="icon-white icon-pencil"></i> <?php echo JText::_('COM_EASYSDI_SERVICE_CSW_BTN_ADD_EXCLUDED_ATTRIBUTE');?>
						</button>
					</fieldset>
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
			
		</div>
		<!-- Begin Sidebar -->
		<div class="span2">
			<h4><?php echo JText::_('JDETAILS');?></h4>
			<hr />
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="control-group">
						<div class="controls">
							<?php echo $this->form->getValue('name'); ?>
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
	
	<input type="hidden" name="layout" id="layout" value="csw" />
	<input type="hidden" name="vs_id" id="vs_id" value="<?php echo JRequest::getVar('virtualservice_id',null); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>