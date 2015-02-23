function loadPerimeter(withdisplay) {
    if (jQuery('#jform_perimeter').length > 0) {
        loadPolygonPerimeter(withdisplay);
    } else {
        loadWfsPerimeter();
    }
}

function loadPolygonPerimeter(withdisplay) {
    var polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {srsName: window.appname.mapPanel.map.projection, projection: window.appname.mapPanel.map.projection});
    window.appname.mapPanel.map.addLayers([polygonLayer]);
    var wkt = jQuery('#jform_perimeter').val();
    var features = new OpenLayers.Format.WKT().read(wkt);
    if (features instanceof Array) {
        for (var i = 0; i < features.length; i++) {
            var geometry = features[i].geometry.transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    new OpenLayers.Projection(window.appname.mapPanel.map.projection)
                    );
            var reprojfeature = new OpenLayers.Feature.Vector(geometry);
            polygonLayer.addFeatures([reprojfeature]);  
        }
    }
    else {
        var geometry = features.geometry.transform(
                new OpenLayers.Projection("EPSG:4326"),
                new OpenLayers.Projection(window.appname.mapPanel.map.projection)
                );
        var reprojfeature = new OpenLayers.Feature.Vector(geometry);
        polygonLayer.addFeatures([reprojfeature]);   
    }
    window.appname.mapPanel.map.zoomToExtent(polygonLayer.getDataExtent());
    if(withdisplay === true){
        jQuery('#perimeter-recap').append('<div id="perimeter-recap-details" style="overflow-y:scroll; height:100px;">');
        jQuery('#perimeter-recap-details').append("<div>" + wkt + "</div>");
        jQuery('#perimeter-recap').append('</div>');
    }
}

var selectLayer;

function loadWfsPerimeter() {
    var url = jQuery('#jform_wfsurl').val();
    var featuretypename = jQuery('#jform_wfsfeaturetypename').val();
    var featuretypefieldid = jQuery('#jform_wfsfeaturetypefieldid').val();
    var wfsUrl = url + '?request=GetFeature&SERVICE=WFS&TYPENAME=' + featuretypename + '&VERSION=1.0.0';
    var wfsUrlWithFilter = wfsUrl + '&FILTER=';
    wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc">');
    var features_object = jQuery('#jform_wfsperimeter').val();


    if (features_object !== "")
        var features = JSON.parse(features_object);
    else
        var features = new Array();
    if (features.length > 1)
    {
        wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Or>');
    }

    for (var i = 0; i < features.length; i++)
    {
        wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:PropertyIsEqualTo><ogc:PropertyName>' + featuretypefieldid + '</ogc:PropertyName><ogc:Literal>' + features[i].id + '</ogc:Literal></ogc:PropertyIsEqualTo>');
    }
    if (features.length > 1)
    {
        wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Or>');
    }
    wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Filter>');
    selectLayer = new OpenLayers.Layer.Vector("Selection", {
        strategies: [new OpenLayers.Strategy.Fixed()],
        protocol: new OpenLayers.Protocol.HTTP({
            url: wfsUrlWithFilter,
            format: new OpenLayers.Format.GML()
        })
    });


    window.appname.mapPanel.map.addLayer(selectLayer);
    selectLayer.events.register("loadend", selectLayer, listenerFeatureAddedToZoom);
}

var listenerFeatureAddedToZoom = function(e) {
    window.appname.mapPanel.map.zoomToExtent(selectLayer.getDataExtent());
};