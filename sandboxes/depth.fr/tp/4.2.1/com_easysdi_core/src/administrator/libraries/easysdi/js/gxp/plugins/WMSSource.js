/**
* @version     4.0.0
* @package     com_easysdi_core
* @copyright   Copyright (C) 2012. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: WMSSource(config)
 *
 *    Plugin for using WMS layers with :class:`gxp.Viewer` instances. The
 *    plugin issues a GetCapabilities request to create a store of the WMS's
 *    layers.
 */   
/** api: example
 *  Configuration in the  :class:`gxp.Viewer`:
 *
 *  .. code-block:: javascript
 *
 *    defaultSourceType: "gxp_wmssource",
 *    sources: {
 *        "opengeo": {
 *            url: "http://suite.opengeo.org/geoserver/wms"
 *        }
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "opengeo",
 *        name: "world",
 *        group: "background"
 *    }
 *
 *  For initial programmatic layer configurations, to leverage lazy loading of
 *  the Capabilities document, it is recommended to configure layers with the
 *  fields listed in :obj:`requiredProperties`.
 */
sdi.gxp.plugins.WMSSource = Ext.extend(gxp.plugins.WMSSource, {
    
    /** api: ptype = gxp_wmssource */
    ptype: "sdi_gxp_wmssource",
    
    
     
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord`` or null when the source is lazy.
     *
     *  Create a layer record given the config. Applications should check that
     *  the source is not :obj:`lazy`` or that the ``config`` is complete (i.e.
     *  configured with all fields listed in :obj:`requiredProperties` before
     *  using this method. Otherwise, it is recommended to use the asynchronous
     *  :meth:`gxp.Viewer.createLayerRecord` method on the target viewer
     *  instead, which will load the source's store to complete the
     *  configuration if necessary.
     */
    createLayerRecord: function(config) {
        var record = sdi.gxp.plugins.WMSSource.superclass.createLayerRecord.apply(this, arguments);
        if(!jQuery.isEmptyObject(record)){
	 record.data.layer.attribution = config.attribution;
	 }
        return record;
        
//        var record, original;
//        var index = this.store.findExact("name", config.name);
//        if (index > -1) {
//            original = this.store.getAt(index);
//        } else if (Ext.isObject(config.capability)) {
//            original = this.store.reader.readRecords({capability: {
//                request: {getmap: {href: this.trimUrl(this.url, this.baseParams)}},
//                layers: [config.capability]}
//            }).records[0];
//        } else if (this.layerConfigComplete(config)) {
//            original = this.createLazyLayerRecord(config);
//        }
//        if (original) {
//
//            var layer = original.getLayer().clone();
//
//            /**
//             * TODO: The WMSCapabilitiesReader should allow for creation
//             * of layers in different SRS.
//             */
//            var projection = this.getMapProjection();
//            
//            // If the layer is not available in the map projection, find a
//            // compatible projection that equals the map projection. This helps
//            // us in dealing with the different EPSG codes for web mercator.
//            var layerProjection = this.getProjection(original);
//
//            var projCode = (layerProjection || projection).getCode(),
//                bbox = original.get("bbox"), maxExtent;
//            if (bbox && bbox[projCode]){
//                layer.addOptions({projection: layerProjection});
//                maxExtent = OpenLayers.Bounds.fromArray(bbox[projCode].bbox, layer.reverseAxisOrder());
//            } else {
//                var llbbox = original.get("llbbox");
//                if (llbbox) {
//                    var extent = OpenLayers.Bounds.fromArray(llbbox).transform("EPSG:4326", projection);
//                    // make sure maxExtent is valid (transform does not succeed for all llbbox)
//                    if ((1 / extent.getHeight() > 0) && (1 / extent.getWidth() > 0)) {
//                        // maxExtent has infinite or non-numeric width or height
//                        // in this case, the map maxExtent must be specified in the config
//                        maxExtent = extent;
//                    }
//                }
//            }
//            
//            // update params from config
//            layer.mergeNewParams({
//                STYLES: config.styles,
//                FORMAT: config.format,
//                TRANSPARENT: config.transparent,
//                CQL_FILTER: config.cql_filter
//            });
//            
//            var singleTile = false;
//            if ("tiled" in config) {
//                singleTile = !config.tiled;
//            } else {
//                // for now, if layer has a time dimension, use single tile
//                if (original.data.dimensions && original.data.dimensions.time) {
//                    singleTile = true;
//                }
//            }
//
//            layer.setName(config.title || layer.name);
//            layer.addOptions({
//                attribution: config.attribution,
//                maxExtent: maxExtent,
//                restrictedExtent: maxExtent,
//                singleTile: singleTile,
//                ratio: config.ratio || 1,
//                visibility: ("visibility" in config) ? config.visibility : true,
//                opacity: ("opacity" in config) ? config.opacity : 1,
//                buffer: ("buffer" in config) ? config.buffer : 1,
//                dimensions: original.data.dimensions,
//                transitionEffect: singleTile ? 'resize' : null,
//                minScale: config.minscale,
//                maxScale: config.maxscale
//            });
//            
//            // data for the new record
//            var data = Ext.applyIf({
//                title: layer.name,
//                group: config.group,
//                infoFormat: config.infoFormat,
//                source: config.source,
//                properties: "gxp_wmslayerpanel",
//                fixed: config.fixed,
//                selected: "selected" in config ? config.selected : false,
//                restUrl: this.restUrl,
//                layer: layer
//            }, original.data);
//            
//            // add additional fields
//            var fields = [
//                {name: "source", type: "string"}, 
//                {name: "group", type: "string"},
//                {name: "properties", type: "string"},
//                {name: "fixed", type: "boolean"},
//                {name: "selected", type: "boolean"},
//                {name: "restUrl", type: "string"},
//                {name: "infoFormat", type: "string"}
//            ];
//            original.fields.each(function(field) {
//                fields.push(field);
//            });
//
//            var Record = GeoExt.data.LayerRecord.create(fields);
//            record = new Record(data, layer.id);
//            record.json = config;
//
//        } else {
//            if (window.console && this.store.getCount() > 0 && config.name !== undefined) {
//                console.warn("Could not create layer record for layer '" + config.name + "'. Check if the layer is found in the WMS GetCapabilities response.");
//            }
//        }
//        return record;
    }
    
    
    
});

Ext.preg(sdi.gxp.plugins.WMSSource.prototype.ptype, sdi.gxp.plugins.WMSSource);
