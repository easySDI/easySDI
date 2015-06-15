function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {
        srsName: app.mapPanel.map.projection,
        projection: app.mapPanel.map.projection,
        styleMap: customStyleMap});
    /*polygonLayer.events.on({
     featuresadded: onFeaturesAdded,
     beforefeatureadded: beforeFeatureAdded
     });*/

    //polygonLayer.events.register("featureadded", polygonLayer, listenerFeatureAdded);
    polygonLayer.events.on({
        featuresadded: onPolygonAdded,
        beforefeatureadded: beforeFeatureAdded,
        featuremodified: onPolygonModified,
        beforefeaturemodified: beforeFeatureAdded
    });


    boxLayer = new OpenLayers.Layer.Vector("Rectangle Layer", {
        srsName: app.mapPanel.map.projection,
        projection: app.mapPanel.map.projection,
        styleMap: customStyleMap});
    boxLayer.events.on({
        featuresadded: onBoxAdded
    });

    drawControls = {
        polygon: new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}}),
        box: new OpenLayers.Control.DrawFeature(boxLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}})
    };
    for (var key in drawControls) {
        app.mapPanel.map.addControl(drawControls[key]);
    }
    app.mapPanel.map.addLayers([polygonLayer, boxLayer]);
}

function onPolygonAdded(event) {
    putFeaturesVerticesInHiddenField(event.features[0].clone());
    miniLayer.removeAllFeatures();
    miniLayer.addFeatures([event.features[0].clone()]);
    orderSurfaceChecking();
    checkSelfIntersect(event.features[0]);
    selectEditTool(event);
}

function onPolygonModified(event) {
    putFeaturesVerticesInHiddenField(event.feature.clone());
    miniLayer.removeAllFeatures();
    miniLayer.addFeatures([event.feature.clone()]);
    orderSurfaceChecking();
    checkSelfIntersect(event.feature);
}

function onBoxAdded(event) {
    //only for the Box, not the edit handles...
    if (event.features[0].state == 'Insert') {
        miniLayer.removeAllFeatures();
        miniLayer.addFeatures([event.features[0].clone()]);
        putFeaturesVerticesInHiddenField(event.features[0].clone());
        checkMinimumRectangle(event);
        orderSurfaceChecking();
        selectEditTool(event);
    }

}

function onBoxModified(event) {
    miniLayer.removeAllFeatures();
    miniLayer.addFeatures([event.feature.clone()]);
    putFeaturesVerticesInHiddenField(event.feature.clone());
    orderSurfaceChecking();
}

function checkMinimumRectangle(event) {
    feature = event.features[0];
    if (feature.geometry.getArea() < mapMinSurfaceRectangle) {
        feature.geometry.resize(50, feature.geometry.getCentroid());
    }
    boxLayer.redraw();
}

function onFeaturesAdded(event) {
    putFeaturesVerticesInHiddenField(event.features[0].clone());
}

function selectEditTool(event) {
    if (event.features[0].state == 'Insert') {
        if (freePerimeterTool == 'polygon') {
            selectPolygonEdit();
        }
        else if (freePerimeterTool == 'rectangle') {
            selectRectangleEdit(event.features[0]);
        }
    }
}

function selectPolygonEdit() {
    //resetAll();
    setFreePerimeterTool('polygon');
    disableDrawControls();
    jQuery('#btn-perimeter1b').removeClass('active');
    selectControl = new OpenLayers.Control.ModifyFeature(polygonLayer);
    selectControl.selectFeature(polygonLayer.features[polygonLayer.features.length - 1]);
    initSelectcontrol(selectControl);
}

function selectRectangleEdit(feature) {
    getRectangleRotation(feature)
    setFreePerimeterTool('rectangle');
    disableDrawControls();
    jQuery('#btn-perimeter1a').removeClass('active');
    selectControl = new OpenLayers.Control.TransformFeature(boxLayer, {
        renderIntent: "transform",
        rotationHandleSymbolizer: "rotate",
        irregular: true,
        eventListeners: {
            'transformcomplete': function (o) {
                onBoxModified(o);
            }
        }
    });

    app.mapPanel.map.addControl(selectControl);
    selectControl.setFeature(feature,{rotation: getRectangleRotation(feature)});
    selectControl.activate();
    initSelectcontrol(selectControl);
}

function getRectangleRotation(feature) {
    var dx = feature.geometry.components[0].components[1].x - feature.geometry.components[0].components[0].x;
    var dy = feature.geometry.components[0].components[1].y - feature.geometry.components[0].components[0].y;
    var angle = Math.atan2(dx, dy);
    return angle * -180 / Math.PI;
}

function putFeaturesVerticesInHiddenField(feature) {
    //jQuery('#t-surface').val(JSON.stringify(feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
    orderSurfaceChecking();

    var geometry = feature.geometry.transform(
            new OpenLayers.Projection(app.mapPanel.map.projection),
            new OpenLayers.Projection("EPSG:4326")
            );

    var wkt = new OpenLayers.Format.WKT();
    var featureAsString = wkt.write(feature);
    jQuery('#t-features').val(featureAsString);
}

function selectPerimeter1() {
    if (freePerimeterTool == 'polygon') {
        selectPolygon();
    }
    else {
        selectRectangle();
    }
}


function checkSelfIntersect(feature) {
    var lines = new Array();
    var isSelfIntersect = false;

    // do not test non-polygons
    if (feature.geometry instanceof OpenLayers.Geometry.Polygon) {
        var polygonSize = feature.geometry.components[0].components.length;
        var components = feature.geometry.components[0].components;
        var i = 0;
        while (i < polygonSize - 1) {
            lines.push(new OpenLayers.Geometry.LineString([
                new OpenLayers.Geometry.Point(components [i].x, components [i].y),
                new OpenLayers.Geometry.Point(components [i + 1].x, components [i + 1].y)
            ]));

            i++;
        }
        for (i = 0; i < lines.length; i++) {
            count = 0;
            for (j = 0; j < lines.length; j++) {
                //Do not compare a line with itslf
                if (i != j) {
                    if (lines[i].intersects(lines[j])) {
                        count++;
                    }
                }
                if (count > 2) {
                    //More than 2 intersectios for a line, mean that a line intersects another one.
                    isSelfIntersect = true;
                    break;
                }
            }
        }
        if (isSelfIntersect) {
            //More than 2 intersectios for a line, mean that a line intersects another one.
            var message = Joomla.JText._('COM_EASYSDI_SHOP_BASKET_ERROR_SELFINTERSECT', 'Self-intersecting perimeter is not allowed');
            alertControl.raiseAlert('<span>' + message + '</span>');
        } else {
            alertControl.clearAlert();
        }
    }
}


function reloadFeatures1() {
    var wkt = jQuery('#features').val();
    var feature = new OpenLayers.Format.WKT().read(wkt);

    if (freePerimeterTool == 'polygon') {
        reloadPolygon(feature);
    }
    else if (freePerimeterTool == 'rectangle') {
        reloadRectangle(feature);
    }
}


function reloadRectangle(feature) {
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    boxLayer.addFeatures([feature]);
    putFeaturesVerticesInHiddenField(feature.clone());
    selectRectangleEdit(feature);
}
function reloadPolygon(feature) {
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    polygonLayer.events.register("featureadded", polygonLayer, listenerFeatureDrawToZoom);
    polygonLayer.addFeatures([feature]);
    putFeaturesVerticesInHiddenField(feature.clone());
    selectPolygonEdit();
}

var listenerFeatureDrawToZoom = function (e) {
    polygonLayer.events.unregister("featureadded", polygonLayer, listenerFeatureDrawToZoom);
    listenerFeatureAddedToZoom(e);
};

function selectPolygon() {
    resetAll();
    setFreePerimeterTool('polygon');
    selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}});
    initSelectcontrol(selectControl);
}
;

function selectRectangle() {
    resetAll();
    setFreePerimeterTool('rectangle');
    selectControl = new OpenLayers.Control.DrawFeature(boxLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}});
    initSelectcontrol(selectControl);
}

function initSelectcontrol(selectControl) {
    app.mapPanel.map.addControl(selectControl);
    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val(Joomla.JText._('FREEPERIMETER', 'Périmètre libre'));
    jQuery('#t-features').val('');
    toggleSelectControl('selection');
}

function setFreePerimeterTool(tool) {
    freePerimeterTool = tool;
    jQuery('#t-freeperimetertool').val(tool);
}

