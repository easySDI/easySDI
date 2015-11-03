js = jQuery.noConflict();
js(document).ready(function () {
    enableAccessScope();
    onProductStorageChange();
    onPricingChange();
    enableDownload();
    enableExtraction();
    enableFreePerimeter();

    js('#adminForm').submit(function (event) {
        console.log('here');
        return false;
        if (js('#jform_deposit').val() != '') {
            js('#jform_deposit_hidden').val(js('#jform_deposit').val());
        }
        if (js('#jform_file').val() != '') {
            js('#jform_file_hidden').val(js('#jform_file').val());
        }
    });
    js('#jform_restrictedperimeter').change(enableFreePerimeter);

    js('#jform_testurlauthentication').click(onTestUrlAuthenticationClick);
    js('#jform_testurlauthentication').parent().append('<span id="result_testurlauthentication"></span>');
});
Joomla.submitbutton = function (task)
{
    if (task == 'diffusion.cancel') {
        Joomla.submitform(task, document.getElementById('adminForm'));
    }
    else {

        if (task != 'diffusion.cancel' && document.formvalidator.isValid(document.id('adminForm'))) {
            if (js('#jform_hasextraction').is(':checked')) {
                var perimeterselected = false;
                js('.perimeterselect').each(function () {
                    var currentElement = js(this);
                    if (currentElement.val() != -1) {
                        perimeterselected = true;
                    }
                })
                if (perimeterselected == false) {
                    alert(msgNoPerimeter);
                } else {
                    Joomla.submitform(task, document.getElementById('adminForm'));
                }
            } else {
                Joomla.submitform(task, document.getElementById('adminForm'));
            }
        }
        else {
            alert(msgFormValidationFailed);
        }
    }
}
function onProductStorageChange() {
    var storage = js("#jform_productstorage_id :selected").val();
    switch (storage) {
        case "1":
            js('#file').show();
            js('#fileurl,#userurl, #passurl, #testurlauthentication').hide();
            js('#packageurl').removeAttr('required');
            js('#perimeter_id,#packageurl').hide();
            break;
        case "2":
            js('#file').hide();
            js('#fileurl, #userurl, #passurl, #testurlauthentication').show();
            js('#packageurl').removeAttr('required');
            js('#perimeter_id,#packageurl').hide();
            break;
        case "3":
            js('#file').hide();
            js('#fileurl, #userurl, #passurl, #testurlauthentication').hide();
            js('#perimeter_id,#packageurl').show();
            js('#packageurl').attr('required');
            break;
    }
}
var globdata;
function onPricingChange() {

    switch (js('#jform_pricing_id').val()) {
        case '1': // FREE
            js('#fieldset_download').show();
            js('#pricing_profile_id').hide();
            break;

        case '2': // FEE WITHOUT PRICING PROFILE
            js('#fieldset_download').hide();
            js('#pricing_profile_id').hide();
            break;

        case '3': // FEE WITH PRICING PROFILE
            js('#fieldset_download').hide();

            if (!js('#pricing_profile_id option').length) {
                js.ajax({
                    url: urlProfiles,
                    type: "POST",
                    data: {
                        version_id: version
                    }
                }).fail(function () {
                    console.log('todo');
                }).done(function (data) {
                    data.each(function (item) {
                        js('#pricing_profile_id select').append(js('<option>', {
                            value: item.id,
                            text: item.name
                        })).trigger('liszt:updated');
                    });
                });
            }

            js('#pricing_profile_id').show();
            break;
    }
}

function enableDownload() {
    if (js('#jform_hasdownload').is(':checked')) {
        js('#div_download').show();
    } else {
        js('#div_download').hide();
    }
}

function enableExtraction() {
    if (js('#jform_hasextraction').is(':checked')) {
        js('#div_extraction').show();
    } else {
        js('#div_extraction').hide();
    }
}

function cleanDownload() {
    js('#jform_productstorage_id').find("option").attr("selected", false);
    js('#jform_fileurl').val('');
    js('#jform_perimeter_id').find("option").attr("selected", false);
    js('#jform_packageurl').val('');
    cleanFile();
}

function cleanExtraction() {
    js('#jform_productmining_id').find("option").attr("selected", false);
    js('#jform_surfacemin').val('');
    js('#jform_surfacemax').val('');
    cleanDeposit();
}

function cleanFile() {
    js('#jform_file').val('');
    js('#jform_file_hidden').val('');
}
function cleanDeposit() {
    js('#jform_deposit').val('');
    js('#jform_deposit_hidden').val('');
}

function enableFreePerimeter() {
    if (freePerimeter)
        return;

    if (js('#jform_restrictedperimeter0').length == 0 || js('#jform_restrictedperimeter0').is(':checked') == true) {
        js('#jform_perimeter1').removeAttr('disabled', 'disabled');
    } else {
        js('#jform_perimeter1').attr('disabled', 'disabled');
        js('#jform_perimeter1 option[value=-1]').attr("selected", "selected");

    }
    js('#jform_perimeter1').trigger("liszt:updated");
}

function onTestUrlAuthenticationClick() {
    js.ajax({
        url: "<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.testURLAccessibility') ?>",
        type: "POST",
        data: {
            url: js('#jform_fileurl').val(),
            user: js('#jform_userurl').val(),
            password: js('#jform_passurl').val()
        }
    }).fail(function () {
        console.log('todo');
    }).done(function (data) {
        js('#result_testurlauthentication').removeClass('success error');
        if (data && data.success)
            js('#result_testurlauthentication').html(testOk).addClass('success');
        else {
            js('#result_testurlauthentication').html(testKo).addClass('error');
        }
    }).always(function () {
        js('#jform_testurlauthentication').blur();
    })
            ;

    return false;
}
;

    