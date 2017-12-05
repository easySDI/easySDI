/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: OSMSource(config)
 *
 *    Plugin for using OpenStreetMap layers with :class:`gxp.Viewer` instances.
 *
 *    Available layer names are "mapnik" and "osmarender"
 */
/** api: example
 *  The configuration in the ``sources`` property of the :class:`gxp.Viewer` is
 *  straightforward:
 *
 *  .. code-block:: javascript
 *
 *    "osm": {
 *        ptype: "gxp_osmsource"
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "osm",
 *        name: "osmarander"
 *    }
 *
 */
sdi.gxp.plugins.OSMSource = Ext.extend(gxp.plugins.OSMSource, {
    /** api: ptype = gxp_googlesource */
    ptype: "sdi_gxp_osmsource",
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord``
     *
     *  Create a layer record given the config.
     */
    createLayerRecord: function(config) {
        var record = sdi.gxp.plugins.OSMSource.superclass.createLayerRecord.apply(this, arguments);

        record.json = config;
        return record;
    },

    /** api: method[createStore]
     *
     *  Creates a store of layer records.  Fires "ready" when store is loaded.
     */
    createStore: function() {

        var options = {
            projection: "EPSG:900913",
            maxExtent: new OpenLayers.Bounds(-128 * 156543.0339, -128 * 156543.0339,
                128 * 156543.0339, 128 * 156543.0339
            ),
            maxResolution: 156543.03390625,
            numZoomLevels: 19,
            units: "m",
            buffer: 1,
            transitionEffect: "resize"
        };

        var layers = [
            new OpenLayers.Layer.OSM(
                "OpenStreetMap", [
                    "https://a.tile.openstreetmap.org/${z}/${x}/${y}.png",
                    "https://b.tile.openstreetmap.org/${z}/${x}/${y}.png",
                    "https://c.tile.openstreetmap.org/${z}/${x}/${y}.png"
                ],
                OpenLayers.Util.applyDefaults({
                    attribution: this.mapnikAttribution,
                    type: "mapnik"
                }, options)
            )
        ];

        this.store = new GeoExt.data.LayerStore({
            layers: layers,
            fields: [
                { name: "source", type: "string" },
                { name: "name", type: "string", mapping: "type" },
                { name: "abstract", type: "string", mapping: "attribution" },
                { name: "group", type: "string", defaultValue: "background" },
                { name: "fixed", type: "boolean", defaultValue: true },
                { name: "selected", type: "boolean" }
            ]
        });
        this.store.each(function(l) {
            l.set("group", "background");
        });
        this.fireEvent("ready", this);

    }
});

Ext.preg(sdi.gxp.plugins.OSMSource.prototype.ptype, sdi.gxp.plugins.OSMSource);