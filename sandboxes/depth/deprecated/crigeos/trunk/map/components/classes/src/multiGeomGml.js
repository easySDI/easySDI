/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */


 /**
  *  Custom WFS Protocol class that reads multiple layers from the different
  * geometry properties in a WFS response GML.
  */
 OpenLayers.Protocol.WFS.Custom = OpenLayers.Class(OpenLayers.Protocol.WFS.v1, {

   initialize: function(options) {
     this.readModeMulti = false;
     OpenLayers.Protocol.WFS.v1.prototype.initialize.apply(this, [options]);
   },

    /**
     * Method: handleRead
     * Deal with response from the read request.
     *
     * Parameters:
     * response - {<OpenLayers.Protocol.Response>} The response object to pass
     *     to the user callback. In this case, there are multiple sets of features,
     *     one per layer.
     * options - {Object} The user options passed to the read call.
     */
    handleRead: function(response, options) {
        if(options.callback) {
            var request = response.priv;
            if(request.status >= 200 && request.status < 300) {
                // success
                  response.features = [];
                  var idx=0;
                  for (var geom in SData.searchPrecisions) {
                    var p = SData.searchPrecisions[geom];
                    if (p.required) {
                      // Because the geometry attributes were included in the request by also parsing the active
                      // search precisions, idx should always tally to the index of the geom attribute in the response.
                      this.format.geometryIndex = idx;
                      // this will create the features using the geom identified by its index.
                      var features = this.parseFeatures(request);
                      response.features.push(features);
                      idx++;
                    }
                  }
                  //No search precision defined or selected, must add a feature corresponding to the search layer definition
                  if(idx === 0)
                  {
                  	this.format.geometryIndex = idx;
                  	var features = this.parseFeatures(request);
                    response.features.push(features);
                  }
                response.code = OpenLayers.Protocol.Response.SUCCESS;
            } else {
                // failure
                response.code = OpenLayers.Protocol.Response.FAILURE;
            }
            options.callback.call(options.scope, response);
        };
    },

    CLASS_NAME: "OpenLayers.Protocol.WFS.Custom"

 });

/**
 * Custom version of the OpenLayers GML format reader. This is no different from
 * the standard GML format, except that it allows the reader to be configured to
 * create the geometry from any geom in the feature, not just the first.
 */
OpenLayers.Format.GML.custom = OpenLayers.Class(OpenLayers.Format.GML.v2, {

  initialize: function(options) {
    OpenLayers.Format.GML.v2.prototype.initialize.apply(this, [options]);
  },

  /**
   * Replace the default GML readers with ones that can get geometry info from
   * the geom identified by the geometryIndex, rather than always the first one.
   * Also ensure that the readers don't enforce only one geom, by always pushing the
   * geometry onto the components array rather than replacing the array.
   */
  readers: {
    "gml": OpenLayers.Util.applyDefaults({
      "boundedBy": function(node, obj) {
        var container = {};
        this.readChildNodes(node, container);
        if(container.components && container.components.length > 0) {
          obj.bounds = container.components[this.geometryIndex];
        }
      },
      "MultiPoint": function(node, container) {
        var obj = {components: []};
        this.readChildNodes(node, obj);
        container.components.push(
          new OpenLayers.Geometry.MultiPoint(obj.components)
        );
      },
      "MultiLineString": function(node, container) {
        var obj = {components: []};
        this.readChildNodes(node, obj);
        container.components.push(
          new OpenLayers.Geometry.MultiLineString(obj.components)
        );
      },
      "LinearRing": function(node, obj) {
        var container = {};
        this.readChildNodes(node, container);
        if (typeof obj.components=="undefined")
          obj.components = [];
        obj.components.push(new OpenLayers.Geometry.LinearRing(
          container.points
        ));
      },
      "MultiPolygon": function(node, container) {
        var obj = {components: []};
        this.readChildNodes(node, obj);
        container.components.push(
          new OpenLayers.Geometry.MultiPolygon(obj.components)
        );
      }
    }, OpenLayers.Format.GML.v2.prototype.readers["gml"]),
    "feature": OpenLayers.Util.applyDefaults({
      "_typeName": function(node, obj) {
        var container = {components: [], attributes: {}};
        this.readChildNodes(node, container);
        // we have to clone the container's attributes as they will be duplicated into other features.
        var attrs = OpenLayers.Util.applyDefaults({}, container.attributes);
        // look for common gml namespaced elements
        if(container.name) {
            attrs.name = container.name;
        }
        var feature = new OpenLayers.Feature.Vector(
            container.components[this.geometryIndex], attrs
        );
        if (!this.singleFeatureType) {
            feature.type = node.nodeName.split(":").pop();
            feature.namespace = node.namespaceURI;
        }
        var fid = node.getAttribute("fid") ||
            this.getAttributeNS(node, this.namespaces["gml"], "id");
        if(fid) {
            feature.fid = fid;
        }
        if(this.internalProjection && this.externalProjection &&
           feature.geometry) {
            feature.geometry.transform(
                this.externalProjection, this.internalProjection
            );
        }
        if(container.bounds) {
            feature.geometry.bounds = container.bounds;
        }
        obj.features.push(feature);
      }
    }, OpenLayers.Format.GML.Base.prototype.readers["feature"]),
    "wfs": OpenLayers.Format.GML.Base.prototype.readers["wfs"]
  },

  CLASS_NAME: "OpenLayers.Format.GML.custom"

});

/**
 * Custom version of the WFST format reader class. This integrates our custom
 * GML format reader, allowing the geom for features to be picked up from
 * a different property index.
 * Set the geometryIndex before calling the read method.
 *
 * This class has a dependency on SData.searchPrecisions
 */
OpenLayers.Format.WFST.Custom = OpenLayers.Class(
    OpenLayers.Format.WFST.v1_0_0, {

  initialize: function(options) {
    this.geometryIndex = 0; // default
    OpenLayers.Format.WFST.v1_0_0.prototype.initialize.apply(this, [options]);
  },

  /**
   * Property: readers
   * Contains public functions, grouped by namespace prefix, that will
   *     be applied when a namespaced node is found matching the function
   *     name.  The function will be applied in the scope of this parser
   *     with two arguments: the node being read and a context object passed
   *     from the parent.
   */
  readers: {
      "wfs": OpenLayers.Format.WFST.v1_0_0.prototype.readers["wfs"],
      "gml": OpenLayers.Format.GML.custom.prototype.readers["gml"],
      "feature": OpenLayers.Format.GML.custom.prototype.readers["feature"],
      "ogc": OpenLayers.Format.Filter.v1_0_0.prototype.readers["ogc"]
  },

  CLASS_NAME: "OpenLayers.Format.WFST.Custom"

});

/**
 * Class: OpenLayers.Strategy.FixedMultiLayer
 * A simple strategy that behaves like a fixed strategy but can load data onto multiple
 * layers if there are several geometry attributes in a feature type.
 *
 * Inherits from:
 *  - <OpenLayers.Strategy.Fixed>
 */
OpenLayers.Strategy.FixedMultiLayer = OpenLayers.Class(OpenLayers.Strategy.Fixed, {

  initialize: function(options) {
    this.layers = [];
    this.protocol = options.protocol;
    // Don't want to activate for each layer added.
    options.autoActivate=false;
    OpenLayers.Strategy.Fixed.prototype.initialize.apply(this, [options]);
  },

  destroy: function() {
    OpenLayers.Strategy.Fixed.prototype.destroy.apply(this, arguments);
  },

  /**
   * Grab any layers added to this strategy into an array, since we can have multiple.
   */
  setLayer: function(layer) {
    this.layers.push(layer);
  },

  activate: function() {
    if(OpenLayers.Strategy.prototype.activate.apply(this, arguments)) {
      Ext.each(this.layers, function(item) {
        item.events.on({
          "refresh": this.load,
          scope: this
        });
      }, this);
      this.load();
      return true;
    }
    return false;
  },

    /**
     * Method: deactivate
     * Deactivate the strategy.  Undo what is done in <activate>.
     *
     * Returns:
     * {Boolean} The strategy was successfully deactivated.
     */
    deactivate: function() {
        var deactivated = OpenLayers.Strategy.prototype.deactivate.call(this);
        if(deactivated) {
            this.layer.events.un({
                "refresh": this.load,
                "visibilitychanged": this.load,
                scope: this
            });
        }
        return deactivated;
    },

    /**
     * Method: load
     * Tells protocol to load data.
     *
     * Parameters:
     * options - {Object} options to pass to protocol read.
     */
    load: function(options) {
      Ext.each(this.layers, function(item) {
        item.events.triggerEvent("loadstart");
      });
      if (typeof this.protocol != "undefined") {
        this.protocol.read(OpenLayers.Util.applyDefaults({
          callback: this.merge,
          scope: this
        }, options));
      }
    },


    /**
     * Method: merge
     * Add all features to all layers.
     */
    merge: function(resp) {
      var featuresArr = resp.features;
      // Build a quick way to lookup the geom attribute indexes
      var geomAttrs = {};
      var idx=0;
      for (var geom in SData.searchPrecisions) {
        if (SData.searchPrecisions[geom].required) {
          geomAttrs[geom] = {index: idx};
          idx++;
        }
      }
      
      //No precision defined or selected, must use the search layer geometry
      if(idx === 0)
      {
      	geomAttrs[SData.searchLayer.geometryName] = {index: idx};
      }
      Ext.each(this.layers, function(layer, i) {
        layer.destroyFeatures();
        layer.addFeatures(featuresArr[geomAttrs[layer.geometryName].index]);
        layer.events.triggerEvent("loadend");
      }, this);
    },

  CLASS_NAME: "OpenLayers.Strategy.FixedMultiLayer"

});