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
$document->addScript(JURI::root(true) .'/components/com_easysdi_core/libraries/OpenLayers-2.13.1/OpenLayers.js' );
$document->addScript(JURI::root(true) .'/components/com_easysdi_core/libraries/proj4js-1.1.0/lib/proj4js-combined.js' );
$document->addScript('components/com_easysdi_service/views/policy/tmpl/policy.js?v=' . sdiFactory::getSdiFullVersion());
$document->addScript('components/com_easysdi_service/views/policy/tmpl/wms.js?v=' . sdiFactory::getSdiFullVersion());
JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
JText::script('COM_EASYSDI_SERVICE_MODAL_ERROR');
JText::script('COM_EASYSDI_SERVICE_CONFIRM_DELETION');
JText::script('COM_EASYSDI_SERVICE_MSG_MODAL_SAVED');
JText::script('COM_EASYSDI_SERVICE_MSG_MODAL_MISSING_BBOX_BOUNDARIES');
JText::script('COM_EASYSDI_SERVICE_MSG_MODAL_MALFORMED_BBOX_BOUNDARIES');
JText::script('COM_EASYSDI_SERVICE_FORM_VAIDATION_FAILED_IMG_SIZE');
JText::script('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_MINIMUMWIDTH');
JText::script('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_MINIMUMHEIGHT');
JText::script('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_MAXIMUMWIDTH');
JText::script('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_MAXIMUMHEIGHT');
JText::script('COM_EASYSDI_SERVICE_POLICY_LAYER_SETTINGS_DEFINED');
JText::script('COM_EASYSDI_SERVICE_POLICY_LAYER_SETTINGS_INHERITED');


function printSpatialPolicyForm ($data, $physicalServiceID = 0) {
	$db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__sdi_sys_spatialoperator');
        $query->where('state = 1');
        $query->order('ordering');
        
	$db->setQuery($query);
	$db->execute();
	$resultset = $db->loadObjectList();
	
	$wmts_spatialpolicy_id = (!empty($data->wms_spatialpolicy_id)) ? $data->wms_spatialpolicy_id : -1;
	
	if (0 == $physicalServiceID) {
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_wms_spatialpolicy');
                $query->where('id = ' . $query->quote($wmts_spatialpolicy_id));
                
	}
	else {
                $query = $db->getQuery(true);
                $query->select('wsp.*, psp.anyitem');
                $query->from('#__sdi_physicalservice_policy psp');
                $query->innerJoin('#__sdi_wms_spatialpolicy wsp ON wsp.id = psp.wms_spatialpolicy_id');
                $query->where('psp.physicalservice_id = ' . $physicalServiceID);
                $query->where('psp.policy_id = ' . $data->id);
                
	}
	
	$db->setQuery($query);
	$db->execute();
	$spatialpolicy = $db->loadObject();
	
	
	$html = '';
	$prefix = 'inherit';

	
	
	
	if (0 == $physicalServiceID) {
		$prefix .= '_policy[' . $wmts_spatialpolicy_id . ']';
		$html .= '
		<label class="checkbox">
		<input type="checkbox" name="' . $prefix . '[anyservice]" class="anyservice" value="1" ' . ((1 == $data->anyservice)?'checked="checked"':'') . ' /><label for="' . $prefix . '[anyservice]">' . JText::_('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_ANYSERVICE') . '</label>
		</label>
		';
	}
	else {
		$prefix .= '_server[' . $physicalServiceID . ']';
                $query = $db->getQuery(true);
                $query->select('psp.anyitem');
                $query->from('#__sdi_physicalservice_policy psp');
                $query->where('psp.physicalservice_id = ' . (int)$physicalServiceID);
                $query->where('psp.policy_id = ' . (int)$data->id);
                
		$db->setQuery($query);
		$db->execute();
		$anyItem = $db->loadResult();
		//$anyItem = (isset($spatialpolicy->anyitem))?$spatialpolicy->anyitem:1;
		$html .= '
		<label class="checkbox">
		<input type="checkbox" name="' . $prefix . '[anyitem]" class="anyitem" data-ps_id="' . $physicalServiceID . '" value="1" ' . ((1 == $anyItem)?'checked="checked"':'') . ' /><label for="' . $prefix . '[anyitem]">' . JText::_('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_ANYITEM') . '</label>
		</label>
		';
	}
	
	$html .= '
	<div class="well">
		<div class="control-group">
			<label class="control-label" for="' . $prefix . '[minimumscale]">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_MINIMUM_SCALE') . '</label>
			<div class="controls">
				<input type="text" name="' . $prefix . '[minimumscale]" value="' . ((isset($spatialpolicy->minimumscale))?$spatialpolicy->minimumscale:'') . '" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="' . $prefix . '[maximumscale]">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_MAXIMUM_SCALE') . '</label>
			<div class="controls">
				<input type="text" name="' . $prefix . '[maximumscale]" value="' . ((isset($spatialpolicy->maximumscale))?$spatialpolicy->maximumscale:'') . '" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="' . $prefix . '[geographicfilter]">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_FILTER') . '</label>
			<div class="controls">
				<textarea name="' . $prefix . '[geographicfilter]" rows="8" class="input-xxlarge">' . ((isset($spatialpolicy->geographicfilter))?$spatialpolicy->geographicfilter:'') . '</textarea>
			</div>
		</div>
	</div>
	<input type="hidden" name="' . $prefix . '[maxx]" id="' . str_replace(Array('[', ']'), '_', $prefix) . 'maxx" />
	<input type="hidden" name="' . $prefix . '[maxy]" id="' . str_replace(Array('[', ']'), '_', $prefix) . 'maxy" />
	<input type="hidden" name="' . $prefix . '[minx]" id="' . str_replace(Array('[', ']'), '_', $prefix) . 'minx" />
	<input type="hidden" name="' . $prefix . '[miny]" id="' . str_replace(Array('[', ']'), '_', $prefix) . 'miny" />
	<input type="hidden" name="' . $prefix . '[srssource]" id="' . str_replace(Array('[', ']'), '_', $prefix) . 'srssource" />
	';
	echo $html;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=policy&layout=wms&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="policy-form" class="form-validate">
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
						<?php foreach($this->form->getFieldset('policy') as $field): ?> 
							<div class="control-group" id="<?php echo $field->fieldname;?>">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
						<?php endforeach; ?>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('allowedoperation_wms'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('allowedoperation_wms'); ?></div>
						</div>
						<?php foreach($this->form->getFieldset('wms_policy') as $field): 
						?> 
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
						<?php endforeach; ?>
						
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
						</div>
					</fieldset>
					
					<div class="control-group">
					<?php 
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
							<?php foreach($this->item->physicalService as $ps): ?>
								<div class="accordion-group">
								<div class="accordion-heading">
								  <a class="accordion-toggle" data-toggle="collapse" data-parent="#ps_accordion" href="#collapse_<?php echo $ps->id; ?>">
										<?php echo $ps->name . ' &mdash; ' . $ps->url; ?>
								  </a>
								</div>
								<div class="accordion-body collapse" id="collapse_<?php echo $ps->id; ?>">
								  <div class="accordion-inner">
										
										<?php printSpatialPolicyForm($this->item, $ps->id); 
                                                                                echo '<input type="hidden" name="enabled[' . $ps->id . ']" value="0"/>';
                                                                                ?>
                                                                      
										<table class="table table-striped" id="table-layers-<?php echo $ps->id; ?>" >
											<thead>
												<tr>
													<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_LAYER_ENABLED' );?></th>
													<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_LAYER_NAME' );?></th>
													<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_LAYER_DESCRIPTION' );?></th>
													<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_LAYER_SETTINGS' );?></th>
													<th></th>
												</tr>
											</thead>
											<tbody>
											<?php foreach($ps->getLayerList() as $layer):?>
												<tr>
													<td>
														<?php
															$checked = (1 == $layer->enabled)?'checked="checked"':'';
															echo '<input type="checkbox" name="enabled[' . $ps->id . '][' . $layer->name . ']" value="1" ' . $checked . '/>';
														?>
													</td>
													<td>
														<?php echo $layer->name; ?>
														&nbsp;
													</td>
													<td><?php echo $layer->description; ?></td>
													<td>
														<?php
															if ($layer->hasConfig()) {
																echo '<span id="configured' . $ps->id . '' . $layer->name . '" class="label label-success">' . JText::_('COM_EASYSDI_SERVICE_POLICY_LAYER_SETTINGS_DEFINED') . '</span>';
															}else{
																echo '<span id="configured' . $ps->id . '' . $layer->name . '" class="label">' . JText::_('COM_EASYSDI_SERVICE_POLICY_LAYER_SETTINGS_INHERITED') . '</span>';
															}
														?>
													</td>
													<td>
														<div class="btn-group">
														  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
														    <?php echo JText::_('COM_EASYSDI_SERVICE_ACTIN_SETTINGS');?>
														    <span class="caret"></span>
														  </a>
														  <ul class="dropdown-menu">
														   <li><a class="btn_modify_layer" data-toggle="modal" data-target="#layer_settings_modal" data-psid="<?php echo $ps->id;?>" data-vsid="<?php echo $this->item->virtualservice_id;?>" data-policyid="<?php echo $this->item->id;?>" data-layername="<?php echo $layer->name;?>">
																<?php echo JText::_('COM_EASYSDI_SERVICE_BTN_SETTINGS');?>
															</a></li>
															<li><a  class="btn_delete_layer" data-psid="<?php echo $ps->id;?>" data-policyid="<?php echo $this->item->id;?>" data-layername="<?php echo $layer->name;?>">
																<?php echo JText::_('COM_EASYSDI_SERVICE_BTN_DELETE_SETTINGS');?>
															</a></li>
														  </ul>
														</div>
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
	
	<input type="hidden" name="layout" id="layout" value="wms" />
	<input type="hidden" name="vs_id" id="vs_id" value="<?php echo JRequest::getVar('virtualservice_id',null); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<div id="layer_settings_modal" class="modal hide fade form-horizontal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 712px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel"><?php echo JText::_('COM_EASYSDI_SERVICE_WMS_MODAL_TITLE');?> : <span id="layer_name"></span></h3>
	</div>
	<div class="modal-body">
		<div id="modal_alert"></div>
		<img class="loaderImg" src="<?php echo JURI::base(true).'/components/com_easysdi_service/assets/images/loader.gif'; ?>" />
		<form id="modal_layer_form"></form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_EASYSDI_SERVICE_MODAL_CANCEL');?></button>
		<button class="btn btn-primary"><?php echo JText::_('COM_EASYSDI_SERVICE_MODAL_SAVE');?></button>
	</div>
</div>