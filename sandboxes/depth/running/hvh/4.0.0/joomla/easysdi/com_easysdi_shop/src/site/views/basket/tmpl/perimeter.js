function selectPerimeter(isrestrictedbyperimeter, perimeterid, perimetername, wmsurl, wmslayername, wfsurl, featuretypename, namespace, featuretypefieldgeometry, featuretypefieldid, featuretypefieldname) {
    resetAll();

    fieldid = featuretypefieldid;
    fieldname = featuretypefieldname;
    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');
    if (isrestrictedbyperimeter === 0) {
        perimeterLayer = new OpenLayers.Layer.WMS("perimeterLayer",
                wmsurl,
                {layers: wmslayername,
                    transparent: true});

        selectControl = new OpenLayers.Control.GetFeature({
            protocol: new OpenLayers.Protocol.WFS({
                version: "1.0.0",
                url: wfsurl,
                srsName: app.mapPanel.map.projection,
                featureType: featuretypename,
                featureNS: namespace,
                geometryName: featuretypefieldgeometry
            }),
            box: true,
            multipleKey: "shiftKey",
            toggleKey: "ctrlKey"
        });

    } else {
        var featurerestriction = getUserRestrictedExtentFeature(userperimeter);
        perimeterLayer = new OpenLayers.Layer.Vector("perimeterLayer", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            protocol: new OpenLayers.Protocol.WFS({
                url: wfsurl,
                featureType: featuretypename,
                featureNS: namespace,
                srsName: app.mapPanel.map.projection
            }),
            styleMap: new OpenLayers.StyleMap({
                fillColor: "#ffcc66",
                fillOpacity: 0,
                strokeColor: "black",
                strokeWidth: 2,
            }),
            filter: new OpenLayers.Filter.Spatial({
                type: OpenLayers.Filter.Spatial.INTERSECTS,
                value: featurerestriction.geometry
            })
        });

//         perimeterLayer = new OpenLayers.Layer.WMS("perimeterLayer",
//                wmsurl,     
//                {layers: wmslayername,
//                    transparent: true,
//                filter: new OpenLayers.Filter.Spatial({
//                type: OpenLayers.Filter.Spatial.INTERSECTS,
//                value: featurerestriction.geometry
//            })}           
//            );

        selectControl = new OpenLayers.Control.GetFeature({
            protocol: new OpenLayers.Protocol.WFS({
                version: "1.0.0",
                url: wfsurl,
                srsName: app.mapPanel.map.projection,
                featureType: featuretypename,
                featureNS: namespace,
                geometryName: featuretypefieldgeometry,
                defaultFilter: new OpenLayers.Filter.Spatial({
                    type: OpenLayers.Filter.Spatial.INTERSECTS,
                    value: featurerestriction.geometry
                })
            }),
            box: true,
            multipleKey: "shiftKey",
            toggleKey: "ctrlKey"
        });
    }
    
//    if(!jQuery('#modal-perimeter').isShown){
        loadingPerimeter = new Ext.LoadMask(Ext.getBody(), {
            msg: "Chargement de la couche de périmètre..."
        });    
        loadingPerimeter.show();
//    }
    app.mapPanel.map.addLayer(perimeterLayer);
    perimeterLayer.events.register("loadend", perimeterLayer, listenerLoadEnd);

    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    app.mapPanel.map.addLayer(selectLayer);
    selectControl.events.register("featureselected", this, listenerFeatureSelected);
    selectControl.events.register("featureunselected", this, listenerFeatureUnselected);
    app.mapPanel.map.addControl(selectControl);

    return false;
};

function getWMSFilter(){
    var filter = '<ogc:Filter>';
   filter += '<ogc:Within>';
   filter += '   <ogc:PropertyName>the_geom</ogc:PropertyName>';
   filter += '   <gml:Polygon gid="pp9"';
   filter += '      srsName="http://www.opengis.net/gml/srs/epsg.xml#4326">';
   filter += '      <gml:outerBoundaryIs>';
   filter += '          <gml:LinearRing>';
   filter += '          <gml:coordinates>'+userperimeter+'</gml:coordinates>';
   filter += '          </gml:LinearRing>';
   filter += '      </gml:outerBoundaryIs>';
   filter += '   </gml:Polygon>';
   filter += '</ogc:Within>';
   filter += '</ogc:Filter>';
   return filter;

}

var listenerLoadEnd = function() {
    loadingPerimeter.hide();
};
var listenerFeatureSelected = function(e) {
    var alreadySelected = selectLayer.features;
    for (var i = 0; i < alreadySelected.length; i++) {
        if (alreadySelected[i].attributes[fieldid] === e.feature.attributes[fieldid])
            return;
    }
    selectLayer.addFeatures([e.feature]);
    miniLayer.addFeatures([e.feature.clone()]);
    
    var features_text = jQuery('#t-features').val();
    if (features_text !== "")
        var features = JSON.parse(features_text);
    else
        var features = new Array();
    features.push({"id": e.feature.attributes[fieldid], "name": e.feature.attributes[fieldname]});
    jQuery('#t-features').val(JSON.stringify(features));
    if (jQuery('#t-surface').val() !== '')
        var surface = parseInt(jQuery('#t-surface').val());
    else
        var surface = 0;
    jQuery('#t-surface').val(JSON.stringify(surface + e.feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
};

var listenerFeatureUnselected = function(e) {
    selectLayer.removeFeatures([e.feature]);
    miniLayer.removeFeatures([e.feature]);
    
    var features_text = jQuery('#t-features').val();
    if (features_text !== "")
        var features = JSON.parse(features_text);
    else
        return;
    jQuery.each(features, function(index, value) {
        if (typeof value === "undefined")
            return true;
        if (value.id === e.feature.attributes[fieldid]) {
            features.splice(index, 1);
        }
    });
    if (features.size === 0)
        jQuery('#t-features').val('');
    else
        jQuery('#t-features').val(JSON.stringify(features));
};

function reloadFeatures(wfsurl, featuretypename, featuretypefieldid) {
    var wfsUrl = wfsurl + '?request=GetFeature&SERVICE=WFS&TYPENAME=' + featuretypename + '&VERSION=1.0.0';
    var wfsUrlWithFilter = wfsUrl + '&FILTER=';
    wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc">');
    var features_text = jQuery('#features').val();
    if (features_text !== "")
        var features = JSON.parse(features_text);
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
    app.mapPanel.map.removeLayer(selectLayer);
    miniapp.mapPanel.map.removeLayer(miniLayer);
    selectLayer = new OpenLayers.Layer.Vector("Selection", {
        strategies: [new OpenLayers.Strategy.Fixed()],
        protocol: new OpenLayers.Protocol.HTTP({
            url: wfsUrlWithFilter,
            format: new OpenLayers.Format.GML()
        })
    });
    miniLayer = new OpenLayers.Layer.Vector("Selection", {
        strategies: [new OpenLayers.Strategy.Fixed()],
        protocol: new OpenLayers.Protocol.HTTP({
            url: wfsUrlWithFilter,
            format: new OpenLayers.Format.GML()
        })
    });
    miniLayer.events.register("featuresadded", miniLayer, listenerMiniFeaturesAdded);

    app.mapPanel.map.addLayer(selectLayer);
    app.mapPanel.map.zoomToExtent(selectLayer.getDataExtent());
    miniapp.mapPanel.map.addLayer(miniLayer);
    miniapp.mapPanel.map.zoomToExtent(miniLayer.getDataExtent());
}
