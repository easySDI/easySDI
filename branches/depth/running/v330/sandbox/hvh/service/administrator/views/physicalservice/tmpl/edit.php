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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'physicalservice.cancel' || document.formvalidator.isValid(document.id('physicalservice-form'))) {
			Joomla.submitform(task, document.getElementById('physicalservice-form'));
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
		var resourceauthentication 		= document.getElementById("jformresourceauthentication_id");
		while ( serviceauthentication.options.length > 1 ) serviceauthentication.options[1] = null;
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
		document.getElementById('div-supportedversions').innerHTML = '<span class="star">*</span>';
		document.getElementById('jform_compliance').value = "";
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
				dv=document.createElement('span');
		    	dv.className = "label label-important";
		    	txt=document.createTextNode('<?php echo JText::_('COM_EASYSDI_SERVICE_FORM_DESC_SERVICE_NEGOTIATION_ERROR'); ?>');
		    	dv.appendChild(txt);
		    	document.getElementById('div-supportedversions').appendChild(dv);
				document.getElementById('jform_compliance').value = "";
				return;
			}
			var arrcompliance=new Array();
			document.getElementById('div-supportedversions').innerHTML = '<span class="star">*</span>';
			var JSONobject = JSON.parse(JSONtext, function (key, value) {
				var type;

			    if(key && typeof key === 'string' && key == 'ERROR'){
			    	dv=document.createElement('span');
			    	dv.className = "label label-important";
			    	txt=document.createTextNode(value);
			    	dv.appendChild(txt);
			    	document.getElementById('div-supportedversions').appendChild(dv);
			    	document.getElementById('jform_compliance').value = "";
					return;
			    }
			    if (value && typeof value === 'string') {
			    	dv=document.createElement('span');
			    	dv.className = "label label-info";
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

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=physicalservice&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="physicalservice-form" class="form-validate">
	<div id="progress">
		<img id="progress_image"  src="components/com_easysdi_service/assets/images/loader.gif" alt="">
	</div>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
            	<ul class="nav nav-tabs">
					<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_EASYSDI_SERVICE_TAB_NEW_SERVICE') : JText::sprintf('COM_EASYSDI_SERVICE_TAB_EDIT_SERVICE', $this->item->id); ?></a></li>
					<li><a href="#provider" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_TAB_PROVIDER');?></a></li>
					<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_TAB_PUBLISHING');?></a></li>
					<?php if ($this->canDo->get('core.admin')): ?>
					<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_EASYSDI_SERVICE_TAB_RULES');?></a></li>
				<?php endif ?>
				</ul>
				
				<div class="tab-content">
					<!-- Begin Tabs -->
					<div class="tab-pane active" id="details">
						<?php foreach($this->form->getFieldset('details') as $field): 
							$property = substr($field->id,6);
							if($property == 'resourceauthentication_id')
							{
								?>
								<div class="control-group">
									<div class="control-label"><?php echo $field->label; ?></div>
									<div class="controls"><?php echo JHTML::_("select.genericlist",$this->currentresourceauthenticationconnectorlist, 'jform[resourceauthentication_id]', 'size="1 class="inputbox"" ', 'id', 'value', $this->item->resourceauthentication_id ); ?></div>
								</div>
								<?php
							}
							else {
							?>
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
							<?php }?>
						<?php endforeach; ?>
					</div>

					<div class="tab-pane" id="provider">
						<?php foreach($this->form->getFieldset('provideroptions') as $field):
							$property = substr($field->id,6);
							if($property == 'serviceauthentication_id')
							{
								?>
								<div class="control-group">
									<div class="control-label"><?php echo $field->label; ?></div>
									<div class="controls"><?php echo JHTML::_("select.genericlist",$this->currentserviceauthenticationconnectorlist, 'jform[serviceauthentication_id]', 'size="1" ', 'id', 'value', $this->item->serviceauthentication_id ); ?></div>
								</div>
								<?php
							}
							else {
							?>
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
							<?php }?>
						<?php endforeach; ?>
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

    <input type="hidden" name="authenticationconnectorlist" id="authenticationconnectorlist" value='<?php echo json_encode($this->authenticationconnectorlist);?>' />
    
</form>