function enableAccessScope(){
    // hide fields
    jQuery("#organisms, #users, #categories").hide();
    
    // public case
    if(jQuery("#jform_accessscope_id").val() == 1){
        // reset fields
        jQuery("#jform_users, #jform_organisms, #jform_categories").val("").trigger("liszt:updated");
    }
    // organism case
    else if(jQuery("#jform_accessscope_id").val() == 2){
        jQuery("#organisms").show();
        // reset fields
        jQuery("#jform_users, #jform_categories").val("").trigger("liszt:updated");
    }
    // user case
    else if(jQuery("#jform_accessscope_id").val() == 3){
        jQuery("#users").show();
        // reset fields
        jQuery("#jform_organisms, #jform_categories").val("").trigger("liszt:updated");
    }
    // category case
    else if(jQuery("#jform_accessscope_id").val() == 4){
        jQuery("#categories").show();
        // reset fields
        jQuery("#jform_users, #jform_organisms").val("").trigger("liszt:updated");
    }
}