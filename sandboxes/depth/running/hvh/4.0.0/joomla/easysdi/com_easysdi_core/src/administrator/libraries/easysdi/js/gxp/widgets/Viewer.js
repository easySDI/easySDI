Ext.namespace("gxp");

var sourceConfig;

gxp.Viewer.prototype.addExtraLayer = function(sourceConfig, layerConfig) {
    this.sources[sourceConfig.id] = sourceConfig;
    this.initialConfig.map.layers.push(layerConfig);

    var queue = [];
    queue.push(this.createSourceLoader(sourceConfig.id));

    gxp.util.dispatch(queue, this.reactivate, this);
};

gxp.Viewer.prototype.reactivate = function() {
    // initialize tooltips
    Ext.QuickTips.init();

    var mapConfig = this.initialConfig.map;
    if (mapConfig && mapConfig.layers) {
        var conf, source, record, baseRecords = [], overlayRecords = [];
        for (var i = 0; i < mapConfig.layers.length; ++i) {
            conf = mapConfig.layers[i];
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
        }

        var panel = this.mapPanel;
        var map = panel.map;
        extent = record.getLayer().maxExtent.clone();
        map.zoomToExtent(extent);

        var records = baseRecords.concat(overlayRecords);
        if (records.length) {
            panel.layers.add(records);
        }

    }

    // respond to any queued requests for layer records
    this.checkLayerRecordQueue();

    // broadcast ready state
    this.fireEvent("ready");
};




