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
				echo '<fieldset class="adminform"><legend>' . JText::_( 'COM_EASYSDI_SERVICE_WMS_SERVER' ) . $ps['name'] . ' (' . $ps['resourceurl'] . ')</legend>
					<ul class="adminformlist">
							<li>
								<label id="jform_wms_prefix-lbl" for="jform_wms_prefix" title aria-invalid="false">' . JText::_('COM_EASYSDI_SERVICE_WMS_SERVER_PREFIXE') . '</label>
								<input type="text" name="servicepolicy[wms_prefix_' . $ps['id'] . ']" id="servicepolicy_wms_prefix_' . $ps['id'] . '" value="' . $ps['prefix'] . '" class="inputbox" size="40" aria-invalid="false" />
							</li>
							<li>
								<label id="jform_wms_namespace-lbl" for="jform_wms_namespace" title aria-invalid="false">' . JText::_('COM_EASYSDI_SERVICE_WMS_SERVER_NAMESPACE') . '</label>
								<input type="text" name="servicepolicy[wms_namespace_' . $ps['id'] . ']" id="servicepolicy_wms_namespace_' . $ps['id'] . '" value="' . $ps['namespace'] . '" class="inputbox" size="40" aria-invalid="false" />
							</li>
							<li>
								<table class="admintable" id="wms_layers">
									<tbody>
										<tr>
											<th><span style="font-weight: bold;">Name</span></th>
											<th><span style="font-weight: bold;">Min scale</span></th>
											<th><span style="font-weight: bold;">Max scale</span></th>
											<th><span style="font-weight: bold;">Geographic filter</span></th>
										</tr>
										<tr>
											<td colspan="4">
												<input type="checkbox" name="wms_anyItem" id="wms_anyItem_' . $ps['id'] . '"/>All
											</td>
										</tr>';
										foreach ($ps['layers'] as $layer) {
											echo '
												<tr>
													<td>
														<input type="checkbox" name="wms_layer_' . $ps['id'] . '_' . $layer['id'] . '" id="wms_layer_' . $ps['id'] . '_' . $layer['id'] . '"/>
														' . $layer['name'] . '<br />"' . $layer['description'] . '"
													</td>
													<td>
														<input type="text" size="10" id="jform_wms_minimumscale_' . $ps['id'] . '_' . $layer['id'] . '" name="wmslayerpolicy[wms_minimumscale_' . $ps['id'] . '_' . $layer['id'] . ']" value="' . $layer['minimumscale'] . '"/>
													</td>
													<td>
														<input type="text" size="10" id="jform_wms_maximumscale_' . $ps['id'] . '_' . $layer['id'] . '" name="wmslayerpolicy[wms_maximumscale_' . $ps['id'] . '_' . $layer['id'] . ']" value="' . $layer['maximumscale'] . '"/>
													</td>
													<td>
														<input type="textarea" rows="3"  cols="30" size="40" id="jform_wms_geographicfilter_' . $ps['id'] . '_' . $layer['id'] . '" name="wmslayerpolicy[wms_geographicfilter_' . $ps['id'] . '_' . $layer['id'] . ']" value="' . $layer['geographicfilter'] . '"/>
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