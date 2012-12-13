/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Layer.js
 * @require OpenLayers/Format/JSON.js
 * @require OpenLayers/Format/GeoJSON.js
 * @require OpenLayers/BaseTypes/Class.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = PrintProvider
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */
Ext.namespace("sdi.geoext.data");

/** api: example
 *  Minimal code to print as much of the current map extent as possible as
 *  soon as the print service capabilities are loaded, using the first layout
 *  reported by the print service:
 * 
 *  .. code-block:: javascript
 *     
 *      var mapPanel = new GeoExt.MapPanel({
 *          renderTo: "mappanel",
 *          layers: [new OpenLayers.Layer.WMS("wms", "/geoserver/wms",
 *              {layers: "topp:tasmania_state_boundaries"})],
 *          center: [146.56, -41.56],
 *          zoom: 7
 *      });
 *      var printProvider = new GeoExt.data.PrintProvider({
 *          url: "/geoserver/pdf",
 *          listeners: {
 *              "loadcapabilities": function() {
 *                  var printPage = new GeoExt.data.PrintPage({
 *                      printProvider: printProvider
 *                  });
 *                  printPage.fit(mapPanel, true);
 *                  printProvider.print(mapPanel, printPage);
 *              }
 *          }
 *      });
 */

/** api: constructor
 *  .. class:: PrintProvider
 * 
 *  Provides an interface to a Mapfish or GeoServer print module. For printing,
 *  one or more instances of :class:`GeoExt.data.PrintPage` are also required
 *  to tell the PrintProvider about the scale and extent (and optionally
 *  rotation) of the page(s) we want to print. 
 */

/** sdi extension
 * 
 */
sdi.geoext.data.PrintProvider = Ext.extend(GeoExt.data.PrintProvider, {
   
    
    /** api: method[loadCapabilities]
     *
     *  Loads the capabilities from the print service. If this instance is
     *  configured with either ``capabilities`` or a ``url`` and ``autoLoad``
     *  set to true, then this method does not need to be called from the
     *  application.
     */
    loadCapabilities: function() {
        if (!this.url) {
            return;
        }
        var url = this.url + "info.json";
        Ext.Ajax.request({
            url: url,
            method: "GET",
            disableCaching: false,
            success: function(response) {
                this.capabilities = Ext.decode(response.responseText);
                this.capabilities.createURL = this.createurl ;
                this.capabilities.printURL = this.printurl ;
                this.loadStores();
            },
            params: this.initialConfig.baseParams,
            scope: this
        });
    }
    
  
    
});
