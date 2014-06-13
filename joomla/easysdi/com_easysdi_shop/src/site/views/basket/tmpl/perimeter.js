var fromreload = false;

function selectPerimeter(isrestrictedbyperimeter, perimeterid, perimetername, wmsurl, wmslayername, wfsurl, featuretypename, namespace, featuretypefieldgeometry, featuretypefieldid, featuretypefieldname, prefix) {
    resetAll();

    fieldid = featuretypefieldid;
    fieldname = featuretypefieldname;
    jQuery('#t-perimeter').val(perimeterid);
    jQuery('#t-perimetern').val(perimetername);
    jQuery('#t-features').val('');
    jQuery('#t-surface').val('');
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
                featurePrefix: prefix,
                featureNS: namespace,
                geometryName: featuretypefieldgeometry
            }),
            box: false,
            click: true,
            multipleKey: "ctrlKey",
            clickout: true
        });

    } else {

        
        var featurerestriction = getUserRestrictedExtentFeature(userperimeter);
        var g = featurerestriction.geometry;
        var exp = new OpenLayers.Format.WKT().write(featurerestriction);
        
         
        //----------------------------------------------------------------------
        // The WMS version of the perimeter layer filtered 
        // by user restricted perimeter
        //----------------------------------------------------------------------
         perimeterLayer = new OpenLayers.Layer.WMS("perimeterLayer",
                wmsurl,     
                {layers: wmslayername,
                 transparent: true,
                 CQL_FILTER: 'INTERSECTS(the_geom,' + exp + ')'},
                 {tileOptions: {maxGetUrlLength: 2048}, transitionEffect: 'resize'}
            );

        //----------------------------------------------------------------------
        
        selectControl = new OpenLayers.Control.GetFeature({
            protocol: new OpenLayers.Protocol.WFS({
                version: "1.0.0",
                url: wfsurl,
                srsName: app.mapPanel.map.projection,
                featureType: featuretypename,
                featureNS: namespace,
                featurePrefix: prefix,
                geometryName: featuretypefieldgeometry,
                defaultFilter: new OpenLayers.Filter.Spatial({
                    type: OpenLayers.Filter.Spatial.INTERSECTS,
                    value: featurerestriction.geometry
                })
            }),
            box: false,
            click: true,
            multipleKey: "ctrlKey",
            clickout: true
        });
    }

    app.mapPanel.map.addLayer(perimeterLayer);
    //perimeterLayer.events.register("loadend", perimeterLayer, listenerLoadEnd);

    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    selectLayer.events.register("featureadded", selectLayer, listenerFeatureAdded);
    app.mapPanel.map.addLayer(selectLayer);
    selectControl.events.register("featureselected", this, listenerFeatureSelected);
    selectControl.events.register("featureunselected", this, listenerFeatureUnselected);
    
    app.mapPanel.map.addControl(selectControl);

    toggleSelectControl('selection');

    return false;
};

var listenerFeatureSelected = function(e) {
    if(fromreload === true){
        selectLayer.removeAllFeatures();
        miniLayer.removeAllFeatures();
        fromreload = false;
    }
    var alreadySelected = selectLayer.features;
    for (var i = 0; i < alreadySelected.length; i++) {
        if (alreadySelected[i].attributes[fieldid] === e.feature.attributes[fieldid])
            return;
    }   
        
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
    
    selectLayer.addFeatures([e.feature]);
};

var listenerFeatureUnselected = function(e) {
    selectLayer.removeFeatures([e.feature]);
    var  features = miniLayer.features;
    for (var i = 0; i < features.length ; i++){
        if(features[i].attributes['id'] === e.feature.attributes['id']){
            miniLayer.removeFeatures([features[i]]);
            break;
        }
    }
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
    
    if (jQuery('#t-surface').val() !== ''){
        var surface = parseInt(jQuery('#t-surface').val());
        jQuery('#t-surface').val(JSON.stringify(surface - e.feature.geometry.getGeodesicArea(app.mapPanel.map.projection)));
    }
};

function reloadFeatures(wfsurl, featuretypename, featuretypefieldid) {
    jQuery('#t-features').val(jQuery('#features').val());
    jQuery('#t-surface').val(jQuery('#surface').val());
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
    selectLayer = new OpenLayers.Layer.Vector("Selection", {
        strategies: [new OpenLayers.Strategy.Fixed()],
        protocol: new OpenLayers.Protocol.HTTP({
            url: wfsUrlWithFilter,
            format: new OpenLayers.Format.GML()
        })
    });

    app.mapPanel.map.addLayer(selectLayer);
    selectLayer.events.register("featureadded", selectLayer, listenerFeatureAdded);
    selectLayer.events.register("loadend", selectLayer, listenerFeatureAddedToZoom);
    fromreload = true;
}

var listenerFeatureAddedToZoom = function (e){
    app.mapPanel.map.zoomToExtent(selectLayer.getDataExtent());
};

