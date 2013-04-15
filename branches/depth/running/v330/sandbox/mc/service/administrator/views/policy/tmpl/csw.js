// fired when a user input data in the state field
/*function onStateChange () {
	var values = jQuery('#jform_csw_state').val();
	if (null != values) {
		for (var i = 0; i < values.length; i++) {
			var text = jQuery('#jform_csw_state option[value="' + values[i] + '"]').text();
			if ('published' == text) {
				console.log('unlock');
				jQuery('#jform_csw_version_id').removeAttr('disabled');
				return;
			}
		}
	}
	console.log('lock');
	jQuery('#jform_csw_version_id').attr('disabled', 'disabled');
}*/

jQuery(document).ready(function () {
	//onClick on the button to add a new excluded attribute field
	jQuery('#btn_add_excluded_attribute').click(function () {
		var count = jQuery(this).data('count');
		jQuery('#div_excluded_attributes').append('<textarea name="excluded_attribute[' + count + ']" rows="5" class="span12"></textarea><br /><br />');
		count++;
		jQuery(this).data('count', count);
		return false;
	});
});