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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');
$document->addScript('components/com_easysdi_service/views/policy/tmpl/policy.js');
$document->addScript('components/com_easysdi_service/views/policy/tmpl/wfs.js');
JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
JText::script('COM_EASYSDI_SERVICE_MODAL_ERROR');
JText::script('COM_EASYSDI_SERVICE_CONFIRM_DELETION');

function printSpatialPolicyForm ($data, $physicalServiceID = 0) {
	$db = JFactory::getDbo();
	$db->setQuery('
		SELECT *
		FROM #__sdi_sys_spatialoperator
		WHERE state = 1
		ORDER BY ordering;
	');
	$db->execute();
	$resultset = $db->loadObjectList();
	
	$wfs_spatialpolicy_id = (!empty($data->wfs_spatialpolicy_id)) ? $data->wfs_spatialpolicy_id : -1;
	
	if (0 == $physicalServiceID) {
		$query = '
			SELECT *
			FROM #__sdi_wfs_spatialpolicy
			WHERE id = \'' . $data->wfs_spatialpolicy_id . '\';
		';
	}
	else {
		$query = '
			SELECT wsp.*, psp.anyitem
			FROM #__sdi_wfs_spatialpolicy wsp
			JOIN #__sdi_physicalservice_policy psp
			ON wsp.id = psp.wfs_spatialpolicy_id
			WHERE psp.physicalservice_id = ' . $physicalServiceID . '
			AND psp.policy_id = ' . $data->id . ';
		';
	}
	$db->setQuery($query);
	$db->execute();
	$spatialpolicy = $db->loadObject();
	
	
	$html = '';
	$prefix = 'inherit';
	if (0 == $physicalServiceID) {
		$prefix .= '_policy[' . $wfs_spatialpolicy_id . ']';
		$html .= '
			<label class="checkbox">
				<input type="checkbox" name="' . $prefix . '[anyservice]" value="1" ' . ((1 == $data->anyservice)?'checked="checked"':'') . ' /><label for="' . $prefix . '[anyservice]">' . JText::_('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WFS_ANYSERVICE') . '</label>
			</label>
		';
	}
	else {
		$prefix .= '_server[' . $physicalServiceID . ']';
		$anyItem = (isset($spatialpolicy->anyitem))?$spatialpolicy->anyitem:1;
		$html .= '
			<label class="checkbox">
				<input type="checkbox" name="' . $prefix . '[anyitem]" value="1" ' . ((1 == $anyItem)?'checked="checked"':'') . ' /><label for="' . $prefix . '[anyitem]">' . JText::_('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WFS_ANYITEM') . '</label>
			</label>
		';
	}
	
	$html .= '	<br />
		<label for="' . $prefix . '[localgeographicfilter]">' . JText::_('COM_EASYSDI_SERVICE_WFS_LAYER_LOCAL_FILTER') . '</label>
		<textarea name="' . $prefix . '[localgeographicfilter]" rows="10" class="span12">' . ((isset($spatialpolicy->localgeographicfilter))?$spatialpolicy->localgeographicfilter:'') . '</textarea>
		<br />
		<label for="' . $prefix . '[remotegeographicfilter]">' . JText::_('COM_EASYSDI_SERVICE_WFS_LAYER_REMOTE_FILTER') . '</label>
		<textarea name="' . $prefix . '[remotegeographicfilter]" rows="10" class="span12">' . ((isset($spatialpolicy->remotegeographicfilter))?$spatialpolicy->remotegeographicfilter:'') . '</textarea>
		<br />
		<br />
		<br />
	';
	echo $html;
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=policy&layout=wfs&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="policy-form" class="form-validate">
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_SERVICE_TAB_NEW_POLICY') : JText::sprintf('COM_EASYSDI_SERVICE_TAB_EDIT_POLICY', $this->item->id); ?></a></li>
				<li><a href="#layers" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_LAYERS');?></a></li>
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
							<div class="control-label"><?php echo $this->form->getLabel('allowedoperation_wfs'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('allowedoperation_wfs'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
						</div>
					</fieldset>
					
					<div class="control-group">
					<?php foreach($this->form->getFieldset('wfs_policy_hidden') as $field):?> 
						<div class="controls"><?php echo $field->input; ?></div>
					<?php
					endforeach;
					foreach($this->form->getFieldset('hidden') as $field):
					?> 
						<div class="controls"><?php echo $field->input; ?></div>
					<?php endforeach; ?>
					</div>
				</div>
				
				<div class="tab-pane" id="layers">
					<?php if (empty($this->item->id)): ?>
						<?php echo JText::_('COM_EASYSDI_SERVICE_LAYER_NOT_DISPLAYABLE');?>
					<?php endif; ?>
					<?php if (!empty($this->item->id)): ?>
					<fieldset>
						<legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_INHERITANCE' );?></legend>
						
						<?php printSpatialPolicyForm($this->item); ?>
						
					</fieldset>
					<fieldset>
						<div class="accordion" id="ps_accordion">
							<?php foreach($this->item->physicalService as $ps):?>
								<div class="accordion-group">
								<div class="accordion-heading">
								  <a class="accordion-toggle" data-toggle="collapse" data-parent="#ps_accordion" href="#collapse_<?php echo $ps->id; ?>">
										<?php echo $ps->name . ' &mdash; ' . $ps->url; ?>
								  </a>
								</div>
								<div class="accordion-body collapse" id="collapse_<?php echo $ps->id; ?>">
								  <div class="accordion-inner">
										
										<?php printSpatialPolicyForm($this->item, $ps->id); ?>
										
										<table class="table table-striped" >
											<thead>
												<tr>
													<th>name</th>
													<th>description</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
											<?php foreach($ps->getLayerList() as $layer):?>
												<tr>
													<td>
														<?php echo $layer->name; ?>
														&nbsp;
														<?php
															if ($layer->hasConfig()) {
																echo '<span class="label label-info">' . JText::_('COM_EASYSDI_SERVICE_LAYER_HAS_CONFIG') . '</span>';
															}
														?>
													</td>
													<td><?php echo $layer->description; ?></td>
													<td>
														<button type="button" class="btn btn_modify_layer" data-toggle="modal" data-target="#layer_settings_modal" data-psid="<?php echo $ps->id;?>" data-policyid="<?php echo $this->item->id;?>" data-layername="<?php echo $layer->name;?>">
															<?php echo JText::_('COM_EASYSDI_SERVICE_BTN_SETTINGS');?>
														</button>
														<button type="button" class="btn btn-danger btn_delete_layer" data-psid="<?php echo $ps->id;?>" data-policyid="<?php echo $this->item->id;?>" data-layername="<?php echo $layer->name;?>">
															<?php echo JText::_('COM_EASYSDI_SERVICE_BTN_DELETE_SETTINGS');?>
														</button>
													</td>
												</tr>
											<?php endforeach; ?>
											</tbody>
										</table>
								  </div>
								</div>
						  </div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
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
	
	<input type="hidden" name="layout" id="layout" value="wfs" />
	<input type="hidden" name="vs_id" id="vs_id" value="<?php echo JRequest::getVar('virtualservice_id',null); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<div id="layer_settings_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 712px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel"><?php echo JText::_('COM_EASYSDI_SERVICE_WFS_MODAL_TITLE');?> : <span id="layer_name"></span></h3>
	</div>
	<div class="modal-body">
		<img class="loaderImg" src="<?php echo JURI::base(true).DS.'components'.DS.'com_easysdi_service'.DS.'assets'.DS.'images'.DS.'loader.gif'; ?>" />
		<form id="modal_layer_form"></form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_EASYSDI_SERVICE_MODAL_CANCEL');?></button>
		<button class="btn btn-primary"><?php echo JText::_('COM_EASYSDI_SERVICE_MODAL_SAVE');?></button>
	</div>
</div>