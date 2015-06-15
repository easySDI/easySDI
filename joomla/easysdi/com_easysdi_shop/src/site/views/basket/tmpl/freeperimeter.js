function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {
        srsName: app.mapPanel.map.projection,
        projection: app.mapPanel.map.projection,
        styleMap: customStyleMap});

    /*polygonLayer.events.on({
     featuresadded: onPolygonAdded,
     beforefeatureadded: beforeFeatureAdded,
     featuremodified: onPolygonModified,
     beforefeaturemodified: beforeFeatureAdded
     });*/

    polygonLayer.events.on({
        featuresadded: onPolygonAdded,
        featuremodified: onPolygonModified
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
    //miniLayer.removeAllFeatures();
    //miniLayer.addFeatures([event.features[0].clone()]);
    orderSurfaceChecking();
    //checkSelfIntersect(event.features[0]);
    selectPolygonEdit();
}

function onPolygonModified(event) {
    putFeaturesVerticesInHiddenField(event.feature.clone());
    //miniLayer.removeAllFeatures();
    //miniLayer.addFeatures([event.feature.clone()]);
    orderSurfaceChecking();
    //checkSelfIntersect(event.feature);
}

function onBoxAdded(event) {
    //only for the Box, not the edit handles...
    if (event.features[0].state == 'Insert') {
        //miniLayer.removeAllFeatures();
        //miniLayer.addFeatures([event.features[0].clone()]);
        putFeaturesVerticesInHiddenField(event.features[0].clone());
        checkMinimumRectangle(event);
        orderSurfaceChecking();
        selectRectangleEdit(event.features[0]);
    }

}

function onBoxModified(event) {
    //miniLayer.removeAllFeatures();
    //miniLayer.addFeatures([event.feature.clone()]);
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


function selectPolygonEdit() {

    setFreePerimeterTool('polygon');
    disableDrawControls();
    jQuery('#btn-perimeter1b').removeClass('active');
    jQuery('#btn-perimeter1b').blur();
    var theFeature = polygonLayer.features[polygonLayer.features.length - 1];
    selectControl = new OpenLayers.Control.ModifyFeature(polygonLayer);
    //app.mapPanel.map.addControl(selectControl);
    //selectControl.activate();
    selectControl.selectFeature(theFeature);

    //selectControl.activate();


    initSelectcontrol(selectControl);
}

function selectRectangleEdit(feature) {
    getRectangleRotation(feature)
    setFreePerimeterTool('rectangle');
    disableDrawControls();
    jQuery('#btn-perimeter1a').removeClass('active');
    jQuery('#btn-perimeter1a').blur();
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
    selectControl.setFeature(feature, {rotation: getRectangleRotation(feature)});
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




function reloadFeatures1() {
    var wkt = jQuery('#features').val();
    var feature = new OpenLayers.Format.WKT().read(wkt);
    if (freePerimeterTool == 'rectangle') {
        reloadRectangle(feature);
    } else {
        setFreePerimeterTool('polygon'); //if undefined tool, default is polygon
        reloadPolygon(feature);
    }
    miniLayer.addFeatures([feature.clone()]);
}


function reloadRectangle(feature) {
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    boxLayer.events.register("featureadded", boxLayer, listenerRectangleDrawToZoom);
    boxLayer.addFeatures([feature]);
    putFeaturesVerticesInHiddenField(feature.clone());
    selectRectangleEdit(feature);
}

var listenerRectangleDrawToZoom = function (e) {
    boxLayer.events.unregister("featureadded", boxLayer, listenerRectangleDrawToZoom);
    listenerFeatureAddedToZoom(e);
};

function reloadPolygon(feature) {
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    polygonLayer.events.register("featureadded", polygonLayer, listenerPolygonDrawToZoom);
    polygonLayer.addFeatures([feature]);
    putFeaturesVerticesInHiddenField(feature.clone());
    selectPolygonEdit();
}

var listenerPolygonDrawToZoom = function (e) {
    polygonLayer.events.unregister("featureadded", polygonLayer, listenerPolygonDrawToZoom);
    listenerFeatureAddedToZoom(e);
};

function selectPolygon() {
    resetAll();
    setFreePerimeterTool('polygon');
    selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}});
    jQuery('#t-features').val('');
    initSelectcontrol(selectControl);
}

function selectRectangle() {
    resetAll();
    setFreePerimeterTool('rectangle');
    selectControl = new OpenLayers.Control.DrawFeature(boxLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}});
    jQuery('#t-features').val('');
    initSelectcontrol(selectControl);
}

function initSelectcontrol(selectControl) {
    app.mapPanel.map.addControl(selectControl);
    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val(Joomla.JText._('FREEPERIMETER', 'Périmètre libre'));
    toggleSelectControl('selection');
}

function setFreePerimeterTool(tool) {
    freePerimeterTool = tool;
    jQuery('#t-freeperimetertool').val(tool);
}

