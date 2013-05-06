Joomla.submitbutton = function (task) {
	if (task == 'policy.cancel' || document.formvalidator.isValid(document.id('policy-form'))) {
		if ('policy.cancel' != task && 'WMS' == jQuery('#jform_layout').val()) {
			if(jQuery('#jform_wms_maximumheight').val()!= '' && !jQuery.isNumeric(jQuery('#jform_wms_maximumheight').val()) ){
				alert(Joomla.JText._('COM_EASYSDI_SERVICE_FORM_VALIDATION_FAILED_IMG_SIZE', 'Invalid value for : ')+Joomla.JText._('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_MAXIMUMHEIGHT', 'Maximum Height'));
				return;
			}
			if(jQuery('#jform_wms_minimumheight').val()!= '' &&  !jQuery.isNumeric(jQuery('#jform_wms_minimumheight').val()) ){
				alert(Joomla.JText._('COM_EASYSDI_SERVICE_FORM_VALIDATION_FAILED_IMG_SIZE', 'Invalid value for : ')+Joomla.JText._('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_MINIMUMHEIGHT','Minimum Height'));
				return;
			}
			if(jQuery('#jform_wms_maximumwidth').val()!= '' &&  !jQuery.isNumeric(jQuery('#jform_wms_maximumwidth').val()) ){
				alert(Joomla.JText._('COM_EASYSDI_SERVICE_FORM_VALIDATION_FAILED_IMG_SIZE', 'Invalid value for : ')+Joomla.JText._('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_MAXIMUMWIDTH','Maximum Width'));
				return;
			}
			if(jQuery('#jform_wms_minimumwidth').val()!= '' &&  !jQuery.isNumeric(jQuery('#jform_wms_minimumwidth').val()) ){
				alert(Joomla.JText._('COM_EASYSDI_SERVICE_FORM_VALIDATION_FAILED_IMG_SIZE', 'Invalid value for : ')+Joomla.JText._('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_WMS_MINIMUMWIDTH','Minimum width')); 
				return;
			}
			jQuery(document).trigger('recalculate', task);
		}
		else {
			Joomla.submitform(task, document.getElementById('policy-form'));
		}
	}
	else {
		alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED', 'Form validation failed')); 
	}
}

function enableAccessScope(){
	if(jQuery('#jform_accessscope_id').val() == 1){
		jQuery("#jform_organisms").val("").trigger('liszt:updated');
		jQuery("#jform_users").val("").trigger('liszt:updated');
		jQuery("#organisms").hide();
		jQuery("#users").hide();
	}
	else if(jQuery('#jform_accessscope_id').val() == 2){
		jQuery("#organisms").show();
		jQuery("#jform_users").val("").trigger('liszt:updated');
		jQuery("#users").hide();
	}
	else if(jQuery('#jform_accessscope_id').val() == 3){
		jQuery("#users").show();
		jQuery("#jform_organisms").val("").trigger('liszt:updated');
		jQuery("#organisms").hide();
	}
}

/*
 * Check the state of the checkboxes and determine whether elements must be hidden or shown
*/
function initVisibility () {
	if (jQuery('.anyservice').is(":checked")) {
		jQuery('#ps_accordion').hide();
	}
	else {
		jQuery('#ps_accordion').show();
	}
	
	if (jQuery('#jform_anyoperation1').is(":checked")) {
		jQuery('.allowedoperation').parent().parent().hide();
	}
	else {
		jQuery('.allowedoperation').parent().parent().show();
	}
	
	jQuery('.anyitem').each(function () {
		var ps_id = jQuery(this).data('ps_id');
		if (jQuery(this).is(":checked")) {
			jQuery('#table-layers-' + ps_id).hide();
		}
		else {
			jQuery('#table-layers-' + ps_id).show();
		}
	});
}

function popAlert (msg, cssClass) {
	jQuery('#system-message-container').html('<div class="alert ' + cssClass + '"><button type="button" class="close" data-dismiss="alert">&times;</button><h4 class="alert-heading">Message</h4><p>' + msg + '</p></div>');
	jQuery('#system-message-container').alert();
}

function popModalAlert (msg, cssClass) {
	jQuery('#modal_alert').html('<div class="alert ' + cssClass + '"><button type="button" class="close" data-dismiss="alert">&times;</button><h4 class="alert-heading">Message</h4><p>' + msg + '</p></div>');
	jQuery('#modal_alert').alert();
}

jQuery(document).ready(function (){

	enableAccessScope();
	initVisibility();
	
	//preinput allowFrom and allowTo
	var now = new Date();
	var date = {
		year: now.getFullYear(),
		month: (10 > (now.getMonth() + 1))? '0' + (now.getMonth() + 1):(now.getMonth() + 1),
		day: (10 > now.getDate())? '0' + now.getDate():now.getDate()
	};
	var allowFrom = jQuery('#jform_allowfrom');
	var allowTo = jQuery('#jform_allowto');
	if ('0000-00-00' == allowFrom.val() || '' == allowFrom.val()) {
		allowFrom.val(date.year + '-' + date.month + '-' + date.day);
	}
	if ('0000-00-00' == allowTo.val() || '' == allowTo.val()) {
		date.year += 100;
		allowTo.val(date.year + '-' + date.month + '-' + date.day);
	}
	
	jQuery('.anyservice').change(function () {
		if (jQuery(this).is(":checked")) {
			jQuery('#ps_accordion').hide();
		}
		else {
			jQuery('#ps_accordion').show();
		}
	});
	
	jQuery('.anyitem').change(function () {
		var ps_id = jQuery(this).data('ps_id');
		if (jQuery(this).is(":checked")) {
			jQuery('#table-layers-' + ps_id).hide();
		}
		else {
			jQuery('#table-layers-' + ps_id).show();
		}
	});
	
	jQuery('input[name="jform[anyoperation]"]').click(function () {
		if (jQuery('#jform_anyoperation1').is(":checked")) {
			jQuery(".allowedoperation").val("").trigger('liszt:updated');
			jQuery('.allowedoperation').parent().parent().hide();
		}
		else {
			jQuery('.allowedoperation').parent().parent().show();
		}
	});
	
});