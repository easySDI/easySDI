function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    polygonLayer.events.on({
        featuresadded: onFeaturesAdded,
        beforefeatureadded: beforeFeatureAdded
    });
    
    polygonLayer.events.register("featureadded", polygonLayer, listenerFeatureAdded);
    app.mapPanel.map.addLayers([polygonLayer]);

    drawControls = {
        polygon: new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}}),
        box: new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}})
    };
    for (var key in drawControls) {
        app.mapPanel.map.addControl(drawControls[key]);
    }
}

function onFeaturesAdded(event) {
    putFeaturesVerticesInHiddenField(event.features[0].clone());
}

function putFeaturesVerticesInHiddenField(feature) {
    jQuery('#t-surface').val(JSON.stringify(feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
    
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection(app.mapPanel.map.projection),
            new OpenLayers.Projection("EPSG:4326")
            );

    var wkt = new OpenLayers.Format.WKT();
    var featureAsString = wkt.write(feature);
    jQuery('#t-features').val(featureAsString);
}

function selectPerimeter1() {
    selectRectangle();
};

function reloadFeatures1() {
    var wkt = jQuery('#features').val();
    var feature = new OpenLayers.Format.WKT().read(wkt);
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    polygonLayer.events.register("featureadded", polygonLayer, listenerFeatureDrawToZoom);
    polygonLayer.addFeatures([feature]);    
    putFeaturesVerticesInHiddenField(feature.clone());
};

var listenerFeatureDrawToZoom = function(e) {
    polygonLayer.events.unregister("featureadded", polygonLayer, listenerFeatureDrawToZoom);
    listenerFeatureAddedToZoom(e);
};

function selectPolygon() {
    resetAll();
    selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}});
    initSelectcontrol(selectControl);
};

function selectRectangle() {
    resetAll();
    toggleRectangle();
};

function toggleRectangle() {
   selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}});
   initSelectcontrol(selectControl);
};

function initSelectcontrol(selectControl){
    app.mapPanel.map.addControl(selectControl);
    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val(Joomla.JText._('FREEPERIMETER', 'Périmètre libre'));
    jQuery('#t-features').val('');
    toggleSelectControl('selection');
};

