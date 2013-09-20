function selectMyPerimeter(perimeterid, perimetername, userextent) {
    resetAll();
    jQuery('#btn-selection').hide();

    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');

    var transformedFeature = getUserRestrictedExtentFeature(userextent);
    myLayer = new OpenLayers.Layer.Vector("myLayer");
    myLayer.addFeatures([transformedFeature]);

    app.mapPanel.map.addLayer(myLayer);
    app.mapPanel.map.zoomToExtent(transformedFeature.geometry.getBounds());

    miniLayer.removeAllFeatures();
    miniLayer.addFeatures([transformedFeature.clone()]);
    
    putFeaturesVerticesInHiddenField(transformedFeature);
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
