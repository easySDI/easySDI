/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

/**
 * Define a couple WMS/WFS to allow features selection :
 * - WMS displays features
 * - WFS performs the selection
 * Specific functionnalities "user perimeter" and "indoor level navigation" are
 * handle by this class.
 * Objects that have to be previously initialized in the calling context :
 * - app : a gxp.viewer object
 * Variables that have to be declared in the calling context :
 * - selectLayer 
 * - selectControl
 * @param {type} item
 * @returns {predefinedPerimeter}
 */
function predefinedPerimeter(item) {
    this.item = item;
}
;

/**
 * Build and add to the current gxp viewer (app) layer and corresponding service source.
 * Configure and add to the map a OpenLayers.Control.GetFeature.
 * @returns {undefined}
 */
predefinedPerimeter.prototype.init = function(userrestriction) {
    this.userrestriction = userrestriction;
    //Perimeter Layer WMS
    this.initPerimeterLayer();

    //Vector layer to handle selection
    this.initSelectLayer();

    //Map select control on WFS
    this.initSelectControl();
};

/**
 * 
 * @returns {undefined}
 */
predefinedPerimeter.prototype.initPerimeterLayer = function() {
    var layerconfig = {type: "OpenLayers.Layer.WMS",
        name: this.item.maplayername,
        transparent: true,
        isindoor: this.item.isindoor,
        servertype: this.item.server,
        levelfield: this.item.levelfield,
        opacity: this.item.opacity,
        source: this.item.source,
        tiled: false,
        title: "perimeterLayer",
        iwidth: "360",
        iheight: "360",
        visibility: true};
    var sourceconfig = {id: this.item.source,
        ptype: "sdi_gxp_wmssource",
        hidden: "true",
        url: this.item.wmsurl
    };

    //Handle restriction on user specific perimeter
    if (typeof (this.userrestriction) !== 'undefined') {
        var exp = new OpenLayers.Format.WKT().write(this.userrestriction);
        //Geoserver
        if (this.item.server === "1") {
            layerconfig.cql_filter = "INTERSECTS(" + this.item.featuretypefieldgeometry + "," + exp + ")";
        }
        /**
         * ArcGIS : geometry filter has to be sent in a specific parametre 'geometry'
         * describe here : http://resources.arcgis.com/en/help/rest/apiref/
         * TODO : find a way to send geometry parameter in the GetMap request
         * WMSSource doesn't send it if we just set it on the layerconfig like below
         * 
         * NB : see note in myperimeter.js, ArcGIS server can't filter WFS requests
         * as expected, so ArcGIS server can't support user perimeter filter functionnality.
         */
//        if (this.item.server === "2") {
//            var polygon = "{\"rings\" : [ [ [6.101531982421875,46.23451309019769], [6.1052656173706055,46.237006565073216], [6.112003326416016,46.235641104770565], [6.109728813171387,46.232613223769555], [6.104021072387695,46.2313960872759] ,[6.101531982421875,46.23451309019769]],  ],\"spatialReference\" : {\"wkid\" : 4326}}";
//            layerconfig.geometry = polygon;
//        }
    }

    var queue = app.addExtraLayer(sourceconfig, layerconfig);
    gxp.util.dispatch(queue, app.reactivate, app);
};

/**
 * Initialize the vector layer in which selected features will be drawn 
 * @returns {undefined}
 */
predefinedPerimeter.prototype.initSelectLayer = function() {
    //Selection  Layer
    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    app.mapPanel.map.addLayer(selectLayer);

    //Keep selection layer on top
    app.mapPanel.map.events.register('addlayer', this, function() {
        if (app.mapPanel.map.getLayersByName("Selection").length > 0) {
            app.mapPanel.map.setLayerIndex(selectLayer, app.mapPanel.map.getNumLayers());
        }
    });
};

/**
 * Initialize the OpenLayers map control GetFeature with the WFS parameters
 * @returns {undefined}
 */
predefinedPerimeter.prototype.initSelectControl = function() {
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
        box: false,
        click: true,
        toggle: true,
        multipleKey: "ctrlKey",
        clickout: false
    });

    //Build the default filter : merge existing filters on user perimeter and indoor level
    selectControl.protocol.defaultFilter = this.getSelectControlFilter();

    //In case indoor level navigation is active on the map
    if (this.item.featuretypefieldlevel && typeof (app.mapPanel.map.indoorlevelslider) !== 'undefined') {
        //Update indoor level filter at each IndoorLevelSlider event
        app.mapPanel.map.events.register("indoorlevelchanged", this, function() {
            if (selectLayer)
                selectLayer.removeAllFeatures();
            if (selectControl && selectControl.protocol) {
                selectControl.protocol.defaultFilter = this.getSelectControlFilter();
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
predefinedPerimeter.prototype.getIndoorLevelFilter = function() {
    selectControl.fieldlevel = this.item.prefix + ':' + this.item.featuretypefieldlevel;
    return new OpenLayers.Filter.Comparison({
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: selectControl.fieldlevel,
        value: app.mapPanel.map.indoorlevelslider.getLevel().code
    });
};

/**
 * Return the specific user perimeter filter if restriction has to be applied
 * @returns {undefined}
 */
predefinedPerimeter.prototype.getUserPerimeterFilter = function() {
    var g = this.userrestriction.geometry;
    return  new OpenLayers.Filter.Spatial({
        type: OpenLayers.Filter.Spatial.INTERSECTS,
        value: g
    });
};

/**
 * Return a complete filter to apply on the GetFeature control.
 * This feature merge user perimeter filter and indoor level navigation filter.
 * @returns {undefined}
 */
predefinedPerimeter.prototype.getSelectControlFilter = function() {
    var merged, userfilter, levelfilter;

    //If userperimeter activated, handle restriction with user specific perimeter
    if (typeof (this.userrestriction) !== 'undefined') {
        userfilter = this.getUserPerimeterFilter();
    }

    //In case indoor level navigation is active on the map, handle filter on indoor level value
    if (this.item.featuretypefieldlevel && typeof (app.mapPanel.map.indoorlevelslider) !== 'undefined') {
        levelfilter = this.getIndoorLevelFilter();
    }

    //Merged, if needed, filters
    if (levelfilter && userfilter) {
        merged = new OpenLayers.Filter.Logical({
            type: OpenLayers.Filter.Logical.AND,
            filters: [levelfilter, userfilter]
        });
    } else {
        merged = levelfilter || userfilter || undefined;
    }

    return merged;
};

/**
 * Define the function to call when a feature is selected on the map
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureSelected = function(f) {
    selectControl.events.register("featureselected", this, f);
};

/**
 * Define the function to call when a feature is unselected from the map
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureUnSelected = function(f) {
    selectControl.events.register("featureunselected", this, f);
};

/**
 * Define the function to call when indoor level changed
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerIndoorLevelChanged = function(f) {
    app.mapPanel.map.events.register("indoorlevelchanged", this, f);
};

/**
 * Define the function to call after a feature was added to the map.
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureAdded = function(f) {
    selectLayer.events.register("featureadded", selectLayer, f);
};
