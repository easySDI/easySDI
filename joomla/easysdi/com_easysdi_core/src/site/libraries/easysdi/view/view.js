/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

function enableAccessScope(){
    // hide fields
    jQuery("#organisms, #users, #categories").hide();
    
    // public case
    if(jQuery("#jform_accessscope_id").val() == 1){
        // reset fields
        jQuery("#jform_users, #jform_organisms, #jform_categories").val("").trigger("liszt:updated");
    }
    // organism case
    else if(jQuery("#jform_accessscope_id").val() == 3){
        jQuery("#organisms").show();
        // reset fields
        jQuery("#jform_users, #jform_categories").val("").trigger("liszt:updated");
    }
    // user case
    else if(jQuery("#jform_accessscope_id").val() == 4){
        jQuery("#users").show();
        // reset fields
        jQuery("#jform_organisms, #jform_categories").val("").trigger("liszt:updated");
    }
    // category case
    else if(jQuery("#jform_accessscope_id").val() == 2){
        jQuery("#categories").show();
        // reset fields
        jQuery("#jform_users, #jform_organisms").val("").trigger("liszt:updated");
    }
}