/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: GoolgeSource(config)
 *
 *    Plugin for using Google layers with :class:`gxp.Viewer` instances. The
 *    plugin uses the GMaps v3 API and also takes care of loading the
 *    required Google resources.
 *
 *    Available layer names for this source are "ROADMAP", "SATELLITE",
 *    "HYBRID" and "TERRAIN"
 */
/** api: example
 *  The configuration in the ``sources`` property of the :class:`gxp.Viewer` is
 *  straightforward:
 *
 *  .. code-block:: javascript
 *
 *    "google": {
 *        ptype: "gxp_google"
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "google",
 *        name: "TERRAIN"
 *    }
 *
 */
sdi.gxp.plugins.GoogleSource = Ext.extend(gxp.plugins.GoogleSource, {
    /** api: ptype = gxp_googlesource */
    ptype: "sdi_gxp_googlesource",
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord``
     *
     *  Create a layer record given the config.
     */
    createLayerRecord: function(config) {
        var record = sdi.gxp.plugins.GoogleSource.superclass.createLayerRecord.apply(this, arguments);

        record.json = config;
        return record;
    }
});

Ext.preg(sdi.gxp.plugins.GoogleSource.prototype.ptype, sdi.gxp.plugins.GoogleSource);