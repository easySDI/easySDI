js = jQuery.noConflict();

js(document).ready(function () {
    enablePricing();
    enableAccessScope();
    onProductStorageChange();
    onPricingChange();
    toggleDownload();
    toggleExtraction();
    enableFreePerimeter();

    js('#adminForm').submit(function (event) {
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
    js('#jform_hasextraction').click(function () {
        toggleExtraction();
    })
    js('#jform_hasdownload').click(function () {
        toggleDownload();
    })
});

Joomla.submitbutton = function (task)
{

    //cancel
    if (task == 'diffusion.cancel') {
        Joomla.submitform(task, document.getElementById('adminForm'));
    }
    //create or update
    else {

        //cleanup unused profiles
        if (js('#jform_pricing_id').val() != '3') {
            js("jform_pricing_profile_id").val([]);
            js('#pricing_profile_id option').remove();
        }

        if (document.formvalidator.isValid(document.id('adminForm'))) {
            if (js('input[name="jform[hasextraction]"]:checked').val() == 1) {
                //check that at least one perimeter is active
                if (jQuery("[id^=jform_perimeter][id$=_1]:checked").length < 1) {
                    alert(msgNoPerimeter);
                } else {
                    Joomla.submitform(task, document.getElementById('adminForm'));
                }
            } else {
                Joomla.submitform(task, document.getElementById('adminForm'));
            }
        }
        else { //form is invalid
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
            js('#fileurl, #testurlauthentication').hide();
            js('#perimeter_id,#packageurl,#userurl, #passurl').show();
            js('#packageurl').attr('required');
            break;
    }
}

var globdata;

function onPricingChange() {

    switch (js('#jform_pricing_id').val()) {
        case '1': // FREE
            //js('#fieldset_download').show();
            unlockDownload();
            hidePricingProfile();
            break;

        case '2': // FEE WITHOUT PRICING PROFILE
            //js('#fieldset_download').hide();
            lockDownload();
            hidePricingProfile();
            break;

        case '3': // FEE WITH PRICING PROFILE
            //js('#fieldset_download').hide();
            lockDownload();
            showPricingProfile();
            break;
    }
}

function showPricingProfile() {
    //update list
    if (!js('#pricing_profile_id option').length) {
        js.ajax({
            url: urlProfiles,
            type: "POST",
            data: {
                version_id: version
            }
        }).fail(function () {
            //console.log('todo');
        }).done(function (data) {
            data.each(function (item) {
                js('#pricing_profile_id select').append(js('<option>', {
                    value: item.id,
                    text: item.name,
                    selected: item.affected_diffusion != null
                })).trigger('liszt:updated');
            });
        });
    }
    js('#pricing_profile_id').show();
    js('#jform_pricing_profile_id').addClass('required');
}

function hidePricingProfile() {
    js('#pricing_profile_id').hide();
    js('#jform_pricing_profile_id').removeClass('required');
}

function enablePricing() {
    if (sdiPricingActivated) {
        js('#pricing_remark').show();
        js('#pricing_id').show();
    } else {
        js('#pricing_remark').hide();
        js('#pricing_id').hide();
        js('#jform_pricing_id').val(sdiPricingFreeVal);
    }
}

function lockDownload() {
    js("label[for='jform_hasdownload0']").click();
    js("label[for='jform_hasdownload0']").addClass('disabled');
    js("#jform_hasdownload0").prop("checked", true);
    js("#jform_hasdownload1").prop("checked", false);
    js("label[for='jform_hasdownload1']").hide();
    js('#no-download-on-paid-message').remove();
    js("#fieldset_download > legend").append('<small id="no-download-on-paid-message" class="text-error">' + Joomla.JText._('COM_EASYSDI_SHOP_FORM_MSG_DIFFUSION_DOWNLOAD_DISABLED_WITH_PAID') + '</small>');
}

function unlockDownload() {
    js("label[for='jform_hasdownload0']").removeClass('disabled');
    js("label[for='jform_hasdownload1']").show();
    js('#no-download-on-paid-message').remove();
}

function toggleDownload() {
    if (js('input[name="jform[hasdownload]"]:checked').val() == 1) {
        js('#div_download').show();
    } else {
        js('#div_download').hide();
    }
}

function toggleExtraction() {
    if (js('input[name="jform[hasextraction]"]:checked').val() == 1) {
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
            console.log(data);
        }
    }).always(function () {
        js('#jform_testurlauthentication').blur();
    })
            ;

    return false;
}
    