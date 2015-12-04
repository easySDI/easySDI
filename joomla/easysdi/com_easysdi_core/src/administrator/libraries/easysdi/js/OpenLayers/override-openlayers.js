/**
* @version     4.3.2
* @package     com_easysdi_core
* @copyright   Copyright (C) 2012. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

//This file is based on heron-mc overrides in GPL v3
//See original file here: https://github.com/heron-mc/heron-mc/blob/master/heron/lib/override-openlayers.js#L1000
//https://github.com/heron-mc/heron-mc/issues/378
//thanks to heron-mc team

/**
 * bug with IE11: an extra NS1 namespace is inserted in the WFS-request XML.
 * This extra namespace is not valid and causes an error on execution.
 * If multiple operations are send in a single operation namespaces NS2, NS3 and so on, are
 * generated by IE11. The fixes below (by Bart vd E) take care of this.
 *
 * Fixed in OpenLayers (due v 2.14):
 * https://github.com/openlayers/openlayers/commit/821975c1f500e26c6663584356db5d65b57f70d9
 * Fix for three protocols: SOS, CSW and WFS (3 versions)
 */

/** CSW **/

/*
 * Overriding OpenLayers to add xmlns NS
 *
 */
OpenLayers.Util.extend(OpenLayers.Format.CSWGetRecords.v2_0_2.prototype.namespaces,
    {
        xmlns: "http://www.w3.org/2000/xmlns/"
    });


/**
 * Method: write
 * Given an configuration js object, write a CSWGetRecords request.
 *
 * Parameters:
 * options - {Object} A object mapping the request.
 *
 * Returns:
 * {String} A serialized CSWGetRecords request.
 */
OpenLayers.Format.CSWGetRecords.v2_0_2.prototype.write = function (options) {
    var node = this.writeNode("csw:GetRecords", options);
    this.setAttributeNS(
        node, this.namespaces.xmlns,
        "xmlns:gmd", this.namespaces.gmd
    );
    return OpenLayers.Format.XML.prototype.write.apply(this, [node]);
};

/** SOS **/

/*
 * Overriding OpenLayers to add xmlns NS
 *
 */
OpenLayers.Util.extend(OpenLayers.Format.SOSGetObservation.prototype.namespaces,
    {
        xmlns: "http://www.w3.org/2000/xmlns/"
    });

/**
 * Method: write
 * Given an configuration js object, write a CSWGetRecords request.
 *
 * Parameters:
 * options - {Object} A object mapping the request.
 *
 * Returns:
 * {String} A serialized CSWGetRecords request.
 */
OpenLayers.Format.SOSGetObservation.prototype.write = function (options) {
    var node = this.writeNode("sos:GetObservation", options);
    this.setAttributeNS(
        node, this.namespaces.xmlns,
        "xmlns:om", this.namespaces.om
    );
    this.setAttributeNS(
        node, this.namespaces.xmlns,
        "xmlns:ogc", this.namespaces.ogc
    );
    this.setAttributeNS(
        node, this.namespaces.xsi,
        "xsi:schemaLocation", this.schemaLocation
    );
    return OpenLayers.Format.XML.prototype.write.apply(this, [node]);
};

/** WFS v1 **/

/*
 * Overriding OpenLayers to add xmlns NS (needed once for all WFS formatters)
 * Overiding OpenLayers to add support for GML 3.2.1
 *
 */
OpenLayers.Util.extend(OpenLayers.Format.WFST.v1.prototype.namespaces,
    {
        xmlns: "http://www.w3.org/2000/xmlns/",
        gml32: "http://www.opengis.net/gml/3.2"
    });

/**
 * Property: writers
 * As a compliment to the readers property, this structure contains public
 *     writing functions grouped by namespace alias and named like the
 *     node names they produce.
 */
OpenLayers.Format.WFST.v1.prototype.writers =
{
    "wfs": {
        "GetFeature": function (options) {
            var node = this.createElementNSPlus("wfs:GetFeature", {
                attributes: {
                    service: "WFS",
                    version: this.version,
                    handle: options && options.handle,
                    outputFormat: options && options.outputFormat,
                    maxFeatures: options && options.maxFeatures,
                    "xsi:schemaLocation": this.schemaLocationAttr(options)
                }
            });
            if (typeof this.featureType == "string") {
                this.writeNode("Query", options, node);
            } else {
                for (var i = 0, len = this.featureType.length; i < len; i++) {
                    options.featureType = this.featureType[i];
                    this.writeNode("Query", options, node);
                }
            }
            return node;
        },
        "Transaction": function (obj) {
            obj = obj || {};
            var options = obj.options || {};
            var node = this.createElementNSPlus("wfs:Transaction", {
                attributes: {
                    service: "WFS",
                    version: this.version,
                    handle: options.handle
                }
            });
            var i, len;
            var features = obj.features;
            if (features) {
                // temporarily re-assigning geometry types
                if (options.multi === true) {
                    OpenLayers.Util.extend(this.geometryTypes, {
                        "OpenLayers.Geometry.Point": "MultiPoint",
                        "OpenLayers.Geometry.LineString": (this.multiCurve === true) ? "MultiCurve" : "MultiLineString",
                        "OpenLayers.Geometry.Polygon": (this.multiSurface === true) ? "MultiSurface" : "MultiPolygon"
                    });
                }
                var name, feature;
                for (i = 0, len = features.length; i < len; ++i) {
                    feature = features[i];
                    name = this.stateName[feature.state];
                    if (name) {
                        this.writeNode(name, {
                            feature: feature,
                            options: options
                        }, node);
                    }
                }
                // switch back to original geometry types assignment
                if (options.multi === true) {
                    this.setGeometryTypes();
                }
            }
            if (options.nativeElements) {
                for (i = 0, len = options.nativeElements.length; i < len; ++i) {
                    this.writeNode("wfs:Native",
                        options.nativeElements[i], node);
                }
            }
            return node;
        },
        "Native": function (nativeElement) {
            var node = this.createElementNSPlus("wfs:Native", {
                attributes: {
                    vendorId: nativeElement.vendorId,
                    safeToIgnore: nativeElement.safeToIgnore
                },
                value: nativeElement.value
            });
            return node;
        },
        "Insert": function (obj) {
            var feature = obj.feature;
            var options = obj.options;
            var node = this.createElementNSPlus("wfs:Insert", {
                attributes: {
                    handle: options && options.handle
                }
            });
            this.srsName = this.getSrsName(feature);
            this.writeNode("feature:_typeName", feature, node);
            return node;
        },
        "Update": function (obj) {
            var feature = obj.feature;
            var options = obj.options;
            var node = this.createElementNSPlus("wfs:Update", {
                attributes: {
                    handle: options && options.handle,
                    typeName: (this.featureNS ? this.featurePrefix + ":" : "") +
                    this.featureType
                }
            });
            if (this.featureNS) {
                this.setAttributeNS(
                    node, this.namespaces.xmlns,
                    "xmlns:" + this.featurePrefix, this.featureNS
                );
            }

            // add in geometry
            var modified = feature.modified;
            if (this.geometryName !== null && (!modified || modified.geometry !== undefined)) {
                this.srsName = this.getSrsName(feature);
                this.writeNode(
                    "Property", {name: this.geometryName, value: feature.geometry}, node
                );
            }

            // add in attributes
            for (var key in feature.attributes) {
                if (feature.attributes[key] !== undefined &&
                    (!modified || !modified.attributes ||
                    (modified.attributes && (key in modified.attributes)))) {
                    this.writeNode(
                        "Property", {name: key, value: feature.attributes[key]}, node
                    );
                }
            }

            // add feature id filter
            this.writeNode("ogc:Filter", new OpenLayers.Filter.FeatureId({
                fids: [feature.fid]
            }), node);

            return node;
        },
        "Property": function (obj) {
            var node = this.createElementNSPlus("wfs:Property");
            this.writeNode("Name", obj.name, node);
            if (obj.value !== null) {
                this.writeNode("Value", obj.value, node);
            }
            return node;
        },
        "Name": function (name) {
            return this.createElementNSPlus("wfs:Name", {value: name});
        },
        "Value": function (obj) {
            var node;
            if (obj instanceof OpenLayers.Geometry) {
                node = this.createElementNSPlus("wfs:Value");
                var geom = this.writeNode("feature:_geometry", obj).firstChild;
                node.appendChild(geom);
            } else {
                node = this.createElementNSPlus("wfs:Value", {value: obj});
            }
            return node;
        },
        "Delete": function (obj) {
            var feature = obj.feature;
            var options = obj.options;
            var node = this.createElementNSPlus("wfs:Delete", {
                attributes: {
                    handle: options && options.handle,
                    typeName: (this.featureNS ? this.featurePrefix + ":" : "") +
                    this.featureType
                }
            });
            if (this.featureNS) {
                this.setAttributeNS(
                    node, this.namespaces.xmlns,
                    "xmlns:" + this.featurePrefix, this.featureNS
                );
            }
            this.writeNode("ogc:Filter", new OpenLayers.Filter.FeatureId({
                fids: [feature.fid]
            }), node);
            return node;
        }
    }
};

/** WFS v1.0.0 **/


/**
 * Property: writers
 * As a compliment to the readers property, this structure contains public
 *     writing functions grouped by namespace alias and named like the
 *     node names they produce.
 */
OpenLayers.Format.WFST.v1_0_0.prototype.writers = {
    "wfs": OpenLayers.Util.applyDefaults({
        "Query": function (options) {
            options = OpenLayers.Util.extend({
                featureNS: this.featureNS,
                featurePrefix: this.featurePrefix,
                featureType: this.featureType,
                srsName: this.srsName,
                srsNameInQuery: this.srsNameInQuery
            }, options);
            var prefix = options.featurePrefix;
            var node = this.createElementNSPlus("wfs:Query", {
                attributes: {
                    typeName: (prefix ? prefix + ":" : "") +
                    options.featureType
                }
            });
            if (options.srsNameInQuery && options.srsName) {
                node.setAttribute("srsName", options.srsName);
            }
            if (options.featureNS) {
                this.setAttributeNS(
                    node, this.namespaces.xmlns,
                    "xmlns:" + prefix, options.featureNS
                );
            }
            if (options.propertyNames) {
                for (var i = 0, len = options.propertyNames.length; i < len; i++) {
                    this.writeNode(
                        "ogc:PropertyName",
                        {property: options.propertyNames[i]},
                        node
                    );
                }
            }
            if (options.filter) {
                this.setFilterProperty(options.filter);
                this.writeNode("ogc:Filter", options.filter, node);
            }
            return node;
        }
    }, OpenLayers.Format.WFST.v1.prototype.writers["wfs"]),
    "gml": OpenLayers.Format.GML.v2.prototype.writers["gml"],
    "feature": OpenLayers.Format.GML.v2.prototype.writers["feature"],
    "ogc": OpenLayers.Format.Filter.v1_0_0.prototype.writers["ogc"]
};

/** WFS v1.1.0 **/

/* NS already added in v1. */

/**
 * Property: writers
 * As a compliment to the readers property, this structure contains public
 *     writing functions grouped by namespace alias and named like the
 *     node names they produce.
 */
OpenLayers.Format.WFST.v1_1_0.prototype.writers = {
    "wfs": OpenLayers.Util.applyDefaults({
        "GetFeature": function (options) {
            var node = OpenLayers.Format.WFST.v1.prototype.writers["wfs"]["GetFeature"].apply(this, arguments);
            options && this.setAttributes(node, {
                resultType: options.resultType,
                startIndex: options.startIndex,
                count: options.count
            });
            return node;
        },
        "Query": function (options) {
            options = OpenLayers.Util.extend({
                featureNS: this.featureNS,
                featurePrefix: this.featurePrefix,
                featureType: this.featureType,
                srsName: this.srsName
            }, options);
            var prefix = options.featurePrefix;
            var node = this.createElementNSPlus("wfs:Query", {
                attributes: {
                    typeName: (prefix ? prefix + ":" : "") +
                    options.featureType,
                    srsName: options.srsName
                }
            });
            if (options.featureNS) {
                this.setAttributeNS(node, this.namespaces.xmlns,
                    "xmlns:" + prefix, options.featureNS);
            }
            if (options.propertyNames) {
                for (var i = 0, len = options.propertyNames.length; i < len; i++) {
                    this.writeNode(
                        "wfs:PropertyName",
                        {property: options.propertyNames[i]},
                        node
                    );
                }
            }
            if (options.filter) {
                OpenLayers.Format.WFST.v1_1_0.prototype.setFilterProperty.call(this, options.filter);
                this.writeNode("ogc:Filter", options.filter, node);
            }
            return node;
        },
        "PropertyName": function (obj) {
            return this.createElementNSPlus("wfs:PropertyName", {
                value: obj.property
            });
        }
    }, OpenLayers.Format.WFST.v1.prototype.writers["wfs"]),
    "gml": OpenLayers.Format.GML.v3.prototype.writers["gml"],
    "feature": OpenLayers.Format.GML.v3.prototype.writers["feature"],
    "ogc": OpenLayers.Format.Filter.v1_1_0.prototype.writers["ogc"]
};

/** END fix Namespaces IE11 */