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
		if (task == 'layer.cancel' || document.formvalidator.isValid(document.id('layer-form'))) {
			Joomla.submitform(task, document.getElementById('layer-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	var request;
	var selectedservice;
	var layername_select; 
	function getLayers (selectObj)
	{
		layername_select = document.getElementById('jform_layername');
		while ( layername_select.options.length > 0 ) layername_select.options[0] = null;
		
		var idx = selectObj.selectedIndex; 
		
		selectedservice = selectObj.options[idx].value;
		user = document.getElementById('jform_user').value;
		password = document.getElementById('jform_password').value;
		if (document.getElementById(selectedservice))
		{
			var jsonalllayers = document.getElementById(selectedservice).value; 
			var allayers = JSON.parse(jsonalllayers);
			for(var i=0; i < allayers.length ; i++){
				addLayerOption(allayers[i], allayers[i]);
			}
		} 
		else
		{
			request = false;
		    if (window.XMLHttpRequest){
		    	request = new XMLHttpRequest();
		    } else if (window.ActiveXObject) {
		        try{
		        	request = new ActiveXObject("Msxml2.XMLHTTP");
		        }catch(e){
		            try{
		            	request = new ActiveXObject("Microsoft.XMLHTTP");
		            }catch(e){
		            	request = false;
		            }
		        }
		    }
		    if(!request)
			    return;

		    var query 			= "index.php?option=com_easysdi_map&task=getLayers&service="+selectedservice+"&user="+user+"&password="+password;
			
		    document.getElementById("progress").style.visibility = "visible";
		    request.onreadystatechange = setLayers;
		    request.open("GET", query, true);
		    request.send(null);
		}		
	}

	function setLayers()
	{
	    if(request.readyState == 4){
	    	layername_select = document.getElementById('jform_layername');
			while ( layername_select.options.length > 0 ) layername_select.options[0] = null;
			
	    	document.getElementById("progress").style.visibility = "hidden";
			var JSONtext = request.responseText;
			
			if(JSONtext == "[]"){
				return;
			}

			
			var ok = true;
			
			var JSONobject = JSON.parse(JSONtext, function (key, value) {
				if(key && typeof key === 'string' && key == 'ERROR'){
					alert(value);   
					ok = false;	
					return;
			    }
			    if (value && typeof value === 'string') {
			    	addLayerOption(value, value);
			    }
			});

			if(ok)
			{
				var input = document.createElement("input");
				input.setAttribute("type", "hidden");
				input.setAttribute("name", selectedservice);
				input.setAttribute("id", selectedservice);
				input.setAttribute("value", JSONtext);
				document.getElementById("layer-form").appendChild(input);
			}
	    }
	}

	function addLayerOption (id, value)
	{
		var onloadlayername = document.getElementById('jform_onloadlayername').value;
		
		var option = new Option( id,value);
    	var i = layername_select.length;
		layername_select.options[layername_select.length] = option;
		if(onloadlayername && onloadlayername == value)
			layername_select.options[i].selected = "1";
	}
	
	function init()
	{
		if(document.getElementById ('jform_asOL').checked == true)
			document.getElementById ('jform_asOLparams').disabled = false;
		
		var service_select = document.getElementById('jform_service_id');
		getLayers(service_select);
	}
	
	function enableOlparams()
	{
		if(document.getElementById ('jform_asOL').checked == true)
		{
			document.getElementById ('jform_asOLparams').disabled = false;
			document.getElementById ('jform_asOLparams').value = "";
		}
		else
		{
			document.getElementById ('jform_asOLparams').disabled = true;
			document.getElementById ('jform_asOLparams').value = "";
		}
	}
	
	window.addEvent('domready', init);
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_easysdi_map&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="layer-form" class="form-validate">
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
					<li><?php echo $field->label;echo $field->input;  echo $this->form->getField('getlayers')->input;?></li>
					 
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
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('asOL'); ?> <?php echo $this->form->getInput('asOL'); ?>
				</li>

				<li><?php echo $this->form->getLabel('asOLparams'); ?> <?php echo $this->form->getInput('asOLparams'); ?>
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
	<div class="clr"></div>

	<style type="text/css">
/* Temporary fix for drifting editor fields */
.adminformlist li {
	clear: both;
}
</style>
</form>
