var   fieldlevel, prefix, selectControl;

Ext.onReady(function() {
    window.appname.on("ready", function() {
        
        
        fieldlevel = perimeter.featuretypefieldlevel;
        prefix = perimeter.prefix; 
    
        var layerconfig = {type: "OpenLayers.Layer.WMS",
            name: perimeter.maplayername,
            transparent: true,
            isindoor: perimeter.isindoor,
            servertype: perimeter.server,
            levelfield: perimeter.levelfield,
            opacity: perimeter.opacity,
            source: perimeter.source,
            tiled: true,
            title: "perimeterLayer",
            iwidth: "360",
            iheight: "360",
            visibility: true};
        var sourceconfig = {id: perimeter.source,
            ptype: "sdi_gxp_wmssource",
            hidden: "true",
            url: perimeter.wmsurl
        };

        var queue = window.parent.app.addExtraLayer(sourceconfig, layerconfig);
        gxp.util.dispatch(queue, window.parent.app.reactivate, window.parent.app);

        //Select control
        selectControl = new OpenLayers.Control.GetFeature({
            protocol: new OpenLayers.Protocol.WFS({
                version: "1.0.0",
                url: perimeter.wfsurl,
                srsName: app.mapPanel.map.projection,
                featureType: perimeter.featuretypename,
                featurePrefix: perimeter.prefix,
                featureNS: perimeter.namespace,
                geometryName: perimeter.featuretypefieldgeometry
            }),
            box: true,
            click: true,
            multipleKey: "ctrlKey",
            clickout: true
        });
        //Manage indoor level filter on select control tool
        if (perimeter.featuretypefieldlevel) {
            selectControl.protocol.defaultFilter = getSelectControlLevelFilter();
        }

        //Selection  Layer
        selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
        selectLayer.events.register("featureadded", selectLayer, listenerFeatureAdded);
        app.mapPanel.map.addLayer(selectLayer);

        //Keep selection layer on top
        app.mapPanel.map.events.register('addlayer', this, function() {
            app.mapPanel.map.setLayerIndex(selectLayer, app.mapPanel.map.getNumLayers());
        });

        //Select control
        selectControl.events.register("featureselected", this, listenerFeatureSelected);
//        selectControl.events.register("featureunselected", this, listenerFeatureUnselected);
        //Managing indoor navigation with predefined perimeter WFS
        if (perimeter.featuretypefieldlevel) {
            app.mapPanel.map.events.register("indoorlevelchanged", this, function(level) {
                if (selectLayer)
                    selectLayer.removeAllFeatures();
                jQuery('#t-features').val('');
                if (selectControl && selectControl.protocol) {
                    selectControl.protocol.defaultFilter = getSelectControlLevelFilter();
                }
            });
        }
        app.mapPanel.map.addControl(selectControl);
        toggleSelectControl('selection');
    }
    );
});

//Get the OpenLayers Filter to apply for features selection
var getSelectControlLevelFilter = function() {
    selectControl.fieldlevel = prefix + ':' + fieldlevel;
    return new OpenLayers.Filter.Comparison({
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: selectControl.fieldlevel,
        value: app.mapPanel.map.indoorlevelslider.getLevel().code
    });
};