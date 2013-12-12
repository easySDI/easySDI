function selectMyPerimeter(perimeterid, perimetername, userextent) {
    resetAll();
    jQuery('#btns-selection').hide();
    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');
    jQuery('#t-surface').val('');

    var transformedFeature = getUserRestrictedExtentFeature(userextent);

    jQuery('#t-surface').val(JSON.stringify(transformedFeature.geometry.getGeodesicArea(app.mapPanel.map.projection)));

    myLayer = new OpenLayers.Layer.Vector("myLayer");
    myLayer.events.register("featureadded", myLayer, listenerFeatureAdded);
    myLayer.addFeatures([transformedFeature]);
    app.mapPanel.map.addLayer(myLayer);
    app.mapPanel.map.zoomToExtent(transformedFeature.geometry.getBounds());

    putFeaturesVerticesInHiddenField(transformedFeature);
}

function getUserRestrictedExtentFeature(text) {
    var geojson_format = new OpenLayers.Format.GeoJSON();
    var features = geojson_format.read(JSON.parse(text));
    var polygonList = new Array();

    if (features instanceof Array) {
        for (var i = 0; i < features.length; i++) {
            var feature = features[i];
            var geometry = tranformGeometry(feature);
            polygonList.push(geometry);
        }
    } else if (features instanceof OpenLayers.Feature.Vector) {
        var geometry = tranformGeometry(features);
        polygonList.push(geometry);
    }

    var collectionGeometry = new OpenLayers.Geometry.Collection(polygonList);
    var multigeomFeature = new OpenLayers.Feature.Vector(collectionGeometry);
    return  multigeomFeature;
}

function tranformGeometry(feature) {
    return feature.geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
}
