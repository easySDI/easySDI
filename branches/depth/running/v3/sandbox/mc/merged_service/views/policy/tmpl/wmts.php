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
var_dump($this->item->physicalservice[0]['layers']);
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'policy.cancel' || document.formvalidator.isValid(document.id('policy-form'))) {
			Joomla.submitform(task, document.getElementById('policy-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&layout=wmts&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="policy-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_INFOS' );?></legend>
			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('policy') as $field): ?>
					
					<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
		
		<?php
			foreach ($this->item->physicalservice as $ps) {
				echo '<fieldset class="adminform"><legend>' . JText::_( 'COM_EASYSDI_SERVICE_WMTS_SERVER' ) . $ps['name'] . ' (' . $ps['resourceurl'] . ')</legend>
					<ul class="adminformlist">
							<li>
								<label id="jform_wmts_prefix-lbl" for="jform_wmts_prefix" title aria-invalid="false">' . JText::_('COM_EASYSDI_SERVICE_WMS_SERVER_PREFIXE') . '</label>
								<input type="text" name="servicepolicy[wmts_prefix_' . $ps['id'] . ']" id="servicepolicy_wmts_prefix_' . $ps['id'] . '" value="' . $ps['prefix'] . '" class="inputbox" size="40" aria-invalid="false" />
							</li>
							<li>
								<label id="jform_wmts_namespace-lbl" for="jform_wmts_namespace" title aria-invalid="false">' . JText::_('COM_EASYSDI_SERVICE_WMS_SERVER_NAMESPACE') . '</label>
								<input type="text" name="servicepolicy[wmts_namespace_' . $ps['id'] . ']" id="servicepolicy_wmts_namespace_' . $ps['id'] . '" value="' . $ps['namespace'] . '" class="inputbox" size="40" aria-invalid="false" />
							</li>
							<li>
								<table class="admintable" id="wmts_layers">
									<tbody>
										<tr>
													<th><span style="font-weight: bold;">Geographic filter</span></th>
													<th><span style="font-weight: bold;">TileMatrixSet Id</span></th>
													<th><span style="font-weight: bold;">TileMatrix min scale denominator</span></th>
										</tr>
										<tr>
											<td colspan="4">
												<input type="checkbox" name="wmts_anyItem" id="wmts_anyItem_' . $ps['id'] . '"/>All
											</td>
										</tr>';
										foreach ($ps['layers'] as $layer) {
											echo '
												<tr>
													<td>
														<label for="jform_wmts_bbox_minimumx_' . $ps['id'] . '_' . $layer['id'] . '" >Min X </label>
														<input type="text" size="10" id="jform_wmts_bbox_minimumx_' . $ps['id'] . '_' . $layer['id'] . '" name="wmtslayerpolicy[wmts_bbox_minimumx_' . $ps['id'] . '_' . $layer['id'] . ']" value="' . ((isset($layer['bbox_minimumx']))?$layer['bbox_minimumx']:'') . '"/><br />
														<label for="jform_wmts_bbox_minimumy_' . $ps['id'] . '_' . $layer['id'] . '" >Min Y </label>
														<input type="text" size="10" id="jform_wmts_bbox_minimumy_' . $ps['id'] . '_' . $layer['id'] . '" name="wmtslayerpolicy[wmts_bbox_minimumy_' . $ps['id'] . '_' . $layer['id'] . ']" value="' . ((isset($layer['bbox_minimumy']))?$layer['bbox_minimumy']:'') . '"/><br />
														<label for="jform_wmts_bbox_maximumx_' . $ps['id'] . '_' . $layer['id'] . '" >Max X </label>
														<input type="text" size="10" id="jform_wmts_bbox_maximumx_' . $ps['id'] . '_' . $layer['id'] . '" name="wmtslayerpolicy[wmts_bbox_maximumx_' . $ps['id'] . '_' . $layer['id'] . ']" value="' . ((isset($layer['bbox_maximumx']))?$layer['bbox_maximumx']:'') . '"/><br />
														<label for="jform_wmts_bbox_maximumx_' . $ps['id'] . '_' . $layer['id'] . '" >Max Y </label>
														<input type="text" size="10" id="jform_wmts_bbox_maximumy_' . $ps['id'] . '_' . $layer['id'] . '" name="wmtslayerpolicy[wmts_bbox_maximumy_' . $ps['id'] . '_' . $layer['id'] . ']" value="' . ((isset($layer['bbox_maximumy']))?$layer['bbox_maximumy']:'') . '"/>
													</td>
													<td>
														
													</td>
													<td>
														<input type="text" size="10" id="jform_wmts_minimumscale_' . $ps['id'] . '_' . $layer['id'] . '" name="wmtslayerpolicy[wmts_minimumscale_' . $ps['id'] . '_' . $layer['id'] . ']" value="' . ((isset($layer['minimumscale']))?$layer['minimumscale']:'') . '"/>
														<br /><br /><br />
													</td>
												</tr>
											';
										}
						echo '</tbody>
								</table>
						</li>
					</ul>
				</fieldset>';
			}
		?>
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