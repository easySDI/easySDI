function selectPerimeter(perimeter, isrestrictedbyperimeter) {
    resetAllSelection();
    selectPredefinedPerimeter(perimeter, isrestrictedbyperimeter);
    return false;
}

function selectPredefinedPerimeter(perimeter, isrestrictedbyperimeter) {
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
    selectLayer.styleMap = customStyleMap;
    addSelectCounter(perimeter.id)
    return grid;
}

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
var listenerIndoorLevelChanged = function (e) {
    jQuery('#t-features').val('');
    updateSelectCounter(selectLayer.features);
};

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
var listenerWFSFeatureAdded = function (e) {
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
var listenerFeatureSelected = function (e) {
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

    orderSurfaceChecking();
    selectLayer.addFeatures([e.feature]);
    updateSelectCounter(selectLayer.features);
};

/**
 * 
 * @param {type} e
 * @returns {undefined}
 */
var listenerFeatureUnselected = function (e) {
    selectLayer.removeFeatures([e.feature]);
    /*var features = miniLayer.features;
     for (var i = 0; i < features.length; i++) {
     if (features[i].attributes['id'] === e.feature.attributes['id']) {
     miniLayer.removeFeatures([features[i]]);
     break;
     }
     }*/
    var features_text = jQuery('#t-features').val();
    if (features_text !== "")
        var features = JSON.parse(features_text);
    else
        return;

    jQuery.each(features, function (index, value) {
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

    orderSurfaceChecking();
    updateSelectCounter(selectLayer.features);
};

/**
 * 
 * @param {type} perimeter
 * @returns {undefined}
 */
function reloadFeatures(perimeter) {
    selectPredefinedPerimeter(perimeter, 0);
    var wfsurl = perimeter.wfsurl;
    var featuretypefieldid = perimeter.prefix + ':' + perimeter.featuretypefieldid;

    jQuery('#t-features').val(jQuery('#features').val());
    jQuery('#t-surface').val(jQuery('#surface').val());

    var features_text = jQuery('#features').val();
    if (features_text !== "") {
        var features = JSON.parse(features_text);
    } else {
        var features = new Array();
    }

    var tempWFSfilterList = [];
    var tempWFSfilter;

    for (var i = 0; i < features.length; i++) {
        tempWFSfilterList.push(
                new OpenLayers.Filter.Comparison({
                    type: OpenLayers.Filter.Comparison.EQUAL_TO,
                    property: featuretypefieldid,
                    value: features[i].id
                }));
    }

    initStyleMap();

    if (features.length > 1) {
        tempWFSfilter = new OpenLayers.Filter.Logical({
            type: OpenLayers.Filter.Logical.OR,
            filters: tempWFSfilterList
        });
    }
    else {
        tempWFSfilter = tempWFSfilterList[0];
    }

    var protoWFS = new OpenLayers.Protocol.WFS(
            {
                version: "1.0.0",
                url: wfsurl,
                featureType: perimeter.featuretypename,
                featureNS: perimeter.namespace,
                featurePrefix: perimeter.prefix,
                geometryName: perimeter.featuretypefieldgeometry,
                defaultFilter: tempWFSfilter
            }
    );
    protoWFS.read({
        readOptions: {output: "object"},
        resultType: "hits",
        maxFeatures: null,
        callback: function (resp) {
            selectLayer.addFeatures(resp.features);
            selectControl.modifiers = {
                multiple: selectControl.multiple,
                toggle: selectControl.toggle
            };

            selectControl.select(resp.features);
            protoWFS.defaultFilter = null;
            updateSelectCounter(selectLayer.features);
            for (var i = 0; i < resp.features.length; i++) {
                miniLayer.addFeatures([resp.features[i].clone()]);
                if (typeof resp.features[i].data[fieldname] === "undefined") {
                    jQuery('#perimeter-recap-details').append(jQuery('<div>' + resp.features[i] + '</div>'));
                } else {
                    jQuery('#perimeter-recap-details').append(jQuery('<div>' + resp.features[i].data[fieldname] + '</div>'));
                }
                jQuery('#perimeter-recap-details').show();
            }
            app.mapPanel.map.zoomToExtent(selectLayer.getDataExtent());
        }
    });
}
;



