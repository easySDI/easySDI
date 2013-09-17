var map, perimeterLayer, drawControls, selectLayer, hover, polygonLayer, boxLayer, selectControl, request, myLayer;
function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    boxLayer = new OpenLayers.Layer.Vector("Box layer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    polygonLayer.events.on({
        featuresadded: onFeaturesAdded,
        beforefeatureadded: beforeFeatureAdded
    });
    boxLayer.events.on({
        featuresadded: onFeaturesAdded,
        beforefeatureadded: beforeFeatureAdded
    });
    app.mapPanel.map.addLayers([polygonLayer, boxLayer]);
    polyOptions = {stopDown: true, stopUp: true};
    drawControls = {
        polygon: new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: polyOptions}),
        box: new OpenLayers.Control.DrawFeature(boxLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: polyOptions})
    };
    for (var key in drawControls) {
        app.mapPanel.map.addControl(drawControls[key]);
    }
}
function selectPerimeter1(){
    
}
function reloadFeatures1(){
    
}
function toggleControl(element) {
    resetAll();
    for (key in drawControls) {
        var control = drawControls[key];
        if (element == key) {
            control.activate();
        } else {
            control.deactivate();
        }
    }

    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val('freeperimeter');
    jQuery('#t-features').val('');
}

function onFeaturesAdded(event) {
    putFeaturesVerticesInHiddenField(event.features[0]);
}

function putFeaturesVerticesInHiddenField(feature) {
    var vertices = feature.geometry.getVertices();
    var pointsAsString = '';
    for (var i = 0; i < vertices.length; i++) {
        pointsAsString += vertices[i].x;
        pointsAsString += ' ';
        pointsAsString += vertices[i].y;
        pointsAsString += ', ';
    }
    jQuery('#t-features').val(JSON.stringify(pointsAsString));
    jQuery('#t-surface').val(JSON.stringify(feature.geometry.getArea()));
}

function beforeFeatureAdded(event) {
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].__proto__.CLASS_NAME == "OpenLayers.Layer.Vector") {
            app.mapPanel.map.layers[j].removeAllFeatures();
        }
    }
    jQuery('#t-features').val('');
    jQuery('#t-surface').val('');
}

function resetAll() {
    

    jQuery('#t-perimeter').val(jQuery('#perimeter').val());
    jQuery('#t-perimetern').val(jQuery('#perimetern').val());
    jQuery('#t-surface').val(jQuery('#surface').val());
    jQuery('#t-features').val(jQuery('#features').val());
    
    for (var j = 0; j < app.mapPanel.map.layers.length; j++) {
        if (app.mapPanel.map.layers[j].__proto__.CLASS_NAME == "OpenLayers.Layer.Vector") {
            app.mapPanel.map.layers[j].removeAllFeatures();
        }
    }
    if (app.mapPanel.map.getLayersByName("perimeterLayer").length > 0) {
        app.mapPanel.map.removeLayer(perimeterLayer);
        app.mapPanel.map.removeLayer(selectLayer);
        app.mapPanel.map.removeLayer(hover);        
    }
    if (app.mapPanel.map.getLayersByName("myLayer").length > 0) {
        app.mapPanel.map.removeLayer(myLayer);
    }
    app.mapPanel.map.removeControl(selectControl);
    for (key in drawControls) {
        var control = drawControls[key];
        control.deactivate();
    }
}

function savePerimeter() {
    jQuery("#progress").css('visibility', 'visible');
    request = false;
    if (window.XMLHttpRequest) {
        request = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try {
            request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                request = false;
            }
        }
    }
    if (!request) {
        alert('Error');
        return;
    }

    var extent = {"id": jQuery('#t-perimeter').val(), "name": jQuery('#t-perimetern').val(), "surface": jQuery('#t-surface').val(), "features": JSON.parse(jQuery('#t-features').val())};
    var query = "index.php?option=com_easysdi_shop&task=addExtentToBasket&item=" + JSON.stringify(extent);
    request.onreadystatechange = displayExtentRecap;
    request.open("GET", query, true);
    request.send(null);
}

function displayExtentRecap() {
    if (request.readyState === 4) {
        jQuery('#perimeter').val(jQuery('#t-perimeter').val());
        jQuery('#perimetern').val(jQuery('#t-perimetern').val());
        jQuery('#surface').val(jQuery('#t-surface').val());
        jQuery('#features').val(jQuery('#t-features').val());
        jQuery('#perimeter-recap').empty();
        jQuery('#perimeter-recap').append("<div><h3>Surface</h3></div>");
        jQuery('#perimeter-recap').append("<div>" + jQuery('#surface').val() + "</div>");
        jQuery('#perimeter-recap').append("<div><h3>" + jQuery('#perimetern').val() + "</h3></div>");
        var features_text = jQuery('#features').val();
        jQuery('#perimeter-recap').append("<div>" + features_text + "</div>");
//        if (features_text !== '')
//            var features = JSON.parse(features_text);
//        else
//            return;
//        jQuery.each(features, function(index, value) {
//            if (typeof value === "undefined")
//                return true;
//            jQuery('#perimeter-recap').append("<div>" + value.name + "</div>");
//        });
    }
}

function cancel() {
    resetAll();
    jQuery('#modal-perimeter [id^="btn-perimeter"]').removeClass('active');
    if (jQuery('#perimeter').val() != '') {
        eval('selectPerimeter' + jQuery('#perimeter').val() + '()');
        eval('reloadFeatures' + jQuery('#perimeter').val() + '()');
        jQuery('#btn-perimeter' + jQuery('#perimeter').val()).addClass('active');
    }
}

function selectPerimeter(perimeterid, perimetername, wmsurl, wmslayername, wfsurl, featuretypename, namespace, featuretypefieldgeometry, featuretypefieldid, featuretypefieldname) {
    resetAll();
    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');
    perimeterLayer = new OpenLayers.Layer.WMS("perimeterLayer",
            wmsurl,
            {layers: wmslayername})
    app.mapPanel.map.addLayer(perimeterLayer);
    app.mapPanel.map.setLayerIndex(perimeterLayer, 0);
    selectControl = new OpenLayers.Control.GetFeature({
        protocol: new OpenLayers.Protocol.WFS({
            version: "1.0.0",
            url: wfsurl,
            srsName: app.mapPanel.map.projection,
            featureType: featuretypename,
            featureNS: namespace,
            geometryName: featuretypefieldgeometry
        }),
        box: true,
        hover: true,
        multipleKey: "shiftKey",
        toggleKey: "ctrlKey"
    });
    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    hover = new OpenLayers.Layer.Vector("Hover", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    app.mapPanel.map.addLayers([selectLayer, hover]);
    selectControl.events.register("featureselected", this, function(e) {
        selectLayer.addFeatures([e.feature]);
        var features_text = jQuery('#t-features').val();
        if (features_text !== "")
            var features = JSON.parse(features_text);
        else
            var features = new Array();

        features.push({"id": e.feature.attributes[featuretypefieldid], "name": e.feature.attributes[featuretypefieldname]});
        jQuery('#t-features').val(JSON.stringify(features));
        if (jQuery('#t-surface').val() != '')
            var surface = parseInt(jQuery('#t-surface').val());
        else
            var surface = 0;
        jQuery('#t-surface').val(JSON.stringify(surface + e.feature.geometry.getArea()));
    });
    selectControl.events.register("featureunselected", this, function(e) {
        selectLayer.removeFeatures([e.feature]);
        var features_text = jQuery('#t-features').val();
        if (features_text !== "")
            var features = JSON.parse(features_text);
        else
            return;
        jQuery.each(features, function(index, value) {
            if (typeof value === "undefined")
                return true;
            if (value.id == e.feature.attributes[featuretypefieldid]) {
                features.splice(index, 1);
            }
        });
        if (features.size == 0)
            jQuery('#t-features').val('');
        else
            jQuery('#t-features').val(JSON.stringify(features));
    });
//    selectControl.events.register("hoverfeature", this, function(e) {
//        hover.addFeatures([e.feature]);
//    });
//    selectControl.events.register("outfeature", this, function(e) {
//        hover.removeFeatures([e.feature]);
//    });
    app.mapPanel.map.addControl(selectControl);
    selectControl.activate();
    return false;
}

function reloadFeatures(wfsurl, featuretypename, featuretypefieldid) {
    var wfsUrl = wfsurl + '?request=GetFeature&SERVICE=WFS&TYPENAME=' + featuretypename + '&VERSION=1.0.0';
    var wfsUrlWithFilter = wfsUrl + '&FILTER=';
    wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc">');
    var features_text = jQuery('#features').val();
    if (features_text !== "")
        var features = JSON.parse(features_text);
    else
        var features = new Array();
    if (features.length > 1)
    {
        wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Or>');
    }

    for (var i = 0; i < features.length; i++)
    {

        wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:PropertyIsEqualTo><ogc:PropertyName>' + featuretypefieldid + '</ogc:PropertyName><ogc:Literal>' + features[i].id + '</ogc:Literal></ogc:PropertyIsEqualTo>');
    }
    if (features.length > 1)
    {
        wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Or>');
    }
    wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Filter>');
    app.mapPanel.map.removeLayer(selectLayer);
    selectLayer = new OpenLayers.Layer.Vector("Selection", {
        strategies: [new OpenLayers.Strategy.Fixed()],
        protocol: new OpenLayers.Protocol.HTTP({
            url: wfsUrlWithFilter,
            format: new OpenLayers.Format.GML()
        })
    });
    app.mapPanel.map.addLayer(selectLayer);
}


