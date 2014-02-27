function selectMyPerimeter(perimeterid, perimetername, userextent) {
    resetAll();
    jQuery('#btns-selection').hide();
    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');
    jQuery('#t-surface').val('');

    var transformedFeature = getUserRestrictedExtentFeature(userextent);

    jQuery('#t-surface').val(JSON.stringify(transformedFeature.geometry.getGeodesicArea(app.mapPanel.map.projection)));

    myLayer = new OpenLayers.Layer.Vector("myLayer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    myLayer.events.register("featureadded", myLayer, listenerFeatureAdded);
    //myLayer.events.register("loadend", myLayer, listenerFeatureAddedToZoomStart);
    myLayer.addFeatures([transformedFeature]);
    app.mapPanel.map.addLayer(myLayer);
    
    app.mapPanel.map.zoomToExtent(transformedFeature.geometry.getBounds());

    putFeaturesVerticesInHiddenField(transformedFeature.clone());
}

function getUserRestrictedExtentFeature(text) {
    var features = new OpenLayers.Format.WKT().read(text);
    var polygonList = new Array();

    if (features instanceof Array) {
        for (var i = 0; i < features.length; i++) {
            var feature = features[i];
            if (feature.geometry instanceof OpenLayers.Geometry.MultiPolygon) {
                var geoms = feature.geometry.components;
                for (var j = 0; j < geoms.length; j++) { 
                    var geometry = tranformGeometry(geoms[j]);
                    polygonList.push(geometry);
                }
            } else {
                var geometry = tranformGeometry(feature.geometry);
                polygonList.push(geometry);
            }

        }
    } else if (features instanceof OpenLayers.Feature.Vector) {
        if (features.geometry instanceof OpenLayers.Geometry.MultiPolygon) {
            var geoms = features.geometry.components;
            for (var j = 0; j < geoms.length; j++) {
                var geometry = tranformGeometry(geoms[j]);
                polygonList.push(geometry);
            }
        } else {
            var geometry = tranformGeometry(features.geometry);
            polygonList.push(geometry);
        }

    }

   // var collectionGeometry = new OpenLayers.Geometry.Collection(polygonList);
    var collectionGeometry = new OpenLayers.Geometry.MultiPolygon(polygonList);
    var multigeomFeature = new OpenLayers.Feature.Vector(collectionGeometry);
    return  multigeomFeature;
}

function tranformGeometry(geometry) {
    return geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
}


