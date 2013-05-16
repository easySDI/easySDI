// fired when a user input data in the state field
function onStateChange () {
	var values = jQuery('#jform_csw_state').val();
	if (null != values) {
		for (var i = 0; i < values.length; i++) {
			var text = jQuery('#jform_csw_state option[value="' + values[i] + '"]').text();
			if ('published' == text) {
				jQuery('#jform_csw_version_id').show();
				jQuery("#jform_csw_version_id").trigger("liszt:updated");
				return;
			}
		}
	}
	jQuery('#jform_csw_version_id').hide();
	jQuery("#jform_csw_version_id").trigger("liszt:updated");
}

jQuery(document).ready(function () {
	//onClick on the button to add a new excluded attribute field
	jQuery('#btn_add_excluded_attribute').click(function () {
		var count = jQuery(this).data('count');
		jQuery('#div_excluded_attributes').append(
			'<div class="div_ea_' + count + ' span12">' + 
				'<textarea name="excluded_attribute[' + count + ']" rows="5" class="span10"></textarea>' +
				'<button type="button" class="btn btn-danger btn_ea_delete">' + Joomla.JText._('COM_EASYSDI_SERVICE_POLICY_CSW_BTN_DELETE_EXCLUDED_ATTRIBUTE') + '</button>' +
				'<br /><br />' +
			'</div>'
		);
		count++;
		jQuery(this).data('count', count);
		return false;
	});
	
	jQuery('button.btn_ea_delete').click(function () {
		var parent = jQuery(this).parent();
		parent.children('textarea').html('');
		parent.hide();
	});
});