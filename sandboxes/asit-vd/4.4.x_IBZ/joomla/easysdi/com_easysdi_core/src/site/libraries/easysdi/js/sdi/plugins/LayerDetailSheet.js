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
 *  class = LayerDetailSheet
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.plugins");

/** api: constructor
 *  
 *
 *    Plugin for opening the layer's detail sheet.
 
 */
sdi.plugins.LayerDetailSheet = Ext.extend(gxp.plugins.Tool, {
    /** api: ptype = sdi_layerdetailsheet */
    ptype: "sdi_layerdetailsheet",
    /** api: config[layerDetailMenuText]
     *  ``String``
     *  Text for detail sheet menu item (i18n).
     */
    layerDetailMenuText: "Layer details sheet",
    /** api: config[layerDetailActionTip]
     *  ``String``
     *  Text for detail sheet action tooltip (i18n).
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
                    SqueezeBox.resize({x: this.initialConfig.iwidth, y: this.initialConfig.iheight});
                    SqueezeBox.setContent('iframe', record.json.href);
                },
                scope: this
            }]);
        var layerDetailAction = actions[0];

        this.target.on("layerselectionchange", function(record) {
            selectedLayer = record;
            layerDetailAction.setDisabled(
                    !record || !record.json || !record.json.href 
                    );
        }, this);

        return actions;
    }

});

Ext.preg(sdi.plugins.LayerDetailSheet.prototype.ptype, sdi.plugins.LayerDetailSheet);
