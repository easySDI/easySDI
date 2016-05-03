/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */

/**
 * @requires plugins/LayerSource.js
 */

/** api: (define)
 *  module = gxp.plugins
 *  class = OLSource
 */

/** api: (extends)
 *  plugins/LayerSource.js
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: OLSource(config)
 *
 *    Plugin for using any ``OpenLayers.Layer`` layers with :class:`gxp.Viewer`
 *    instances.
 *
 *    Configuration for layers from a :class:`gxp.OLSource`:
 *
 *    * type: ``String`` - the CLASS_NAME of an ``OpenLayers.Layer``
 *    * args: ``Array`` - the arguments passed to the layer's constructor
 */
/** api: example
 *  The configuration in the ``sources`` property of the :class:`gxp.Viewer` is
 *  straightforward:
 *
 *  .. code-block:: javascript
 *
 *    "ol": {
 *        ptype: "gxp_olsource"
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "ol",
 *        type: "OpenLayers.Layer.OSM"
 *        args: ["Mapnik"]
 *    }
 *
 */
sdi.gxp.plugins.OLSource = Ext.extend(gxp.plugins.OLSource, {
    /** api: ptype = gxp_olsource */
    ptype: "sdi_gxp_olsource",
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord``
     *
     *  Create a layer record given the config.
     */
    createLayerRecord: function(config) {
        var record = sdi.gxp.plugins.OLSource.superclass.createLayerRecord.apply(this, arguments);

        record.json = config;
        return record;
    }
});

Ext.preg(sdi.gxp.plugins.OLSource.prototype.ptype, sdi.gxp.plugins.OLSource);
