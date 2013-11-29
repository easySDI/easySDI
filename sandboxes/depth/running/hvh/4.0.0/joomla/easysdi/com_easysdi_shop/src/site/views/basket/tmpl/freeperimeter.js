function initDraw() {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    polygonLayer.events.on({
        featuresadded: onFeaturesAdded,
        beforefeatureadded: beforeFeatureAdded
    });
    polygonLayer.events.register("loadend", polygonLayer, listenerFeatureDrawToZoom);
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
    putFeaturesVerticesInHiddenField(event.features[0]);
}

function putFeaturesVerticesInHiddenField(feature) {

    var geometry = feature.geometry.transform(
            new OpenLayers.Projection(app.mapPanel.map.projection),
            new OpenLayers.Projection("EPSG:4326")
            );

    var pointsAsString = '';
    var components = new Array();
    if (geometry instanceof OpenLayers.Geometry.MultiPolygon) {
        components = geometry.components;
        for (var j = 0; j < components.length; j++) {
            pointsAsString += '[';
            var vertices = components[j].getVertices();
            for (var i = 0; i < vertices.length; i++) {
                pointsAsString += vertices[i].x;
                pointsAsString += ' ';
                pointsAsString += vertices[i].y;
                if (i < vertices.length - 1)
                    pointsAsString += ', ';
            }
            pointsAsString += ']';
        }
    } else {
        var vertices = geometry.getVertices();
        for (var i = 0; i < vertices.length; i++) {
            pointsAsString += vertices[i].x;
            pointsAsString += ' ';
            pointsAsString += vertices[i].y;
            if (i < vertices.length - 1)
                pointsAsString += ', ';
        }
    }



    jQuery('#t-features').val(JSON.stringify(pointsAsString));
    jQuery('#t-surface').val(JSON.stringify(feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
}

function selectPerimeter1() {
    selectPolygon();
//    drawControls['polygon'].activate();
}

function reloadFeatures1() {
    var wkt = 'POLYGON((' + JSON.parse(jQuery('#features').val()) + '))';
    var feature = new OpenLayers.Format.WKT().read(wkt);
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    var reprojfeature = new OpenLayers.Feature.Vector(geometry);
    polygonLayer.addFeatures([reprojfeature]);
//    app.mapPanel.map.zoomToExtent(polygonLayer.getDataExtent());
    putFeaturesVerticesInHiddenField(reprojfeature);
}

var listenerFeatureDrawToZoom = function(e) {
    app.mapPanel.map.zoomToExtent(polygonLayer.getDataExtent());
//    miniLayer.addFeatures(selectLayer.features);
};

function selectPolygon() {
    resetAll();

    selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}});
    app.mapPanel.map.addControl(selectControl);
    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val('FREE PERIMETER');
    jQuery('#t-features').val('');
}

function selectRectangle() {
    resetAll();

    selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}});
    app.mapPanel.map.addControl(selectControl);
    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val('FREE PERIMETER');
    jQuery('#t-features').val('');
}

