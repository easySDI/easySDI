//Reproject in EPSG:4326
function reprojectWKT(wkt) {
    var features = new OpenLayers.Format.WKT().read(wkt);
    var reprojfeatures = new Array();
    if (features instanceof Array) {
        for (var i = 0; i < features.length; i++) {
            var geometry = features[i].geometry.transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    new OpenLayers.Projection(app.mapPanel.map.projection)
                    );
            var reprojfeature = new OpenLayers.Feature.Vector(geometry);
            reprojfeatures.push(reprojfeature);
        }
    }
    else {
        var geometry = features.geometry.transform(
                new OpenLayers.Projection("EPSG:4326"),
                new OpenLayers.Projection(app.mapPanel.map.projection)
                );
        var reprojfeature = new OpenLayers.Feature.Vector(geometry);
        reprojfeatures.push(reprojfeature);
    }
    var reprojwkt = new OpenLayers.Format.WKT().write(reprojfeatures);
    jQuery('#perimeter-recap-details').append("<div>" + reprojwkt + "</div>");
}