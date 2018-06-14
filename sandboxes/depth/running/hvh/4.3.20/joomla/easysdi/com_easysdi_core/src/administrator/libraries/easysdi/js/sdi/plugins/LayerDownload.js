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
 *
 *    Plugin for downloading the linked product.
 */
sdi.plugins.LayerDownload = Ext.extend(gxp.plugins.Tool, {
    
    /** api: ptype = sdi_layerdownload */
    ptype: "sdi_layerdownload",
    
    /** api: config[layerDownloadMenuText]
     *  ``String``
     *  Text for download action menu item (i18n).
     */
    layerDownloadMenuText: "Download",

    /** api: config[layerDownloadActionTip]
     *  ``String``
     *  Text for download action tooltip (i18n).
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
            tooltip: this.layerDownloadActionTip,
            handler: function() {
               var record = selectedLayer;
               SqueezeBox.initialize({});
               SqueezeBox.resize({x: this.initialConfig.iwidth, y: this.initialConfig.iheight});
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
