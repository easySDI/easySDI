function selectMyPerimeter(perimeterid, perimetername, userextent) {
    resetAll();
    jQuery('#btns-selection').hide();

    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');
    jQuery('#t-surface').val('');

    var transformedFeature = getUserRestrictedExtentFeature(userextent);
    
    var t = transformedFeature.geometry.getGeodesicArea(app.mapPanel.map.projection);
    jQuery('#t-surface').val(JSON.stringify(transformedFeature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
    
    myLayer = new OpenLayers.Layer.Vector("myLayer");
    myLayer.events.register("featureadded", myLayer, listenerFeatureAdded);
    myLayer.addFeatures([transformedFeature]);
    app.mapPanel.map.addLayer(myLayer);
    app.mapPanel.map.zoomToExtent(transformedFeature.geometry.getBounds());
    
    
//        miniLayer.addFeatures(myLayer.features);
        
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
