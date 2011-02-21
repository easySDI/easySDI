/**
 * Copyright (c) 2008-2009 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/LegendImage.js
 * @requires GeoExt/widgets/LayerLegend.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = WMSLegend
 *  base_link = `Ext.Panel <http://extjs.com/deploy/dev/docs/?class=Ext.Panel>`_
 */
Ext.namespace('GeoExt');

/** api: constructor
 *  .. class:: WMSLegend(config)
 *
 *  Show a legend image for a WMS layer. The image can be read from the styles
 *  field of a layer record (if the record comes e.g. from a
 *  :class:`GeoExt.data.WMSCapabilitiesReader`). If not provided, a
 *  GetLegendGraphic request will be issued to retrieve the image.
 */
GeoExt.WMSLegend = Ext.extend(GeoExt.LayerLegend, {

    /** api: config[imageFormat]
     *  ``String``  
     *  The image format to request the legend image in if the url cannot be
     *  determined from the styles field of the layer record. Defaults to
     *  image/gif.
     */
    imageFormat: "image/gif",
    
    /** api: config[defaultStyleIsFirst]
     *  ``String``
     *  The WMS spec does not say if the first style advertised for a layer in
     *  a Capabilities document is the default style that the layer is
     *  rendered with. We make this assumption by default. To be strictly WMS
     *  compliant, set this to false, but make sure to configure a STYLES
     *  param with your WMS layers, otherwise LegendURLs advertised in the
     *  GetCapabilities document cannot be used.
     */
    defaultStyleIsFirst: true,

    /** private: method[initComponent]
     *  Initializes the WMS legend. For group layers it will create multiple
     *  image box components.
     */
    initComponent: function() {
        GeoExt.WMSLegend.superclass.initComponent.call(this);
        this.update();
    },

    /** private: method[getLegendUrl]
     *  :param layerName: ``String`` A sublayer.
     *  :param layerNames: ``Array(String)`` The array of sublayers,
     *      read from this.layerRecord if not provided.
     *  :return: ``String`` The legend URL.
     *
     *  Get the legend URL of a sublayer.
     */
    getLegendUrl: function(layerName, layerNames) {
        var rec = this.layerRecord;
        var url;
        var styles = rec && rec.get("styles");
        var layer = rec.get("layer");
        layerNames = layerNames ||
                             (layer.params.LAYERS instanceof Array) ?
                             layer.params.LAYERS :
                             layer.params.LAYERS.split(",");

        var styleNames = layer.params.STYLES &&
                             layer.params.STYLES.split(",");
        var idx = layerNames.indexOf(layerName);
        var styleName = styleNames && styleNames[idx];
        // check if we have a legend URL in the record's
        // "styles" data field
        if(styles && styles.length > 0) {
            if(styleName) {
                Ext.each(styles, function(s) {
                    url = (s.name == styleName && s.legend) && s.legend.href;
                    return !url;
                });
            } else if(this.defaultStyleIsFirst === true && !styleNames &&
                      !layer.params.SLD && !layer.params.SLD_BODY) {
                url = styles[0].legend && styles[0].legend.href;
            }
        }
        return url ||
               layer.getFullRequestString({
                   REQUEST: "GetLegendGraphic",
                   WIDTH: null,
                   HEIGHT: null,
                   EXCEPTIONS: "application/vnd.ogc.se_xml",
                   LAYER: layerName,
                   LAYERS: null,
                   STYLE: (styleName !== '') ? styleName: null,
                   STYLES: null,
                   SRS: null,
                   FORMAT: this.imageFormat
        });
    },

    /** private: method[update]
     *  Update the legend, adding, removing or updating
     *  the per-sublayer box component.
     */
    update: function() {
        GeoExt.WMSLegend.superclass.update.apply(this, arguments);
        
        var layerNames, layerName, i, len;
        
        var layer = this.layerRecord.get("layer");
        layerNames = (layer.params.LAYERS instanceof Array) ? 
            layer.params.LAYERS :
            layer.params.LAYERS.split(",");

        var destroyList = [];
        var textCmp = this.items.get(0);
        this.items.each(function(cmp) {
            i = layerNames.indexOf(cmp.itemId);
            if(i < 0 && cmp != textCmp) {
                destroyList.push(cmp);
            } else if(cmp !== textCmp){
                layerName = layerNames[i];
                var newUrl = this.getLegendUrl(layerName, layerNames);
                if(!OpenLayers.Util.isEquivalentUrl(newUrl, cmp.url)) {
                    cmp.setUrl(newUrl);
                }
            }
        }, this);
        for(i = 0, len = destroyList.length; i<len; i++) {
            var cmp = destroyList[i];
            // cmp.destroy() does not remove the cmp from
            // its parent container!
            this.remove(cmp);
            cmp.destroy();
        }

        for(i = 0, len = layerNames.length; i<len; i++) {
            layerName = layerNames[i];
            if(!this.items || !this.getComponent(layerName)) {
                this.add({
                    xtype: "gx_legendimage",
                    url: this.getLegendUrl(layerName, layerNames),
                    itemId: layerName
                });
            }
        }
        this.doLayout();
    }
});

/** private: method[supports]
 *  Private override
 */
GeoExt.WMSLegend.supports = function(layerRecord) {
    return layerRecord.get("layer") instanceof OpenLayers.Layer.WMS;
};

/** api: legendtype = gx_wmslegend */
GeoExt.LayerLegend.types["gx_wmslegend"] = GeoExt.WMSLegend;

/** api: xtype = gx_wmslegend */
Ext.reg('gx_wmslegend', GeoExt.WMSLegend);
