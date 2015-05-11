/**
 * 
 * @param {type} item
 * @returns {predefinedPerimeter}
 */

function predefinedPerimeter(item) {
    this.item = item;    
};

/**
 * Build and add to the given gxp viewer layer and corresponding service source.
 * Configure and add to the map a OpenLayers.Control.GetFeature.
 * @param {gxp.Viewer} application
 * @returns {undefined}
 */
predefinedPerimeter.prototype.addPerimeterTo = function(application) {
//    this.application = application;
    
    var layerconfig = {type: "OpenLayers.Layer.WMS",
        name: this.item.maplayername,
        transparent: true,
        isindoor: this.item.isindoor,
        servertype: this.item.server,
        levelfield: this.item.levelfield,
        opacity: this.item.opacity,
        source: this.item.source,
        tiled: true,
        title: "perimeterLayer",
        iwidth: "360",
        iheight: "360",
        visibility: true};
    var sourceconfig = {id: this.item.source,
        ptype: "sdi_gxp_wmssource",
        hidden: "true",
        url: this.item.wmsurl
    };

    var queue = app.addExtraLayer(sourceconfig, layerconfig);
    gxp.util.dispatch(queue, app.reactivate, app);

    //Select control
    selectControl = new OpenLayers.Control.GetFeature({
        protocol: new OpenLayers.Protocol.WFS({
            version: "1.0.0",
            url: this.item.wfsurl,
            srsName: app.mapPanel.map.projection,
            featureType: this.item.featuretypename,
            featurePrefix: this.item.prefix,
            featureNS: this.item.namespace,
            geometryName: this.item.featuretypefieldgeometry
        }),
        box: true,
        click: true,
        multipleKey: "ctrlKey",
        clickout: true
    });
  
    //Selection  Layer
    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    app.mapPanel.map.addLayer(selectLayer);

    //Keep selection layer on top
    app.mapPanel.map.events.register('addlayer', this, function() {
        app.mapPanel.map.setLayerIndex(selectLayer, application.mapPanel.map.getNumLayers());
    });
    
    //In case indoor level navigation is active on the map
    if (this.item.featuretypefieldlevel) {
        //Manage indoor level filter on select control tool
        selectControl.protocol.defaultFilter = this.getSelectControlLevelFilter();
        
        //Manage indoor navigation with predefined perimeter WFS
        app.mapPanel.map.events.register("indoorlevelchanged", this, function() {
            if (selectLayer)
                selectLayer.removeAllFeatures();
            if (selectControl && selectControl.protocol) {
                selectControl.protocol.defaultFilter = this.getSelectControlLevelFilter();
            }
        });
    }
    app.mapPanel.map.addControl(selectControl);
    selectControl.activate();
};

/**
 * Return an OpenLayers Filter corresponding to indoor level value
 * @returns {OpenLayers.Filter.Comparison}
 */
predefinedPerimeter.prototype.getSelectControlLevelFilter = function() {
    selectControl.fieldlevel = this.item.prefix + ':' + this.item.featuretypefieldlevel;
    return new OpenLayers.Filter.Comparison({
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: selectControl.fieldlevel,
        value: app.mapPanel.map.indoorlevelslider.getLevel().code
    });
};

/**
 * Defined the function to call when a feature is selected on the map
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureSelected = function(f){
    selectControl.events.register("featureselected", this, f);
};

/**
 * Defined the function to call when a feature is unselected from the map
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureUnSelected = function(f){
    selectControl.events.register("featureunselected", this, f);
};

/**
 * Defined the function to call when indoor level changed
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerIndoorLevelChanged = function(f){
   app.mapPanel.map.events.register("indoorlevelchanged", this, f);
};

/**
 * 
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureAdded = function(f){
   selectLayer.events.register("featureadded", selectLayer, f);
};
