/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("gxp");

var sourceConfig;
var layerConfig;

/**
 * Add a source to the viewer and a layer config to the map configuration
 * the layer is not added to the map here.
 * @param {type} lsourceConfig
 * @param {type} llayerConfig
 * @returns {Ext@call;extend.prototype.addExtraLayer.queue|gxp.Viewer.prototype.addExtraLayer.queue|Array}
 */
gxp.Viewer.prototype.addExtraLayer = function(lsourceConfig, llayerConfig) {

    sourceConfig = lsourceConfig;
    layerConfig = llayerConfig;
    if (this.sources[sourceConfig.id] === undefined) {
        this.sources[sourceConfig.id] = sourceConfig;
    }
    this.initialConfig.map.layers.push(layerConfig);

    var queue = [];
    queue.push(this.createSourceLoader(sourceConfig.id));

    return queue;
};

/**
 * Create layer record and add layer to the map
 * Call after addExtraLayer
 * @returns {undefined}
 */
gxp.Viewer.prototype.reactivate = function() {
    // initialize tooltips
    Ext.QuickTips.init();

    var mapConfig = this.initialConfig.map;
    if (mapConfig && mapConfig.layers) {
        var conf, source, record, baseRecords = [],
            overlayRecords = [];
        //Get the last layer
        conf = mapConfig.layers[mapConfig.layers.length - 1];
        source = this.layerSources[conf.source];
        if (source) {
            if (source.id === sourceConfig.id) {
                // source may not have loaded properly (failure handled elsewhere)
                record = source.createLayerRecord(conf);
                if (record) {
                    if (record.get("group") === "background") {
                        baseRecords.push(record);
                    } else {
                        overlayRecords.push(record);
                    }
                }
            }
        }

        var panel = this.mapPanel;
        //extent = record.getLayer().maxExtent.clone();

        var records = baseRecords.concat(overlayRecords);
        if (records.length) {
            panel.layers.add(records);
        }
    }

    // respond to any queued requests for layer records
    this.checkLayerRecordQueue();
    return record;
};

/**
 * Overwrite the initMapPanel method in gxp.Viewer to avoid the default blank baselayer.
 * Since easySDI doesn't allow any map without baselayer, we don't need this baselayer.
 * Without it, an easySDI baselayer can set the resolutions of the map (for example with WMTS )
 */
gxp.Viewer.prototype.initMapPanel = function() {
    var config = Ext.apply({}, this.initialConfig.map);
    var mapConfig = {};

    // split initial map configuration into map and panel config
    if (this.initialConfig.map) {
        var props = "theme,controls,resolutions,projection,units,maxExtent,restrictedExtent,maxResolution,numZoomLevels,panMethod".split(",");
        var prop;
        for (var i = props.length - 1; i >= 0; --i) {
            prop = props[i];
            if (prop in config) {
                mapConfig[prop] = config[prop];
                delete config[prop];
            }
        }
    }

    this.mapPanel = Ext.ComponentMgr.create(Ext.applyIf({
        xtype: config.xtype || "gx_mappanel",
        map: Ext.applyIf({
            theme: mapConfig.theme || null,
            controls: mapConfig.controls || [
                new OpenLayers.Control.Navigation({
                    zoomWheelOptions: { interval: 250 },
                    dragPanOptions: { enableKinetic: true }
                }),
                new OpenLayers.Control.PanPanel(),
                new OpenLayers.Control.ZoomPanel(),
                new OpenLayers.Control.Attribution()
            ],
            maxExtent: mapConfig.maxExtent && OpenLayers.Bounds.fromArray(mapConfig.maxExtent),
            restrictedExtent: mapConfig.restrictedExtent && OpenLayers.Bounds.fromArray(mapConfig.restrictedExtent),
            numZoomLevels: mapConfig.numZoomLevels || 20
        }, mapConfig),
        center: config.center && new OpenLayers.LonLat(config.center[0], config.center[1]),
        resolutions: config.resolutions,
        forceInitialExtent: true,
        layers: [],
        items: this.mapItems,
        plugins: this.mapPlugins,
        tbar: config.tbar || new Ext.Toolbar({
            hidden: true
        })
    }, config));
    this.mapPanel.getTopToolbar().on({
        afterlayout: this.mapPanel.map.updateSize,
        show: this.mapPanel.map.updateSize,
        hide: this.mapPanel.map.updateSize,
        scope: this.mapPanel.map
    });

    this.mapPanel.layers.on({
        "add": function(store, records) {
            // check selected layer status
            var record;
            for (var i = records.length - 1; i >= 0; i--) {
                record = records[i];
                if (record.get("selected") === true) {
                    this.selectLayer(record);
                }
            }
        },
        "remove": function(store, record) {
            if (record.get("selected") === true) {
                this.selectLayer();
            }
        },
        scope: this
    });
};