function initMyPerimeter() {
    myLayer = new OpenLayers.Layer.Vector("myLayer", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection, styleMap: customStyleMap});
    myLayer.events.register("featureadded", myLayer, listenerFeatureAddedToZoom);
    app.mapPanel.map.addLayer(myLayer);
}

function selectMyPerimeter(perimeterid, perimetername, userextent) {
    resetAll();
    //app.mapPanel.map.addLayer(myLayer);
    jQuery('#btns-selection').hide();
    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');
    jQuery('#t-surface').val('');

    var transformedFeature = getUserRestrictedExtentFeature(userextent);
    jQuery('#t-surface').val(JSON.stringify(transformedFeature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
    myLayer.addFeatures([transformedFeature]);

    putFeaturesVerticesInHiddenField(transformedFeature.clone());
    return transformedFeature.clone();
}

function reloadMyPerimeter(perimeterid, perimetername, userextent){
    var myPerim = selectMyPerimeter(perimeterid, perimetername, userextent);
    miniLayer.addFeatures([myPerim]);
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

    var collectionGeometry = new OpenLayers.Geometry.MultiPolygon(polygonList);
    var multigeomFeature = new OpenLayers.Feature.Vector(collectionGeometry);
    return  multigeomFeature;

    /**
     * Below is a version of getUserRestrictedExtentFeature() method written to test
     * ArcGIS server WFS capability to answer to a geometric filter.
     * Results are :
     * - ArcGIS server doesn't support MultiPolygon type in filter, so we build
     * a OpenLayers.Geometry.Polygon when it is possible (if the user perimeter is
     * a simple polygon, if it is not, we can't do anything more...)
     * - But, unfortunately, we've got in response "Wrong coordinates" to the filter
     * built with the polygon geometry. The reason is : 
     * OpenLayers builds the GetFeature POST body request with GML2, which is invalid,
     * it should be GML3 (https://geonet.esri.com/thread/26811)
     * 
     * Conclusion : user perimeter filter is not compatible with ArcGIS server.
     * 
     */
//    var polygonFeature;
//    var multipolygonList = new Array();
//
//    if (features instanceof Array) {
//        for (var i = 0; i < features.length; i++) {
//            var feature = features[i];
//            if (feature.geometry instanceof OpenLayers.Geometry.MultiPolygon) {
//                var geoms = feature.geometry.components;
//                for (var j = 0; j < geoms.length; j++) { 
//                    var geometry = tranformGeometry(geoms[j]);
//                    multipolygonList.push(geometry);
//                }
//            } else {
//                var geometry = tranformGeometry(feature.geometry);
//                multipolygonList.push(geometry);
//            }
//
//        }
//    } else if (features instanceof OpenLayers.Feature.Vector) {
//        if (features.geometry instanceof OpenLayers.Geometry.MultiPolygon) {
//            var geoms = features.geometry.components;
//            for (var j = 0; j < geoms.length; j++) {
//                var geometry = tranformGeometry(geoms[j]);
//                multipolygonList.push(geometry);
//            }
//        } else {
//            var geometry = tranformGeometry(features.geometry);
//            polygonFeature = geometry;
//        }
//
//    }
//
//   var collectionGeometry;
//   if(multipolygonList.length > 0){
//     collectionGeometry = new OpenLayers.Geometry.MultiPolygon(multipolygonList);
//   }
//   if(polygonFeature){
//        collectionGeometry = new OpenLayers.Geometry.Polygon(new OpenLayers.Geometry.LinearRing(polygonFeature.components[0].components));
//   }
//    var multigeomFeature = new OpenLayers.Feature.Vector(collectionGeometry);
//
//    return  multigeomFeature;
}
;

function tranformGeometry(geometry) {
    return geometry.transform(
            new OpenLayers.Projection("EPSG:4326"),
            new OpenLayers.Projection(app.mapPanel.map.projection)
            );
}


