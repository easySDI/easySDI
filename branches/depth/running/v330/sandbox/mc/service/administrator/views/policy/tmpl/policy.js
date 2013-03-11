Joomla.submitbutton = function(task)
{
	if (task == 'policy.cancel' || document.formvalidator.isValid(document.id('policy-form'))) {
		Joomla.submitform(task, document.getElementById('policy-form'));
	}
	else {
		alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED', 'Form validation failed'));
	}
}

jQuery('#layer_settings_modal .btn-primary').click(function() {
	jQuery('#layer_settings_modal').modal('hide');
	console.log('pelle');
});
