var i = 1;
jQuery(document).ready(function() {
    if (jQuery("#jform_level").val().length > 0) {
        var levels = jQuery.parseJSON(jQuery("#jform_level").val());
        jQuery.each(levels, function(index, object) {
            addRow(object.code, object.label, object.defaultlevel);
        })
    }

    jQuery("#add_row").click(function() {
        addRow('', '');
    });
    jQuery("#delete_row").click(function() {
        if (i > 1) {
            jQuery("#level" + (i - 1)).html('');
            i--;
        }
    });
    
    jQuery(document).on('click','[id^="jform[defaultlevel"]' ,function() {
        
            jQuery('[id^="jform[defaultlevel"]').each(function() {
                jQuery(this).attr('checked', false);
            });
            jQuery(this).attr('checked', true);
        
    });
});



function addRow(code, label, dflt) {
    var checked = '';
    if (dflt) {
        checked = "checked";
    }
    jQuery('#level' + i).html("<td></td>\n\
    <td><input name='jform[label][" + i + "]' id='jform_label" + i + "'  type='text' placeholder='Label' class='form-control' value='" + label + "'></td>\n\
    <td><input  name='jform[code][" + i + "]' id='jform_code" + i + "' type='text' placeholder='Code'  class='form-control' value='" + code + "'></td>\n\
    <td><input type='checkbox' name='jform[defaultlevel][" + i + "]' id='jform[defaultlevel" + i + "]' value='1' " + checked + " /></td>");
    jQuery('#tab-dyn').append('<tr id="level' + (i + 1) + '"></tr>');
    
    i++;
}

