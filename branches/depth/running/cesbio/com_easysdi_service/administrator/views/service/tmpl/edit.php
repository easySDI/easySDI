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
JHtml::_('behavior.formvalidation');
// Import CSS
$document = &JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'service.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	var request;
	
	function getAuthenticationConnector(){
		var serviceSelector 			= document.getElementById("jform_serviceconnector_id");
		var service 					= serviceSelector.options[serviceSelector.selectedIndex].value;
		var serviceauthentication 		= document.getElementById("jformserviceauthentication_id");
		while ( serviceauthentication.options.length > 1 ) serviceauthentication.options[1] = null;
		var resourceauthentication 		= document.getElementById("jformresourceauthentication_id");
		while ( resourceauthentication.options.length > 1 ) resourceauthentication.options[1] = null;
		var authenticationconnectorlist = document.getElementById("authenticationconnectorlist").value;
		var JSONobjects 				= JSON.parse(authenticationconnectorlist);
		for(var i=0; i < JSONobjects.length ; i++){
			if(JSONobjects[i].service == service){
				var option = new Option( JSONobjects[i].value, JSONobjects[i].id);
				if(JSONobjects[i].level == 1){
					resourceauthentication.options[resourceauthentication.length] = option;
				}else{
					serviceauthentication.options[serviceauthentication.length] = option;
				}
			}
		}
	}
	
	function negoVersionService(){
		var url 			= document.getElementById("jform_resourceurl").value;
		var user 			= document.getElementById("jform_resourceusername").value;
		var password 		= document.getElementById("jform_resourcepassword").value;
		var serurl 			= document.getElementById("jform_serviceurl").value;
		var seruser 		= document.getElementById("jform_serviceusername").value;
		var serpassword 	= document.getElementById("jform_servicepassword").value;
		var serviceSelector = document.getElementById("jform_serviceconnector_id");
		var service 		= serviceSelector.options[serviceSelector.selectedIndex].text;
		var query 			= "index.php?option=com_easysdi_service&task=negotiation&resurl="+url+"&resuser="+user+"&respassword="+password+"&service="+service;
		if(serurl.length > 0)
		{
			query 			= query + "&serurl="+url+"&seruser="+user+"&serpassword="+password;
		}
	    request 			= getHTTPObject();
	    document.getElementById("progress").style.visibility = "visible";
	    request.onreadystatechange = getSupportedVersions;
	    request.open("GET", query, true);
	    request.send(null);
	}

	function getHTTPObject(){
	    var xhr = false;
	    if (window.XMLHttpRequest){
	        xhr = new XMLHttpRequest();
	    } else if (window.ActiveXObject) {
	        try{
	            xhr = new ActiveXObject("Msxml2.XMLHTTP");
	        }catch(e){
	            try{
	                xhr = new ActiveXObject("Microsoft.XMLHTTP");
	            }catch(e){
	                xhr = false;
	            }
	        }
	    }
	    return xhr;
	}

	function getSupportedVersions()
	{
	    if(request.readyState == 4){
	    	document.getElementById("progress").style.visibility = "hidden";
			var JSONtext = request.responseText;
			if(JSONtext == "[]"){
				dv=document.createElement('div');
		    	dv.className = "errornegotiation";
		    	txt=document.createTextNode('<?php echo JText::_('COM_EASYSDI_SERVICE_FORM_DESC_SERVICE_NEGOTIATION_ERROR'); ?>');
		    	dv.appendChild(txt);
		    	document.getElementById('div-supportedversions').appendChild(dv);
				document.getElementById('jform_compliance').value = "";
				return;
			}
			var arrcompliance=new Array();
			document.getElementById('div-supportedversions').innerHTML = '';
			var JSONobject = JSON.parse(JSONtext, function (key, value) {
				var type;

			    if(key && typeof key === 'string' && key == 'ERROR'){
			    	dv=document.createElement('div');
			    	dv.className = "errornegotiation";
			    	txt=document.createTextNode(value);
			    	dv.appendChild(txt);
			    	document.getElementById('div-supportedversions').appendChild(dv);
			    	document.getElementById('jform_compliance').value = "";
					return;
			    }
			    if (value && typeof value === 'string') {
			    	dv=document.createElement('div');
			    	dv.className = "supportedversion";
			    	txt=document.createTextNode(value);
			    	dv.appendChild(txt);
			    	document.getElementById('div-supportedversions').appendChild(dv);

			    	arrcompliance.push(key);
			    }
			});
			if(arrcompliance.length >0)
				document.getElementById('jform_compliance').value = JSON.stringify(arrcompliance);
			else
				document.getElementById('jform_compliance').value = "";
	    }
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=service&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
	<div id="progress">
		<img id="progress_image"  src="components/com_easysdi_service/assets/images/loader.gif" alt="">
	</div>
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYSDI_SERVICE_LEGEND_SERVICE'); ?></legend>
		   <ul class="adminformlist">
				<?php foreach($this->form->getFieldset('details') as $field): ?>
				<?php
					if($field->name=="jform[state]"){
						if($this->canDo->get('core.edit.state'))
						{
							?><li><?php echo $field->label;echo $field->input;?></li><?php 
						}
						continue;
					} ?>
					<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'service-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_SERVICE_LEGEND_AUTHENTICATION_OPTIONS'), 'authenticationoptions-details'); ?>
			<fieldset class="adminform">
				<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('authenticationoptions') as $field): ?>
					<?php 
					$property = substr($field->id,6);
					if($property == 'resourceauthentication_id')
					{
						?>
						<li><?php echo $field->label;
						echo JHTML::_("select.genericlist",$this->currentresourceauthenticationconnectorlist, 'jform[resourceauthentication_id]', 'size="1 class="inputbox"" ', 'id', 'value', $this->item->resourceauthentication_id );
						?></li>
						<?php
					}
					else {
					?>
					<li><?php echo $field->label;echo $field->input;?></li>
					<?php
					} 
				endforeach; ?>
			</ul>
			</fieldset>
			
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_SERVICE_LEGEND_PROVIDER_OPTIONS'), 'provideroptions-details'); ?>
			<fieldset class="adminform">
				<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('provideroptions') as $field): ?>
					<?php 
					$property = substr($field->id,6);
					if($property == 'serviceauthentication_id')
					{
						?>
						<li><?php echo $field->label;
						echo JHTML::_("select.genericlist",$this->currentserviceauthenticationconnectorlist, 'jform[serviceauthentication_id]', 'size="1" ', 'id', 'value', $this->item->serviceauthentication_id );
						?></li>
						
						<?php
					}
					else {
					?>
					<li><?php echo $field->label;echo $field->input;?></li>
					<?php
					} 
				endforeach; ?>
				
			</ul>
			</fieldset>
			
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_SERVICE_LEGEND_SERVICE_NEGOTIATION'), 'negotiation-details'); ?>
			<fieldset class="adminform">
				<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('negotiation') as $field): ?>
					<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
			</fieldset>
		
			<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>
			<fieldset class="adminform">
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('created_by'); ?>
					<?php echo $this->form->getInput('created_by'); ?></li>
		            
					<li><?php echo $this->form->getLabel('created'); ?>
					<?php echo $this->form->getInput('created'); ?></li>
		
		            <?php if ($this->item->modified_by) : ?>
						<li><?php echo $this->form->getLabel('modified_by'); ?>
						<?php echo $this->form->getInput('modified_by'); ?></li>
			            
						<li><?php echo $this->form->getLabel('modified'); ?>
						<?php echo $this->form->getInput('modified'); ?></li>
					<?php endif; ?>
				</ul>
			</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>

	<div class="clr"></div>
	<div class="width-100 fltlft">
		<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_SERVICE_FIELDSET_RULES'), 'access-rules'); ?>
			<fieldset class="panelform">
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>
		
    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
    <input type="hidden" name="authenticationconnectorlist" id="authenticationconnectorlist" value='<?php echo json_encode($this->authenticationconnectorlist);?>' />
    
</form>