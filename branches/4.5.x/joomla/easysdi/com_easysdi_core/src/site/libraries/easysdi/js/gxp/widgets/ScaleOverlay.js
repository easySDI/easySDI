/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.widgets");

/** api: constructor
 *  .. class:: ScaleOverlay(config)
 *   
 *      Create a panel for showing a ScaleLine control and a combobox for 
 *      selecting the map scale.
 */
sdi.gxp.ScaleOverlay = Ext.extend(gxp.ScaleOverlay, {


    /** private: method[addScaleLine]
     *  
     *  Create the scale line control and add it to the panel.
     */
    addScaleLine: function() {
        var scaleLinePanel = new Ext.BoxComponent({
            autoEl: {
                tag: "div",
                cls: "olControlScaleLine overlay-element overlay-scaleline"
            }
        });
        this.on("afterlayout", function() {
            scaleLinePanel.getEl().dom.style.position = 'relative';
            scaleLinePanel.getEl().dom.style.display = 'inline';

            this.getEl().on("click", this.stopMouseEvents, this);
            this.getEl().on("mousedown", this.stopMouseEvents, this);
        }, this);
        scaleLinePanel.on('render', function() {
            var scaleLine = new OpenLayers.Control.ScaleLine({
                bottomInUnits: SdiScaleLineParams.bottomInUnits,
                bottomOutUnits: SdiScaleLineParams.bottomOutUnits,
                topInUnits: SdiScaleLineParams.topInUnits,
                topOutUnits: SdiScaleLineParams.topOutUnits,
                geodesic: true,
                div: scaleLinePanel.getEl().dom
            });

            this.map.addControl(scaleLine);
            scaleLine.activate();
        }, this);
        this.add(scaleLinePanel);
    }

});

Ext.reg('sdi_gxp_scaleoverlay', sdi.gxp.ScaleOverlay);