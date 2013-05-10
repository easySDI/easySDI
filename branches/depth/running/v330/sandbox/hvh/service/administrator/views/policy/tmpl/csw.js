// fired when a user input data in the state field
function onStateChange () {
	var values = jQuery('#jform_csw_state').val();
	if (null != values) {
		for (var i = 0; i < values.length; i++) {
			var text = jQuery('#jform_csw_state option[value="' + values[i] + '"]').text();
			if ('published' == text) {
				jQuery('#jform_csw_version_id').parent().parent().show();
				return;
			}
		}
	}
	jQuery('#jform_csw_version_id').parent().parent().hide();
}

function enableState(){
	if(jQuery('#jform_csw_anystate1').is(":checked")){
		jQuery("#jform_csw_state").val("").trigger('liszt:updated');
		jQuery('#jform_csw_state').parent().parent().hide();
		jQuery('#jform_csw_version_id').parent().parent().hide();
	}else{
		jQuery('#jform_csw_state').parent().parent().show();
		jQuery('#jform_csw_version_id').parent().parent().hide();
	}
	
}

function onDeleteExcludedAttribute (index) {
	var parent = jQuery('.div_ea_' + index);
	parent.remove();
}

function onAddExcludedAttribute () {
	var count = jQuery('#btn_add_excluded_attribute').data('count');
	jQuery('#div_excluded_attributes').append(
			'<div class="div_ea_' + count + ' input-xxlarge">' + 
				'<input type="text" name="excluded_attribute[' + count + ']" class="span10" value="" />'+
				'<button class="btn btn-danger btn-small btn_ea_delete" onClick="onDeleteExcludedAttribute(' +count+ ');return false;"><i class="icon-white icon-remove"></i></button>'+
				'<br /><br />' +
			'</div>'
		);
	count++;
	jQuery('#btn_add_excluded_attribute').data('count', count);
}

jQuery(document).ready(function () {
	onStateChange();
	enableState();
	
	jQuery('input[name="jform[csw_anystate]"]').click(function () {
		enableState();
	});
	
});