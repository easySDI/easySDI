var app;

Ext.onReady(function () {
    if ('undefined' === typeof app) {
        app = window.appname;
    }
    app.on("ready", function () {
        var grid = new predefinedPerimeter(perimeter);
        grid.init();
        grid.setListenerFeatureSelected(listenerFeatureSelected);
        grid.setListenerIndoorLevelChanged(cleanSelectionValues);
        initStyleMap();
        selectLayer.styleMap = customStyleMap;
    }
    );
});

/**
 * Update form elements displaying informations after a feature was selected
 * @param {type} e
 * @returns {undefined}
 */
var listenerFeatureSelected = function (e) {
    selectLayer.removeAllFeatures();
    selectLayer.addFeatures([e.feature]);
    js('#featurecode').val(e.feature.attributes[perimeter.featuretypefieldresource]);
    js('.sdi-map-feature-selection-name span').text(e.feature.attributes[perimeter.featuretypefieldname]);
    js('.sdi-map-feature-selection-description span').text(e.feature.attributes[perimeter.featuretypefielddescription]);
    enableSave();
};

/**
 * Clean form when no feature is selected
 * @returns {undefined}
 */
var cleanSelectionValues = function () {
    js('#featurecode').val('');
    js('.sdi-map-feature-selection-name span').text('');
    js('.sdi-map-feature-selection-description span').text('');
    enableSave();
};

/**
 * Check if download can be activated and update form element accordingly
 * @returns {undefined}
 */
var enableSave = function () {
    if (js('#termsofuse').is(':checked') == true && js('#featurecode').val() != '') {
        js('#saveSubmit').removeAttr('disabled', 'disabled');
        js('#saveSubmit').attr('onclick', 'tokenize();');
        js('#saveSubmit').attr('href', js('#adminForm').attr('action') + '&id=' + js('#id').attr('value') + '&featurecode=' + js('#featurecode').attr('value'));
    }
    else {
        js('#saveSubmit').attr('disabled', 'disabled');
        js('#saveSubmit').attr('onclick', 'return false;');
        js('#saveSubmit').attr('href', '#');
    }  
}