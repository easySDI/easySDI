var i = 1;
jQuery(document).ready(function () {
    if (jQuery("#jform_level").val().length > 0) {
        var levels = jQuery.parseJSON(jQuery("#jform_level").val());
        jQuery.each(levels, function (index, object) {
            addRow(object.code, object.label, object.defaultlevel);
        })
    }

    jQuery("#add_row").click(function () {
        addRow('', '');
    });

    jQuery("#jform_type").click(function () {
        displayMapTypeOptions();
        jQuery('.nav-tabs a[href="#misc"]').tab('show');
    });

    jQuery(document).on('click', '[id^="delete_row"]', function () {
        jQuery(this).closest('tr').html('');
    });

    jQuery(document).on('click', '[id^="jform[defaultlevel"]', function () {
        jQuery('[id^="jform[defaultlevel"]').each(function () {
            jQuery(this).attr('checked', false);
        });
        jQuery(this).attr('checked', true);

    });
    displayMapTypeOptions();
});

function addRow(code, label, dflt) {
    var checked = '';
    if (dflt) {
        checked = "checked";
    }
    jQuery('#level' + i).html("<td><input type='checkbox' name='jform[defaultlevel][" + i + "]' id='jform[defaultlevel" + i + "]' value='1' " + checked + " /></td>\n\
    <td><input name='jform[label][" + i + "]' id='jform_label" + i + "'  type='text' placeholder='Label' class='form-control' value='" + label + "'></td>\n\
    <td><input  name='jform[code][" + i + "]' id='jform_code" + i + "' type='text' placeholder='Code'  class='form-control' value='" + code + "'></td>\n\
    <td><a id='delete_row" + i + "' class='btn btn-default btn-small'><i class='icon-trash'></i></a></td>\n\
");
    jQuery('#tab-dyn').append('<tr id="level' + (i + 1) + '"></tr>');

    i++;

    //
    jQuery("#tab-dyn").tableDnD();
}

function displayMapTypeOptions() {
    if (jQuery("input[name='jform[type]']:checked").val() == 'leaflet') {
        jQuery("#jform_tool1-lbl").parent().parent().hide();
        jQuery("#jform_tool4-lbl").parent().parent().hide();
        jQuery("#jform_tool5-lbl").parent().parent().hide();
        jQuery("#jform_tool10-lbl").parent().parent().hide();
        jQuery("#jform_tool11-lbl").parent().parent().hide();
        jQuery("#jform_tool15-lbl").parent().parent().hide();
        jQuery("#jform_tool17-lbl").parent().parent().hide();
        jQuery("#scaletab").show();
        jQuery("#wfstab").hide();
        jQuery("#indoortab").hide();
    } else {
        jQuery("#jform_tool1-lbl").parent().parent().show();
        jQuery("#jform_tool4-lbl").parent().parent().show();
        jQuery("#jform_tool5-lbl").parent().parent().show();
        jQuery("#jform_tool10-lbl").parent().parent().show();
        jQuery("#jform_tool11-lbl").parent().parent().show();
        jQuery("#jform_tool15-lbl").parent().parent().show();
        jQuery("#jform_tool17-lbl").parent().parent().show();
        jQuery("#scaletab").show();
        jQuery("#wfstab").show();
        jQuery("#indoortab").show();
    }
}