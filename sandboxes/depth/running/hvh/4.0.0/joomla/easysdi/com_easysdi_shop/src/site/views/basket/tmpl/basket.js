var map, perimeterLayer, drawControls, selectLayer, polygonLayer, selectControl, request, myLayer, fieldid, fieldname, loadingPerimeter;
function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    polygonLayer.events.on({
        featuresadded: onFeaturesAdded,
        beforefeatureadded: beforeFeatureAdded
    });
    app.mapPanel.map.addLayers([polygonLayer]);
    polyOptions = {stopDown: true, stopUp: true};
    drawControls = {
        polygon: new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: polyOptions}),
        box: new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: polyOptions})
    };
    for (var key in drawControls) {
        app.mapPanel.map.addControl(drawControls[key]);
    }
}
function selectPerimeter1() {
    drawControls['polygon'].activate();
}
function reloadFeatures1() {
    var wkt = 'POLYGON((' + JSON.parse(jQuery('#features').val()) + '))';
    var feature = new OpenLayers.Format.WKT().read(wkt);
    polygonLayer.addFeatures([feature]);
    app.mapPanel.map.zoomToExtent(polygonLayer.getDataExtent());
    
    putFeaturesVerticesInHiddenField(feature);
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
        if(i < vertices.length-1) pointsAsString += ', ';
    }
    jQuery('#t-features').val(JSON.stringify(pointsAsString));
    jQuery('#t-surface').val(JSON.stringify(feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
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
    if (typeof selectControl !== 'undefined') {
        selectControl.events.unregister("featureselected", this, listenerFeatureSelected);
        selectControl.events.unregister("featureunselected", this, listenerFeatureUnselected);
        app.mapPanel.map.removeControl(selectControl);
    }
    if (app.mapPanel.map.getLayersByName("perimeterLayer").length > 0) {
        perimeterLayer.events.unregister("loadend", perimeterLayer, listenerLoadEnd);
//        app.mapPanel.map.removeLayer(perimeterLayer);
//        app.mapPanel.map.removeLayer(selectLayer);        
    }
    if (app.mapPanel.map.getLayersByName("myLayer").length > 0) {
        app.mapPanel.map.removeLayer(myLayer);
    }
    
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

    var extent = {"id": jQuery('#t-perimeter').val(), 
                    "name": jQuery('#t-perimetern').val(), 
                    "surface": jQuery('#t-surface').val(), 
                    "allowedbuffer": jQuery('#allowedbuffer').val(),
                    "buffer": jQuery('#buffer').val(),
                    "features": JSON.parse(jQuery('#t-features').val())};
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
        jQuery('#perimeter-recap').append("<div><h3>"+Joomla.JText._('COM_EASYSDI_SHOP_BASKET_SURFACE', 'Surface')+"</h3>");
        jQuery('#perimeter-recap').append("<div>" + jQuery('#surface').val() + "</div></div>");
        
        jQuery('#perimeter-recap').append("<div><h3>" + jQuery('#perimetern').val() + "</h3></div>");
        var features_text = jQuery('#features').val();
        
        if (features_text == '')
            return;
        
        try {
            var features = JSON.parse(features_text);
            if(jQuery('#perimeter').val() == 1){
                jQuery('#perimeter-recap').append("<div>" + features + "</div>");
                return;
            }
            jQuery.each(features, function(index, value) {
                if (typeof value === "undefined")
                    return true;
                jQuery('#perimeter-recap').append("<div>" + value.name + "</div>");
            });
        } catch (e) {
            jQuery('#perimeter-recap').append("<div>" + features_text + "</div>");
        }
            
        
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

function selectPerimeter(isrestrictedbyperimeter, perimeterid, perimetername, wmsurl, wmslayername, wfsurl, featuretypename, namespace, featuretypefieldgeometry, featuretypefieldid, featuretypefieldname) {
    resetAll();
    fieldid = featuretypefieldid;
    fieldname = featuretypefieldname;
    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');
    if (isrestrictedbyperimeter === 0) {
        perimeterLayer = new OpenLayers.Layer.WMS("perimeterLayer",
                wmsurl,
                {layers: wmslayername})
                
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
        multipleKey: "shiftKey",
        toggleKey: "ctrlKey"
    });

    } else {
        var featurerestriction = getUserRestrictedExtentFeature(userperimeter);
        perimeterLayer = new OpenLayers.Layer.Vector("perimeterLayer", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            protocol: new OpenLayers.Protocol.WFS({
                url: wfsurl,
                featureType: featuretypename,
                featureNS: namespace,
                srsName: app.mapPanel.map.projection
            }),
            styleMap: new OpenLayers.StyleMap({
                strokeWidth: 3,
                strokeColor: "#333333"
            }),
            filter: new OpenLayers.Filter.Spatial({
                type: OpenLayers.Filter.Spatial.INTERSECTS,
                value: featurerestriction.geometry
            })
        });
        
        selectControl = new OpenLayers.Control.GetFeature({
        protocol: new OpenLayers.Protocol.WFS({
            version: "1.0.0",
            url: wfsurl,
            srsName: app.mapPanel.map.projection,
            featureType: featuretypename,
            featureNS: namespace,
            geometryName: featuretypefieldgeometry,
            defaultFilter: new OpenLayers.Filter.Spatial({
                type: OpenLayers.Filter.Spatial.INTERSECTS,
                value: getUserRestrictedExtentFeature(userperimeter).geometry
            })
        }),
        box: true,
        multipleKey: "shiftKey",
        toggleKey: "ctrlKey"
    });
    }
    loadingPerimeter = new Ext.LoadMask(Ext.getBody(), {
        msg: "Chargement de la couche de périmètre..."
    });
    loadingPerimeter.show();
    app.mapPanel.map.addLayer(perimeterLayer);
    perimeterLayer.events.register("loadend", perimeterLayer, listenerLoadEnd);
    app.mapPanel.map.setLayerIndex(perimeterLayer, 0);
    
    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    app.mapPanel.map.addLayer(selectLayer);
    selectControl.events.register("featureselected", this, listenerFeatureSelected);
    selectControl.events.register("featureunselected", this, listenerFeatureUnselected);
    app.mapPanel.map.addControl(selectControl);
    selectControl.activate();
    return false;
}
;

var listenerLoadEnd = function() {
    loadingPerimeter.hide();
};
var listenerFeatureSelected = function(e) {
    var alreadySelected = selectLayer.features;
    for (var i = 0; i < alreadySelected.length; i++) {
        if (alreadySelected[i].attributes[fieldid] === e.feature.attributes[fieldid])
            return;
    }
    selectLayer.addFeatures([e.feature]);
    var features_text = jQuery('#t-features').val();
    if (features_text !== "")
        var features = JSON.parse(features_text);
    else
        var features = new Array();
    features.push({"id": e.feature.attributes[fieldid], "name": e.feature.attributes[fieldname]});
    jQuery('#t-features').val(JSON.stringify(features));
    if (jQuery('#t-surface').val() !== '')
        var surface = parseInt(jQuery('#t-surface').val());
    else
        var surface = 0;
    jQuery('#t-surface').val(JSON.stringify(surface + e.feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
};
var listenerFeatureUnselected = function(e) {
    selectLayer.removeFeatures([e.feature]);
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
};
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

function getUserRestrictedExtentFeature(text) {
    var wkt = 'POLYGON((' + text + '))';
    var feature = new OpenLayers.Format.WKT().read(wkt);
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection('EPSG:4326'),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    return new OpenLayers.Feature.Vector(geometry);
}
