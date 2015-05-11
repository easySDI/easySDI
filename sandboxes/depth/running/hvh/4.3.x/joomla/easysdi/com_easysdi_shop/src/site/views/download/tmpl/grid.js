var app;

Ext.onReady(function() {
    if ('undefined' === typeof app) {
        app = window.appname;
    }
    app.on("ready", function() {
            var grid = new predefinedPerimeter(perimeter);
            grid.init();
            grid.setListenerFeatureSelected(listenerFeatureSelected);
            grid.setListenerIndoorLevelChanged(cleanSelectionValues);
        }
    );
});

var listenerFeatureSelected = function(e) {
    selectLayer.removeAllFeatures();
    selectLayer.addFeatures([e.feature]);
    js('#url').val(e.feature.attributes[perimeter.featuretypefieldresource]);
    js('.sdi-map-feature-selection-name span').text(e.feature.attributes[perimeter.featuretypefieldname]);
    js('.sdi-map-feature-selection-description span').text(e.feature.attributes[perimeter.featuretypefielddescription]);
    enableSave();
};

var cleanSelectionValues = function() {
    js('#url').val('');
    js('.sdi-map-feature-selection-name span').text('');
    js('.sdi-map-feature-selection-description span').text('');
    enableSave();
};

var enableSave = function() {
    if (js('#termsofuse').is(':checked') == true && js('#url').val() != '')
        js('#saveSubmit').removeAttr('disabled', 'disabled');
    else
        js('#saveSubmit').attr('disabled', 'disabled');
}