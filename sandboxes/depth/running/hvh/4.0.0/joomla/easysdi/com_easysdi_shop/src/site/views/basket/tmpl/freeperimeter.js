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
    jQuery('#t-surface').val(JSON.stringify(feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
    
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection(app.mapPanel.map.projection),
            new OpenLayers.Projection("EPSG:4326")
            );

    var pointsAsString = '';
    var components = new Array();
    if (geometry instanceof OpenLayers.Geometry.Polygon) {
        var vertices = geometry.getVertices();
        pointsAsString += 'POLYGON ';
        pointsAsString += '((';
        for (var i = 0; i < vertices.length; i++) {
            pointsAsString += vertices[i].x;
            pointsAsString += ' ';
            pointsAsString += vertices[i].y;
            if (i < vertices.length - 1)
                pointsAsString += ', ';
        }
        pointsAsString += '))';
    }
    else {
        components = geometry.components;
        
            pointsAsString += 'MULTIPOLYGON ';
        
        for (var j = 0; j < components.length; j++) {
            pointsAsString += '((';
            var vertices = components[j].getVertices();
            for (var i = 0; i < vertices.length; i++) {
                pointsAsString += vertices[i].x;
                pointsAsString += ' ';
                pointsAsString += vertices[i].y;
                if (i < vertices.length - 1)
                    pointsAsString += ', ';
            }
            pointsAsString += '))';
            if(j < components.length -1){
                pointsAsString += ',';
            }
        }
    } 

    jQuery('#t-features').val(JSON.stringify(pointsAsString));
}

function selectPerimeter1() {
    selectPolygon();
}

function reloadFeatures1() {
    var wkt = jQuery('#features').val();
    var feature = new OpenLayers.Format.WKT().read(wkt);
    var geometry = feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
    polygonLayer.addFeatures([feature]);
//    app.mapPanel.map.zoomToExtent(polygonLayer.getDataExtent());
    putFeaturesVerticesInHiddenField(feature);
}

var listenerFeatureDrawToZoom = function(e) {
    app.mapPanel.map.zoomToExtent(polygonLayer.getDataExtent());
};

function selectPolygon() {
    resetAll();
    selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.Polygon, {handlerOptions: {stopDown: 0, stopUp: 0}});
    app.mapPanel.map.addControl(selectControl);
    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val(Joomla.JText._('FREEPERIMETER', 'Périmètre libre'));
    jQuery('#t-features').val('');
}

function selectRectangle() {
    resetAll();
    selectControl = new OpenLayers.Control.DrawFeature(polygonLayer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {stopDown: 1, stopUp: 1, irregular: 1}});
    app.mapPanel.map.addControl(selectControl);
    jQuery('#t-perimeter').val('1');
    jQuery('#t-perimetern').val(Joomla.JText._('FREEPERIMETER', 'Périmètre libre'));
    jQuery('#t-features').val('');
}

