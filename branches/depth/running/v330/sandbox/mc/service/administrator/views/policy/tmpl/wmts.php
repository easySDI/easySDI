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
$document->addScript('components/com_easysdi_service/views/policy/tmpl/wmts.js');
JText::script('JGLOBAL_VALIDATION_FORM_FAILED');

//TODO: use this fct to implement inherited forms
function printSpatialPolicyForm ($suffix, $data) {
	$db = JFactory::getDbo();
	$db->setQuery('
		SELECT *
		FROM #__sdi_sys_spatialoperator
		WHERE state = 1
		ORDER BY ordering;
	');
	$db->execute();
	$resultset = $db->loadObjectList();
	
	$html = '
		<label class="checkbox">
			<input type="checkbox" name="anyitem" value="1" ' . ((1 == $data->anyitem)?'checked="checked"':'') . ' /> ' . JText::_('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMTS_ANYITEM') . '
			<input type="checkbox" name="inheritedspatialpolicy" value="1" ' . ((1 == $data->inheritedspatialpolicy)?'checked="checked"':'') . ' /> ' . JText::_('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMTS_INHERITEDSPATIALPOLICY') . '
		</label>
		<hr />
		<table>
			<tr>
				<td></td>
				<td>
					<input type="text" name="northBoundLatitude_' . $suffix . '" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_NORTH_BOUND_LATITUDE') . '" value="' . $data->northBoundLatitude . '"/>
				</td>
				<td></td>
			</tr>
			<tr>
				<td>
					<input type="text" name="westBoundLongitude_' . $suffix . '" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_WEST_BOUND_LONGITUDE') . '" value="' . $data->westBoundLongitude . '"/>
				</td>
				<td></td>
				<td>
					<input type="text" name="eastBoundLongitude_' . $suffix . '" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_EAST_BOUND_LONGITUDE') . '" value="' . $data->eastBoundLongitude . '"/>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="text" name="southBoundLatitude_' . $suffix . '" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_SOUTH_BOUND_LATITUDE') . '" value="' . $data->southBoundLatitude . '"/>
				</td>
				<td></td>
			</tr>
		</table>
		<hr />
		<select name="spatial_operator_id_' . $suffix . '">
			<option value="">' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_SPATIAL_OPERATOR_LABEL') . '</option>';
			foreach ($resultset as $spatialOperator) {
				$html .= '<option value="' . $spatialOperator->id . '" ' . (($spatialOperator->id == $data->spatialOperator)?'selected="selected"':'') . '>' . $spatialOperator->value . '</option>';
			}
	$html .= '</select>
		<hr />
	';
	return $html;
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&layout=wmts&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="policy-form" class="form-validate">
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
						<div class="control-group">
							<div class="control-label"><?php echo $field->label; ?></div>
							<div class="controls"><?php echo $field->input; ?></div>
						</div>
					<?php endforeach; ?>
					</fieldset>
					
					<div class="control-group">
					<?php foreach($this->form->getFieldset('wmts_policy_hidden') as $field):?> 
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
					<fieldset>
						<legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_INHERITANCE' );?></legend>
						
						<!-- TODO : form with Policy lvl herited bbox and operator -->
						
					</fieldset>
					<fieldset>
					<?php if (empty($this->item->id)): ?>
						<?php echo JText::_('COM_EASYSDI_SERVICE_LAYER_NOT_DISPLAYABLE');?>
					<?php endif; ?>
					<?php if (!empty($this->item->id)): ?>
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
										
										<!-- TODO : form with PS lvl herited bbox and operator -->
										
										<table class="table" >
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
													<td><?php echo $layer->name; ?></td>
													<td><?php echo $layer->description; ?></td>
													<td>
														<button type="button" class="btn btn_modify_layer" data-toggle="modal" data-target="#layer_settings_modal" data-psid="<?php echo $ps->id;?>" data-policyid="<?php echo $this->item->id;?>" data-layername="<?php echo $layer->name;?>">
															<?php echo JText::_('COM_EASYSDI_SERVICE_BTN_SETTINGS');?>
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
	
	<input type="hidden" name="layout" id="layout" value="wmts" />
	<input type="hidden" name="vs_id" id="vs_id" value="<?php echo JRequest::getVar('virtualservice_id',null); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<div id="layer_settings_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 712px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel"><?php echo JText::_('COM_EASYSDI_SERVICE_WMTS_MODAL_TITLE');?> : <span id="layer_name"></span></h3>
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