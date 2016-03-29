jQuery(document).ready(function() {
    if (jQuery('#jform_reflectedmetadata').is(":checked")) {
        jQuery("#metadata :input").val("");
        jQuery("#metadata :input").attr("disabled", true);
        jQuery("#jform_country_id").attr("disabled", true).trigger('liszt:updated');
        jQuery('#jform_reflectedmetadata').removeAttr("disabled");
        jQuery("#metadata :checkbox").attr('checked', 'checked');
    }
    if (jQuery('#jform_inheritedcontact').is(":checked") && !jQuery('#jform_reflectedmetadata').is(":checked")) {
        jQuery("#contact :input").val("");
        jQuery("#contact :input").attr("disabled", true);
        jQuery("#jform_country_id").attr("disabled", true).trigger('liszt:updated');
        jQuery('#jform_inheritedcontact').removeAttr("disabled");
    }
    jQuery('#servicemetadata input:checked').each(function() {
        jQuery('#' + jQuery(this).attr('id').replace('inherited', '')).attr("disabled", true);
    });

    enableMetadata();

    enableOrganism();
});

function enableOrganism() {
    if (jQuery('#jform_servicescope_id').val() != 2) {
        jQuery("#jform_organisms").val("").trigger('liszt:updated');
        jQuery("#organisms").hide();
    }
    else
    {
        jQuery("#organisms").show();
    }
}
Joomla.submitbutton = function(task)
{
    if (task == 'virtualservice.cancel') {
        Joomla.submitform(task, document.getElementById('virtualservice-form'));
    }
    else if (document.formvalidator.isValid(document.id('virtualservice-form'))) {
        if ((!jQuery('#jform_reflectedmetadata').is(":checked") && !jQuery('#jform_inheritedtitle').is(":checked")) && !jQuery('#jform_title').val()) {
            alert(Joomla.JText._('COM_EASYSDI_SERVICE_FORM_SERVICE_METADATA_ERROR', 'At least a title must be given for the service metadata.'));
            return;
        }
        Joomla.submitform(task, document.getElementById('virtualservice-form'));
    }
    else {
        alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED', 'Form validation failed'));
    }
}

function changeReflectedMetadata()
{
    if (jQuery('#jform_reflectedmetadata').is(":checked")) {
        jQuery("#metadata :input").val("");
        jQuery("#metadata :input").attr("disabled", true);
        jQuery("#jform_country_id").attr("disabled", true).trigger('liszt:updated');
        jQuery('#jform_reflectedmetadata').removeAttr("disabled");
        jQuery("#metadata :checkbox").attr('checked', 'checked');
    } else {
        jQuery('#metadata :input').removeAttr("disabled");
        jQuery('#jform_country_id').removeAttr("disabled").trigger('liszt:updated');
        jQuery("#metadata :checkbox").removeAttr('checked');
    }
    jQuery("#metadata").trigger("liszt:updated");
}

function changeInheritedContact()
{
    if (jQuery('#jform_inheritedcontact').is(":checked")) {
        jQuery("#contact :input").val("");
        jQuery("#contact :input").attr("disabled", true);
        jQuery("#jform_country_id").attr("disabled", true).trigger('liszt:updated');
        jQuery('#jform_inheritedcontact').removeAttr("disabled");
    } else {
        jQuery('#contact :input').removeAttr("disabled");
        jQuery('#jform_country_id').removeAttr("disabled").trigger('liszt:updated');
    }
}

function changeMetadataServiceField(fieldname)
{
    if (jQuery('#jform_inherited' + fieldname).is(":checked")) {
        jQuery("#jform_" + fieldname).val("");
        jQuery("#jform_" + fieldname).attr("disabled", true);
    } else {
        jQuery('#jform_' + fieldname).removeAttr("disabled");
    }
}

function enableMetadata() {
    var nserver = jQuery('#jform_physicalservice_id :selected').length;
    if (nserver > 1) {
        jQuery('#metadata :input').removeAttr("disabled");
        jQuery('#jform_reflectedmetadata').attr("disabled", true);
        jQuery('#jform_reflectedmetadata').attr('checked', false);
        jQuery("#metadata :checkbox").removeAttr('checked');
        jQuery('#metadata :checkbox').attr("disabled", true);
        jQuery("#jform_country_id").removeAttr("disabled").trigger('liszt:updated');
    } else {
        jQuery('#jform_reflectedmetadata').removeAttr("disabled");
    }
}

function updateAgregatedVersion()
{
    //Metadata handling
    enableMetadata();

    //Supported version handling
    var supportedVersionsArray;
    jQuery('#div-supportedversions').html("");
    jQuery('#jform_compliance').val("");
    jQuery('#jform_physicalservice_id option:selected').each(function() {
        var selected = jQuery(this).text();
        var versions = selected.split(' - ')[1];
        var versionsArray = versions.substring(1, versions.length - 1).split('-');

        if (supportedVersionsArray) {
            var j = supportedVersionsArray.length;
            while (j--) {
                if (!contains(versionsArray, supportedVersionsArray[j])) {
                    supportedVersionsArray.splice(1, j);
                }
            }

        } else {
            supportedVersionsArray = versionsArray;
        }

    });

    jQuery('#jform_compliance').val(JSON.stringify(supportedVersionsArray));

    if (supportedVersionsArray && supportedVersionsArray.length > 0)
    {
        jQuery('#div-supportedversions').html(createSupportedVersionLabel(supportedVersionsArray));
    }
}

function contains(arr, findValue) {
    var i = arr.length;

    while (i--) {
        if (arr[i] === findValue)
            return true;
    }
    return false;
}

function createSupportedVersionLabel(versions) {
    var html = '';
    for (var i = 0; i < versions.length; i++) {
        html += '<span class="label label-info">';
        html += versions[i];
        html += '</span>';
    }
    return html;
}