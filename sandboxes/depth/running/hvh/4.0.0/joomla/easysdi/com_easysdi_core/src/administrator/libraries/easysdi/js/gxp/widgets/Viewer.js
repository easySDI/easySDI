Ext.namespace("gxp");

gxp.Viewer.prototype.addLayer = function(layer) {
    var source, record, baseRecords = [], overlayRecords = [];
    source = this.layerSources[layer.source];
    // source may not have loaded properly (failure handled elsewhere)
    if (source) {
        record = source.createLayerRecord(layer);
        if (record) {
            if (record.get("group") === "background") {
                baseRecords.push(record);
            } else {
                overlayRecords.push(record);
            }
        }
    }

    var panel = this.mapPanel;
    var map = panel.map;

    var records = baseRecords.concat(overlayRecords);
    if (records.length) {
        panel.layers.add(records);
    }
}


