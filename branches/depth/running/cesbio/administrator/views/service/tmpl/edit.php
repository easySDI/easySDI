<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
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
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css');
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
	
	/**
	 * @function negoVersionServer
	 * Build the request for the proxy PHP that will perform the negotiation  version.
	 */
	function negoVersionService(){
		var url 			= document.getElementById("jform_resourceurl").value;
		var user 			= document.getElementById("jform_resourceusername").value;
		var password 		= document.getElementById("jform_resourcepassword").value;
		var serurl 			= document.getElementById("jform_serviceurl").value;
		var seruser 		= document.getElementById("jform_serviceusername").value;
		var serpassword 	= document.getElementById("jform_servicepassword").value;
		var serviceSelector = document.getElementById("jform_serviceconnector_id");
		var service 		= serviceSelector.options[serviceSelector.selectedIndex].text;
		var query 			= "index.php?option=com_easysdi_core&task=negotiation&resurl="+url+"&resuser="+user+"&respassword="+password+"&service="+service;
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

	/**
	 * Instantiate Http Request
	 */
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

	/**
	 * @function getSupportedVersions
	 * Get the request response and fill appropriate fields in the document
	 */
	function getSupportedVersions()
	{
	    // if request object received response
	    if(request.readyState == 4){
	    	document.getElementById("progress").style.visibility = "hidden";
			var JSONtext = request.responseText;
			
			//Set the supported versions (for the server)
	/*		var JSONobject = JSON.parse(JSONtext, function (key, value) {
			    var type;
			    if (value && typeof value === 'string') {
			    	var version = document.getElementById(value+"_"+currentServerIndex);
			    	version.setAttribute("class","supportedversion");
			    	document.getElementById(value+"_"+currentServerIndex+"_state").value = "supported";
			    	unsupportedVersions = unsupportedVersions.remove(value);
			    }
			});*/

			//None of the available versions are supported by the remote server
			//Send a negotiation error
/*			if(availableVersions.length == unsupportedVersions.length){
				document.getElementById("supportedVersionsByConfig").value=JSON.stringify(new Array('NA'));
				removeAllElementChild( document.getElementById("supportedVersionsByConfigText"));
				document.getElementById("supportedVersionsByConfigText").appendChild(createSupportedVersionByConfigTable(new Array('NA'))) ;
				return;
			}*/

			//Set the unsupported versions (for the server)
	/*		var len = unsupportedVersions.length;
			for (var i = 0; i<len; i++) {
				var version = document.getElementById(unsupportedVersions[i]+"_"+currentServerIndex);
		    	version.setAttribute("class","unsupportedversion");
		    	document.getElementById(unsupportedVersions[i]+"_"+currentServerIndex+"_state").value = "unsupported";
			}*/

	    }
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
	
	<div id="progress">
		<img id="progress_image"  src="components/com_easysdi_core/helpers/loader.gif" alt="">
	</div>
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYSDI_CORE_LEGEND_SERVICE'); ?></legend>
		   <ul class="adminformlist">
				<?php foreach($this->form->getFieldset('details') as $field): ?>
					<li><?php echo $field->label;echo $field->input;?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'service-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_EASYSDI_CORE_LEGEND_SERVICE_NEGOTIATION'), 'negotiation-details'); ?>
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

    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>