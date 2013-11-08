/**
 * @requires plugins/Tool.js
 */

/** api: (define)
 *  module = sdi.plugins
 *  class = LayerDetailSheet
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
sdi.plugins.LayerDetailSheet = Ext.extend(gxp.plugins.Tool, {
    
    /** api: ptype = gxp_removelayer */
    ptype: "sdi_layerdetailsheet",
    
    /** api: config[removeMenuText]
     *  ``String``
     *  Text for remove menu item (i18n).
     */
    layerDetailMenuText: "Layer details sheet",

    /** api: config[removeActionTip]
     *  ``String``
     *  Text for remove action tooltip (i18n).
     */
    layerDetailActionTip: "Layer details sheet",
    
    /** api: method[addActions]
     */
    addActions: function() {
        var selectedLayer;
        var actions = sdi.plugins.LayerDetailSheet.superclass.addActions.apply(this, [{
            menuText: this.layerDetailMenuText,
            iconCls: "gxp-icon-getfeatureinfo",
            disabled: true,
            tooltip: this.layerDetailActionTip,
            handler: function() {
               var record = selectedLayer;
               SqueezeBox.initialize({});
               SqueezeBox.setContent('iframe', record.json.href);
            },
            scope: this
        }]);
        var layerDetailAction = actions[0];

        this.target.on("layerselectionchange", function(record) {
            selectedLayer = record;
            layerDetailAction.setDisabled(
                this.target.mapPanel.layers.getCount() <= 1 || !record
            );
        }, this);
        var enforceOne = function(store) {
            layerDetailAction.setDisabled(
                !selectedLayer || store.getCount() <= 1
            );
        };
        this.target.mapPanel.layers.on({
            "add": enforceOne,
            "remove": enforceOne
        });
        
        return actions;
    }
        
});

Ext.preg(sdi.plugins.LayerDetailSheet.prototype.ptype, sdi.plugins.LayerDetailSheet);
