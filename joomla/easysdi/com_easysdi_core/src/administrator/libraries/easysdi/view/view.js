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


