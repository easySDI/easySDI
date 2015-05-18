var fromreload = false;

function selectPerimeter(perimeter, isrestrictedbyperimeter) {
    resetAll();

    fieldid = perimeter.featuretypefieldid;
    fieldname = perimeter.featuretypefieldname;
    fieldlevel = perimeter.featuretypefieldlevel;
    prefix = perimeter.prefix;
    jQuery('#t-perimeter').val(perimeter.id);
    jQuery('#t-perimetern').val(perimeter.name);
    jQuery('#t-features').val('');
    jQuery('#t-surface').val('');

    var grid = new predefinedPerimeter(perimeter);

    //Current user is not subject to perimeter restriction
    if (isrestrictedbyperimeter === 0) {
        grid.init();
    } else {
        var featurerestriction = getUserRestrictedExtentFeature(userperimeter);
        grid.init(featurerestriction);
    }
    grid.setListenerFeatureAdded(listenerFeatureAdded);
    grid.setListenerFeatureSelected(listenerFeatureSelected);
    grid.setListenerFeatureUnSelected(listenerFeatureUnselected);
    grid.setListenerIndoorLevelChanged(listenerIndoorLevelChanged);
    toggleSelectControl('selection');
    return false;
}

//Get the OpenLayers Filter to apply for features selection
//var getSelectControlLevelFilter = function() {
//    selectControl.fieldlevel = prefix + ':' + fieldlevel;
//    return new OpenLayers.Filter.Comparison({
//        type: OpenLayers.Filter.Comparison.EQUAL_TO,
//        property: selectControl.fieldlevel,
//        value: app.mapPanel.map.indoorlevelslider.getLevel().code
//    });
//};

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
var listenerIndoorLevelChanged = function(e) {
    jQuery('#t-features').val('');
};

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
var listenerWFSFeatureAdded = function(e) {
    listenerFeatureAdded(e);

    if (typeof e.feature.data[fieldname] === "undefined") {
        jQuery('#perimeter-recap-details').append(jQuery('<div>' + e.feature + '</div>'));
    } else {
        jQuery('#perimeter-recap-details').append(jQuery('<div>' + e.feature.data[fieldname] + '</div>'));
    }
    jQuery('#perimeter-recap-details').show();
}

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
var listenerFeatureSelected = function(e) {
    if (fromreload === true) {
        selectLayer.removeAllFeatures();
        miniLayer.removeAllFeatures();
        fromreload = false;
    }
    var alreadySelected = selectLayer.features;
    for (var i = 0; i < alreadySelected.length; i++) {
        if (alreadySelected[i].attributes[fieldid] === e.feature.attributes[fieldid])
            return;
    }

    var features_text = jQuery('#t-features').val();
    var features;
    if (features_text !== "") {
        try {
            features = JSON.parse(features_text);
        } catch (ex) {
            //t-features must hold geometry wkt definition
            features = new Array();
        }
    } else {
        features = new Array();
    }
    features.push({"id": e.feature.attributes[fieldid], "name": e.feature.attributes[fieldname]});
    jQuery('#t-features').val(JSON.stringify(features));
    if (jQuery('#t-surface').val() !== '')
        var surface = parseInt(jQuery('#t-surface').val());
    else
        var surface = 0;

    jQuery('#t-surface').val(JSON.stringify(surface + e.feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));

    selectLayer.addFeatures([e.feature]);
};

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
var listenerFeatureUnselected = function(e) {
    selectLayer.removeFeatures([e.feature]);
    var features = miniLayer.features;
    for (var i = 0; i < features.length; i++) {
        if (features[i].attributes['id'] === e.feature.attributes['id']) {
            miniLayer.removeFeatures([features[i]]);
            break;
        }
    }
    var features_text = jQuery('#t-features').val();
    if (features_text !== "")
        var features = JSON.parse(features_text);
    else
        return;
    jQuery.each(features, function(index, value) {
        if (typeof value === "undefined")
            return true;
        if (value.id === e.feature.attributes[fieldid]) {
            features.splice(index, 1);
        }
    });
    if (features.size === 0)
        jQuery('#t-features').val('');
    else
        jQuery('#t-features').val(JSON.stringify(features));

    if (jQuery('#t-surface').val() !== '') {
        var surface = parseInt(jQuery('#t-surface').val());
        jQuery('#t-surface').val(JSON.stringify(surface - e.feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
    }
};

/**
 * 
 * @param {type} perimeter
 * @returns {undefined}
 */
function reloadFeatures(perimeter) {
    var wfsurl = perimeter.wfsurl;
    var featuretypename = perimeter.prefix + ':' + perimeter.featuretypename;
    var featuretypefieldid = perimeter.prefix + ':' + perimeter.featuretypefieldid;

    jQuery('#t-features').val(jQuery('#features').val());
    jQuery('#t-surface').val(jQuery('#surface').val());
    var wfsUrl = wfsurl + '?request=GetFeature&SERVICE=WFS&TYPENAME=' + featuretypename + '&VERSION=1.0.0';
    var wfsUrlWithFilter = wfsUrl + '&FILTER=';
    wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc">');

    var features_text = jQuery('#features').val();
    if (features_text !== "") {
        var features = JSON.parse(features_text);
    } else {
        var features = new Array();
    }

    if (features.length > 1) {
        wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Or>');
    }

    for (var i = 0; i < features.length; i++) {
        wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:PropertyIsEqualTo><ogc:PropertyName>' + featuretypefieldid + '</ogc:PropertyName><ogc:Literal>' + features[i].id + '</ogc:Literal></ogc:PropertyIsEqualTo>');
    }
    if (features.length > 1) {
        wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Or>');
    }
    wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Filter>');

//    selectLayer.events.register("featureadded", selectLayer, listenerFeatureAdded);
    app.mapPanel.map.removeLayer(selectLayer);

    selectLayer = new OpenLayers.Layer.Vector("Selection", {
        strategies: [new OpenLayers.Strategy.Fixed()],
        protocol: new OpenLayers.Protocol.HTTP({
            url: wfsUrlWithFilter,
            format: new OpenLayers.Format.GML()
        })
    });
    selectLayer.events.register("featureadded", selectLayer, listenerWFSFeatureAdded);
    selectLayer.events.register("loadend", selectLayer, listenerFeatureAddedToZoom);
    app.mapPanel.map.addLayer(selectLayer);
    fromreload = true;
}
;



