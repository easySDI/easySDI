js = jQuery.noConflict();

function loadPerimeter(withdisplay) {
    if (jQuery('#jform_perimeter').length > 0) {
        loadPolygonPerimeter(withdisplay);
    } else {
        loadWfsPerimeter();
    }
}

var dest, polygonLayer;

function loadPolygonPerimeter(withdisplay) {
    polygonLayer = new OpenLayers.Layer.Vector("Polygon Layer", {srsName: window.appname.mapPanel.map.projection, projection: window.appname.mapPanel.map.projection});

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
        var source = new OpenLayers.Projection("EPSG:4326");
        dest = new OpenLayers.Projection(window.appname.mapPanel.map.projection);
        features.geometry.transform(source, dest);
        polygonLayer.addFeatures([features]);
    }

    window.appname.mapPanel.map.addLayers([polygonLayer]);
    window.appname.mapPanel.map.zoomToExtent(polygonLayer.getDataExtent());

    if (withdisplay === true) {
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
    var namespace = jQuery('#jform_wfsnamespace').val();
    var prefix = jQuery('#jform_wfsprefix').val();
    var featuretypefieldgeometry = jQuery('#jform_wfsfeaturetypefieldgeometry').val();

    var features_object = jQuery('#jform_wfsperimeter').val();

    selectLayer = new OpenLayers.Layer.Vector("Selection");
    window.appname.mapPanel.map.addLayer(selectLayer);

    if (features_object !== "")
        var features = JSON.parse(features_object);
    else
        var features = new Array();


    var tempWFSfilterList = [];
    var tempWFSfilter;

    for (var i = 0; i < features.length; i++) {
        tempWFSfilterList.push(
                new OpenLayers.Filter.Comparison({
                    type: OpenLayers.Filter.Comparison.EQUAL_TO,
                    property: featuretypefieldid,
                    value: features[i].id
                }));
    }

    //initStyleMap();

    if (features.length > 1) {
        tempWFSfilter = new OpenLayers.Filter.Logical({
            type: OpenLayers.Filter.Logical.OR,
            filters: tempWFSfilterList
        });
    }
    else {
        tempWFSfilter = tempWFSfilterList[0];
    }

    var protoWFS = new OpenLayers.Protocol.WFS(
            {
                version: "1.0.0",
                url: url,
                featureType: featuretypename,
                featureNS: namespace,
                featurePrefix: prefix,
                geometryName: featuretypefieldgeometry,
                defaultFilter: tempWFSfilter
            }
    );

    protoWFS.read({
        readOptions: {output: "object"},
        resultType: "hits",
        maxFeatures: null,
        callback: function (resp) {
            selectLayer.addFeatures(resp.features);
            window.appname.mapPanel.map.zoomToExtent(selectLayer.getDataExtent());
        }
    });
}


js(document).on('click', 'button.delete', function () {
    var delete_url = 'index.php?option=com_easysdi_shop&task=pricingprofile.delete&id=';
    var profile_id = js(this).attr('data-id');
    js('#btn_delete').attr('href', delete_url + profile_id);
    js('#deleteModal').modal('show');
});