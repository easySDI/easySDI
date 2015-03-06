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
    if(this.sources[sourceConfig.id] === undefined){
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
        var conf, source, record, baseRecords = [], overlayRecords = [];
        //Get the last layer
        conf = mapConfig.layers[mapConfig.layers.length-1];
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
        var map = panel.map;
        extent = record.getLayer().maxExtent.clone();
//        map.zoomToExtent(extent);

        var records = baseRecords.concat(overlayRecords);
        if (records.length) {
            panel.layers.add(records);
        }
    }

    // respond to any queued requests for layer records
    this.checkLayerRecordQueue();
};






