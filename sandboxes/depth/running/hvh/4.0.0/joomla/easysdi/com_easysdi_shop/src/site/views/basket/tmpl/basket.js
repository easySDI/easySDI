var map, perimeterLayer, selectControl, selectLayer, polygonLayer, selectControl, request, myLayer, fieldid, fieldname, loadingPerimeter, miniLayer, minimap;
function initMiniMap() {
    minimap = new OpenLayers.Map({div: 'minimap', controls: []});
    var layer = app.mapPanel.map.layers[1].clone();
    minimap.addLayer(layer);
    minimap.setBaseLayer(layer);
    minimap.zoomToExtent(app.mapPanel.map.getExtent());
    miniLayer = new OpenLayers.Layer.Vector("miniLayer");
    minimap.addLayer(miniLayer);
    miniLayer.events.register("featuresadded", miniLayer, listenerMiniFeaturesAdded);
}

var listenerMiniFeaturesAdded = function() {
    minimap.zoomToExtent(miniLayer.getDataExtent());

};

var listenerFeatureAdded = function(e) {
    miniLayer.addFeatures([e.feature.clone()]);

    var toobig = false;
    var toosmall = false;
    if (jQuery('#surfacemax').val() !== '') {
        if (parseFloat(jQuery('#t-surface').val()) > parseFloat(jQuery('#surfacemax').val()))
            toobig = true;
    }
    if (jQuery('#surfacemin').val() !== '') {
        if (parseFloat(jQuery('#t-surface').val()) < parseFloat(jQuery('#surfacemin').val()))
            toosmall = true;
    }
    if (toobig || toosmall) {
        jQuery("#alert_template").empty();
        jQuery("#alert_template").append('<span>Your current selection of ' + jQuery('#t-surface').val() + ' is not in the allowed surface range [' + jQuery('#surfacemin').val() + ',' + jQuery('#surfacemax').val() + '].</span>');
        jQuery('#alert_template').fadeIn('slow');
        jQuery('#btn-saveperimeter').attr("disabled", "disabled");
    } else {
        jQuery('#alert_template').fadeOut('slow');
        jQuery('#btn-saveperimeter').removeAttr("disabled");

    }
};



function clearLayersVector() {
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].__proto__.CLASS_NAME === "OpenLayers.Layer.Vector") {
            app.mapPanel.map.layers[j].removeAllFeatures();
        }
    }
    for (var j = 0; j < minimap.layers.length; j++) {
        if (minimap.layers[j].__proto__.CLASS_NAME === "OpenLayers.Layer.Vector") {
            minimap.layers[j].removeAllFeatures();
        }
    }
}

function clearTemporaryFields() {
    jQuery('#t-perimeter').val('');
    jQuery('#t-perimetern').val('');
    jQuery('#t-surface').val('');
    jQuery('#t-features').val('');
    jQuery('#alert_template').fadeOut('slow');
    jQuery('#btn-saveperimeter').removeAttr("disabled");
}

function resetTemporaryFields() {
    jQuery('#t-perimeter').val(jQuery('#perimeter').val());
    jQuery('#t-perimetern').val(jQuery('#perimetern').val());
    jQuery('#t-surface').val(jQuery('#surface').val());
    jQuery('#t-features').val(jQuery('#features').val());
    jQuery('#alert_template').fadeOut('slow');
    jQuery('#btn-saveperimeter').removeAttr("disabled");
}

function saveTemporaryFields() {
    jQuery('#perimeter').val(jQuery('#t-perimeter').val());
    jQuery('#perimetern').val(jQuery('#t-perimetern').val());
    jQuery('#surface').val(jQuery('#t-surface').val());
    jQuery('#features').val(jQuery('#t-features').val());
}

function beforeFeatureAdded(event) {
    clearLayersVector();

    jQuery('#t-features').val('');
    jQuery('#t-surface').val(JSON.stringify(event.feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
}

function resetAll() {
    resetTemporaryFields();
    clearLayersVector();
    jQuery('#btns-selection').show();

    if (typeof selectControl !== 'undefined') {
        selectControl.deactivate();
        toggleSelectControl('pan');
        selectControl.events.unregister("featureselected", this, listenerFeatureSelected);
        selectControl.events.unregister("featureunselected", this, listenerFeatureUnselected);
        app.mapPanel.map.removeControl(selectControl);
    }
    if (app.mapPanel.map.getLayersByName("perimeterLayer").length > 0) {
        perimeterLayer.events.unregister("loadend", perimeterLayer, listenerLoadEnd);
        app.mapPanel.map.removeLayer(perimeterLayer);
        app.mapPanel.map.removeLayer(selectLayer);
    }
    if (app.mapPanel.map.getLayersByName("myLayer").length > 0) {
        app.mapPanel.map.removeLayer(myLayer);
    }
    for (key in drawControls) {
        var control = drawControls[key];
        control.deactivate();
    }
}

function toggleSelectControl(action) {
    if (action == 'selection') {
        if (typeof selectControl !== 'undefined') {
            jQuery('#modal-perimeter [id^="btn-selection"]').addClass('active');
            jQuery('#modal-perimeter [id^="btn-pan"]').removeClass('active');
            selectControl.activate();
        } else {
            jQuery('#modal-perimeter [id^="btn-pan"]').addClass('active');
            jQuery('#modal-perimeter [id^="btn-selection"]').removeClass('active');
        }
    } else {
        jQuery('#modal-perimeter [id^="btn-pan"]').addClass('active');
        jQuery('#modal-perimeter [id^="btn-selection"]').removeClass('active');
        selectControl.deactivate();
    }
}

function cancel() {
    resetAll();
    jQuery('#modal-perimeter [id^="btn-perimeter"]').removeClass('active');
    if (jQuery('#perimeter').val() !== '') {
        eval('selectPerimeter' + jQuery('#perimeter').val() + '()');
        eval('reloadFeatures' + jQuery('#perimeter').val() + '()');
        jQuery('#btn-perimeter' + jQuery('#perimeter').val()).addClass('active');
    }
}

function savePerimeter() {
    jQuery("#progress").css('visibility', 'visible');
//    request = false;
//    if (window.XMLHttpRequest) {
//        request = new XMLHttpRequest();
//    } else if (window.ActiveXObject) {
//        try {
//            request = new ActiveXObject("Msxml2.XMLHTTP");
//        } catch (e) {
//            try {
//                request = new ActiveXObject("Microsoft.XMLHTTP");
//            } catch (e) {
//                request = false;
//            }
//        }
//    }
//    if (!request) {
//        alert('Error');
//        return;
//    }

    var extent = {"id": jQuery('#t-perimeter').val(),
        "name": jQuery('#t-perimetern').val(),
        "surface": jQuery('#t-surface').val(),
        "allowedbuffer": jQuery('#allowedbuffer').val(),
        "buffer": jQuery('#buffer').val(),
        "features": JSON.parse(jQuery('#t-features').val())};
//    var query = "index.php?option=com_easysdi_shop&task=addExtentToBasket&item=" + JSON.stringify(extent);
//    request.onreadystatechange = displayExtentRecap;
//    request.open("GET", query, true);
//    request.send(null);

    jQuery.ajax({
        url: "index.php?option=com_easysdi_shop&task=addExtentToBasket&item=" + JSON.stringify(extent),
        success: function(data) {
            displayExtentRecap();
        }
    });

//    if (typeof selectLayer !== 'undefined') {
//        miniLayer.addFeatures(selectLayer.features);
//    }else if (typeof myLayer !== 'undefined') {
//        miniLayer.addFeatures(myLayer.features);
//    } else {
//        miniLayer.addFeatures(polygonLayer.features);
//    }
}

function displayExtentRecap() {
//    if (request.readyState === 4) {
    saveTemporaryFields();

    jQuery('#perimeter-recap').empty();
    jQuery('#perimeter-recap').append("<div><h3>" + Joomla.JText._('COM_EASYSDI_SHOP_BASKET_SURFACE', 'Surface') + "</h3>");
    jQuery('#perimeter-recap').append("<div>" + jQuery('#surface').val() + "</div></div>");

    jQuery('#perimeter-recap').append("<div><h3>" + jQuery('#perimetern').val() + "</h3></div>");
    var features_text = jQuery('#features').val();

    if (features_text === '')
        return;

    try {
        var features = JSON.parse(features_text);

        jQuery.each(features, function(index, value) {
            if (typeof value === "undefined")
                return true;

            if (typeof value.name === "undefined") {
                jQuery('#perimeter-recap').append("<div>" + features + "</div>");
                return false;
            }
            jQuery('#perimeter-recap').append("<div>" + value.name + "</div>");
        });
    } catch (e) {
        jQuery('#perimeter-recap').append("<div>" + features_text + "</div>");
    }
//    }
}






