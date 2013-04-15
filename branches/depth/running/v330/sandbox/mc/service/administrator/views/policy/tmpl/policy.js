Joomla.submitbutton = function (task) {
	if (task == 'policy.cancel' || document.formvalidator.isValid(document.id('policy-form'))) {
		if ('policy.cancel' != task && 'WMS' == jQuery('#jform_layout').val()) {
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

jQuery(document).ready(function (){
	enableAccessScope();
});

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

function popAlert (msg, cssClass) {
	jQuery('#system-message-container').html('<div class="alert ' + cssClass + '"><button type="button" class="close" data-dismiss="alert">&times;</button><h4 class="alert-heading">Message</h4><p>' + msg + '</p></div>');
	jQuery('#system-message-container').alert();
}