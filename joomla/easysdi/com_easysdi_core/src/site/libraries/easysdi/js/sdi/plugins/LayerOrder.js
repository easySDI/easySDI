/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

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
 *    Plugin for opening shop order form
 */
sdi.plugins.LayerOrder = Ext.extend(gxp.plugins.Tool, {
    
    /** api: ptype = sdi_layerorder */
    ptype: "sdi_layerorder",
    
    /** api: config[layerOrderMenuText]
     *  ``String``
     *  Text for shop menu item (i18n).
     */
    layerOrderMenuText: "Order",

    /** api: config[layerOrderActionTip]
     *  ``String``
     *  Text for shop action tooltip (i18n).
     */
    layerOrderActionTip: "Order",
    
    /** api: method[addActions]
     */
    addActions: function() {
        var selectedLayer;
        var actions = sdi.plugins.LayerDownload.superclass.addActions.apply(this, [{
            menuText: this.layerOrderMenuText,
            iconCls: "gxp-icon-addnote",
            disabled: true,
            tooltip: this.layerOrderActionTip,
            handler: function() {
               var record = selectedLayer;
               SqueezeBox.initialize({});
               SqueezeBox.resize({x: this.initialConfig.iwidth, y: this.initialConfig.iheight});
               SqueezeBox.setContent('iframe', record.json.order);
               
            },
            scope: this
        }]);
        var layerOrderAction = actions[0];

        this.target.on("layerselectionchange", function(record) {
            selectedLayer = record;
            layerOrderAction.setDisabled(
                !record || !record.json || !record.json.order
            );
        }, this);
               
        return actions;
    }
        
});

Ext.preg(sdi.plugins.LayerOrder.prototype.ptype, sdi.plugins.LayerOrder);
