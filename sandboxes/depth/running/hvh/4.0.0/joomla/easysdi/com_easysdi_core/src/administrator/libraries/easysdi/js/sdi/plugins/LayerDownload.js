/**
 * @requires plugins/Tool.js
 */

/** api: (define)
 *  module = sdi.plugins
 *  class = LayerDownload
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.plugins");

/** api: constructor
 *  .. class:: RemoveLayer(config)
 *
 *    Plugin for removing a selected layer from the map.
 *    TODO Make this plural - selected layers
 */
sdi.plugins.LayerDownload = Ext.extend(gxp.plugins.Tool, {
    
    /** api: ptype = gxp_removelayer */
    ptype: "sdi_layerdownload",
    
    /** api: config[removeMenuText]
     *  ``String``
     *  Text for remove menu item (i18n).
     */
    layerDownloadMenuText: "Download",

    /** api: config[removeActionTip]
     *  ``String``
     *  Text for remove action tooltip (i18n).
     */
    layerDownloadActionTip: "Download",
    
    /** api: method[addActions]
     */
    addActions: function() {
        var selectedLayer;
        var actions = sdi.plugins.LayerDownload.superclass.addActions.apply(this, [{
            menuText: this.layerDownloadMenuText,
            iconCls: "gxp-icon-filebrowse",
            disabled: true,
            tooltip: this.layerDetailActionTip,
            handler: function() {
               var record = selectedLayer;
               SqueezeBox.initialize({});
               SqueezeBox.setContent('iframe', record.json.download);
            },
            scope: this
        }]);
        var layerDownloadAction = actions[0];

        this.target.on("layerselectionchange", function(record) {
            selectedLayer = record;
            layerDownloadAction.setDisabled(
                !record || !record.json || !record.json.download
            );
        }, this);
               
        return actions;
    }
        
});

Ext.preg(sdi.plugins.LayerDownload.prototype.ptype, sdi.plugins.LayerDownload);
